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
        $oxTiramizooSetup = new oxTiramizoo_setup();
        $oxTiramizooSetup->install();

        return parent::Init();

    }

    /**
    * Executes parent method parent::render() and returns name of template
    *
    * @return string
    */
    public function render()
    {

        // for test only
        // oxTiramizooApi::getInstance()->synchronizeServiceAreas(80639);
        // $serviceAreasResponse = oxTiramizooConfig::getInstance()->getShopConfVar('service_areas_80639');
        
        parent::render();

        $oxConfig = $this->getConfig();

        $oxTiramizooConfig = oxTiramizooConfig::getInstance();

        $this->_aViewData['oPaymentsList'] = $this->getPaymentsList();

        $sCurrentAdminShop = $oxConfig->getShopId();

        $aShopConfVars = $oxTiramizooConfig->getShopConfVars();
            
        $this->_aViewData['confstrs'] = $aShopConfVars['confstrs'];
        $this->_aViewData['confarrs'] = $aShopConfVars['confarrs'];
        $this->_aViewData['confaarrs'] = $aShopConfVars['confaarrs'];
        $this->_aViewData['confselects'] = $aShopConfVars['confselects'];
        $this->_aViewData['confbools'] = $aShopConfVars['confbools'];
        $this->_aViewData['confnum'] = $aShopConfVars['confnum'];

        $this->_aViewData['version'] = oxTiramizoo_setup::VERSION;

        $this->_aViewData['aRetailLocations'] = oxtiramizooretaillocation::getAll();

        return 'oxTiramizoo_settings.tpl';
    }

    public function getPaymentsList()
    {
        $oxPaymentList = new Payment_List();
        //added 4.3.2
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
        $aPayments  = oxConfig::getParameter( "payment" );

        //assign payments for all shipping methods
        $aTiramizooSoxIds = array('Tiramizoo', 'TiramizooEvening', 'TiramizooSelectTime');

        $oDb = oxDb::getDb();

        foreach ( $aPayments as $sPaymentId => $isAssigned) 
        {
            foreach ( $aTiramizooSoxIds as $soxId) 
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
    }





    /**
    * Saves shop configuration variables
    *
    * @return null
    */
    public function saveConfVars()
    {
        $oxTiramizooConfig = oxTiramizooConfig::getInstance();

        $aConfBools = oxConfig::getParameter( "confbools" );
        $aConfStrs  = oxConfig::getParameter( "confstrs" );
        $aConfArrs  = oxConfig::getParameter( "confarrs" );
        $aConfAarrs = oxConfig::getParameter( "confaarrs" );
        $aConfNums  = oxConfig::getParameter( "confnum" );

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

        if ( is_array( $aConfNums ) ) {
          foreach ( $aConfNums as $sVarName => $aVarVal ) {
            $oxTiramizooConfig->saveShopConfVar( "num", $sVarName, $aVarVal );
          }
        }
    }
  
    /**
     * Set active on/off in tiramizoo delivery and delivery set
     */
    public function saveEnableShippingMethod()
    {
        $aConfStrs = oxConfig::getParameter( "confstrs" );
        $isTiramizooImmediateEnable = intval($aConfStrs['oxTiramizoo_enable_immediate'] == 'on');
        $isTiramizooEveningEnable = intval($aConfStrs['oxTiramizoo_enable_evening'] == 'on');
        $isTiramizooSelectTimeEnable = intval($aConfStrs['oxTiramizoo_enable_select_time'] == 'on');

        $errors = $this->validateEnable();

        if (($isTiramizooImmediateEnable || $isTiramizooEveningEnable || $isTiramizooSelectTimeEnable) && count($errors)) {
            $isTiramizooImmediateEnable = 0;
            $isTiramizooEveningEnable = 0;
            $isTiramizooSelectTimeEnable = 0;

            oxSession::setVar('oxTiramizoo_settings_errors', $errors);
            $this->getConfig()->saveShopConfVar( "str", 'oxTiramizoo_enable_immediate', 0);
            $this->getConfig()->saveShopConfVar( "str", 'oxTiramizoo_enable_evening', 0);
            $this->getConfig()->saveShopConfVar( "str", 'oxTiramizoo_enable_select_time', 0);
        }

        $enableEveningErrors = $this->validateEveningDelivery();

        if (count($enableEveningErrors)) {
            $this->getConfig()->saveShopConfVar( "str", 'oxTiramizoo_enable_evening', 0);
            $errors = array_merge($errors, $enableEveningErrors);
            oxSession::setVar('oxTiramizoo_settings_errors', $errors);
        }

        $sql = "UPDATE oxdelivery
                    SET OXACTIVE = " . $isTiramizooImmediateEnable . "
                    WHERE OXID = 'Tiramizoo';";

        oxDb::getDb()->Execute($sql);

        $sql = "UPDATE oxdeliveryset
                    SET OXACTIVE = " . $isTiramizooImmediateEnable . "
                    WHERE OXID = 'Tiramizoo';";

        oxDb::getDb()->Execute($sql);

        $sql = "UPDATE oxdelivery
                    SET OXACTIVE = " . $isTiramizooEveningEnable . "
                    WHERE OXID = 'TiramizooEvening';";

        oxDb::getDb()->Execute($sql);

        $sql = "UPDATE oxdeliveryset
                    SET OXACTIVE = " . $isTiramizooEveningEnable . "
                    WHERE OXID = 'TiramizooEvening';";

        oxDb::getDb()->Execute($sql);


        $sql = "UPDATE oxdelivery
                    SET OXACTIVE = " . $isTiramizooSelectTimeEnable . "
                    WHERE OXID = 'TiramizooSelectTime';";

        oxDb::getDb()->Execute($sql);

        $sql = "UPDATE oxdeliveryset
                    SET OXACTIVE = " . $isTiramizooSelectTimeEnable . "
                    WHERE OXID = 'TiramizooSelectTime';";

        oxDb::getDb()->Execute($sql);

    }

    /**
     * Saves main user parameters.
     *
     * @return mixed
     */
    public function synchronize()
    {
        // synchronizing config params

        $aApiKeys = oxtiramizooretaillocation::getAll(); 


        foreach ($aApiKeys as $oTiramizooRetailLocation) 
        {
            oxTiramizooConfig::getInstance()->synchronizeAll( $oTiramizooRetailLocation->getApiToken() );
        }

        // clear cache 
        // oxUtils::getInstance()->rebuildCache();
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
        $this->saveConfVars();
        $this->saveEnableShippingMethod();       
        $this->assignPaymentsToTiramizoo();
 
        // clear cache 
        //oxUtils::getInstance()->rebuildCache();
    
        return 'oxtiramizoo_settings';
    }


    public function addNewLocation()
    {

        $sApiToken = trim(oxConfig::getParameter('api_token'));
        $oTiramizooRetailLocation = oxNew('oxtiramizooretaillocation');

        
        if ($sOxid = $oTiramizooRetailLocation->getOxidByApiToken( $sApiToken )) 
        {
            $oTiramizooRetailLocation->load( $sOxid );            
        }

        //@ToDo: change this
        $oTiramizooRetailLocation->oxtiramizooretaillocation__oxname = new oxField(date('Y-m-d H:i:s'));
        $oTiramizooRetailLocation->oxtiramizooretaillocation__oxapitoken = new oxField( $sApiToken );

        $oTiramizooRetailLocation->save();

        try
        {
            oxTiramizooApi::getApiInstance( $sApiToken )->getRemoteConfiguration();

        } catch (oxTiramizoo_ApiException $e) {
            $oTiramizooRetailLocation->delete();

            return 'oxtiramizoo_settings';
        }

        oxTiramizooConfig::getInstance()->synchronizeAll( $sApiToken );
    }

    public function removeLocation()
    {
        $sApiToken = oxConfig::getParameter('api_token');
        $oTiramizooRetailLocation = oxNew('oxtiramizooretaillocation');
        if ($oTiramizooRetailLocation = oxtiramizooretaillocation::findOneByFilters( array('oxapitoken' => $sApiToken) )) 
        {
            $oTiramizooRetailLocation->delete();            
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
        $aConfStrs = oxConfig::getParameter( "confstrs" );
        $aPickupHours = oxConfig::getParameter( "oxTiramizoo_shop_pickup_hour" );
        $aPayments = oxConfig::getParameter( "payment" );

        $errors = array();

        if (!trim($aConfStrs['oxTiramizoo_api_url'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_api_url_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_api_token'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_api_token_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_shop_url'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_shop_url_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_shop_address'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_shop_address_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_shop_city'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_shop_city_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_shop_postal_code'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_shop_postal_code_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_shop_contact_name'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_shop_contact_name_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_shop_phone_number'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_shop_phone_number_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_shop_email_address'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_shop_email_address_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }


        $paymentsAreValid = 0;
        foreach ($aPayments as $paymentName => $paymentIsEnable) 
        {
            if ($paymentIsEnable) {
               $paymentsAreValid = 1;
            }
        }

        if (!$paymentsAreValid) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_payments_required_error', oxLang::getInstance()->getBaseLanguage(), true);
        }

        return $errors;
    }


    /**
     * Validate if enable
     *
     * @return array
     */
    public function validateEveningDelivery()
    {
        $aConfStrs = oxConfig::getParameter( "confstrs" );
        $isTiramizooEveningEnable = intval($aConfStrs['oxTiramizoo_enable_evening'] == 'on');

        $errors = array();

        if ($isTiramizooEveningEnable && !trim($aConfStrs['oxTiramizoo_evening_window'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_not_select_evening_error', oxLang::getInstance()->getBaseLanguage(), true);
        }

        return $errors;
    }

}