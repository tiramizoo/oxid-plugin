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
 * oxTiramizoo API class used to connection with Tiramizoo API. Main functionality
 * are getting configuration, sending order and getting service areas. Implement Singleton pattern.
 * 
 * @extend TiramizooApi
 * @package oxTiramizoo
 */
class oxTiramizoo_Api extends TiramizooApi
{
    /**
     * Array of singleton instances oxTiramizoo_Api class
     * 
     * @var array
     */
    protected static $_instances = null;

    /**
     * Array of available working hours used for lazy loading
     * 
     * @var mixed 
     */
    protected $_aAvailableWorkingHours = null;

    /**
     * Array of remote configuration used for lazy loading
     * 
     * @var mixed 
     */
    protected $_aRemoteConfiguration = null;
    
    /**
     * Create the API object with API token and url
     * executes parent::_construct()
     * 
     * @extend TiramizooApi::_construct()
     *
     * @param string $sApiToken API token
     * 
     * @return null
     */
    public function __construct( $sApiToken )
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');
        $tiramizooApiUrl = $oTiramizooConfig->getShopConfVar('oxTiramizoo_api_url');
        parent::__construct($tiramizooApiUrl, $sApiToken);
    }

    /**
     * Get the instance by API token
     *
     * @param string $sApiToken API token
     * 
     * @return oxTiramizoo_Api
     */
    public static function getApiInstance( $sApiToken )
    {
        if ( !isset(self::$_instances[$sApiToken]) && !self::$_instances[$sApiToken] instanceof oxTiramizoo_Api ) {
            self::$_instances[$sApiToken] = oxnew('oxTiramizoo_Api', $sApiToken );
        }

        return self::$_instances[$sApiToken];
    }

    /**
     * Send order to the API
     * 
     * @param  mixed $data pickup, delivery and items data
     *
     * @return mixed Array with status code of request and response data
     */
    public function sendOrder($data)
    {
        $result = null;
        $this->request('orders', $data, $result);
        return $result;
    }

    /**
     * Get remote configuration
     *
     * @param string $sApiToken API token
     * @throws oxTiramizoo_ApiException if status not equal 200
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
     * @param string $sPostalCode postal code 
     * @param string $aRangeDates range dates
     * 
     * @return mixed Array with status code of request and response data
     */
    public function getAvailableServiceAreas($sPostalCode, $aRangeDates = array())
    {
        $response = null;
        $this->requestGet('service_areas/' . $sPostalCode, $aRangeDates, $response);
        
        return $response;
    }
}
