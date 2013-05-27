<?php

/**
 * Manage tiramizoo delivery sets
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_DeliverySet
{
    /**
     * Is class initialized 
     * 
     * @var bool 
     */
    protected $_isInitialized = false;

    /**
     * Is tiramizoo avialable
     * 
     * @var bool 
     */
    protected $_isTiramizooAvailable = null;

    /**
     * User's postal code get from user's address or checkout address
     * 
     * @var string
     */
    protected $_sDeliveryPostalcode = null;

    /**
     * Current tiramizoo delivery type
     * 
     * @var string
     */ 
    protected $_sTiramizooDeliveryType = null;
    
    /**
     * Current seleceted in checkout process time window
     * 
     * @var oxTiramizoo_TimeWindow
     */ 
    protected $_oSelectedTimeWindow = null;

    /**
     * All tiramizoo delivery types
     * 
     * @var mixed
     */
    protected $_aDeliveryTypes = array('immediate', 'evening');
    
    /**
     * All delivery types available in user's area 
     * 
     * @var mixed
     */
    protected $_aAvailableDeliveryTypes = null;

    /**
     * Current Api token selected by postal code
     * 
     * @var string
     */
    protected $_sCurrentApiToken = null;

    /**
     * Tiramizoo shipping id
     * 
     * @var constant
     */
    const TIRAMIZOO_DELIVERY_SET_ID = 'Tiramizoo';

    /**
     * Tiramizoo packing strategy that pack each products in another package
     * 
     * @var constant
     */
    const TIRAMIZOO_PACKING_STRATEGY_INDIVIDUAL_DIMENSIONS = 0;

    /**
     * Tiramizoo packing strategy that pack articles to packages specified in tiramizoo dashboard
     * 
     * @var constant
     */
    const TIRAMIZOO_PACKING_STRATEGY_PACKAGE_PRESETS = 1;

    /**
     * Tiramizoo packing strategy that pack all articles to one single package
     * 
     * @var constant
     */
    const TIRAMIZOO_PACKING_STRATEGY_SINGLE_PACKAGE = 2;

    /**
     * Initialize 
     * 
     * @param oxuser|null       $oUser             Userr object
     * @param oxaddress|null    $oDeliveryAddress  Delivery address provided in checkout process
     *
     * @return null
     */
    public function init($oUser, $oDeliveryAddress)
    {
        if (!$this->_isInitialized) {        
            $this->_sDeliveryPostalcode = $this->refreshDeliveryPostalCode($oUser, $oDeliveryAddress);

            if ($this->isTiramizooAvailable()) {
                $sTiramizooDeliveryType = $this->getSession()->getVariable('sTiramizooDeliveryType');

                try {
                    $this->setTiramizooDeliveryType($sTiramizooDeliveryType);
                } catch (oxTiramizoo_InvalidDeliveryTypeException $oEx) {
                    $this->_sTiramizooDeliveryType = null;
                    $this->getSession()->deleteVariable('sTiramizooDeliveryType');

                    // try set default delivery type
                    $this->setDefaultDeliveryType();

                    // if was setted redirect
                    if ($sTiramizooDeliveryType) {
                        oxUtilsView::getInstance()->addErrorToDisplay( $oEx->getMessage() );
                        oxUtils::getInstance()->redirect( oxConfig::getInstance()->getShopHomeURL() .'cl=payment', true, 302 );
                    }
                }

                $sSelectedTimeWindow = $this->getSession()->getVariable('sTiramizooTimeWindow');

                try {
                    $this->setSelectedTimeWindow($sSelectedTimeWindow);
                } catch (oxTiramizoo_InvalidTimeWindowException $oEx) {
                    $this->_oSelectedTimeWindow = null;
                    $this->getSession()->deleteVariable('sTiramizooTimeWindow');

                    // try set default time window
                    $this->setDefaultTimeWindow();

                    // if was setted redirect
                    if ($sSelectedTimeWindow) {
                        oxUtilsView::getInstance()->addErrorToDisplay( $oEx->getMessage() );
                        oxUtils::getInstance()->redirect( oxConfig::getInstance()->getShopHomeURL() .'cl=payment', true, 302 );
                    }
                }
            }

            $this->_isInitialized = 1;
        }
    }

    /**
     * Set default delivery type 
     * 
     * @return null
     */
    public function setDefaultDeliveryType()
    {
        $aAvailableDeliveryTypes = $this->getAvailableDeliveryTypes();

        if (count($aAvailableDeliveryTypes)) {
            $aAvailableDeliveryTypeNames = array_keys($aAvailableDeliveryTypes);
            $sFirstIndex = $aAvailableDeliveryTypeNames[0];
            $this->setTiramizooDeliveryType($aAvailableDeliveryTypes[$sFirstIndex]->getType());

            return true;
        }

        return false;
    }

    /**
     * Set Default itme window 
     * 
     * @return null
     */
    public function setDefaultTimeWindow()
    {
        $aAvailableDeliveryTypes = $this->getAvailableDeliveryTypes();

        if (!count($aAvailableDeliveryTypes)) {
            return false;
        }

        $aAvailableDeliveryTypeNames = array_keys($aAvailableDeliveryTypes);
        $sFirstIndex = $aAvailableDeliveryTypeNames[0];

        if ($aAvailableDeliveryTypes[$sFirstIndex]->getDefaultTimeWindow()) {
            $this->setSelectedTimeWindow($aAvailableDeliveryTypes[$sFirstIndex]->getDefaultTimeWindow()->getHash());

            return true;
        }

        return false;
    }


    /**
     * Set current delivery type name if is valid
     * 
     * @throws oxTiramizoo_InvalidDeliveryTypeException
     * @return null
     */
    public function setTiramizooDeliveryType($sTiramizooDeliveryType)
    {
        if ($this->isTiramizooDeliveryTypeValid($sTiramizooDeliveryType)) {
            oxSession::setVar( 'sTiramizooDeliveryType',  $sTiramizooDeliveryType );
            $this->_sTiramizooDeliveryType = $sTiramizooDeliveryType;
        } else {
            $errorMessage = oxLang::getInstance()->translateString('oxTiramizoo_invalid_delivery_type_error', oxLang::getInstance()->getBaseLanguage(), false);
            throw new oxTiramizoo_InvalidDeliveryTypeException($errorMessage);
        }
    }

    /**
     * Set current time window if is valid
     * 
     * @throws oxTiramizoo_InvalidTimeWindowException
     * @return null
     */
    public function setSelectedTimeWindow($sTimeWindow)
    {
        if ($oTimeWindow = $this->getRetailLocation()->getTimeWindowByHash($sTimeWindow)) {
            if ($oTimeWindow->isValid()) {
                oxSession::setVar( 'sTiramizooTimeWindow',  $oTimeWindow->getHash() );
                $this->_oSelectedTimeWindow = $oTimeWindow;
            }
        }

        if (!$oTimeWindow || !$oTimeWindow->isValid()) {
            $errorMessage = oxLang::getInstance()->translateString('oxTiramizoo_invalid_time_window_error', oxLang::getInstance()->getBaseLanguage(), false);
            throw new oxTiramizoo_InvalidTimeWindowException($errorMessage);
        }
    }

    /**
     * Gets validated collection of available delivery types
     *
     * @return mixed
     */
    public function getAvailableDeliveryTypes()
    {
        if ($this->_aAvailableDeliveryTypes === null) {
            $this->_aAvailableDeliveryTypes = array();
            
            foreach ($this->_aDeliveryTypes as $sDeliveryType) 
            {
                $sClass = 'oxTiramizoo_DeliveryType' . ucfirst($sDeliveryType);
                
                $oDeliveryType = oxnew($sClass, $this->getRetailLocation());

                if ($oDeliveryType->isAvailable()) {
                    $this->_aAvailableDeliveryTypes[$sDeliveryType] = $oDeliveryType;
                }
            }
        }

        return $this->_aAvailableDeliveryTypes;
    }

    /**
     * Gets the current time window selected in checkout process
     *
     * @return bool
     */
    public function isTiramizooDeliveryTypeValid($sTiramizooDeliveryType)
    {
        return in_array($sTiramizooDeliveryType, array_keys($this->getAvailableDeliveryTypes()));
    }

    /**
     * Gets the current time window selected in checkout process
     *
     * @return oxTiramizoo_TimeWindow
     */
    public function getSelectedTimeWindow()
    {
        return $this->_oSelectedTimeWindow;
    }

    /**
     * Gets the current tiramizoo delivery type name
     *
     * @return string
     */
    public function getTiramizooDeliveryType()
    {
        return $this->_sTiramizooDeliveryType;
    }


    /**
     * Gets the current tiramizoo delivery type object 
     * 
     * @return oxTiramizoo_DeliveryType|null
     */ 
    public function getTiramizooDeliveryTypeObject()
    {
        foreach ($this->getAvailableDeliveryTypes() as $oDeliveryType) 
        {
            if ($oDeliveryType->getType() == $this->getTiramizooDeliveryType()) {
                return $oDeliveryType;
            }
        }

        return null;
    }

    /**
     * Choose which postal code should be used in amtching user's postal code and tiramizoo service areas
     * 
     * @return string|null
     */
    public function refreshDeliveryPostalCode($oUser, $oDeliveryAddress)
    {
        if ($oDeliveryAddress) {
            if ($sDeliveryPostalCode = $oDeliveryAddress->oxaddress__oxzip->value) {
                return $sDeliveryPostalCode;
            }
        }

        if ($oUser) {
            if ($sDeliveryPostalCode = $oUser->oxuser__oxzip->value) {
                return $sDeliveryPostalCode;
            }
        }

        return null;
    }

    /**
     * Gets the API token matched by user's postal code
     * 
     * @throws oxTiramizoo_NotAvailableException
     * @return string API token
     */
    public function getApiToken()
    {
        if ($this->_sCurrentApiToken == null) {

            $oRetailLocationList = oxnew('oxTiramizoo_RetailLocationList');
            $oRetailLocationList->loadAll();

            foreach ($oRetailLocationList as $oRetailLocation) 
            {
                $aAvailablePostalCodes = $oRetailLocation->getConfVar('postal_codes');

                if (is_array($aAvailablePostalCodes) && in_array($this->_sDeliveryPostalcode, $aAvailablePostalCodes)) {
                    return $this->_sCurrentApiToken = $oRetailLocation->getApiToken();
                }
            }

            throw new oxTiramizoo_NotAvailableException('This postal code id not supported');
        }

        return $this->_sCurrentApiToken;
    }

    /**
     * Gets the instance of current API object
     * 
     * @return oxTiramizoo_Api
     */
    public function getTiramizooApi()
    {
        return oxTiramizoo_Api::getApiInstance($this->getApiToken());
    }

    /**
     * Gets the current retail location object from API token
     * 
     * @throws oxTiramizoo_NotAvailableException
     * @return oxTiramizoo_RetailLocation
     */
    public function getRetailLocation()
    {
        $oRetailLocation = oxnew('oxTiramizoo_RetailLocation');
        $sOxid = $oRetailLocation->getIdByApiToken($this->getApiToken());

        $oRetailLocation->load($sOxid);

        if (!$oRetailLocation->getId()) {
            throw new oxTiramizoo_NotAvailableException('This postal code id not supported');
        }

        return $oRetailLocation;
    }

    /**
     * Validate if tiramizoo service is available with delivery address 
     * 
     * @return bool
     */
    public function isTiramizooAvailable() 
    {
        if ($this->_isTiramizooAvailable === null) {

            if (!$this->getBasket()->isValid()) {
                return $this->_isTiramizooAvailable = false;                
            }

            //check if retail location is fit to postal code service is available in this area
            try {
                $oRetailLocation = $this->getRetailLocation();
            } catch(oxException $oEx) {
                return $this->_isTiramizooAvailable = false;
            }

            //check if exists delivery types
            if (count($this->getAvailableDeliveryTypes()) == 0) {
                return $this->_isTiramizooAvailable = false;   
            }

            $this->_isTiramizooAvailable = true;

        }

        return $this->_isTiramizooAvailable;
    }

    /**
     * oxBasket instance
     *
     * @return oxbasket
     */
    public function getBasket()
    {
        return $this->getSession()->getBasket();
    }

    /**
     * oxSession instance getter
     *
     * @return oxsession
     */
    public function getSession()
    {
        return oxRegistry::getSession();
    }
}
