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

        $oRetailLocation = oxTiramizoo_RetailLocation::findOneByFilters( array('oxapitoken' => $sApiToken) );
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

    public function saveShopConfVar( $sVarType, $sVarName, $sVarVal, $sShopId = null, $sModule = 'oxTiramizoo' )
    {
        parent::saveShopConfVar( $sVarType, $sVarName, $sVarVal, $sShopId, $sModule);
    }

    public function getShopConfVar( $sVarName, $sShopId = null, $sModule = 'oxTiramizoo' )
    {
        return parent::getShopConfVar( $sVarName, $sShopId, $sModule);
    }


    public function getTiramizooConfVars( $sShopId = null )
    {
        if ( !$sShopId ) {
            $sShopId = $this->getShopId();
        }

        $sModule = 'oxTiramizoo';

        $aTypeArray = array("bool"   => 'confbools',
                            "str"    => 'confstrs',
                            "arr"    => 'confarrs',
                            "aarr"   => 'confaarrs',
                            "select" => 'confselects',
                            "int"    => 'confints');

        $oDb = oxDb::getDb();
        $sQ  = "select oxvarname, oxvartype, DECODE( oxvarvalue, '".$this->getConfigParam( 'sConfigKey' )."') as oxvarvalue from oxconfig where oxshopid = '{$sShopId}' AND oxmodule ='{$sModule}';";
        $oRs = $oDb->Execute( $sQ );

        $aValues = array('confbools' => array(),
                         'confstrs' => array(),
                         'confarrs' => array(),
                         'confaarrs' => array(),
                         'confselects' => array(),
                         'confints' => array());


        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while (!$oRs->EOF) {
                
                list($sVarName, $sVarType, $sVarVal) = $oRs->fields;

                $aValues[$aTypeArray[$sVarType]][$sVarName] = $this->decodeValue($sVarType, $sVarVal);

                $oRs->moveNext();
            }
        }

        return $aValues;
    }
}
