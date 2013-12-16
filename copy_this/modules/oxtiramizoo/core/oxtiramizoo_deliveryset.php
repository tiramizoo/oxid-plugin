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
 * Main class for Tiramizoo Delivery logic. Manage tiramizoo delivery sets and time windows.
 *
 * @package oxTiramizoo
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
    protected $_aDeliveryTypes = array('immediate', 'evening', 'special');

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
     * @param oxuser|null       $oUser             User object
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

                $oConfig = oxRegistry::getConfig();

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
                        oxUtils::getInstance()->redirect( $oConfig->getShopHomeURL() .'cl=payment', true, 302 );
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
                        oxUtils::getInstance()->redirect( $oConfig->getShopHomeURL() .'cl=payment', true, 302 );
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
        $blReturn = false;

        $aAvailableDeliveryTypes = $this->getAvailableDeliveryTypes();

        if (count($aAvailableDeliveryTypes)) {
            $aAvailableDeliveryTypeNames = array_keys($aAvailableDeliveryTypes);
            $sFirstIndex = $aAvailableDeliveryTypeNames[0];
            $this->setTiramizooDeliveryType($aAvailableDeliveryTypes[$sFirstIndex]->getType());

            $blReturn = true;
        }

        return $blReturn;
    }

    /**
     * Set Default time window
     *
     * @return bool
     */
    public function setDefaultTimeWindow()
    {
        $blReturn = false;

        $aAvailableDeliveryTypes = $this->getAvailableDeliveryTypes();

        if (count($aAvailableDeliveryTypes)) {
            $aAvailableDeliveryTypeNames = array_keys($aAvailableDeliveryTypes);
            $sFirstIndex = $aAvailableDeliveryTypeNames[0];

            if ($aAvailableDeliveryTypes[$sFirstIndex]->getDefaultTimeWindow()) {
                $this->setSelectedTimeWindow($aAvailableDeliveryTypes[$sFirstIndex]->getDefaultTimeWindow()->getHash());

                $blReturn = true;
            }
        }

        return $blReturn;
    }

    /**
     * Set current delivery type name if is valid
     *
     * @param string $sTiramizooDeliveryType delivery type name
     * @throws oxTiramizoo_InvalidDeliveryTypeException if delivery type is invalid
     *
     * @return null
     */
    public function setTiramizooDeliveryType($sTiramizooDeliveryType)
    {
        if ($this->isTiramizooDeliveryTypeValid($sTiramizooDeliveryType)) {
            $this->getSession()->setVariable( 'sTiramizooDeliveryType',  $sTiramizooDeliveryType );
            $this->_sTiramizooDeliveryType = $sTiramizooDeliveryType;
        } else {
            $oLang = oxRegistry::getLang();
            $sTplLanguage = $oLang->getTplLanguage();

            $errorMessage = $oLang->translateString('oxTiramizoo_invalid_delivery_type_error', $sTplLanguage, true);
            throw new oxTiramizoo_InvalidDeliveryTypeException($errorMessage);
        }
    }

    /**
     * Set current time window if is valid
     *
     * @param string $sTimeWindow time window hash name
     * @throws oxTiramizoo_InvalidTimeWindowException if not found time window object or is not valid
     *
     * @return null
     */
    public function setSelectedTimeWindow($sTimeWindow)
    {
        if ($oTimeWindow = $this->getRetailLocation()->getTimeWindowByHash($sTimeWindow)) {
            if ($oTimeWindow->isValid()) {
                $this->getSession()->setVariable( 'sTiramizooTimeWindow',  $oTimeWindow->getHash() );
                $this->_oSelectedTimeWindow = $oTimeWindow;
            }
        }

        if (!$oTimeWindow || !$oTimeWindow->isValid()) {
            $oLang = oxRegistry::getLang();
            $sTplLanguage = $oLang->getTplLanguage();

            $errorMessage = $oLang->translateString('oxTiramizoo_invalid_time_window_error', $sTplLanguage, true);
            throw new oxTiramizoo_InvalidTimeWindowException($errorMessage);
        }
    }

    /**
     * Gets validated collection of available delivery types
     *
     * @return array
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
        $oReturn = null;

        foreach ($this->getAvailableDeliveryTypes() as $oDeliveryType)
        {
            if ($oDeliveryType->getType() == $this->getTiramizooDeliveryType()) {
                $oReturn = $oDeliveryType;
                break;
            }
        }

        return $oReturn;
    }

    /**
     * Choose which postal code should be used in amtching user's postal code and tiramizoo service areas
     *
     * @param oxuser       $oUser             User object
     * @param oxaddress    $oDeliveryAddress  Delivery address provided in checkout process
     *
     * @return string|null
     */
    public function refreshDeliveryPostalCode($oUser, $oDeliveryAddress)
    {
        $oReturn = null;

        if ($oDeliveryAddress && $sDeliveryPostalCode = $oDeliveryAddress->oxaddress__oxzip->value) {
            $oReturn = $sDeliveryPostalCode;
        } elseif ($oUser && $sDeliveryPostalCode = $oUser->oxuser__oxzip->value) {
            $oReturn = $sDeliveryPostalCode;
        }

        return $oReturn;
    }

    /**
     * Gets the API token matched by user's postal code
     *
     * @throws oxTiramizoo_NotAvailableException if token is not valid
     *
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

                if (is_array($aAvailablePostalCodes)
                    && in_array($this->_sDeliveryPostalcode, $aAvailablePostalCodes)
                ) {
                    $this->_sCurrentApiToken = $oRetailLocation->getApiToken();
                }
            }

            if (!$this->_sCurrentApiToken) {
                throw new oxTiramizoo_NotAvailableException('This postal code id not supported');
            }
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
     * @throws oxTiramizoo_NotAvailableException if retail location is not exists
     *
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
                $this->_isTiramizooAvailable = false;
            } else {
                //check if retail location is fit to postal code service is available in this area
                try {
                    $oRetailLocation = $this->getRetailLocation();

                    //check if exists delivery types
                    if (count($this->getAvailableDeliveryTypes()) == 0) {
                        $this->_isTiramizooAvailable = false;
                    } else {
                        $this->_isTiramizooAvailable = true;
                    }

                } catch(oxException $oEx) {
                    $this->_isTiramizooAvailable = false;
                }
            }
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
