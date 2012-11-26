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


            $this->_aViewData["aTiramizooWebhookResponse"] = unserialize(base64_decode($oOrder->oxorder__tiramizoo_webhook_response->value));
            $this->_aViewData["aTiramizooParams"] = unserialize(base64_decode($oOrder->oxorder__tiramizoo_params->value));
        }

        return "oxtiramizoo_order_tab.tpl";
    }

}
