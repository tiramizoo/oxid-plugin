<?php

require_once  dirname(__FILE__) . '/../modules/oxtiramizoo/core/oxtiramizoo_setup.php';

class oxTiramizoo_settings extends Shop_Config
{
  const OX_TIRAMIZOO_MODULE_NAME = 'oxTiramizoo';
  
  /**
   * Current Version String.
   * @var string
   */
  protected $_sVersion = 'oxTiramizoo Module v0.1';
  /**
   * Current class template.
   * @var string
   */
  protected $_sThisTemplate = 'oxTiramizoo_settings.tpl';
  
  protected $oxTiramizoo_is_module_installed = null;

  public function init()
  {
      $oxTiramizooConfig = $this->getConfig();
      if(!(int)$oxTiramizooConfig->getConfigParam('oxTiramizoo_is_installed'))
      {
          $oxTiramizooSetup = new oxTiramizoo_setup();
          $oxTiramizooSetup->install();
      }

      return parent::Init();
  }

  /**
   * Executes parent method parent::render() and returns name of template
   * file "payengine.tpl".
   *
   * @return string
   */
  public function render()
  {
    $myConfig  = $this->getConfig();
    parent::render();

    $this->_aViewData['oPaymentsList'] = $this->getPaymentsList();

    $sCurrentAdminShop = $myConfig->getShopId();

    $this->_aViewData['aAvailablePickupHours'] = array('9:00', '9:30', '10:00', '10:30', '11:00', '11:30', 
                                                       '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', 
                                                       '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', 
                                                       '18:00', '18:30');

    return $this->_sThisTemplate;
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
    $myConfig = $this->getConfig();

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

    for ($iPickupHourIterator; $iPickupHourIterator <=3; $iPickupHourIterator++)
    {
        $aConfStrs['oxTiramizoo_shop_pickup_hour_' . $iPickupHourIterator++] = '';
    }

    if ( is_array( $aConfBools ) ) {
      foreach ( $aConfBools as $sVarName => $sVarVal ) {
          $myConfig->saveShopConfVar( "bool", $sVarName, $sVarVal);
      }
    }

    if ( is_array( $aConfStrs ) ) {
      foreach ( $aConfStrs as $sVarName => $sVarVal ) {
        $myConfig->saveShopConfVar( "str", $sVarName, $sVarVal);
      }
    }

    if ( is_array( $aConfArrs ) ) {
      foreach ( $aConfArrs as $sVarName => $aVarVal ) {
        // home country multiple selectlist feature
        if ( !is_array( $aVarVal ) ) {
          $aVarVal = $this->_multilineToArray($aVarVal);
        }
        $myConfig->saveShopConfVar("arr", $sVarName, $aVarVal);
      }
    }

    if ( is_array( $aConfAarrs ) ) {
      foreach ( $aConfAarrs as $sVarName => $aVarVal ) {
        $myConfig->saveShopConfVar( "aarr", $sVarName, $this->_multilineToAarray( $aVarVal ));
      }
    }
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

        
        // clear cache 
        oxUtils::getInstance()->rebuildCache();
    
        return 'oxtiramizoo_settings';
    }
  

}