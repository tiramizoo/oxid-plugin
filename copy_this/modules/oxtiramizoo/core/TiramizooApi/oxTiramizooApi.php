<?php
require_once 'TiramizooApi.php';

class oxTiramizooApi extends TiramizooApi
{
    protected static $_instance = null;

    protected function __construct()
    {
        $tiramizooApiUrl = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_api_url');
        $tiramizooApiKey = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_api_key');
        parent::__construct($tiramizooApiUrl, $tiramizooApiKey);
    }

    public static function getInstance()
    {
        if ( !self::$_instance instanceof oxTiramizooApi ) {
                self::$_instance = new oxTiramizooApi();
        }

        return self::$_instance;
    }

    public function getQuotes($data, $cache = false)
    {
        $result = null;

        if ($cache) {
            $cachedDataKey = md5(json_encode($data));
            $cachedesultVarName = 'oxTiramizooQuote_' . $cachedDataKey;

            if ($result = oxSession::hasVar($cachedesultVarName)) {
                return oxSession::getVar($cachedesultVarName);
            }
        }

        $this->request('quotes', $data, $result);

        if ($cache && in_array($result['http_status'], array(200, 201))) {
            oxSession::setVar('oxTiramizooQuote_' . $cachedDataKey, $result);
        }

        return $result;
    }

    public function setOrder($data)
    {
        $result = null;
        $this->request('orders', $data, $result);
        return $result;
    }

}