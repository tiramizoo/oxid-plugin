<?php

if ( !class_exists('TiramizooApi') ) {
    require_once 'TiramizooApi.php';
}

if ( !class_exists('oxTiramizooArticleHelper') ) {
    require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_articlehelper.php';
}

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
     * Create the API object with api token and url get from appliaction config
     */
    protected function __construct()
    {
        $tiramizooApiUrl = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_api_url');
        $tiramizooApiToken = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_api_token');
        parent::__construct($tiramizooApiUrl, $tiramizooApiToken);
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

        //change pickup before time exceed maximum delivery hour
        if (strtotime(date('H:i', strtotime($oPickup->before))) > strtotime(oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour'))) {
            $oPickup->before = date('c', strtotime(date('Y-m-d', strtotime($sTiramizooWindow)) . ' ' . oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour')));
        }

        return $oPickup;
    }

    /**
     * Build delivery object from user data. Used for build partial data 
     * to send order API request
     * 
     * @param  oxUser $oUser
     * @param  mixed $oDeliveryAddress oxAddress if filled by user or null
     * @return stdClass Delivery object
     */
    public function buildDeliveryObject(oxUser $oUser, $oDeliveryAddress)
    {
        $oDelivery = new stdClass();

        $oDelivery->email = $oUser->oxuser__oxusername->value; 

        if ($oDeliveryAddress)  {
            $oDelivery->address_line_1 = $oDeliveryAddress->oxaddress__oxstreet->value . ' ' . $oDeliveryAddress->oxaddress__oxstreetnr->value;
            $oDelivery->city = $oDeliveryAddress->oxaddress__oxcity->value;
            $oDelivery->postal_code = $oDeliveryAddress->oxaddress__oxzip->value;
            $oDelivery->country_code = $oDeliveryAddress->oxaddress__oxcountryid->value;
            $oDelivery->phone_number = $oDeliveryAddress->oxaddress__oxfon->value;
            
            $oDelivery->name = $oDeliveryAddress->oxaddress__oxfname->value . ' ' . $oDeliveryAddress->oxaddress__oxlname->value;

            if ($oDeliveryAddress->oxaddress__oxcompany->value) {
                $oDelivery->name = $oDeliveryAddress->oxaddress__oxcompany->value . ' / ' . $oDelivery->name;
            }

        } else {
            $oDelivery->address_line_1 = $oUser->oxuser__oxstreet->value . ' ' . $oUser->oxuser__oxstreetnr->value;
            $oDelivery->city = $oUser->oxuser__oxcity->value;
            $oDelivery->postal_code = $oUser->oxuser__oxzip->value;
            $oDelivery->country_code = $oUser->oxuser__oxcountryid->value;
            $oDelivery->phone_number = $oUser->oxuser__oxfon->value;
            
            $oDelivery->name = $oUser->oxuser__oxfname->value . ' ' . $oUser->oxuser__oxlname->value;

            if ($oUser->oxuser__oxcompany->value) {
                $oDelivery->name = $oUser->oxuser__oxcompany->value . ' / ' . $oDelivery->name;
            }
        }

        //get country code
        $oCountry = oxNew('oxcountry');
        $oCountry->load($oDelivery->country_code);

        $sTiramizooWindow = oxSession::getVar( 'sTiramizooTimeWindow' );
        $oDelivery->after = date('c', strtotime($sTiramizooWindow));
        $oDelivery->before = date('c', strtotime('+' . oxConfig::getInstance()->getShopConfVar('oxTiramizoo_pickup_del_offset') . 'minutes', strtotime($sTiramizooWindow)));

        //change delivery before time exceed maximum delivery hour
        if (strtotime(date('H:i', strtotime($oDelivery->before))) > strtotime(oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour'))) {
            $oDelivery->before = date('c', strtotime(date('Y-m-d', strtotime($sTiramizooWindow)) . ' ' . oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour')));
        }


        $oDelivery->country_code = strtolower($oCountry->oxcountry__oxisoalpha2->value);

        return $oDelivery;
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

        $stdPackageWidth = oxconfig::getInstance()->getShopConfVar('oxTiramizoo_std_package_width');
        $stdPackageLength = oxconfig::getInstance()->getShopConfVar('oxTiramizoo_std_package_length');
        $stdPackageHeight = oxconfig::getInstance()->getShopConfVar('oxTiramizoo_std_package_height');
        $stdPackageWeight = oxconfig::getInstance()->getShopConfVar('oxTiramizoo_std_package_weight');

        $useStandardPackage = $stdPackageWidth && $stdPackageLength && $stdPackageHeight && $stdPackageWeight;

        $standardPackageAddedToItems = 0;

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
            } else {
                $items[] = $item;
            }

        }

        return $items;
    }
}