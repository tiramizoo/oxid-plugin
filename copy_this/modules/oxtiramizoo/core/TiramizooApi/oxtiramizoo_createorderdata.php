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
 * Class used for generating objects before send to API.
 *
 * @package oxTiramizoo
 */
class oxTiramizoo_CreateOrderData
{
    /**
     * Salt thst used for generating external id
     */
    const TIRAMIZOO_SALT = 'oxTiramizoo';

    /**
     * Relative uri path to API endpoint
     *
     * @var string
     */
    protected $_sApiWebhookUrl = '/modules/oxtiramizoo/api.php';

    /**
     * Tiramizoo Data
     *
     * @var stdClass
     */
    protected $_oTiramizooData = null;

    /**
     * Selected time window Object
     *
     * @var oxTiramizoo_TimeWindow
     */
    protected $_oTimeWindow = null;

    /**
     * Basket object
     *
     * @var oxBasket
     */
    protected $_oBasket = null;

    /**
     * Current Retail Location object
     *
     * @var oxTiramizoo_RetailLocation
     */
    protected $_oRetailLocation = null;

    /**
     * External id used for identify order
     *
     * @var string
     */
    protected $_sExternalId = '';

    /**
     * Array of packages and their dimesnions
     *
     * @var array
     */
    protected $_aPackages = array();

    /**
     * Pickup object
     *
     * @var stdClass
     */
    protected $_oPickup = null;

    /**
     * Delivery object
     *
     * @var stdClass
     */
    protected $_oDelivery = null;

    /**
     * Information that standard package has been
     * already added to items array
     *
     * @var bool
     */
    protected $_standardPackageAddedToItems = null;

    /**
     * Information that package strategy use
     * one package to pack all items
     *
     * @var bool
     */
    protected $_useStandardPackage = false;

    /**
     * Array of packed items
     *
     * @var array
     */
    protected $_items = array();

    /**
     * Class constructor, assign basic properties.
     *
     * @param oxTiramizoo_TimeWindow        $oTimeWindow        Current time window object
     * @param oxBasket                      $oxBasket           Basket object
     * @param oxTiramizoo_RetailLocation    $oRetailLocation    Current retail location
     *
     * @return null
     */
    public function __construct($oTimeWindow, $oBasket, $oRetailLocation)
    {
        $this->_oTimeWindow = $oTimeWindow;
        $this->_oBasket = $oBasket;
        $this->_oRetailLocation = $oRetailLocation;
    }

    /**
     * Returns shop url, retrieved from oxTiramizoo configuration.
     *
     * @return string
     */
    public function getShopUrl()
    {
        return $this->getTiramizooConfig()->getShopConfVar('oxTiramizoo_shop_url');
    }

    /**
     * Returns webhook endpoint relative uri.
     *
     * @return string
     */
    public function getWebhookUrl()
    {
        return trim($this->getShopUrl(), '/') . $this->_sApiWebhookUrl;
    }

    /**
     * Generate and set external id. Uses salt to better fresult.
     *
     * @return null
     */
    public function generateExternalId()
    {
        $this->_sExternalId = md5(time() . self::TIRAMIZOO_SALT);
    }

    /**
     * Returns external id. Genearate if not exists.
     *
     * @return string
     */
    public function getExternalId()
    {
        if (!$this->_sExternalId) {
            $this->generateExternalId();
        }

        return $this->_sExternalId ;
    }

    /**
     * Returns Basket object.
     *
     * @return oxBasket
     */
    public function getBasket()
    {
        return $this->_oBasket;
    }

    /**
     * Returns Tiramizoo Config class.
     *
     * @return oxTiramizoo_Config
     */
    public function getTiramizooConfig()
    {
        return oxRegistry::get('oxTiramizoo_Config');
    }

    /**
     * Returns TiramizooData Object.
     *
     * @return stdClass
     */
    public function getTiramizooDataObject()
    {
        return $this->_oTiramizooData;
    }

    /**
     * Returns TiramizooData Object. If empty create one.
     *
     * @return stdClass
     */
    public function getCreatedTiramizooOrderDataObject()
    {
        if ($this->_oTiramizooData === null) {
            $this->createTiramizooOrderDataObject();
        }
        return $this->_oTiramizooData;
    }

    /**
     * Returns TiramizooData Object. Create stdClass
     * object and assign object properties with data
     *
     * @return stdClass
     */
	public function createTiramizooOrderDataObject()
	{
        $this->_oTiramizooData = new stdClass();

        $this->_oTiramizooData->description = $this->getDescription();
        $this->_oTiramizooData->external_id = $this->getExternalId();
        $this->_oTiramizooData->web_hook_url = $this->getWebhookUrl();
        $this->_oTiramizooData->pickup = $this->_oPickup;
        $this->_oTiramizooData->delivery = $this->_oDelivery;
        $this->_oTiramizooData->packages = $this->_aPackages;

        return $this->_oTiramizooData;
	}

