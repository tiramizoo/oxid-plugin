<?php

class oxTiramizoo_order extends oxTiramizoo_order_parent
{
    protected $_isTiramizooError = false;
    protected $_external_id = null;


    public function getTiramizooTimeWindow()
    {
        if (oxSession::getVar('sShipSet') == 'Tiramizoo') {
            $paymentView = new oxTiramizoo_Payment();
            return $paymentView->getLabelDeliveryWindow(oxSession::getVar( 'sTiramizooTimeWindow' ));
        }
        return null;
    }

    public function postOrderToTiramizoo()
    {
        $oBasket = $this->getSession()->getBasket();

        $oxTiramizooConfig = $this->getConfig();

        $oOrder = oxNew( 'oxorder' );
        $deliveryAddress = $oOrder->getDelAddressInfo();

        $this->_external_id = md5(time());

        oxSession::setVar( 'sTiramizooExternalId', $this->_external_id);

        $oUser = $this->getUser();

        $data = new stdClass();

        $data->pickup = new stdClass();
        $data->pickup->address_line_1 = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_address');
        $data->pickup->city = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_city');
        $data->pickup->postal_code = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_postal_code');
        $data->pickup->country_code = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_country_code');
        $data->pickup->name = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_contact_name');
        $data->pickup->phone_number = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_phone_number');
        $data->pickup->email = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_email_address');
        $data->pickup->after = date('c', strtotime(oxSession::getVar( 'sTiramizooTimeWindow' )));
        $data->pickup->before = date('c', strtotime('+' . $oxTiramizooConfig->getShopConfVar('oxTiramizoo_pickup_del_offset') . 'minutes', strtotime(oxSession::getVar( 'sTiramizooTimeWindow' ))));


        $data->delivery = new stdClass();
        $data->delivery->email = $oUser->oxuser__oxusername->value; 

        if ($deliveryAddress)  {
            $data->delivery->address_line_1 = $deliveryAddress->oxaddress__oxstreet->value . ' ' . $deliveryAddress->oxaddress__oxstreetnr->value;
            $data->delivery->city = $deliveryAddress->oxaddress__oxcity->value;
            $data->delivery->postal_code = $deliveryAddress->oxaddress__oxzip->value;
            $data->delivery->country_code = $deliveryAddress->oxaddress__oxcountry->value;
            $data->delivery->name = $deliveryAddress->oxaddress__oxfname->value . ' ' . $deliveryAddress->oxaddress__oxlname->value;
            $data->delivery->phone_number = $deliveryAddress->oxaddress__oxfon->value;
        } else {
            $data->delivery->address_line_1 = $oUser->oxuser__oxstreet->value . ' ' . $oUser->oxuser__oxstreetnr->value;
            $data->delivery->city = $oUser->oxuser__oxcity->value;
            $data->delivery->postal_code = $oUser->oxuser__oxzip->value;
            $data->delivery->country_code = $oUser->oxuser__oxcountry->value;
            $data->delivery->name = $oUser->oxusers__oxfname->value . ' ' . $oUser->oxuser__oxlname->value;
            $data->delivery->phone_number = $oUser->oxuser__oxfon->value;
        }

        $data->delivery->country_code = 'de';
        $data->delivery->phone_number = '+48508411677';

        $data->description = "oxTiramizoo articles test";
        $data->external_id = $this->_external_id;
        $data->web_hook_url = trim($oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_url'), '/') . '/index.php?cl=oxtiramizoo_webhook';

        $data->items = array();


        //TODO: create basket items to other class
        foreach ($oBasket->getBasketArticles() as $key => $oArticle) 
        {
            $item = new stdClass();
            $item->weight = null;
            $item->width = null;
            $item->height = null;
            $item->length = null;

            $inheritedData = $this->_getArticleInheritData($oArticle);

            //article is disabled return false
            if ($oArticle->oxarticles__tiramizoo_enable->value == -1) {
                return false;
            }

            if ($oArticle->oxarticles__tiramizoo_enable->value == 0) {
                if (isset($inheritedData['tiramizoo_enable']) && ($inheritedData['tiramizoo_enable'] == -1)) {
                    return false;
                }            
            }

            if ($oArticle->oxarticles__oxweight->value) {
                $item->weight = $oArticle->oxarticles__oxweight->value;
            } else {
                $item->weight = isset($inheritedData['weight']) && $inheritedData['weight'] ? $inheritedData['weight'] : 0;
            }

            if ($oArticle->oxarticles__oxwidth->value) {
                $item->width = $oArticle->oxarticles__oxwidth->value * 100;
            } else {
                $item->width = isset($inheritedData['width']) && $inheritedData['width'] ? $inheritedData['width'] : 0;
            }

            if ($oArticle->oxarticles__oxheight->value) {
                $item->height = $oArticle->oxarticles__oxheight->value * 100;
            } else {
                $item->height = isset($inheritedData['height']) && $inheritedData['height'] ? $inheritedData['height'] : 0;
            }

            if ($oArticle->oxarticles__oxlength->value) {
                $item->length = $oArticle->oxarticles__oxlength->value * 100;
            } else {
                $item->length = isset($inheritedData['length']) && $inheritedData['length'] ? $inheritedData['length'] : 0;
            }

            $item->quantity = $oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value);


            $item->weight = floatval($item->weight);
            $item->width = floatval($item->width);
            $item->height = floatval($item->height);
            $item->length = floatval($item->length);
            $item->quantity = floatval($item->quantity);


            $data->items[] = $item;
        }


        require_once getShopBasePath() . '/modules/oxtiramizoo/core/TiramizooApi/oxTiramizooApi.php';

        $result = oxSession::getVar('tiramizooOrderResponse');

        if (!$result) {
            $result = oxTiramizooApi::getInstance()->setOrder($data);
            if (in_array($result['http_status'], array(200, 201))) {
                oxSession::setVar('tiramizooOrderResponse', $result);
            } else {
                $this->_isTiramizooError = true;
            }
        }

        return $result;
    }


