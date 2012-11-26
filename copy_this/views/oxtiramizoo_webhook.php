<?php
class oxTiramizoo_Webhook extends oxUBase
{
    public function render()
    {
        $aApiResponse = $this->getConfig()->getParameter('api_response');

        $sql = "UPDATE oxorder 
                    SET TIRAMIZOO_WEBHOOK_RESPONSE = '" . base64_encode(serialize($aApiResponse)) . "'
                    WHERE TIRAMIZOO_EXTERNAL_ID = '" . $aApiResponse->external_id . "';";

        oxDb::getDb()->Execute($sql);

        header("HTTP/1.1 200 OK");
        die('OK');
        return;
    }
}