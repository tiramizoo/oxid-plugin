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


    public static function findOneByFilters($aFilters) 
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizooConfig');

        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $whereItems = array();

        foreach ($aFilters as $sColumnName => $value) 
        {
            $whereItems[] =  $sColumnName . " = " . $oDb->quote( $value );
        }

        $sQ = "SELECT * FROM oxtiramizooretaillocation WHERE OXSHOPID = '" . $oTiramizooConfig->getShopId() . "' AND " . implode(' AND ', $whereItems);
        $rs = $oDb->select( $sQ );
        
        if ( $rs && $rs->RecordCount() ) {

            $oTiramizooRetailLocation = oxNew('oxTiramizoo_RetailLocation');
            $oTiramizooRetailLocation->load( $rs->fields['OXID'] );            

            return $oTiramizooRetailLocation;
        }

        return null;
    }

    public function getOxidByApiToken($sApiToken) 
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizooConfig');

	    $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
	    $sQ = "SELECT oxid FROM " . $this->_sCoreTbl . " WHERE OXSHOPID = '" . $oTiramizooConfig->getShopId() . "' AND oxapitoken = " . $oDb->quote( $sApiToken );
	    $rs = $oDb->select( $sQ );
	    
	    if ( $rs && $rs->RecordCount() ) {
	    	return $rs->fields['oxid'];
	    }

	    return false;
    }

    public static function getAll() 
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizooConfig');

	    $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
	    $sQ = "SELECT * FROM oxtiramizooretaillocation WHERE OXSHOPID = '" . $oTiramizooConfig->getShopId() . "';";
	    $oRs = $oDb->select( $sQ );
	    
	    $result = array();


        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while (!$oRs->EOF) {
    			$oTiramizooRetailLocation = oxNew('oxTiramizoo_RetailLocation');
            	$oTiramizooRetailLocation->load( $oRs->fields['OXID'] );            

            	$result[] = $oTiramizooRetailLocation;
                $oRs->moveNext();
            }
        }


	    return $result;
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
        return oxTiramizoo_RetailLocationConfig::findByFilters(array('OXRETAILLOCATIONID' => $this->getId()));        
    }

    public function synchronizeConfiguration($aRemoteConfiguration)
    {
        $response = $aRemoteConfiguration;

        if ($response['http_status'] != 200) {
            throw new oxTiramizoo_ApiException("Can't connect to Tiramizoo API", 1);
        }

        $aResponse = oxTiramizooApi::objectToArray($response['response']);

        foreach ($aResponse as $sConfigIndex => $sVarVal) 
        {
            //@ToDo: better choose type
            if(is_array($sVarVal)) {
                $sVarType = 'aarr';
            } else {
                $sVarType = 'str';
            }

            $sValue  = $sVarVal ;

            $sOxRetailLocationConfig = oxTiramizoo_RetailLocationConfig::findOneByFiltersOrCreate(array('oxretaillocationid' => $this->getId(), 'oxvarname' => $sConfigIndex));

            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname = new oxField($sConfigIndex);
            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxvartype = new oxField($sVarType);
            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarvalue = new oxField( base64_encode( serialize( $sValue ) ) );
            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxretaillocationid = new oxField($this->getId());
            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxlastsync = new oxField(oxTiramizoo_Date::date());

            $sOxRetailLocationConfig->save();
        }
    }

    public function synchronizeServiceAreas($aAvaialbleServiceAreas)
    {
        $response = $aAvaialbleServiceAreas;

        if ($response['http_status'] != 200) {
            throw new oxTiramizoo_ApiException("Can't connect to Tiramizoo API", 1);
        }

        $aResponse = oxTiramizooApi::objectToArray($response['response']);

        foreach ($aResponse as $sConfigIndex => $sVarVal) 
        {
            //@ToDo: better choose type
            if(is_array($sVarVal)) {
                $sVarType = 'aarr';
            } else {
                $sVarType = 'str';
            }

            $sValue  = $sVarVal ;

            $sOxRetailLocationConfig = oxTiramizoo_RetailLocationConfig::findOneByFiltersOrCreate(array('oxretaillocationid' => $this->getId(), 'oxvarname' => $sConfigIndex));

            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname = new oxField($sConfigIndex);
            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxvartype = new oxField($sVarType);
            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarvalue = new oxField( base64_encode( serialize( $sValue ) ) );
            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxretaillocationid = new oxField($this->getId());
            $sOxRetailLocationConfig->oxtiramizooretaillocationconfig__oxlastsync = new oxField(oxTiramizoo_Date::date());

            $sOxRetailLocationConfig->save();
        }
    }

    public function delete($sOXID = null)
    {
        $aRetailLocationConfigs = $this->getRetailLocationConfigs();

        foreach ($aRetailLocationConfigs as $oRetailLocationConfig) 
        {
            $oRetailLocationConfig->delete();
        }

        return parent::delete();
    }



    public function getAvailableTimeWindows()
    {
        $aTimeWindows = $this->getConfVar('time_windows');

        if ($aTimeWindows) {
            
            //sort by delivery from date
            foreach ($aTimeWindows as $oldKey => $aTimeWindow) 
            {
                $oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindow);

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
            $oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindow);
            
            if ($oTimeWindow->getHash() == $sHash) {
                return $oTimeWindow;
            }
        }
        return null;
    }


}