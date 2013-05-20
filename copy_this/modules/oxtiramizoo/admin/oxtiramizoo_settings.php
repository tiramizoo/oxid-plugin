<?php

/**
* Tiramizoo settings
*
* @package: oxTiramizoo
*/
class oxTiramizoo_settings extends Shop_Config
{

    public function init()
    {
        $oxTiramizooSetup = oxNew('oxTiramizoo_setup');
        $oxTiramizooSetup->install();

        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            parent::init();
        }
        // @codeCoverageIgnoreEnd
    }

    /**
    * Executes parent method parent::render() and returns name of template
    *
    * @return string
    */
    public function render()
    {
        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            parent::render();
        }
        // @codeCoverageIgnoreEnd

        $oxTiramizooConfig = oxRegistry::get('oxTiramizooConfig');

        $this->_aViewData['oPaymentsList'] = $this->getPaymentsList();

        $sCurrentAdminShop = $oxTiramizooConfig->getShopId();

        $aShopConfVars = $oxTiramizooConfig->getTiramizooConfVars();
        
        $this->_aViewData['confstrs'] = $aShopConfVars['confstrs'];
        $this->_aViewData['confarrs'] = $aShopConfVars['confarrs'];
        $this->_aViewData['confaarrs'] = $aShopConfVars['confaarrs'];
        $this->_aViewData['confselects'] = $aShopConfVars['confselects'];
        $this->_aViewData['confbools'] = $aShopConfVars['confbools'];
        $this->_aViewData['confints'] = $aShopConfVars['confints'];

        $this->_aViewData['version'] = oxTiramizoo_setup::VERSION;

        $oRetailLocationList = oxnew('oxTiramizoo_RetailLocationList');
        $oRetailLocationList->loadAll();

        $this->_aViewData['aRetailLocations'] = $oRetailLocationList;

        return 'oxTiramizoo_settings.tpl';
    }

    public function getPaymentsList()
    {
        $oxPaymentList = oxNew('Payment_List');
        $oxPaymentList->init();

        $aPaymentList = array();
        $soxId = 'Tiramizoo';
        $oDb = oxDb::getDb();

        foreach ($oxPaymentList->getItemList() as $key => $oPayment) 
        {
            $aPaymentList[$oPayment->oxpayments__oxid->value] = array();
            $aPaymentList[$oPayment->oxpayments__oxid->value]['desc'] = $oPayment->oxpayments__oxdesc->value;

            $sID = $oDb->getOne("select oxid from oxobject2payment where oxpaymentid = " . $oDb->quote( $oPayment->oxpayments__oxid->value ) . "  and oxobjectid = ".$oDb->quote( $soxId )." and oxtype = 'oxdelset'", false, false);

            $aPaymentList[$oPayment->oxpayments__oxid->value]['checked'] = isset($sID) && $sID;
        }  

        return $aPaymentList;
    }

    public function assignPaymentsToTiramizoo()
    {
        $aPayments = $this->getConfig()->getRequestParameter("payment");

        //assign payments for Tiramizoo
        $soxId = 'Tiramizoo';

        $oDb = oxDb::getDb();

        foreach ( $aPayments as $sPaymentId => $isAssigned) 
        {
            if ($isAssigned) {
                // check if we have this entry already in
                $sID = $oDb->getOne("SELECT oxid FROM oxobject2payment WHERE oxpaymentid = " . $oDb->quote( $sPaymentId ) . "  AND oxobjectid = ".$oDb->quote( $soxId )." AND oxtype = 'oxdelset'", false, false);
                if ( !isset( $sID) || !$sID) {
                    $oObject = oxNew( 'oxbase' );
                    $oObject->init( 'oxobject2payment' );
                    $oObject->oxobject2payment__oxpaymentid = new oxField($sPaymentId);
                    $oObject->oxobject2payment__oxobjectid  = new oxField($soxId);
                    $oObject->oxobject2payment__oxtype      = new oxField("oxdelset");
                    $oObject->save();
                }
            } else {
                $oDb->Execute("DELETE FROM oxobject2payment WHERE oxpaymentid = " . $oDb->quote( $sPaymentId ) . "  AND oxobjectid = ".$oDb->quote( $soxId )." AND oxtype = 'oxdelset'");
            }
        }
    }

    /**
    * Saves shop configuration variables
    *
    * @return null
    */
    public function saveConfVars()
    {
        $oxTiramizooConfig = oxRegistry::get('oxTiramizooConfig');

        $aConfBools = $this->getConfig()->getRequestParameter("confbools");
        $aConfStrs  = $this->getConfig()->getRequestParameter("confstrs");
        $aConfArrs  = $this->getConfig()->getRequestParameter("confarrs");
        $aConfAarrs = $this->getConfig()->getRequestParameter("confaarrs");
        $aConfInts  = $this->getConfig()->getRequestParameter("confints");

        if ( is_array( $aConfBools ) ) {
          foreach ( $aConfBools as $sVarName => $sVarVal ) {
              $oxTiramizooConfig->saveShopConfVar( "bool", $sVarName, $sVarVal);
          }
        }

        if ( is_array( $aConfStrs ) ) {
          foreach ( $aConfStrs as $sVarName => $sVarVal ) {
            $oxTiramizooConfig->saveShopConfVar( "str", $sVarName, $sVarVal);
          }
        }

        if ( is_array( $aConfArrs ) ) {
          foreach ( $aConfArrs as $sVarName => $aVarVal ) {
            if ( !is_array( $aVarVal ) ) {
              $aVarVal = $this->_multilineToArray($aVarVal);
            }
            $oxTiramizooConfig->saveShopConfVar("arr", $sVarName, $aVarVal);
          }
        }

        if ( is_array( $aConfAarrs ) ) {
          foreach ( $aConfAarrs as $sVarName => $aVarVal ) {
            $oxTiramizooConfig->saveShopConfVar( "aarr", $sVarName, $this->_multilineToAarray( $aVarVal ));
          }
        }

        if ( is_array( $aConfInts ) ) {
          foreach ( $aConfInts as $sVarName => $aVarVal ) {
            $oxTiramizooConfig->saveShopConfVar( "int", $sVarName, $aVarVal );
          }
        }
    }


    public function tiramizooApiUrlHasChanged()
    {
        $oxTiramizooConfig = oxRegistry::get('oxTiramizooConfig');

        $aConfStrs = $this->getConfig()->getRequestParameter( "confstrs" );

        if ($aConfStrs['oxTiramizoo_api_url'] != $oxTiramizooConfig->getShopConfVar('oxTiramizoo_api_url')) {
            return true;
        }

        return false;
    }
  
    /**
     * Set active on/off in tiramizoo delivery and delivery set
     */
    public function saveEnableShippingMethod()
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizooConfig');

        $isTiramizooEnable = 1;

        $errors = $this->validateEnable();

        if (count($errors)) {
            $isTiramizooEnable = 0;
        }

        $sql = "UPDATE oxdelivery
                    SET OXACTIVE = " . $isTiramizooEnable . "
                    WHERE OXID = 'TiramizooStandardDelivery' AND OXSHOPID = '" . $oTiramizooConfig->getShopId() .  "';";

        oxDb::getDb()->Execute($sql);

        $sql = "UPDATE oxdeliveryset
                    SET OXACTIVE = " . $isTiramizooEnable . "
                    WHERE OXID = 'Tiramizoo' AND OXSHOPID = '" . $oTiramizooConfig->getShopId() .  "';";

        oxDb::getDb()->Execute($sql);
    }

    /**
     * Saves main user parameters.
     *
     * @return mixed
     */
    public function synchronize()
    {
        try 
        {
            $oTiramizooConfig = oxRegistry::get('oxTiramizooConfig');

            $oRetailLocationList = oxNew('oxTiramizoo_RetailLocationList');
            $oRetailLocationList->loadAll();

            foreach ($oRetailLocationList->getArray() as $oRetailLocation) 
            {
                $oTiramizooConfig->synchronizeAll( $oRetailLocation->getApiToken() );
            }

        } catch (oxTiramizoo_ApiException $e) {
            echo $e->getMessage();
            return 'oxTiramizoo_settings.tpl';
        }

        return 'oxtiramizoo_settings';
    }


    /**
     * Saves main user parameters.
     *
     * @return mixed
     */
    public function save()
    {
        // saving config params
        if ($this->tiramizooApiUrlHasChanged()) {
            
            $this->saveConfVars();

            $oRetailLocationList = oxnew('oxTiramizoo_RetailLocationList');
            $oRetailLocationList->loadAll();

            foreach ($oRetailLocationList->getArray() as $oRetailLocation) 
            {
                try
                {
                    $remote = oxTiramizooApi::getApiInstance( $oRetailLocation->getApiToken() )->getRemoteConfiguration();
                } catch (oxTiramizoo_ApiException $e) {
                    $oRetailLocation->delete();
                }
            }    
        } else {
            $this->saveConfVars();
        }

        $this->saveEnableShippingMethod();       
        $this->assignPaymentsToTiramizoo();
    
        return 'oxtiramizoo_settings';
    }


    public function addNewLocation()
    {

        $sApiToken = trim($this->getConfig()->getRequestParameter('api_token'));
        $oTiramizooConfig = oxRegistry::get('oxTiramizooConfig');
        $oTiramizooRetailLocation = oxNew('oxTiramizoo_RetailLocation');
        
        if ($sOxid = $oTiramizooRetailLocation->getIdByApiToken( $sApiToken )) 
        {
            $oTiramizooRetailLocation->load( $sOxid );            
        }

        //@ToDo: change this
        $oTiramizooRetailLocation->oxtiramizooretaillocation__oxname = new oxField(oxTiramizoo_Date::date());
        $oTiramizooRetailLocation->oxtiramizooretaillocation__oxapitoken = new oxField( $sApiToken );
        $oTiramizooRetailLocation->oxtiramizooretaillocation__oxshopid = new oxField( $oTiramizooConfig->getShopId() );

        $oTiramizooRetailLocation->save();

        try
        {
            oxTiramizooApi::getApiInstance( $sApiToken )->getRemoteConfiguration();
        } catch (oxTiramizoo_ApiException $e) {
            $oTiramizooRetailLocation->delete();

            //@todo: add errors
            return 'oxtiramizoo_settings';
        }

        $oTiramizooConfig->synchronizeAll( $sApiToken );
        
        return 'oxtiramizoo_settings';
    }

    public function removeLocation()
    {
        $sApiToken = trim($this->getConfig()->getRequestParameter('api_token'));

        $oRetailLocation = oxNew('oxTiramizoo_RetailLocation');
        $sOxid = $oRetailLocation->getIdByApiToken($sApiToken);

        if ($sOxid) 
        {
            $oRetailLocation->load($sOxid);
            $oRetailLocation->delete();            
        }

        return 'oxtiramizoo_settings';
    }

    /**
     * Validate if enable
     *
     * @return array
     */
    public function validateEnable()
    {
        $aConfStrs = $this->getConfig()->getRequestParameter( "confstrs" );
        $aPayments = $this->getConfig()->getRequestParameter( "payment" );

        $errors = array();

        if (!trim($aConfStrs['oxTiramizoo_api_url'])) {
            $errors[] = oxRegistry::getLang()->translateString('oxTiramizoo_settings_api_url_label', oxRegistry::getLang()->getBaseLanguage(), true) . ' ' . oxRegistry::getLang()->translateString('oxTiramizoo_is_required', oxRegistry::getLang()->getBaseLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_shop_url'])) {
            $errors[] = oxRegistry::getLang()->translateString('oxTiramizoo_settings_shop_url_label', oxRegistry::getLang()->getBaseLanguage(), true) . ' ' . oxRegistry::getLang()->translateString('oxTiramizoo_is_required', oxRegistry::getLang()->getBaseLanguage(), true);
        }

        $paymentsAreValid = 0;
        foreach ($aPayments as $paymentName => $paymentIsEnable) 
        {
            if ($paymentIsEnable) {
               $paymentsAreValid = 1;
            }
        }

        if (!$paymentsAreValid) {
            $errors[] = oxRegistry::getLang()->translateString('oxTiramizoo_payments_required_error', oxRegistry::getLang()->getBaseLanguage(), true);
        }

        return $errors;
    }

}