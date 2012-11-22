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

        foreach ($oBasket->getBasketArticles() as $key => $oArticle) 
        {
            $item = new stdClass();
            $item->weight = 2;
            $item->width = 30;
            $item->height = 30;
            $item->length = 35;

            if ($oArticle->oxarticles__oxweight->value) {
                $item->weight = $oArticle->oxarticles__oxweight->value;
            }

            if ($oArticle->oxarticles__oxwidth->value) {
                $item->width = $oArticle->oxarticles__oxwidth->value;
            }

            if ($oArticle->oxarticles__oxheight->value) {
                $item->height = $oArticle->oxarticles__oxheight->value;
            }

            if ($oArticle->oxarticles__oxlength->value) {
                $item->length = $oArticle->oxarticles__oxlength->value;
            }

            $item->quantity = $oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value);

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
                    $oOrder->oxorder__tiramizoo_tracking_url = new oxField('http://tiramizoo.com/' . $tiramizooResult['response']->uuid, oxField::T_RAW);
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