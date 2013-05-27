<?php

/**
 * Getting data from Tiramizoo Webhook. Update order's state.
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_Webhook extends oxUBase
{
    /**
     * Accept data. Set order's status
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