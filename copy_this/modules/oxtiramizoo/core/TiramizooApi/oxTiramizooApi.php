<?php

if ( !class_exists('TiramizooApi') ) {
    require_once 'TiramizooApi.php';
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
        $oPickup->before = date('c', strtotime('+' . $oxConfig->getShopConfVar('oxTiramizoo_pickup_del_offset') . 'minutes', strtotime($sTiramizooWindow)));

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
            $oDelivery->name = $oDeliveryAddress->oxaddress__oxfname->value . ' ' . $oDeliveryAddress->oxaddress__oxlname->value;
            $oDelivery->phone_number = $oDeliveryAddress->oxaddress__oxfon->value;
        } else {
            $oDelivery->address_line_1 = $oUser->oxuser__oxstreet->value . ' ' . $oUser->oxuser__oxstreetnr->value;
            $oDelivery->city = $oUser->oxuser__oxcity->value;
            $oDelivery->postal_code = $oUser->oxuser__oxzip->value;
            $oDelivery->country_code = $oUser->oxuser__oxcountryid->value;
            $oDelivery->name = $oUser->oxusers__oxfname->value . ' ' . $oUser->oxuser__oxlname->value;
            $oDelivery->phone_number = $oUser->oxuser__oxfon->value;
        }

        //get country code
        $oCountry = oxNew('oxcountry');
        $oCountry->load($oDelivery->country_code);

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

        foreach ($oBasket->getBasketArticles() as $key => $oArticle) 
        {
            //initialize standard class
            $item = new stdClass();
            $item->weight = null;
            $item->width = null;
            $item->height = null;
            $item->length = null;

            //article is disabled return false
            if ($oArticle->oxarticles__tiramizoo_enable->value == -1) {
                return false;
            }

            // check if deliverable is set for articles with stock > 0
            if (oxConfig::getInstance()->getShopConfVar('oxTiramizoo_articles_stock_gt_0')) {
                if ($oArticle->oxarticles__oxstock->value <= 0) {
                    return false;
                }
            }

            //get the data from categories hierarchy
            $inheritedData = $this->_getArticleInheritData($oArticle);

            if ($oArticle->oxarticles__tiramizoo_enable->value == 0) {
                if (isset($inheritedData['tiramizoo_enable']) && (!($inheritedData['tiramizoo_enable']))) {
                    return false;
                }            
            }

            if ($oArticle->oxarticles__oxweight->value) {
                $item->weight = $oArticle->oxarticles__oxweight->value;
            } else {
                $item->weight = isset($inheritedData['weight']) && $inheritedData['weight'] ? $inheritedData['weight'] : 0;
            }

            if ($oArticle->oxarticles__oxwidth->value) {
                $item->width = $oArticle->oxarticles__oxwidth->value * 100;
            } else {
                $item->width = isset($inheritedData['width']) && $inheritedData['width'] ? $inheritedData['width'] : 0;
            }

            if ($oArticle->oxarticles__oxheight->value) {
                $item->height = $oArticle->oxarticles__oxheight->value * 100;
            } else {
                $item->height = isset($inheritedData['height']) && $inheritedData['height'] ? $inheritedData['height'] : 0;
            }

            if ($oArticle->oxarticles__oxlength->value) {
                $item->length = $oArticle->oxarticles__oxlength->value * 100;
            } else {
                $item->length = isset($inheritedData['length']) && $inheritedData['length'] ? $inheritedData['length'] : 0;
            }

            $item->quantity = $oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value);

            // be sure that we have properly types
            $item->weight = floatval($item->weight);
            $item->width = floatval($item->width);
            $item->height = floatval($item->height);
            $item->length = floatval($item->length);
            $item->quantity = floatval($item->quantity);

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Get product data (enable, weight, dimensions) from main category or parents
     * 
     * @param  oxArticle $oArticle
     * @return array
     */
    protected function _getArticleInheritData($oArticle)
    {
        $oCategory = $oArticle->getCategory();

        $aCheckCategories = $this->_getParentsCategoryTree($oCategory);

        $oxTiramizooInheritedData = array();

        $allCategoriesAreEnabled = true;

        foreach ($aCheckCategories as $aCategoryData) 
        {
            if (!isset($aCategoryData['tiramizoo_enable']) || !(intval($aCategoryData['tiramizoo_enable']) > 0)) {
                $allCategoriesAreEnabled = false;
                break;
            }
        }

        $oxTiramizooInheritedData['tiramizoo_enable'] = $allCategoriesAreEnabled;

        $oxTiramizooInheritedData['weight'] = $oCategory->oxcategories__tiramizoo_weight->value;
        $oxTiramizooInheritedData['width'] = $oCategory->oxcategories__tiramizoo_width->value;
        $oxTiramizooInheritedData['height'] = $oCategory->oxcategories__tiramizoo_height->value;
        $oxTiramizooInheritedData['length'] = $oCategory->oxcategories__tiramizoo_length->value;

        return $oxTiramizooInheritedData;
    }

    /**
     * Recursive method for getting array of arrays product data (enable, weight, dimensions)
     * 
     * @param  oxCategory $oCategory
     * @param  array  $returnCategories
     * @return array Array of categories hierarchy
     */
    protected function _getParentsCategoryTree($oCategory, $returnCategories = array())
    {
        $oxTiramizooCategoryData = array();
        $oxTiramizooCategoryData['oxid'] = $oCategory->oxcategories__oxid->value;
        $oxTiramizooCategoryData['oxtitle'] = $oCategory->oxcategories__oxtitle->value;
        $oxTiramizooCategoryData['oxsort'] = $oCategory->oxcategories__oxsort->value;
        $oxTiramizooCategoryData['tiramizoo_enable'] = $oCategory->oxcategories__tiramizoo_enable->value;
        $oxTiramizooCategoryData['tiramizoo_weight'] = $oCategory->oxcategories__tiramizoo_weight->value;
        $oxTiramizooCategoryData['tiramizoo_width'] = $oCategory->oxcategories__tiramizoo_width->value;
        $oxTiramizooCategoryData['tiramizoo_height'] = $oCategory->oxcategories__tiramizoo_height->value;
        $oxTiramizooCategoryData['tiramizoo_length'] = $oCategory->oxcategories__tiramizoo_length->value;

        array_unshift($returnCategories, $oxTiramizooCategoryData);
        if ($parentCategory = $oCategory->getParentCategory()) {
            $returnCategories = $this->_getParentsCategoryTree($parentCategory, $returnCategories);
        }

        return $returnCategories;
    }
}