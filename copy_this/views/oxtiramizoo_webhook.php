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
            header("HTTP/1.1 500 Internal Server Error");
            die('FALSE');
        }
    }

    public function saveOrderStatus($oApiResponse)
    {
        if ($oApiResponse && isset($oApiResponse->external_id)) {
            $sql = "UPDATE oxorder 
                        SET TIRAMIZOO_WEBHOOK_RESPONSE = '" . base64_encode(serialize($oApiResponse)) . "'
                        WHERE TIRAMIZOO_EXTERNAL_ID = '" . $oApiResponse->external_id . "';";

            oxDb::getDb()->Execute($sql);

            $sql = "UPDATE oxorder 
                        SET TIRAMIZOO_STATUS = '" . $oApiResponse->state . "'
                        WHERE TIRAMIZOO_EXTERNAL_ID = '" . $oApiResponse->external_id . "';";

            oxDb::getDb()->Execute($sql);

            header("HTTP/1.1 200 OK");
            die('OK');
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            die('FALSE');
        }
    }

    public function saveConfiguration()
    {
        $oApiResponse = json_decode(file_get_contents('php://input'));
    }

    public function validResponse()
    {
        //@TODO: need a validation method if tiramizoo is sender IP or something
        return true;
    }
}