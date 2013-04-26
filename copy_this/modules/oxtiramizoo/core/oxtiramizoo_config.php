<?php



/**
 * This class contains tiramizoo config
 *
 * @package: oxTiramizoo
 */
class oxTiramizooConfig extends oxConfig
{
    /**
     * Singleton instance
     * 
     * @var oxTiramizooApi
     */
    protected static $_instance = null;

    /**
     * Get the instance of class
     * 
     * @return oxTiramizooConfig
     */
    public static function getInstance()
    {
        if ( !self::$_instance instanceof oxTiramizooConfig ) {
            self::$_instance = new oxTiramizooConfig();
        }

        return self::$_instance;
    }

    public function __construct()
    {

    }

    /**
     * Synchronize all possible configs
     */
    public function synchronizeAll( $sApiToken = null )
    {
        $oxTiramizooApi = oxTiramizooApi::getApiInstance( $sApiToken );

        $aRemoteConfiguration = $oxTiramizooApi->getRemoteConfiguration();

        $oRetailLocation = oxtiramizooretaillocation::findOneByFilters( array('oxapitoken' => $sApiToken) );
        $oRetailLocation->synchronizeConfiguration( $aRemoteConfiguration );

        $aPickupAddress = $oRetailLocation->getConfVar('pickup_contact');

        // synchronize service areas for 2 days
        $oStartDate = new oxTiramizoo_Date(date('Y-m-d'));
        $oEndDate = new oxTiramizoo_Date(date('Y-m-d'));
        $oEndDate->modify('+2 days');

        $startDate = $oStartDate->getForRestApi();
        $endDate = $oEndDate->getForRestApi();

        $aRangeDates = array('express_from' => $startDate, 
                             'express_to' => $endDate,
                             'standard_from' => $startDate,
                             'standard_to' => $endDate);

        $aAvaialbleServiceAreas = $oxTiramizooApi->getAvailableServiceAreas($aPickupAddress['postal_code'], $aRangeDates);
        $oRetailLocation->synchronizeServiceAreas($aAvaialbleServiceAreas);
    }

    /**
     * Updates or adds new shop configuration parameters to DB.
     * Arrays must be passed not serialized, serialized values are supported just for backward compatibility.
     *
     * @param string $sVarType Variable Type
     * @param string $sVarName Variable name
     * @param mixed  $sVarVal  Variable value (can be string, integer or array)
     * @param string $sShopId  Shop ID, default is current shop
     *
     * @return null
     */
    public function saveShopConfVar( $sVarType, $sVarName, $sVarVal, $sShopId = null, $sModule = '' )
    {
        switch ( $sVarType ) {
            case 'arr':
            case 'aarr':
                if (is_array($sVarVal)) {
                    $sValue = serialize( $sVarVal );
                } else {
                    // Deprecated functionality
                    $sValue  = $sVarVal ;
                    $sVarVal = unserialize( $sVarVal );
                }
                break;
            case 'bool':
                //config param
                $sVarVal = (( $sVarVal == 'true' || $sVarVal) && $sVarVal && strcasecmp($sVarVal, "false"));
                //db value
                $sValue  = $sVarVal?"1":"";
                break;
            default:
                $sValue  = $sVarVal;
                break;
        }

        if ( !$sShopId ) {
            $sShopId = $this->getShopId();
        }

        // Update value only for current shop
        if ($sShopId == $this->getShopId()) {
            $this->setConfigParam( $sVarName, $sVarVal );
        }

        $oDb = oxDb::getDb();

        $rs = $oDb->Execute(
                "select oxid
                from oxtiramizooconfig
                where oxvarname = '$sVarName' AND oxshopid = '$sShopId'"
        );

        $sOxid = null;

        if ($rs != false && $rs->recordCount() > 0) {
            list($sOxid) = $rs->fields;
        }


        $sNewOXIDdQuoted  = $oDb->quote(oxUtilsObject::getInstance()->generateUID());
        $sShopIdQuoted    = $oDb->quote($sShopId);
        $sVarNameQuoted   = $oDb->quote($sVarName);
        $sVarTypeQuoted   = $oDb->quote($sVarType);
        $sVarValueQuoted  = $oDb->quote($sValue);
        $sConfigKeyQuoted = $oDb->quote($this->getConfigParam('sConfigKey'));

        if ($sOxid) {
            $sQ = "UPDATE oxtiramizooconfig SET oxvarvalue = ENCODE( $sVarValueQuoted, $sConfigKeyQuoted), oxlastsync = NOW() where oxvarname = '$sVarName' AND oxshopid = '$sShopId'";
        } else {
            $sQ = "insert into oxtiramizooconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue, oxlastsync)
               values($sNewOXIDdQuoted, $sShopIdQuoted, $sVarNameQuoted, $sVarTypeQuoted, ENCODE( $sVarValueQuoted, $sConfigKeyQuoted), NOW())";
        }

        $oDb->execute( $sQ );
    }

