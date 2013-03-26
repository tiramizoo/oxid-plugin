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
     * @return oxTiramizooHelper
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
        // old var names used before changes:

        // oxTiramizoo_shop_phone_number 
        // oxTiramizoo_version 
        // oxTiramizoo_shop_pickup_hour_1 
        // oxTiramizoo_works_sat 
        // oxTiramizoo_works_sun
        // oxTiramizoo_package_strategy
        // oxTiramizoo_shop_contact_name
        // oxTiramizoo_shop_city
        // oxTiramizoo_shop_country_code
        // oxTiramizoo_shop_country_label
        // oxTiramizoo_pickup_del_offset
        // oxTiramizoo_evening_window
        // oxTiramizoo_shop_pickup_hour_4
        // oxTiramizoo_global_length
        // oxTiramizoo_global_width
        // oxTiramizoo_global_height
        // oxTiramizoo_global_weight
        // oxTiramizoo_order_pickup_offset
        // oxTiramizoo_shop_postal_code
        // oxTiramizoo_price
        // oxTiramizoo_api_token
        // oxTiramizoo_enable_evening
        // oxTiramizoo_enable_immediate
        // oxTiramizoo_enable_select_time
        // oxTiramizoo_shop_email_address
        // oxTiramizoo_shop_address
        // oxTiramizoo_shop_pickup_hour_2
        // oxTiramizoo_shop_pickup_hour_3
        // oxTiramizoo_pickup_time_length
        // oxTiramizoo_is_installed
        // oxTiramizoo_works_mon
        // oxTiramizoo_works_tue
        // oxTiramizoo_works_wed
        // oxTiramizoo_works_thu
        // oxTiramizoo_works_fri
        // oxTiramizoo_api_url
        // oxTiramizoo_shop_url
        // oxTiramizoo_pickup_hour_1
        // oxTiramizoo_pickup_hour_2
        // oxTiramizoo_pickup_hour_3
        // oxTiramizoo_pickup_hour_4
        // oxTiramizoo_pickup_hour_5
        // oxTiramizoo_pickup_hour_6
        // oxTiramizoo_enable_module
        // oxTiramizoo_shop_pickup_hour_5
        // oxTiramizoo_articles_stock_gt_0
        // oxTiramizoo_shop_pickup_hour_6
        // oxTiramizoo_update_errors
        // oxTiramizoo_exclude_days
        // oxTiramizoo_include_days
        // oxTiramizoo_std_package_width
        // oxTiramizoo_std_package_length
        // oxTiramizoo_std_package_height
        // oxTiramizoo_std_package_weight
        // oxTiramizoo_package_size_1
        // oxTiramizoo_package_size_2
        // oxTiramizoo_package_size_3
        // oxTiramizoo_package_size_4
        // oxTiramizoo_package_size_5
        // oxTiramizoo_package_size_6

        // new var names used before changes:

        // oxTiramizoo_service_areas


    }

    /**
     * Synchronize all possible configs
     */
    public function synchronizeAll()
    {
        $oxTiramizooApi = oxTiramizooApi::getInstance();

        try {
            $oxTiramizooApi->synchronizeConfiguration();
            $oxTiramizooApi->synchronizeRetailLocation();
            $oxTiramizooApi->synchronizePackageSizes();
        } catch (oxTiramizoo_ApiException $e) {
            echo $e->getMessage();
        }

        $this->synchronizeTimeWindows();
    }

    public function synchronizeTimeWindows()
    {
        $aPickupPostalCodes = $this->getShopConfVar('aPickupPostalCodes');

        foreach ($aPickupPostalCodes as $sPickupPostalCode) 
        {
            oxTiramizooApi::getInstance()->synchronizeServiceAreas($sPickupPostalCode);
        }
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
    public function saveShopConfVar( $sVarType, $sVarName, $sVarVal, $sShopId = null )
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
        $sQ = "delete from oxtiramizooconfig where oxshopid = '$sShopId' and oxvarname = '$sVarName'";
        $oDb->execute( $sQ );

        $sNewOXIDdQuoted  = $oDb->quote(oxUtilsObject::getInstance()->generateUID());
        $sShopIdQuoted    = $oDb->quote($sShopId);
        $sVarNameQuoted   = $oDb->quote($sVarName);
        $sVarTypeQuoted   = $oDb->quote($sVarType);
        $sVarValueQuoted  = $oDb->quote($sValue);
        $sConfigKeyQuoted = $oDb->quote($this->getConfigParam('sConfigKey'));

        $sQ = "insert into oxtiramizooconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue, oxlastsync)
               values($sNewOXIDdQuoted, $sShopIdQuoted, $sVarNameQuoted, $sVarTypeQuoted, ENCODE( $sVarValueQuoted, $sConfigKeyQuoted), NOW())";

        $oDb->execute( $sQ );
    }

    /**
     * Retrieves shop configuration parameters from DB.
     *
     * @param string $sVarName Variable name
     * @param string $sShopId  Shop ID
     *
     * @return object - raw configuration value in DB
     */
    public function getShopConfVar( $sVarName, $sShopId = null )
    {
        if ( !$sShopId ) {
            $sShopId = $this->getShopId();
        }

        $oDb = oxDb::getDb(true);
        $sQ  = "select oxvartype, DECODE( oxvarvalue, '".$this->getConfigParam( 'sConfigKey' )."') as oxvarvalue from oxtiramizooconfig where oxshopid = '$sShopId' and oxvarname = ".$oDb->quote($sVarName);
        $oRs = $oDb->Execute( $sQ );

        $sValue = null;
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            $sVarType = $oRs->fields['oxvartype'];
            $sVarVal  = $oRs->fields['oxvarvalue'];
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