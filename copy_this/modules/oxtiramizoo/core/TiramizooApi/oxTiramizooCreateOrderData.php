<?php 

class oxTiramizoo_CreateOrderData
{
    const TIRAMIZOO_SALT = 'oxTiramizoo';

    protected $_sApiWebhookUrl = '/modules/oxtiramizoo/api.php';

    protected $_oTiramizooData = null;

    protected $_oTimeWindow = null;
    protected $_oBasket = null;
    protected $_oRetailLocation = null;


    protected $_sExternalId = '';

    protected $_aPackages = array();

    protected $_oPickup = null;
    protected $_oDelivery = null;





    public function __construct($oTimeWindow, $oBasket, $oRetailLocation)
    {
        $this->_oTimeWindow = $oTimeWindow;
        $this->_oBasket = $oBasket;
        $this->_oRetailLocation = $oRetailLocation;        
    }


    public function getShopUrl()
    {
        return $this->getTiramizooConfig()->getShopConfVar('oxTiramizoo_shop_url');
    }

    public function getWebhookUrl()
    {
        return trim($this->getShopUrl(), '/') . $this->_sApiWebhookUrl;
    }

    public function generateExternalId()
    {
        $this->_sExternalId = md5(time() . self::TIRAMIZOO_SALT);
    }

    public function getExternalId()
    {
        if (!$this->_sExternalId) {
            $this->generateExternalId();
        }

        return $this->_sExternalId ;
    }

    public function getBasket()
    {
        return $this->_oBasket;
    }

    public function getTiramizooConfig()
    {
        return oxTiramizooConfig::getInstance();
    }

    public function getTiramizooDataObject()
    {
        return $this->_oTiramizooData;
    }



    public function getCreatedTiramizooOrderDataObject()
    {
        if ($this->_oTiramizooData === null) {
            $this->createTiramizooOrderDataObject();
        }
        return $this->_oTiramizooData;
    }

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
     * @param  oxBasket $oBasket
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
     * @return stdClass Delivery object
     */
    public function buildDelivery(oxUser $oUser, $oDeliveryAddress)
    {
        $this->_oDelivery = new stdClass();

        $this->_oDelivery->email = $oUser->oxuser__oxusername->value; 

        if ($oDeliveryAddress)  {
            $this->_oDelivery->address_line_1 = $oDeliveryAddress->oxaddress__oxstreet->value . ' ' . $oDeliveryAddress->oxaddress__oxstreetnr->value;
            $this->_oDelivery->city = $oDeliveryAddress->oxaddress__oxcity->value;
            $this->_oDelivery->postal_code = $oDeliveryAddress->oxaddress__oxzip->value;
            $this->_oDelivery->country_code = $oDeliveryAddress->oxaddress__oxcountryid->value;
            $this->_oDelivery->phone_number = $oDeliveryAddress->oxaddress__oxfon->value;
            
            $this->_oDelivery->name = $oDeliveryAddress->oxaddress__oxfname->value . ' ' . $oDeliveryAddress->oxaddress__oxlname->value;

            if ($oDeliveryAddress->oxaddress__oxcompany->value) {
                $this->_oDelivery->name = $oDeliveryAddress->oxaddress__oxcompany->value . ' / ' . $this->_oDelivery->name;
            }

        } else {
            $this->_oDelivery->address_line_1 = $oUser->oxuser__oxstreet->value . ' ' . $oUser->oxuser__oxstreetnr->value;
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

        $this->_oPickup->address_line_1 = $aPickupContact['address_line_1'];
        $this->_oPickup->city = $aPickupContact['city'];
        $this->_oPickup->postal_code = $aPickupContact['postal_code'];
        $this->_oPickup->country_code = $aPickupContact['country_code'];
        $this->_oPickup->name = $aPickupContact['name'];
        $this->_oPickup->phone_number = $aPickupContact['phone_number'];

        $this->_oPickup->after = $this->_oTimeWindow->getPickupFrom();
        $this->_oPickup->before = $this->_oTimeWindow->getPickupTo();
    }


    /**
     * Build items data used for both type of request sending order and getting quotes.
     * If product has no specified params e.g. enable, weight, dimensions it inherits 
     * from main category
     * 
     * @param  oxBasket $oBasket
     * @return array
     */
    public function buildItems()
    {
        $oTiramizooConfig = $this->getTiramizooConfig();

        $items = array();

        $sPackageStrategy = $oTiramizooConfig->getShopConfVar('oxTiramizoo_package_strategy');

        $useStandardPackage = false;

        $this->getPackageSizesSortedByVolume();


        if ($sPackageStrategy == 1) {
            $aPackageSizes = $this->getPackageSizesSortedByVolume();

            if (count($aPackageSizes)) {
                $useAutoFittingToPackage = 1;
                $aAutoFitPackageItems = array();
            }
        } else if ($sPackageStrategy == 2) {

            $stdPackageWidth = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_width');
            $stdPackageLength = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_length');
            $stdPackageHeight = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_height');
            $stdPackageWeight = $oTiramizooConfig->getShopConfVar('oxTiramizoo_std_package_weight');

            $useStandardPackage = $stdPackageWidth && $stdPackageLength && $stdPackageHeight && $stdPackageWeight;

            $standardPackageAddedToItems = 0;
        }


        foreach ($this->_oBasket->getBasketArticles() as $key => $oArticle) 
        {
            //initialize standard class
            $item = new stdClass();
            $item->weight = null;
            $item->width = null;
            $item->height = null;
            $item->length = null;

            $item->quantity = $this->_oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value);

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

            $oArticleExtended = oxTiramizoo_ArticleExtended::findOneByFiltersOrCreate(array('oxarticleid' => $oArticle->oxarticles__oxid->value));

            if (!$oArticleExtended->isEnabled()) {
                return false;
            }

            $item = $oArticleExtended->buildArticleEffectiveData($item);
            $item->name = $oArticle->oxarticles__oxtitle->value;  

            if ($useStandardPackage && !$oArticleExtended->hasIndividualPackage()) {
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
            } else if ($useAutoFittingToPackage && !$oArticleExtended->hasIndividualPackage()) {
                for ($i=0; $i < $item->quantity; $i++) {
                   $aAutoFitPackageItems[] = (array)$item;
                }
            } else {
                $items[] = $item;
            }
        }


        if ($useAutoFittingToPackage && count($aAutoFitPackageItems)) {

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

                $aDescriptionNames = array();

                foreach ($package['items'] as $itemInPackage) {
                    $aDescriptionNames[] = $itemInPackage['name']; 
                }
                
                $item->description = substr(implode(', ', $aDescriptionNames), 0, 255);

                $items[] = $item;
            }
        } 

        $this->_aPackages = $items;
    }




    public function getPackageSizesSortedByVolume() 
    {
        $aPackagePresets = $this->_oRetailLocation->getConfVar('package_presets');

        $aPackageSizesSorted = array();

        foreach ($aPackagePresets as $key => $aPackagePreset) {
            $volume = $aPackagePreset['width'] * $aPackagePreset['length'] * $aPackagePreset['height'];
            $aPackageSizesSorted[$volume] = $aPackagePreset;
        }

        ksort($aPackageSizesSorted);

        return $aPackageSizesSorted;
    }

}