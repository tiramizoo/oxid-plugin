<?php


/**
 * Extends oxorder class. Overrides method to allow send order to the API
 */
class oxTiramizoo_oxorder extends oxTiramizoo_oxorder_parent
{

    /**
     * Returns order delivery expenses price object
     *
     * @return oxprice
     */
    public function getOrderDeliveryPrice()
    {
        $this->_oDelPrice = parent::getOrderDeliveryPrice();

        if (in_array(oxSession::getVar('sShipSet'), array('Tiramizoo', 'TiramizooEvening', 'TiramizooSelectTime'))) {
            
            $sTiramizooDeliveryType = oxSession::getVar('sTiramizooDeliveryType');
            $sTiramizooDeliveryType = $sTiramizooDeliveryType ? $sTiramizooDeliveryType : '';

            $this->_oDelPrice->setPrice( $this->oxorder__oxdelcost->value );
        }

        return $this->_oDelPrice;
    }


    /**
     * Load data from basket. Build order data and send order to API. If response status is not 201
     * throw an exception.
     * 
     * @param  oxBasket $oBasket
     * @return null
     */
    protected function _loadFromBasket( oxBasket $oBasket )
    {
        parent::_loadFromBasket($oBasket);

        if (in_array(oxSession::getVar('sShipSet'), array('Tiramizoo', 'TiramizooEvening', 'TiramizooSelectTime'))) {
            $oxConfig = $this->getConfig();
            $oDeliveryAddress = $this->getDelAddressInfo();
            $oUser = $this->getUser();
            $oxTiramizooApi = oxTiramizooApi::getInstance();
            $sCurrentLang = oxLang::getInstance()->getLanguageAbbr(oxLang::getInstance()->getBaseLanguage());

            $tiramizooData = new stdClass();

            $tiramizooData->pickup = $oxTiramizooApi->buildPickupObject( $oxConfig, oxSession::getVar( 'sTiramizooTimeWindow' ) );
            $tiramizooData->delivery = $oxTiramizooApi->buildDeliveryObject( $oUser, $oDeliveryAddress );
            $tiramizooData->description = $oxTiramizooApi->buildDescription( $oBasket );
            $tiramizooData->external_id = md5(time());
            $tiramizooData->web_hook_url = trim($oxConfig->getShopConfVar('oxTiramizoo_shop_url'), '/') . '/modules/oxtiramizoo/api.php';
            $tiramizooData->items = $oxTiramizooApi->buildItemsData( $oBasket );

            $tiramizooResult = $oxTiramizooApi->sendOrder($tiramizooData);

            if (!in_array($tiramizooResult['http_status'], array(201))) {

                // Uncomment to debug
                // echo '<div>';
                // echo json_encode($tiramizooData);
                // echo json_encode($tiramizooResult);
                // echo '</div>';
                // 
                $errorMessage = oxLang::getInstance()->translateString('oxTiramizoo_post_order_error', oxLang::getInstance()->getBaseLanguage(), false);
                throw new oxTiramizoo_SendOrderException( $errorMessage );
            }

            $this->oxorder__tiramizoo_params = new oxField(base64_encode(serialize($tiramizooResult)), oxField::T_RAW);
            $this->oxorder__tiramizoo_status = new oxField($tiramizooResult['response']->state, oxField::T_RAW);
            $this->oxorder__tiramizoo_external_id = new oxField($tiramizooData->external_id, oxField::T_RAW);
            $this->oxorder__tiramizoo_tracking_url = new oxField($tiramizooResult['response']->tracking_url . '?locale=' . $sCurrentLang, oxField::T_RAW);
        }
    }
}