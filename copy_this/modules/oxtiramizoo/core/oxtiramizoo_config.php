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
 * Tiramizoo config class extends oxConfig used for retrieving
 * module configuration. Synchronize and save config variables.
 *
 * @extends oxConfig
 * @package oxTiramizoo
 */
class oxTiramizoo_Config extends oxConfig
{
    /**
     * Synchronize all possible configs
     *
     * @param string $sApiToken API token
     *
     * @return null
     */
    public function synchronizeAll( $sApiToken = null )
    {
        $oTiramizooApi = oxTiramizoo_Api::getApiInstance( $sApiToken );

        $aRemoteConfiguration = $oTiramizooApi->getRemoteConfiguration();

        $oRetailLocation = oxnew('oxTiramizoo_RetailLocation');
        $sOxid = $oRetailLocation->getIdByApiToken($sApiToken);

        $oRetailLocation->load($sOxid);
        $oRetailLocation->synchronizeConfiguration( $aRemoteConfiguration );

        $aPickupAddress = $oRetailLocation->getConfVar('pickup_contact');

        // synchronize service areas for 2 days
        $oStartDate = oxNew('oxTiramizoo_Date', date('Y-m-d'));
        $oEndDate = oxNew('oxTiramizoo_Date', date('Y-m-d'));
        $oEndDate->modify('+7 days');
        $oEndDateExpress = oxNew('oxTiramizoo_Date', date('Y-m-d'));
        $oEndDateExpress->modify('+2 days');

        $startDate = $oStartDate->getForRestApi();
        $endDate = $oEndDate->getForRestApi();

        $aRangeDates = array('express_from' => $startDate,
                             'express_to' => $oEndDateExpress,
                             'standard_from' => $startDate,
                             'standard_to' => $endDate);

        $aAvaialbleServiceAreas = $oTiramizooApi
                                        ->getAvailableServiceAreas($aPickupAddress['postal_code'], $aRangeDates);
        $oRetailLocation->synchronizeConfiguration($aAvaialbleServiceAreas);
    }

    /**
     * Adds or upfate oxTiramizoo Module configuration to DB.
     *
     * @extend oxConfig::saveShopConfVar()
     *
     * @param string $sVarType Variable Type
     * @param string $sVarName Variable name
     * @param mixed  $sVarVal  Variable value (can be string, integer or array)
     * @param string $sShopId  Shop ID, default is current shop
     * @param string $sModule  Module name (oxTiramizoo for default)
     *
     * @return null
     */
    public function saveShopConfVar( $sVarType, $sVarName, $sVarVal, $sShopId = null, $sModule = 'oxTiramizoo' )
    {
        parent::saveShopConfVar( $sVarType, $sVarName, $sVarVal, $sShopId, $sModule);
    }

    /**
     * Retrieves oxTiramizoo Module configuration from DB.
     *
     * @extend oxConfig::saveShopConfVar()
     *
     * @param string $sVarName Variable name
     * @param string $sShopId  Shop ID
     * @param string $sModule  Module name (oxTiramizoo for default)
     *
     * @return object - raw configuration value in DB
     */
    public function getShopConfVar( $sVarName, $sShopId = null, $sModule = 'oxTiramizoo' )
    {
        return parent::getShopConfVar( $sVarName, $sShopId, $sModule);
    }

    /**
     * Retrieves all oxTiramizoo Module configuration from DB.
     *
     * @param string $sShopId  Shop ID
     *
     * @return array - array of all oxTiramizoo Module configuration values
     */
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
        $sQ  = "SELECT  oxvarname,
                        oxvartype,
                        DECODE( oxvarvalue, '".$this->getConfigParam( 'sConfigKey' )."')
                                                AS oxvarvalue
                    FROM oxconfig
                        WHERE oxshopid = '{$sShopId}'
                            AND oxmodule ='{$sModule}';";
        $oRs = $oDb->Execute( $sQ );

        $aValues = array('confbools' => array(),
                         'confstrs' => array(),
                         'confarrs' => array(),
                         'confaarrs' => array(),
                         'confselects' => array(),
                         'confints' => array());

        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while (!$oRs->EOF) {

                //using array_values prevent against problems with fetch type in EE fetch_assoc, CE fetch_num
                list($sVarName, $sVarType, $sVarVal) = array_values($oRs->fields);
                $aValues[$aTypeArray[$sVarType]][$sVarName] = $this->decodeValue($sVarType, $sVarVal);

                $oRs->moveNext();
            }
        }

        return $aValues;
    }
}
