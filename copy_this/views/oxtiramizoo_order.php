<?php
/**
 * This file is part of the module oxTiramizoo for OXID eShop.
 *
 * The module oxTiramizoo for OXID eShop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free Software Foundation
 * either version 3 of the License, or (at your option) any later version.
 *
 * The module oxTiramizoo for OXID eShop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. 
 *  
 * See the GNU General Public License for more details <http://www.gnu.org/licenses/>
 *
 * @copyright: Tiramizoo GmbH
 * @author: Krzysztof Kowalik <kowalikus@gmail.com>
 * @package: oxTiramizoo
 * @license: http://www.gnu.org/licenses/
 * @version: 1.0.0
 * @link: http://tiramizoo.com
 */

require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_helper.php';

/**
 * Tiramizoo Order view. Extends to proccess Tiramizoo delivery
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_order extends oxTiramizoo_order_parent
{
    /**
     * Was any errors when store to API
     * 
     * @var boolean
     */
    protected $_isTiramizooError = false;

    /**
     * Order External_id
     * 
     * @var string
     */
    protected $_external_id = null;

    /**
     * Executes parent::render(), pass variable to template to check
     * if tiramioo module is running now
     * 
     * @return string template file
     */
    public function render()
    {
        $this->_aViewData['isTiramizooOrderView'] = 1;
        return parent::render();
    }

    /**
     * Get tiramizoo deliver time window selected by user 
     * 
     * @return string
     */
    public function getTiramizooTimeWindow()
    {
        if (oxSession::getVar('sShipSet') == 'Tiramizoo') {
            return oxTiramizooHelper::getLabelDeliveryWindow(oxSession::getVar( 'sTiramizooTimeWindow' ));
        }
        return null;
    }

    /**
     * Send order to tiramizoo API
     * 
     * @return mixed response from API
     */
    public function postOrderToTiramizoo()
    {
        $oBasket = $this->getSession()->getBasket();

        $oxConfig = $this->getConfig();

        $oOrder = oxNew( 'oxorder' );
        $deliveryAddress = $oOrder->getDelAddressInfo();

        $this->_external_id = md5(time());

        oxSession::setVar( 'sTiramizooExternalId', $this->_external_id);

        $oUser = $this->getUser();

        $data = new stdClass();

        $data->pickup = new stdClass();
        $data->pickup->address_line_1 = $oxConfig->getShopConfVar('oxTiramizoo_shop_address');
        $data->pickup->city = $oxConfig->getShopConfVar('oxTiramizoo_shop_city');
        $data->pickup->postal_code = $oxConfig->getShopConfVar('oxTiramizoo_shop_postal_code');
        $data->pickup->country_code = $oxConfig->getShopConfVar('oxTiramizoo_shop_country_code');
        $data->pickup->name = $oxConfig->getShopConfVar('oxTiramizoo_shop_contact_name');
        $data->pickup->phone_number = $oxConfig->getShopConfVar('oxTiramizoo_shop_phone_number');
        $data->pickup->email = $oxConfig->getShopConfVar('oxTiramizoo_shop_email_address');
        $data->pickup->after = date('c', strtotime(oxSession::getVar( 'sTiramizooTimeWindow' )));
        $data->pickup->before = date('c', strtotime('+' . $oxConfig->getShopConfVar('oxTiramizoo_pickup_del_offset') . 'minutes', strtotime(oxSession::getVar( 'sTiramizooTimeWindow' ))));

        $data->delivery = new stdClass();
        $data->delivery->email = $oUser->oxuser__oxusername->value; 

        if ($deliveryAddress)  {
            $data->delivery->address_line_1 = $deliveryAddress->oxaddress__oxstreet->value . ' ' . $deliveryAddress->oxaddress__oxstreetnr->value;
            $data->delivery->city = $deliveryAddress->oxaddress__oxcity->value;
            $data->delivery->postal_code = $deliveryAddress->oxaddress__oxzip->value;
            $data->delivery->country_code = $deliveryAddress->oxaddress__oxcountryid->value;
            $data->delivery->name = $deliveryAddress->oxaddress__oxfname->value . ' ' . $deliveryAddress->oxaddress__oxlname->value;
            $data->delivery->phone_number = $deliveryAddress->oxaddress__oxfon->value;
        } else {
            $data->delivery->address_line_1 = $oUser->oxuser__oxstreet->value . ' ' . $oUser->oxuser__oxstreetnr->value;
            $data->delivery->city = $oUser->oxuser__oxcity->value;
            $data->delivery->postal_code = $oUser->oxuser__oxzip->value;
            $data->delivery->country_code = $oUser->oxuser__oxcountryid->value;
            $data->delivery->name = $oUser->oxusers__oxfname->value . ' ' . $oUser->oxuser__oxlname->value;
            $data->delivery->phone_number = $oUser->oxuser__oxfon->value;
        }

        //get country code
        $oCountry = oxNew('oxcountry');
        $oCountry->load($data->delivery->country_code);

        $data->delivery->country_code = strtolower($oCountry->oxcountry__oxisoalpha2->value);

        $itemNames = array();
        foreach ($oBasket->getBasketArticles() as $key => $oArticle) 
        {
            $itemNames[] = $oArticle->oxarticles__oxtitle->value . ' (x' . $oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value) . ')';
        }

        $data->description = substr(implode($itemNames, ', '), 0, 255);
        
        $data->external_id = $this->_external_id;
        $data->web_hook_url = trim($oxConfig->getShopConfVar('oxTiramizoo_shop_url'), '/') . '/modules/oxtiramizoo/api.php';

        $data->items = array();

        require_once getShopBasePath() . '/modules/oxtiramizoo/core/TiramizooApi/oxTiramizooApi.php';

        $data->items = oxTiramizooApi::getInstance()->buildItemsData($oBasket);

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

    /**
     * Check if was error in template
     * 
     * @return boolean
     */
    public function isTiramizooError()
    {
        return $this->_isTiramizooError;
    }

    /**
     * Get the error message
     * 
     * @return string
     */
    public function getTiramizooError()
    {
        if ($this->isTiramizooError()) {
            
            $return = oxLang::getInstance()->translateString('oxTiramizoo_post_order_error', oxLang::getInstance()->getBaseLanguage(), false);

            //$tiramizooResult = $this->postOrderToTiramizoo();

            //uncomment if You want to debug
            // $return = 'Tiramizoo Error code: ' . $tiramizooResult['http_status'] . ' ' . $tiramizooResult['response']->code;    
            // $return .= '<ul>';
            // foreach ($tiramizooResult['response']->errors as $error) {
            //     $return .= '<li>' . $error->message . '</li>';
            // }
            // $return .= '</ul>';

            return $return;
        }
    }

    /**
     * Execute save order with tiramizoo shipping
     * 
     * @return string template
     */
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

        // get basket contents
        $oBasket  = $this->getSession()->getBasket();
        if ( $oBasket->getProductsCount() ) {

            try {
                $oOrder = oxNew( 'oxorder' );

                //check if tiramizoo was selected, proccess order
                if (oxSession::getVar('sShipSet') == 'Tiramizoo') {

                    $tiramizooResult = $this->postOrderToTiramizoo();

                    if ($this->isTiramizooError()) {
                        return;
                    }

                    $oOrder->oxorder__tiramizoo_params = new oxField(base64_encode(serialize($tiramizooResult)), oxField::T_RAW);
                    $oOrder->oxorder__tiramizoo_status = new oxField($tiramizooResult['response']->state, oxField::T_RAW);
                    $oOrder->oxorder__tiramizoo_external_id = new oxField(oxSession::getVar( 'sTiramizooExternalId' ), oxField::T_RAW);
                    $oOrder->oxorder__tiramizoo_tracking_url = new oxField($tiramizooResult['response']->tracking_url . '?locale=' . oxLang::getInstance()->getLanguageAbbr(oxLang::getInstance()->getBaseLanguage()), oxField::T_RAW);
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