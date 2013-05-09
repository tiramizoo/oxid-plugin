<?php

/**
 * Tiramizoo order tab
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_Order_Tab extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxorder and
     * oxuserpayment objects, passes data to Smarty engine and returns
     * name of template file "order_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = oxConfig::getParameter( "oxid");
        if ( $soxId != "-1" && isset( $soxId ) ) {
            // load object
            $oOrder = oxNew( "oxorder" );
            $oOrder->load( $soxId);


            $this->_aViewData["edit"] =  $oOrder;

            $oxTiramizooOrderExtended = oxNew('oxTiramizoo_OrderExtended');
            $soxIdExtended = $oxTiramizooOrderExtended->getIdByOrderId($soxId);
            
            $oxTiramizooOrderExtended->load($soxIdExtended);

            $this->_aViewData["oxTiramizooOrderExtended"] =  $oxTiramizooOrderExtended;


            $this->_aViewData["aTiramizooWebhookResponse"] = unserialize(base64_decode($oxTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_webhook_response->value));
            $this->_aViewData["aTiramizooResponse"] = unserialize(base64_decode($oxTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_response->value));
            $this->_aViewData["aTiramizooRequest"] = unserialize(base64_decode($oxTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_request_data->value));
        }

        return "oxTiramizoo_order_tab.tpl";
    }

}
