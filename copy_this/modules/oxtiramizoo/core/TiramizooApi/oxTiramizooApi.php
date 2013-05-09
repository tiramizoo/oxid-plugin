<?php

/**
 * oxTiramizoo API class used to connection with Tiramizoo API. Main functionality
 * are getting quotes, sending order and build API data.
 *
 * @package: oxTiramizoo
 */
class oxTiramizooApi extends TiramizooApi
{
    /**
     * Singleton instance
     * 
     * @var oxTiramizooApi
     */
    protected static $_instance = null;

    /**
     * Singleton instance
     * 
     * @var oxTiramizooApi
     */
    protected static $_instances = null;

    /**
     * @var mixed used for lazy loading
     **/
    protected $_aAvailableWorkingHours = null;

    /**
     * @var mixed used for lazy loading
     **/
    protected $_aRemoteConfiguration = null;
    
    /**
     * Create the API object with api token and url get from appliaction config
     */
    public function __construct( $sApiToken )
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizooConfig');
        $tiramizooApiUrl = $oTiramizooConfig->getShopConfVar('oxTiramizoo_api_url');
        parent::__construct($tiramizooApiUrl, $sApiToken);
    }

    /**
     * Get the instance of class
     * 
     * @return oxTiramizooApi
     */
    public static function getApiInstance( $sApiToken )
    {
        if ( !isset(self::$_instances[$sApiToken]) && !self::$_instances[$sApiToken] instanceof oxTiramizooApi ) {
            self::$_instances[$sApiToken] = oxnew('oxTiramizooApi', $sApiToken );
        }

        return self::$_instances[$sApiToken];
    }

    /**
     * Send order to the API
     * 
     * @param  mixed $data pickup, delivery and items data
     * @return mixed Array with status code of request and response data
     */
    public function sendOrder($data)
    {
        $result = null;
        $this->request('orders', $data, $result);
        return $result;
    }

    /**
     * Get configuration
     * 
     * @return mixed Array with status code of request and response data
     */
    public function getRemoteConfiguration( $sApiToken = null )
    {
        $data = array();
        
        if ($this->_aRemoteConfiguration === null) {
            $result = null;
            $this->requestGet('configuration', $data, $this->_aRemoteConfiguration);
        }

        if ($this->_aRemoteConfiguration['http_status'] != 200) {
            throw new oxTiramizoo_ApiException("Can't connect to Tiramizoo API", 1);
        }

        return $this->_aRemoteConfiguration;
    }

    /**
     * Get service areas
     * 
     * @param string $sPickupCode
     * @return mixed Array with status code of request and response data
     */
    public function getAvailableServiceAreas($sPostalCode, $aRangeDates = array())
    {
        $response = null;
        $this->requestGet('service_areas/' . $sPostalCode, $aRangeDates, $response);
        
        return $response;
    }


    public static function objectToArray($data)
    {
        if (is_array($data) || is_object($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = self::objectToArray($value);
            }
            return $result;
        }
        return $data;
    }


}