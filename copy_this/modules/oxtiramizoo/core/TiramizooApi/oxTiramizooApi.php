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

        $this->requestGet('service_areas/' . $sPostalCode, $aRangeDates = array(), $response);

        return $response;
    }

    /**
     * Build description from product's names. Used for build partial data to send order API request
     * 
     * @param  oxBasket $oBasket
     * @return string description
     */
    public function buildDescription(oxBasket $oBasket)
    {
        $itemNames = array();
        foreach ($oBasket->getBasketArticles() as $key => $oArticle) 
        {
            $itemNames[] = $oArticle->oxarticles__oxtitle->value . ' (x' . $oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value) . ')';
        }

        //string should be contains at least 255 chars
        return substr(implode($itemNames, ', '), 0, 255);
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

        oxTiramizooConfig::getInstance()->saveShopConfVar('aarr', 'service_areas_' . $sPostalCode, oxTiramizooHelper::getInstance()->objectToArray($response['response']));
    }

    /**
     * Synchronize whole config for all retail locations
     */
    public function synchronizeConfiguration()
    {
        $response = $this->getRemoteConfiguration();

        $aResponse = oxTiramizooHelper::getInstance()->objectToArray($response['response']);

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

    /**
     * Build pickup object from tiramizoo config values. Used for build partial data 
     * to send order API request
     * 
     * @param  oxConfig $oxConfig
     * @param  string Selected tiramizoo delivery window
     * @return stdClass Pickup object
     */
    public function buildPickupObject(oxConfig $oxConfig, $sTiramizooWindow)
    {
        $oPickup = new stdClass();

        $oPickup->address_line_1 = $oxConfig->getShopConfVar('oxTiramizoo_shop_address');
        $oPickup->city = $oxConfig->getShopConfVar('oxTiramizoo_shop_city');
        $oPickup->postal_code = $oxConfig->getShopConfVar('oxTiramizoo_shop_postal_code');
        $oPickup->country_code = $oxConfig->getShopConfVar('oxTiramizoo_shop_country_code');
        $oPickup->name = $oxConfig->getShopConfVar('oxTiramizoo_shop_contact_name');
        $oPickup->phone_number = $oxConfig->getShopConfVar('oxTiramizoo_shop_phone_number');
        $oPickup->email = $oxConfig->getShopConfVar('oxTiramizoo_shop_email_address');
        $oPickup->after = date('c', strtotime($sTiramizooWindow));
        $oPickup->before = date('c', strtotime('+' . $oxConfig->getShopConfVar('oxTiramizoo_pickup_time_length') . 'minutes', strtotime($sTiramizooWindow)));

        //check if pickup is not longer than delivery if yes use delivery as pickup
        $pickupWindowLengthInMinutes =  $oxConfig->getShopConfVar('oxTiramizoo_pickup_time_length') >  $oxConfig->getShopConfVar('oxTiramizoo_pickup_del_offset') ? $oxConfig->getShopConfVar('oxTiramizoo_pickup_del_offset') : $oxConfig->getShopConfVar('oxTiramizoo_pickup_time_length');

        $oPickup->before = date('c', strtotime('+' . $pickupWindowLengthInMinutes . 'minutes', strtotime($sTiramizooWindow)));


        //change pickup before time exceed maximum delivery hour
        if (strtotime(date('H:i', strtotime($oPickup->before))) > strtotime(oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour'))) {
            $oPickup->before = date('c', strtotime(date('Y-m-d', strtotime($sTiramizooWindow)) . ' ' . oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour')));
        }

        return $oPickup;
    }



    /**
     * Build items data used for both type of request sending order and getting quotes.
     * If product has no specified params e.g. enable, weight, dimensions it inherits 
     * from main category
     * 
     * @param  oxBasket $oBasket
     * @return array
     */
    public function buildItemsData(oxBasket $oBasket)
    {
        $items = array();

        $sPackageStrategy = oxconfig::getInstance()->getShopConfVar('oxTiramizoo_package_strategy');

        $useStandardPackage = false;


        if ($sPackageStrategy == 1) {
            $aPackageSizes = oxTiramizooHelper::getInstance()->getPackageSizesSortedByVolume();

            if (count($aPackageSizes)) {
                $useAutoFittingToPackage = 1;
                $aAutoFitPackageItems = array();
            }
        } else if ($sPackageStrategy == 2) {

            $stdPackageWidth = oxconfig::getInstance()->getShopConfVar('oxTiramizoo_std_package_width');
            $stdPackageLength = oxconfig::getInstance()->getShopConfVar('oxTiramizoo_std_package_length');
            $stdPackageHeight = oxconfig::getInstance()->getShopConfVar('oxTiramizoo_std_package_height');
            $stdPackageWeight = oxconfig::getInstance()->getShopConfVar('oxTiramizoo_std_package_weight');

            $useStandardPackage = $stdPackageWidth && $stdPackageLength && $stdPackageHeight && $stdPackageWeight;

            $standardPackageAddedToItems = 0;
        }


        foreach ($oBasket->getBasketArticles() as $key => $oArticle) 
        {
            //initialize standard class
            $item = new stdClass();
            $item->weight = null;
            $item->width = null;
            $item->height = null;
            $item->length = null;
            $item->quantity = $oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value);

            //article is disabled return false
            if ($oArticle->oxarticles__tiramizoo_enable->value == -1) {
                return false;
            }

            //check if deliverable is set for articles with stock > 0
            if (oxConfig::getInstance()->getShopConfVar('oxTiramizoo_articles_stock_gt_0')) {
                if ($oArticle->oxarticles__oxstock->value <= 0) {
                    return false;
                }
            }

            //NOTICE if article is only variant of parent article then load parent product as article 
            if ($oArticle->oxarticles__oxparentid->value) {
                $parentArticleId = $oArticle->oxarticles__oxparentid->value;
                
                $oArticleParent = oxNew( 'oxarticle' );
                $oArticleParent->load($parentArticleId);
                $oArticle = $oArticleParent;
            }

            //reload object with all columns
            $oArticleTmp = oxNew( 'oxarticle' );
            $oArticleTmp->load($oArticle->oxarticles__oxid->value);
            $oArticle = $oArticleTmp;

            //article is disabled return false
            if ($oArticle->oxarticles__tiramizoo_enable->value == -1) {
                return false;
            }

            //get the data from categories hierarchy
            $inheritedData = oxTiramizooArticleHelper::getInstance()->getArticleInheritData($oArticle);

            if ($oArticle->oxarticles__tiramizoo_enable->value == 0) {
                if (isset($inheritedData['tiramizoo_enable']) && (!($inheritedData['tiramizoo_enable']))) {
                    return false;
                }            
            }

            //article override dimensions and weight but only if all parameters are specified
            if ($oArticle->oxarticles__oxweight->value && 
                $oArticle->oxarticles__oxwidth->value &&
                $oArticle->oxarticles__oxheight->value && 
                $oArticle->oxarticles__oxlength->value) {
                    $item->weight = $oArticle->oxarticles__oxweight->value;
                    $item->width = $oArticle->oxarticles__oxwidth->value * 100;
                    $item->height = $oArticle->oxarticles__oxheight->value * 100;
                    $item->length = $oArticle->oxarticles__oxlength->value * 100;
            } else {
                $item->weight = isset($inheritedData['weight']) && $inheritedData['weight'] ? $inheritedData['weight'] : 0;
                $item->width = isset($inheritedData['width']) && $inheritedData['width'] ? $inheritedData['width'] : 0;
                $item->height = isset($inheritedData['height']) && $inheritedData['height'] ? $inheritedData['height'] : 0;
                $item->length = isset($inheritedData['length']) && $inheritedData['length'] ? $inheritedData['length'] : 0;
            }

            $item->quantity = $oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value);

            // be sure that we have properly types
            $item->weight = floatval($item->weight);
            $item->width = floatval($item->width);
            $item->height = floatval($item->height);
            $item->length = floatval($item->length);
            $item->quantity = floatval($item->quantity);

            if ($useStandardPackage && ($inheritedData['tiramizoo_use_package'] && $oArticle->oxarticles__tiramizoo_use_package->value)) {
                if (!$standardPackageAddedToItems) {
                    $standardPackageAddedToItems = 1;

                    list($width, $height, $length) = explode('x', $useStandardPackage);

                    $item->weight = floatval($stdPackageWeight);
                    $item->width = floatval($stdPackageWidth);
                    $item->length = floatval($stdPackageLength);
                    $item->height = floatval($stdPackageHeight);
                    $item->quantity = 1;

                    $items[] = $item;
                }
            } else if ($useAutoFittingToPackage && ($inheritedData['tiramizoo_use_package'] && $oArticle->oxarticles__tiramizoo_use_package->value)) {
                for ($i=0; $i < $item->quantity; $i++) {
                   $aAutoFitPackageItems[] = (array)$item;
                }
            } else {
                $items[] = $item;
            }

        }

        if ($useAutoFittingToPackage) {

                $packIntoBoxes = new packIntoBoxes($aAutoFitPackageItems, $aPackageSizes);
                $packIntoBoxes->pack();

                if ($packIntoBoxes->getIndividualPackageItems()) {
                    throw new oxTiramizoo_NotAvailableException();
                }
                
                foreach($packIntoBoxes->getPackedItems() as $key => $package) 
                {
                    $item = new stdClass();
                    $item->weight = floatval($package['package']['weight']);
                    $item->width = floatval($package['package']['width']);
                    $item->length = floatval($package['package']['length']);
                    $item->height = floatval($package['package']['height']);
                    $item->quantity = 1;

                    $items[] = $item;
                }


        } 

        return $items;
    }
}