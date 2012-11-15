<?php

class TiramizooApi 
{
    private $api_url = null;
    private $api_key = null;

    public function __construct($api_url, $api_key) 
    {
        $this->api_url = $api_url;
        $this->api_key = $api_key;
    }
    
    public function getQuotes($data = array())
    {
        $result = null;
        $this->request('/quotes', $data, $result);
        return $result;
    }

    public function request($method, $data = array(), &$result = false) 
    {
        $c = curl_init();

        curl_setopt($c, CURLOPT_URL, $this->api_url.'/'.$method.'?api_key='.MODULE_SHIPPING_TIRAMIZOO_APIKEY);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, preg_replace_callback('/(\\\u[0-9a-f]{4})/', array($this, "json_unescape"), json_encode($data)));

        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json"
        ));

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        curl_close($c);

        switch ($status) {
            case 200:
            case 201:
                $result = json_decode($result,true);
                return true;
            break;
            default:
                return false;
            break;
        }

    }   
    
    private function json_unescape($m) 
    {
        return json_decode('"'.$m[1].'"');
    }
      
}