    protected function _getArticleInheritData($oArticle)
    {
        $oCategory = $oArticle->getCategory();

        $aCheckCategories = $this->getParentsTree($oCategory);

        $oxTiramizooInheritedData = array();

        foreach ($aCheckCategories as $aCategoryData) 
        {
            if (isset($aCategoryData['tiramizoo_enable'])) {
                $oxTiramizooInheritedData['tiramizoo_enable'] = $aCategoryData['tiramizoo_enable'];
            }

            if ($aCategoryData['tiramizoo_weight']) {
                $oxTiramizooInheritedData['weight'] = $aCategoryData['tiramizoo_weight'];
            }

            if ($aCategoryData['tiramizoo_width']) {
                $oxTiramizooInheritedData['width'] = $aCategoryData['tiramizoo_width'];
            }

            if ($aCategoryData['tiramizoo_height']) {
                $oxTiramizooInheritedData['height'] = $aCategoryData['tiramizoo_height'];
            }

            if ($aCategoryData['tiramizoo_length']) {
                $oxTiramizooInheritedData['length'] = $aCategoryData['tiramizoo_length'];
            }                                    
        }

        return $oxTiramizooInheritedData;
    }

    //@TODO: Remove implementing hierarchy all categories should be filled completly
    public function getParentsTree($oCategory, $returnCategories = array())
    {
        $oxTiramizooCategoryData = array();
        $oxTiramizooCategoryData['oxid'] = $oCategory->oxcategories__oxid->value;
        $oxTiramizooCategoryData['oxtitle'] = $oCategory->oxcategories__oxtitle->value;
        $oxTiramizooCategoryData['oxsort'] = $oCategory->oxcategories__oxsort->value;
        $oxTiramizooCategoryData['tiramizoo_enable'] = $oCategory->oxcategories__tiramizoo_enable->value;
        $oxTiramizooCategoryData['tiramizoo_weight'] = $oCategory->oxcategories__tiramizoo_weight->value;
        $oxTiramizooCategoryData['tiramizoo_width'] = $oCategory->oxcategories__tiramizoo_width->value;
        $oxTiramizooCategoryData['tiramizoo_height'] = $oCategory->oxcategories__tiramizoo_height->value;
        $oxTiramizooCategoryData['tiramizoo_length'] = $oCategory->oxcategories__tiramizoo_length->value;

        array_unshift($returnCategories, $oxTiramizooCategoryData);
        if ($parentCategory = $oCategory->getParentCategory()) {
            $returnCategories = $this->getParentsTree($parentCategory, $returnCategories);
        }

        return $returnCategories;
    }

