<?php

if ( !class_exists('oxTiramizooConfig') ) {
    require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_config.php';
}

if ( !class_exists('oxTiramizooSetup') ) {
    require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_setup.php';
}

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
    $oxConfig  = $this->getConfig();
    parent::render();

    $this->_aViewData['oPaymentsList'] = $this->getPaymentsList();
    $this->_aViewData['aPickupHours'] = $this->getPickupHoursAsArray();

    $sCurrentAdminShop = $oxConfig->getShopId();

    //create an empty array
    $this->_aViewData['aAvailablePickupHours'] = array();

    $minimumPickupHour = oxTiramizooConfig::getInstance()->getConfigParam('minimumDeliveryHour');
    $minimumDeliveryLengthInMinutes = (strtotime(oxTiramizooConfig::getInstance()->getConfigParam('minimumDeliveryWindowLength')) - strtotime('00:00')) / 60;

    $maximumPickupHour = date('H:i', strtotime('-' . $minimumDeliveryLengthInMinutes . 'minutes', strtotime(oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour'))));

    $currentPickupHour = strtotime($minimumPickupHour);

    $this->_aViewData['aAvailablePickupHours'][] = date('H:i', $currentPickupHour);

    $selectedDeliveryPickupHourStepInMinutes = (strtotime(oxTiramizooConfig::getInstance()->getConfigParam('selectedDeliveryPickupHourStep')) - strtotime('00:00')) / 60;

    // do if maximum pickup hour is not occured
    while($currentPickupHour < strtotime($maximumPickupHour)) {
      $currentPickupHour = strtotime('+' . $selectedDeliveryPickupHourStepInMinutes . 'minutes', $currentPickupHour);
      $this->_aViewData['aAvailablePickupHours'][] = date('H:i', $currentPickupHour);
    }

    if (count(oxSession::getVar('oxTiramizoo_settings_errors'))) {
        $this->_aViewData['aErrors'] = oxSession::getVar('oxTiramizoo_settings_errors');
        oxSession::setVar('oxTiramizoo_settings_errors', null);
    }

    $this->_aViewData['version'] = oxTiramizoo_setup::VERSION;

    return 'oxTiramizoo_settings.tpl';
  }
  



  public function getPickupHoursAsArray()
  {
        $aPickupHours = array();

        for ($i = 1; $i <= 6; $i++)
        {
            $aPickupHours[] = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_shop_pickup_hour_' . $i);
        }

        return $aPickupHours;
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
        $aTiramizooSoxIds = array('Tiramizoo', 'TiramizooEvening');

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
        $oxConfig = $this->getConfig();

        $aConfBools = oxConfig::getParameter( "confbools" );
        $aConfStrs  = oxConfig::getParameter( "confstrs" );
        $aConfArrs  = oxConfig::getParameter( "confarrs" );
        $aConfAarrs = oxConfig::getParameter( "confaarrs" );

        $aPickupHoursVars = oxConfig::getParameter( "oxTiramizoo_shop_pickup_hour" );

        $iPickupHourIterator = 1;
        $aPickupKeys = array();

        foreach ($aPickupHoursVars as $sPickupHour) 
        {
            $aPickupKeys[] = intval(str_replace(':', '', $sPickupHour));
        }

        $aPickupHours = array_combine($aPickupKeys, $aPickupHoursVars);
        ksort($aPickupHours);

        foreach ($aPickupHours as $sPickupHour) 
        {
            if (trim($sPickupHour)) {
                $aConfStrs['oxTiramizoo_shop_pickup_hour_' . $iPickupHourIterator++] = trim($sPickupHour);
            }
        }

        for ($iPickupHourIterator; $iPickupHourIterator <= 6; $iPickupHourIterator++)
        {
            $aConfStrs['oxTiramizoo_shop_pickup_hour_' . $iPickupHourIterator++] = '';
        }

        if ( is_array( $aConfBools ) ) {
          foreach ( $aConfBools as $sVarName => $sVarVal ) {
              $oxConfig->saveShopConfVar( "bool", $sVarName, $sVarVal);
          }
        }

        if ( is_array( $aConfStrs ) ) {
          foreach ( $aConfStrs as $sVarName => $sVarVal ) {
            $oxConfig->saveShopConfVar( "str", $sVarName, $sVarVal);
          }
        }

        if ( is_array( $aConfArrs ) ) {
          foreach ( $aConfArrs as $sVarName => $aVarVal ) {
            if ( !is_array( $aVarVal ) ) {
              $aVarVal = $this->_multilineToArray($aVarVal);
            }
            $oxConfig->saveShopConfVar("arr", $sVarName, $aVarVal);
          }
        }

        if ( is_array( $aConfAarrs ) ) {
          foreach ( $aConfAarrs as $sVarName => $aVarVal ) {
            $oxConfig->saveShopConfVar( "aarr", $sVarName, $this->_multilineToAarray( $aVarVal ));
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

        $errors = $this->validateEnable();


        if (($isTiramizooImmediateEnable || $isTiramizooEveningEnable) && count($errors)) {
            $isTiramizooImmediateEnable = 0;
            $isTiramizooEveningEnable = 0;

            oxSession::setVar('oxTiramizoo_settings_errors', $errors);
            $this->getConfig()->saveShopConfVar( "str", 'oxTiramizoo_enable_immediate', 0);
            $this->getConfig()->saveShopConfVar( "str", 'oxTiramizoo_enable_evening', 0);
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
        $this->assignPaymentsToTiramizoo();

        $this->saveEnableShippingMethod();       

        // clear cache 
        oxUtils::getInstance()->rebuildCache();
    
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

        if (!trim($aConfStrs['oxTiramizoo_order_pickup_offset'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_order_to_pickup_offset_label', oxLang::getInstance()->getBaseLanguage(), true) . ' ' . oxLang::getInstance()->translateString('oxTiramizoo_is_required', oxLang::getInstance()->getBaseLanguage(), true);
        }

        if (!count($aPickupHours)) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_pickup_hours_required_error', oxLang::getInstance()->getBaseLanguage(), true);

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
        echo $isTiramizooEveningEnable = intval($aConfStrs['oxTiramizoo_enable_evening'] == 'on');

        $errors = array();

        if ($isTiramizooEveningEnable && !trim($aConfStrs['oxTiramizoo_evening_window'])) {
            $errors[] = oxLang::getInstance()->translateString('oxTiramizoo_settings_not_select_evening_error', oxLang::getInstance()->getBaseLanguage(), true);
        }

        return $errors;
    }

}