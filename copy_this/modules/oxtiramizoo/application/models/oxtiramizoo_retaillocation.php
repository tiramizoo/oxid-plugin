<?php

class oxTiramizoo_RetailLocation extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizooretaillocation';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxTiramizoo_RetailLocation';

    protected $_aConfigVars = null;

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizooretaillocation' );
    }

    public function getIdByApiToken($sApiToken) 
    {
	    $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
	    $sQ = "SELECT oxid FROM " . $this->_sCoreTbl . " WHERE OXSHOPID = '" . $this->getConfig()->getShopId() . "' AND oxapitoken = " . $oDb->quote( $sApiToken );
        
        return $oDb->getOne($sQ);
    }

    public function refreshConfigVars()
    {
        if ($this->_aConfigVars === null) {
            $this->_aConfigVars = array();

            $aRetailLocationConfigs = $this->getRetailLocationConfigs();

            foreach ($aRetailLocationConfigs as $oRetailLocationConfig) 
            {
                $this->_aConfigVars[$oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname->value] = $oRetailLocationConfig;
                $this->_aConfigVars[$oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname->value]->getValue();
            }
        }
    }

    public function getConfVar($sConfVarName)
    {
        $this->refreshConfigVars();

        if (isset($this->_aConfigVars[$sConfVarName])) {
            return $this->_aConfigVars[$sConfVarName]->getValue();
        }
        return null;
    }

    public function getApiToken()
    {
        return $this->oxtiramizooretaillocation__oxapitoken->value;
    }

    public function getRetailLocationConfigs()
    {
        $aRetailLocationConfigList = oxNew('oxTiramizoo_RetailLocationConfigList');
        $aRetailLocationConfigList->loadByRetailLocationId($this->getId());

        return $aRetailLocationConfigList;
    }

    public function synchronizeConfiguration($response)
    {
        if ($response['http_status'] != 200) {
            throw new oxTiramizoo_ApiException("Can't connect to Tiramizoo API", 1);
        }

        $aResponse = $this->objectToArray($response['response']);

        foreach ($aResponse as $sConfigIndex => $sValue) 
        {
            if(is_array($sValue)) {
                $sVarType = 'aarr';
            } else {
                $sVarType = 'str';
            }

            $oRetailLocationConfig = oxNew('oxTiramizoo_RetailLocationConfig');
            $oRetailLocationConfig->load($oRetailLocationConfig->getIdByRetailLocationIdAndVarName($this->getId(), $sConfigIndex));

            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxretaillocationid = new oxField($this->getId());
            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname = new oxField($sConfigIndex);
            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvartype = new oxField($sVarType);
            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarvalue = new oxField( base64_encode( serialize( $sValue ) ) );
            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxlastsync = new oxField(oxTiramizoo_Date::date());

            $oRetailLocationConfig->save();
        }
    }

    public function delete($sOXID = null)
    {
        foreach ($this->getRetailLocationConfigs() as $oRetailLocationConfig) 
        {
            $oRetailLocationConfig->delete();
        }

        return parent::delete();
    }

    public function getAvailableTimeWindows()
    {
        if ($aTimeWindows = $this->getConfVar('time_windows')) {
            
            //sort by delivery from date
            foreach ($aTimeWindows as $oldKey => $aTimeWindow) 
            {
                $oTimeWindow = oxNew('oxTiramizoo_TimeWindow', $aTimeWindow);

                $aTimeWindows[$oTimeWindow->getDeliveryFromDate()->getTimestamp()] = $aTimeWindow;
                unset($aTimeWindows[$oldKey]);
            }

            ksort($aTimeWindows);
        }

        return $aTimeWindows ? $aTimeWindows : array();
    }

    public function getTimeWindowByHash($sHash) 
    {
        foreach ($this->getAvailableTimeWindows() as $aTimeWindow) 
        {
            $oTimeWindow = oxNew('oxTiramizoo_TimeWindow', $aTimeWindow);
            
            if ($oTimeWindow->getHash() == $sHash) {
                return $oTimeWindow;
            }
        }
        return null;
    }

    public function objectToArray($data)
    {
        if (is_array($data) || is_object($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->objectToArray($value);
            }
            return $result;
        }
        return $data;
    }
}