    /**
     * Build description from product's names. Used for build partial data to send order API request
     *
     * @return string description
     */
    public function getDescription()
    {
        $itemNames = array();
        foreach ($this->getBasket()->getBasketArticles() as $key => $oArticle)
        {
            $itemNames[] = $oArticle->oxarticles__oxtitle->value . ' (x' . $this->getBasket()->getArtStockInBasket($oArticle->oxarticles__oxid->value) . ')';
        }

        //string should be contains at least 255 chars
        return substr(implode($itemNames, ', '), 0, 255);
    }

    /**
     * Build delivery object from user data. Used for build partial data
     * to send order API request
     *
     * @param  oxUser $oUser
     * @param  mixed $oDeliveryAddress oxAddress if filled by user or null
     *
     * @return stdClass Delivery object
     */
    public function buildDelivery(oxUser $oUser, $oDeliveryAddress)
    {
        $this->_oDelivery = new stdClass();

        $this->_oDelivery->email = $oUser->oxuser__oxusername->value;

        if ($oDeliveryAddress)  {
            $this->_oDelivery->address_line = $oDeliveryAddress->oxaddress__oxstreet->value . ' ' . $oDeliveryAddress->oxaddress__oxstreetnr->value;
            $this->_oDelivery->city = $oDeliveryAddress->oxaddress__oxcity->value;
            $this->_oDelivery->postal_code = $oDeliveryAddress->oxaddress__oxzip->value;
            $this->_oDelivery->country_code = $oDeliveryAddress->oxaddress__oxcountryid->value;
            $this->_oDelivery->phone_number = $oDeliveryAddress->oxaddress__oxfon->value;

            $this->_oDelivery->name = $oDeliveryAddress->oxaddress__oxfname->value . ' ' . $oDeliveryAddress->oxaddress__oxlname->value;

            if ($oDeliveryAddress->oxaddress__oxcompany->value) {
                $this->_oDelivery->name = $oDeliveryAddress->oxaddress__oxcompany->value . ' / ' . $this->_oDelivery->name;
            }

        } else {
            $this->_oDelivery->address_line = $oUser->oxuser__oxstreet->value . ' ' . $oUser->oxuser__oxstreetnr->value;
            $this->_oDelivery->city = $oUser->oxuser__oxcity->value;
            $this->_oDelivery->postal_code = $oUser->oxuser__oxzip->value;
            $this->_oDelivery->country_code = $oUser->oxuser__oxcountryid->value;
            $this->_oDelivery->phone_number = $oUser->oxuser__oxfon->value;

            $this->_oDelivery->name = $oUser->oxuser__oxfname->value . ' ' . $oUser->oxuser__oxlname->value;

            if ($oUser->oxuser__oxcompany->value) {
                $this->_oDelivery->name = $oUser->oxuser__oxcompany->value . ' / ' . $this->_oDelivery->name;
            }
        }

        //get country code
        $oCountry = oxNew('oxcountry');
        $oCountry->load($this->_oDelivery->country_code);
        $this->_oDelivery->country_code = strtolower($oCountry->oxcountry__oxisoalpha2->value);

        $this->_oDelivery->after = $this->_oTimeWindow->getDeliveryFrom();
        $this->_oDelivery->before = $this->_oTimeWindow->getDeliveryTo();

        return $this->_oDelivery;
    }

    /**
     * Build pickup object from tiramizoo config values. Used for build partial data
     * to send order API request
     *
     * @return stdClass Pickup object
     */
    public function buildPickup()
    {
        $aPickupContact = $this->_oRetailLocation->getConfVar('pickup_contact');

        $this->_oPickup = new stdClass();

        $this->_oPickup->address_line = $aPickupContact['address_line_1'];

        if ($aPickupContact['city']) {
            $this->_oPickup->city = $aPickupContact['city'];
        }

        $this->_oPickup->postal_code = $aPickupContact['postal_code'];
        $this->_oPickup->country_code = $aPickupContact['country_code'];
        $this->_oPickup->name = $aPickupContact['name'];
        $this->_oPickup->phone_number = $aPickupContact['phone_number'];

        $this->_oPickup->after = $this->_oTimeWindow->getPickupFrom();
        $this->_oPickup->before = $this->_oTimeWindow->getPickupTo();

        return $this->_oPickup;
    }

