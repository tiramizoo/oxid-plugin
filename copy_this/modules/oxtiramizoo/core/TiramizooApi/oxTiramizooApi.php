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
    protected function __construct( $sApiToken )
    {
        $tiramizooApiUrl = oxTiramizooConfig::getInstance()->getShopConfVar('oxTiramizoo_api_url');
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
            self::$_instances[$sApiToken] = new oxTiramizooApi( $sApiToken );
        }

        return self::$_instances[$sApiToken];
    }

    /**
     * Get the instance of class
     * 
     * @return oxTiramizooApi
     */
    public static function getInstance()
    {
        if ( !self::$_instance instanceof oxTiramizooApi ) {
                self::$_instance = new oxTiramizooApi();
        }

        return self::$_instance;
    }

    /**
     * Sending request to the API fo getting quotes
     * 
     * @param  mixed $data Request data
     * @return mixed Array with status code of request and response data
     */
    public function getQuotes($data)
    {
        $result = null;

        $this->request('quotes', $data, $result);

        return $result;
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
     * Get working services hours
     * 
     * @param string $sCountryCode
     * @param string $sPickupCode
     * @param string $sDeliveryCode
     * @return mixed Array with status code of request and response data
     */
    public function getAvailableWorkingHours($sCountryCode, $sPickupCode, $sDeliveryCode)
    {
        $data = array();

        $data['country_code'] = $sCountryCode;
        $data['pickup_postal_code'] = $sPickupCode;
        $data['delivery_postal_code'] = $sDeliveryCode;

        if ($this->_aAvailableWorkingHours === null) {
            $result = null;
            $this->requestGet('service_availability', $data, $this->_aAvailableWorkingHours);
        }

        return $this->_aAvailableWorkingHours;
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



    /**
     * Synchronize service areas for one postal code
     * 
     * @param string $sPostalCode postal code parameter to getting time windows from API
     */
    public function synchronizeServiceAreas($sPostalCode, $aRangeDates = array())
    {
        $response = $this->getAvailableServiceAreas($sPostalCode, $aRangeDates = array());

        if ($response['http_status'] != 200) {
            throw new oxTiramizoo_ApiException("Can't connect to Tiramizoo API", 1);
        }

        oxTiramizooConfig::getInstance()->saveShopConfVar('aarr', 'service_areas_' . $sPostalCode, oxTiramizooApi::objectToArray($response['response']));
    }

    /**
     * Synchronize whole config for all retail locations
     */
    public function synchronizeConfiguration()
    {
        $response = $this->getRemoteConfiguration();

        $aResponse = oxTiramizooApi::objectToArray($response['response']);

        foreach ($aResponse as $configIndex => $configValue) 
        {
            //@ToDo: better check
            if(is_array($configValue)) {
                $variableType = 'aarr';
            } else {
                $variableType = 'str';
            }

            oxTiramizooConfig::getInstance()->saveShopConfVar($variableType, $configIndex, $configValue);
        }
    }

    /**
     * Synchronize retail package sizes information
     */
    public function synchronizePackageSizes()
    {
        throw new oxTiramizoo_ApiException("Not implemented yet", 1);
    }

    /**
     * Synchronize retail location information
     */
    public function synchronizeRetailLocation()
    {
        throw new oxTiramizoo_ApiException("Not implemented yet", 1);
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