    public function isTiramizooError()
    {
        return $this->_isTiramizooError;
    }

    public function getTiramizooError()
    {
        if ($this->isTiramizooError()) {
            $tiramizooResult = $this->postOrderToTiramizoo();
            //print_r($tiramizooResult);

            $return = 'Tiramizoo Error code: ' . $tiramizooResult['http_status'] . ' ' . $tiramizooResult['response']->code;    
            $return .= '<ul>';
            foreach ($tiramizooResult['response']->errors as $error) {
                $return .= '<li>' . $error->message . '</li>';
            }
            $return .= '</ul>';
            return $return;
        }
    }



    public function execute()
    {
        if (!$this->getSession()->checkSessionChallenge()) {
            return;
        }

        $myConfig = $this->getConfig();

        if ( !oxConfig::getParameter( 'ord_agb' ) && $myConfig->getConfigParam( 'blConfirmAGB' ) ) {
            $this->_blConfirmAGBError = 1;
            return;
        }

        // for compatibility reasons for a while. will be removed in future
        if ( oxConfig::getParameter( 'ord_custinfo' ) !== null && !oxConfig::getParameter( 'ord_custinfo' ) && $this->isConfirmCustInfoActive() ) {
            $this->_blConfirmCustInfoError =  1;
            return;
        }

        // additional check if we really really have a user now
        if ( !$oUser= $this->getUser() ) {
            return 'user';
        }

        if (oxSession::getVar('sShipSet') == 'Tiramizoo') {
            $tiramizooResult = $this->postOrderToTiramizoo();

            if ($this->isTiramizooError()) {
                return;
            }
        }

        // get basket contents
        $oBasket  = $this->getSession()->getBasket();
        if ( $oBasket->getProductsCount() ) {

            try {
                $oOrder = oxNew( 'oxorder' );

                if (oxSession::getVar('sShipSet') == 'Tiramizoo') {
                    $oOrder->oxorder__tiramizoo_params = new oxField(base64_encode(serialize($tiramizooResult)), oxField::T_RAW);
                    $oOrder->oxorder__tiramizoo_status = new oxField(1, oxField::T_RAW);
                    $oOrder->oxorder__tiramizoo_external_id = new oxField(oxSession::getVar( 'sTiramizooExternalId' ), oxField::T_RAW);
                    $oOrder->oxorder__tiramizoo_tracking_url = new oxField($tiramizooResult['response']->tracking_url, oxField::T_RAW);
                    oxSession::setVar('tiramizooOrderResponse', null);
                }
                
                // finalizing ordering process (validating, storing order into DB, executing payment, setting status ...)
                $iSuccess = $oOrder->finalizeOrder( $oBasket, $oUser );

                // performing special actions after user finishes order (assignment to special user groups)
                $oUser->onOrderExecute( $oBasket, $iSuccess );

                // proceeding to next view
                return $this->_getNextStep( $iSuccess );
            } catch ( oxOutOfStockException $oEx ) {
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx, false, true, 'basket' );
            } catch ( oxNoArticleException $oEx ) {
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            } catch ( oxArticleInputException $oEx ) {
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            }
        }
    }



}