    /**
     * Build items data used for sending order. Returns packages object
     * or false if build item return false.
     *
     * @return mixed
     */
    public function buildItems()
    {
        $oTiramizooConfig = $this->getTiramizooConfig();

        $sPackageStrategy = $oTiramizooConfig->getShopConfVar('oxTiramizoo_package_strategy');

        $this->_useStandardPackage = false;

        if ($sPackageStrategy == oxTiramizoo_DeliverySet::TIRAMIZOO_PACKING_STRATEGY_SINGLE_PACKAGE) {
            $stdPackageWidth = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_width');
            $stdPackageLength = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_length');
            $stdPackageHeight = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_height');
            $stdPackageWeight = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_weight');
            $this->_useStandardPackage = $this->_useStandardPackage($stdPackageWidth, $stdPackageLength, $stdPackageHeight, $stdPackageWeight);
            $this->_standardPackageAddedToItems = 0;
        }

        $this->_items = array();

        foreach ($this->getBasket()->getBasketArticles() as $key => $oArticle)
        {
            $bItemWasBuilt = $this->_buildItem($oArticle);

            if (!$bItemWasBuilt) {
                return false;
            }
        }

        return $this->_aPackages = $this->_items;
    }

    /**
     * Build item based on article. If product has no specified params
     * e.g. enable, weight, dimensions it inherits from main category
     *
     * @param  oxArticle $oArticle
     *
     * @return bool
     */
    protected function _buildItem($oArticle)
    {
        //initialize standard class
        $item = new stdClass();
        $item->weight = null;
        $item->width = null;
        $item->height = null;
        $item->length = null;

        $item->quantity = $this->getBasket()->getArtStockInBasket($oArticle->oxarticles__oxid->value);

        $oTiramizooConfig = $this->getTiramizooConfig();

        //check if deliverable is set for articles with stock > 0
        if ($oTiramizooConfig->getShopConfVar('oxTiramizoo_articles_stock_gt_0') && $oArticle->oxarticles__oxstock->value <= 0) {
                return false;
        }

        //NOTICE if article is only variant of parent article then load parent product as article
        if ($oArticle->oxarticles__oxparentid->value) {
            $parentArticleId = $oArticle->oxarticles__oxparentid->value;

            $oArticleParent = oxNew( 'oxarticle' );
            $oArticleParent->load($parentArticleId);
            $oArticle = $oArticleParent;
        }

        $oArticleExtended = oxNew('oxTiramizoo_ArticleExtended');
        $sOxid = $oArticleExtended->getIdByArticleId($oArticle->getId());

        $oArticleExtended->load($sOxid);

        if (!$oArticleExtended->isEnabled()) {
            return false;
        }

        $item = $oArticleExtended->buildArticleEffectiveData($item);

        $item->description = $oArticle->oxarticles__oxtitle->value;

        //insert item to container
        $this->_insertItem($oArticleExtended, $item);

        return true;
    }

    /**
     * Insert item to container. Uses package strategy to define how
     * item should be packed.
     *
     * @return null
     */
    protected function _insertItem($oArticleExtended, $item)
    {
        $oTiramizooConfig = $this->getTiramizooConfig();

        $sPackageStrategy = $oTiramizooConfig->getShopConfVar('oxTiramizoo_package_strategy');

        if ($this->_useStandardPackage && !$oArticleExtended->hasIndividualPackage()) {
            if (!$this->_standardPackageAddedToItems) {
                $this->_standardPackageAddedToItems = 1;

                $stdPackageWidth = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_width');
                $stdPackageLength = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_length');
                $stdPackageHeight = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_height');
                $stdPackageWeight = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_weight');

                $item->weight = floatval($stdPackageWeight);
                $item->width = floatval($stdPackageWidth);
                $item->length = floatval($stdPackageLength);
                $item->height = floatval($stdPackageHeight);
                $item->quantity = 1;

                $this->_items[] = $item;
            }
        } else if (($sPackageStrategy == oxTiramizoo_DeliverySet::TIRAMIZOO_PACKING_STRATEGY_PACKAGE_PRESETS) && !$oArticleExtended->hasIndividualPackage()) {
            $item->bundle = true;
            $this->_items[] = $item;
        } else {
            $this->_items[] = $item;
        }
    }

    /**
     * Check if standard dimensions and weight are set properly
     *
     * @param  int $stdPackageWidth
     * @param  int $stdPackageLength
     * @param  int $stdPackageHeight
     * @param  int $stdPackageWeight
     *
     * @return bool
     */
    protected function _useStandardPackage($stdPackageWidth, $stdPackageLength, $stdPackageHeight, $stdPackageWeight)
    {
        return $stdPackageWidth && $stdPackageLength && $stdPackageHeight && $stdPackageWeight;
    }
}