    public function setConfVarGroup( $sVarName, $sGroup = null )
    {
        if ($sVarName && ($sGroup !== null)) 
        {
            $oDb = oxDb::getDb();

            $sQ = "UPDATE oxtiramizooconfig SET oxgroup = '$sGroup' WHERE oxvarname = '$sVarName';";
            $oDb->execute( $sQ );
        }
    }


    public function getShopConfVars( $sShopId = null )
    {
        if ( !$sShopId ) {
            $sShopId = $this->getShopId();
        }

        $aTypeArray = array("bool"   => 'confbools',
                            "str"    => 'confstrs',
                            "arr"    => 'confarrs',
                            "aarr"   => 'confaarrs',
                            "select" => 'confselects',
                            "num"    => 'confnum');

        $oDb = oxDb::getDb();
        $sQ  = "select oxvarname, oxvartype, DECODE( oxvarvalue, '".$this->getConfigParam( 'sConfigKey' )."') as oxvarvalue from oxtiramizooconfig where oxshopid = '$sShopId'";
        $oRs = $oDb->Execute( $sQ );

        $aValues = array('confbools' => array(),
                         'confstrs' => array(),
                         'confarrs' => array(),
                         'confaarrs' => array(),
                         'confselects' => array(),
                         'confnum' => array());


        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while (!$oRs->EOF) {
                
                list($sVarName, $sVarType, $sVarVal) = $oRs->fields;

                    switch ( $sVarType ) {  
                    case 'arr':
                    case 'aarr':
                        $sValue =  unserialize( $sVarVal );
                        break;
                    case 'bool':
                        $sValue =  ( $sVarVal == 'true' || $sVarVal == '1' );
                        break;
                    default:
                        $sValue = $sVarVal;
                        break;
                }

                $aValues[$aTypeArray[$sVarType]][$sVarName] = $sValue;

                $oRs->moveNext();
            }
        }

        return $aValues;
    }

    /**
     * Retrieves shop configuration parameters from DB.
     *
     * @param string $sVarName Variable name
     * @param string $sShopId  Shop ID
     *
     * @return object - raw configuration value in DB
     */
    public function getShopConfVar( $sVarName, $sShopId = null, $sModule = '' )
    {
        if ( !$sShopId ) {
            $sShopId = $this->getShopId();
        }

        $oDb = oxDb::getDb( oxDb::FETCH_MODE_NUM );

        $sQ  = "select oxvartype, DECODE( oxvarvalue, '".$this->getConfigParam( 'sConfigKey' )."') as oxvarvalue from oxtiramizooconfig where oxshopid = '$sShopId' and oxvarname = ".$oDb->quote($sVarName);
        $oRs = $oDb->Execute( $sQ );

        $sValue = null;
        if ( $oRs != false && $oRs->recordCount() > 0 ) {

            //$TODO: ugly conversion
            list($sVarType, $sVarVal) = array_values($oRs->fields);

            switch ( $sVarType ) {
                case 'arr':
                case 'aarr':
                    $sValue =  unserialize( $sVarVal );
                    break;
                case 'bool':
                    $sValue =  ( $sVarVal == 'true' || $sVarVal == '1' );
                    break;
                case 'str':
                    $sValue = $sVarVal;
                    break;
                default:
                    $sValue = $sVarVal;
                    break;
            }
        }

        return $sValue;
    }

    /**
     * Function returns default shop ID
     *
     * @return string
     */
    public function getBaseShopId()
    {
        return 'oxbaseshop';
    }

    /**
     * Loads and returns active shop object
     *
     * @return oxshop
     */
    public function getActiveShop()
    {
        if ( $this->_oActShop && $this->_iShopId == $this->_oActShop->getId() &&
             $this->_oActShop->getLanguage() == oxLang::getInstance()->getBaseLanguage() ) {
            return $this->_oActShop;
        }

        $this->_oActShop = oxNew( 'oxshop' );
        $this->_oActShop->load( $this->getShopId() );
        return $this->_oActShop;
    }

}