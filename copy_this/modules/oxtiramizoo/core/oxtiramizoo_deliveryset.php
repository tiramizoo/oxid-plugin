<?php


/**
 * This class contains static methods used for calculating pickup and delivery hours
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_DeliverySet
{
    protected $_sDeliveryPostalcode = '';
    protected $_oUser = null;
    protected $_oDeliveryAddress = null;

    protected $_isTiramizooAvailable = -1;

    protected $_sTiramizooDeliveryType = null;
    protected $_oSelectedTimeWindow = null;

    protected $_aDeliveryTypes = array('immediate', 'evening');
    protected $_aAvailableDeliveryTypes = null;

    protected $_isInitialized = false;

    const TIRAMIZOO_DELIVERY_SET_ID = 'Tiramizoo';

    public function init($oUser, $oDeliveryAddress)
    {
        if (!$this->_isInitialized) {        
            $this->setUser($oUser);
            $this->setDeliveryAddress($oDeliveryAddress);

            if ($this->isTiramizooAvailable()) {
                $sTiramizooDeliveryType = oxSession::getVar('sTiramizooDeliveryType');

                try {
                    $this->setTiramizooDeliveryType($sTiramizooDeliveryType);
                } catch (oxTiramizoo_InvalidDeliveryTypeException $oEx) {
                    $this->_sTiramizooDeliveryType = null;

                    // try set default delivery type
                    $aAvailableDeliveryTypes = $this->getAvailableDeliveryTypes();

                    if (count($aAvailableDeliveryTypes)) {
                        $this->setTiramizooDeliveryType($aAvailableDeliveryTypes[0]->getType());
                    }
                }

                $sSelectedTimeWindow = oxSession::getVar('sTiramizooTimeWindow');

                try {
                    $this->setSelectedTimeWindow($sSelectedTimeWindow);
                } catch (oxTiramizoo_InvalidTimeWindowException $oEx) {
                    $this->_oSelectedTimeWindow = null;

                    // try set default time window
                    $aAvailableDeliveryTypes = $this->getAvailableDeliveryTypes();

                    if (count($aAvailableDeliveryTypes) && $aAvailableDeliveryTypes[0]->getDefaultTimeWindow()) {
                        $this->setSelectedTimeWindow($aAvailableDeliveryTypes[0]->getDefaultTimeWindow()->getHash());
                    }
                }
            }

            $this->_isInitialized = 1;



        }
    }




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

    public function setSelectedTimeWindow($sTimeWindow)
    {
        if ($oTimeWindow = $this->getTimeWindowByHash($sTimeWindow)) {

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

    public function getAvailableDeliveryTypes()
    {
        if ($this->_aAvailableDeliveryTypes == null) {

            $this->_aAvailableDeliveryTypes = array();
            
            foreach ($this->_aDeliveryTypes as $sDeliveryType) 
            {
                $sClass = 'oxTiramizoo_DeliveryType' . ucfirst($sDeliveryType);
                
                $oDeliveryType = new $sClass($this->getAvailableTimeWindows(), $this->getRetailLocation());

                if ($oDeliveryType->isAvailable()) {
                    $this->_aAvailableDeliveryTypes[] = $oDeliveryType;
                }
            }
        }

        return $this->_aAvailableDeliveryTypes;
    }


    public function isTiramizooDeliveryTypeValid($sTiramizooDeliveryType)
    {
        //@TODO: add checks if each type ia available
        return in_array($sTiramizooDeliveryType, $this->_aDeliveryTypes);
    }

    public function getSelectedTimeWindow()
    {
        return $this->_oSelectedTimeWindow;
    }

    public function getTiramizooDeliveryType()
    {
        return $this->_sTiramizooDeliveryType;
    }

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

    public function setUser($oUser)
    {
        $this->_oUser = $oUser;
        $this->_refreshDeliveryPostalCode();
    }

    public function getUser()
    {
        return $this->_oUser;
    }

    public function setDeliveryAddress($oDeliveryAddress)
    {
        $this->_oDeliveryAddress = $oDeliveryAddress;       
        $this->_refreshDeliveryPostalCode();
    }

    public function getDeliveryAddress()
    {
        return $this->_oDeliveryAddress;
    }

    public function _refreshDeliveryPostalCode()
    {
        if ($this->getUser()) {
            if ($sDeliveryPostalCode = $this->getUser()->oxuser__oxzip->value) {
                $this->setDeliveryPostalCode($sDeliveryPostalCode);
            }
        }

        if ($this->getDeliveryAddress()) {
            if ($sDeliveryPostalCode = $this->getDeliveryAddress()->oxaddress__oxzip->value) {
                $this->setDeliveryPostalCode($sDeliveryPostalCode);
            }
        }
    }


    public function getDeliveryPostalCode()
    {
        return $this->_sDeliveryPostalcode;
    }

    public function setDeliveryPostalCode($sDeliveryPostalcode)
    {
        $this->_sDeliveryPostalcode = $sDeliveryPostalcode;
    }


    public function getConfig()
    {
        return oxTiramizooConfig::getInstance();
    }





    public function getApiToken()
    {
        $sDeliveryPostalcode = $this->getDeliveryPostalCode();

        $aRetailLocations = oxtiramizooretaillocation::getAll();

        foreach ($aRetailLocations as $oRetailLocation) 
        {
            $aAvailablePostalCodes = $oRetailLocation->getConfVar('postal_codes');

            if (in_array($sDeliveryPostalcode, $aAvailablePostalCodes)) {
                return $oRetailLocation->getApiToken();
            }
        }

        //@TODO: catch this exception
        throw new oxTiramizoo_NotAvailableException('This postal code id not supported');
    }

    public function getTiramizooApi()
    {
        return oxTiramizooApi::getApiInstance($this->getApiToken());
    }

    public function getRetailLocation()
    {
        $oRetailLocation = oxtiramizooretaillocation::findOneByFilters(array('oxapitoken' => $this->getApiToken()));

        if (!$oRetailLocation) {
            throw new oxTiramizoo_NotAvailableException('This postal code id not supported');
        }

        return $oRetailLocation;
    }

    public function getAvailableTimeWindows()
    {
        $aTimeWindows = $this->getRetailLocation()->getConfVar('time_windows');

        //sort by delivery from date
        foreach ($aTimeWindows as $oldKey => $aTimeWindow) 
        {
            $oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindow);

            $aTimeWindows[$oTimeWindow->getDeliveryFromDate()->getTimestamp()] = $aTimeWindow;
            unset($aTimeWindows[$oldKey]);
        }

        ksort($aTimeWindows);

        return $aTimeWindows ? $aTimeWindows : array();
    }

    public function getTimeWindowByHash($sHash) 
    {
        foreach ($this->getAvailableTimeWindows() as $aTimeWindow) 
        {
            $oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindow);
            
            if ($oTimeWindow->getHash() == $sHash) {
                return $oTimeWindow;
            }
        }
        return null;
    }




    /**
     * Validate basket data to decide if can be delivered by tiramizoo 
     * 
     * @return bool
     */
    public function isTiramizooAvailable() 
    {
        //@ToDo: validate basket and others
        if ($this->_isTiramizooAvailable === -1) {

            try {
                $oRetailLocation = $this->getRetailLocation();
            } catch(Exception $e) {
                return $this->_isTiramizooAvailable = 0;
            }

            if (count($this->getAvailableDeliveryTypes()) == 0) {
                return $this->_isTiramizooAvailable = 0;   
            }

            //@TODO: add items in basket

            $this->_isTiramizooAvailable = 1;

        }

        return $this->_isTiramizooAvailable;
    }


}