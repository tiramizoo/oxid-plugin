<?php
/**
 * This file is part of the oxTiramizoo OXID eShop plugin.
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  module
 * @package   oxTiramizoo
 * @author    Tiramizoo GmbH <support@tiramizoo.com>
 * @copyright Tiramizoo GmbH
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Admin order extended tiramizoo parameters manager.
 * Collects extended order tiramizoo properties ( such as
 * weight, dimensions, enable tiramizoo delivery). 
 * Admin Menu: Administer Orders -> Orders -> Tiramizoo.
 *
 * @extend oxAdminDetails
 * @package oxTiramizoo
 */
class oxTiramizoo_Order_Tab extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxorder and
     * oxuserpayment objects, passes data to Smarty engine and returns
     * name of template file "order_main.tpl".
     * 
     * @extend oxAdminDetails::render
     *
     * @return string
     */
    public function render()
    {
        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            parent::render();
        }
        // @codeCoverageIgnoreEnd

        $soxId = $this->getConfig()->getRequestParameter( "oxid");
        if ( $soxId != "-1" && isset( $soxId ) ) {
            $oOrder = oxNew( "oxorder" );

            $this->_aViewData["edit"] =  $oOrder;

            $oTiramizooOrderExtended = oxNew('oxTiramizoo_OrderExtended');
            $oTiramizooOrderExtended->load($oTiramizooOrderExtended->getIdByOrderId($soxId));

            $this->_aViewData["oxTiramizooOrderExtended"] =  $oTiramizooOrderExtended;

            $this->_aViewData["aTiramizooWebhookResponse"] = unserialize(base64_decode($oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_webhook_response->value));
            $this->_aViewData["aTiramizooResponse"] = unserialize(base64_decode($oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_response->value));
            $this->_aViewData["aTiramizooRequest"] = unserialize(base64_decode($oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_request_data->value));
        }

        return "oxTiramizoo_order_tab.tpl";
    }
}
