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
 * Getting data from Tiramizoo Webhook. Update order's state.
 *
 * @extend oxUBase
 * @package oxTiramizoo
 */
class oxTiramizoo_Webhook extends oxUBase
{
    /**
     * Retrieve webhook call data and handle with proper method.
     * 
     * @return null
     */
    public function render()
    {
        // get Api data
        $oApiResponse = json_decode(file_get_contents('php://input'));

        if ($oApiResponse && isset($oApiResponse->external_id)) {
            $this->saveOrderStatus($oApiResponse);
        } else {
            oxRegistry::get('oxUtils')->setHeader('HTTP/1.1 500 Internal Server Error');
            oxRegistry::get('oxUtils')->showMessageAndExit('FALSE');
        }
    }

    /**
     * Set order's status from API response
     * 
     * @param stdObject $oApiResponse Webhook call response data
     * @return null
     */
    public function saveOrderStatus($oApiResponse)
    {
        $sql = "UPDATE oxtiramizooorderextended 
                    SET TIRAMIZOO_WEBHOOK_RESPONSE = '" . base64_encode(serialize($oApiResponse)) . "',
                        TIRAMIZOO_STATUS = '" . $oApiResponse->state . "'
                    WHERE TIRAMIZOO_EXTERNAL_ID = '" . $oApiResponse->external_id . "';";

        oxDb::getDb()->Execute($sql);

        oxRegistry::get('oxUtils')->setHeader('HTTP/1.1 200 OK');
        oxRegistry::get('oxUtils')->showMessageAndExit('OK');
    }
}
