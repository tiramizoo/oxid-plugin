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
 * Admin tiramizoo API parameters manager.
 * Collects and Updates API connection properties
 * and Tiramizoo deliveryconfiguration.
 * Admin Menu: Tiramizoo -> Settings.
 *
 * @extend Shop_Config
 * @package oxTiramizoo
 */
class oxTiramizoo_settings extends Shop_Config
{
    /**
     * Executes parent::init(), run installation/migration
     * proccess.
     *
     * @extend oxAdminView::init()
     *
     * @return null
     */
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
     * Executes parent method parent::render() and returns
     * name of template "oxTiramizoo_settings.tpl"
     *
     * @extend Shop_Config::render()
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

        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        $this->_aViewData['oPaymentsList'] = $this->getPaymentsList();

        $sCurrentAdminShop = $oTiramizooConfig->getShopId();

        $aShopConfVars = $oTiramizooConfig->getTiramizooConfVars();

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


    /**
    * Retrieve all defined payments
    *
    * @return array
    */
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

            $oPayment->loadInLang(oxRegistry::getLang()->getTplLanguage(), $oPayment->getId());

            $aPaymentList[$oPayment->oxpayments__oxid->value]['desc'] = $oPayment->oxpayments__oxdesc->value;

            $sID = $oDb->getOne("select oxid from oxobject2payment where oxpaymentid = " . $oDb->quote( $oPayment->oxpayments__oxid->value ) . "  and oxobjectid = ".$oDb->quote( $soxId )." and oxtype = 'oxdelset'", false, false);

            $aPaymentList[$oPayment->oxpayments__oxid->value]['checked'] = isset($sID) && $sID;
        }

        return $aPaymentList;
    }

    /**
    * Retrieve all defined payments
    *
    * @return array
    */
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
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        $aConfBools = $this->getConfig()->getRequestParameter("confbools");
        $aConfStrs  = $this->getConfig()->getRequestParameter("confstrs");
        $aConfInts  = $this->getConfig()->getRequestParameter("confints");

        if ( is_array( $aConfBools ) ) {
          foreach ( $aConfBools as $sVarName => $sVarVal ) {
              $oTiramizooConfig->saveShopConfVar( "bool", $sVarName, $sVarVal);
          }
        }

        if ( is_array( $aConfStrs ) ) {
          foreach ( $aConfStrs as $sVarName => $sVarVal ) {
            $oTiramizooConfig->saveShopConfVar( "str", $sVarName, $sVarVal);
          }
        }

        if ( is_array( $aConfInts ) ) {
          foreach ( $aConfInts as $sVarName => $aVarVal ) {
            $oTiramizooConfig->saveShopConfVar( "int", $sVarName, $aVarVal );
          }
        }
    }

    /**
    * Saves shop configuration variables
    *
    * @return bool
    */
    public function tiramizooApiUrlHasChanged()
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        $aConfStrs = $this->getConfig()->getRequestParameter( "confstrs" );

        if ($aConfStrs['oxTiramizoo_api_url'] != $oTiramizooConfig->getShopConfVar('oxTiramizoo_api_url')) {
            return true;
        }

        return false;
    }

    /**
     * Set active on/off in tiramizoo delivery and delivery set
     *
     * @return bool
     */
    public function saveEnableShippingMethod()
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        $isTiramizooEnable = 1;

        $errors = $this->validateEnable();

        if (count($errors)) {
            $isTiramizooEnable = 0;

            foreach ($errors as $error)
            {
                $this->addMessage('error', $error);
            }
        } else {
            $this->addMessage('success', oxRegistry::getLang()->translateString('oxTiramizoo_settings_saved_success', oxRegistry::getLang()->getTplLanguage(), true));
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
     * Synchronize all retail locations.
     *
     * @return mixed
     */
    public function synchronize()
    {
        try
        {
            $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

            $oRetailLocationList = oxNew('oxTiramizoo_RetailLocationList');
            $oRetailLocationList->loadAll();

            foreach ($oRetailLocationList->getArray() as $oRetailLocation)
            {
                $oTiramizooConfig->synchronizeAll( $oRetailLocation->getApiToken() );
            }

            $this->addMessage('success', oxRegistry::getLang()->translateString('oxTiramizoo_synchronize_success', oxRegistry::getLang()->getTplLanguage(), true));

        } catch (oxTiramizoo_ApiException $e) {
            $this->addMessage('error', oxRegistry::getLang()->translateString('oxTiramizoo_synchronize_error', oxRegistry::getLang()->getTplLanguage(), true));
        }
    }


    /**
     * Saves main user parameters and redirect back to tiramizoo settings.
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
                    $remote = oxTiramizoo_Api::getApiInstance( $oRetailLocation->getApiToken() )->getRemoteConfiguration();
                } catch (oxTiramizoo_ApiException $e) {
                    $oRetailLocation->delete();
                }
            }
        } else {
            $this->saveConfVars();
        }

        $this->saveEnableShippingMethod();
        $this->assignPaymentsToTiramizoo();
    }

    /**
     * Add new retail location and redirect back to tiramizoo settings.
     *
     * @return mixed
     */
    public function addNewLocation()
    {
        $sApiToken = trim($this->getConfig()->getRequestParameter('api_token'));
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');
        $oTiramizooRetailLocation = oxNew('oxTiramizoo_RetailLocation');

        if ($sOxid = $oTiramizooRetailLocation->getIdByApiToken( $sApiToken ))
        {
            $oTiramizooRetailLocation->load( $sOxid );
        }

        $oTiramizooRetailLocation->oxtiramizooretaillocation__oxname = new oxField(oxTiramizoo_Date::date());
        $oTiramizooRetailLocation->oxtiramizooretaillocation__oxapitoken = new oxField( $sApiToken );
        $oTiramizooRetailLocation->oxtiramizooretaillocation__oxshopid = new oxField( $oTiramizooConfig->getShopId() );

        $oTiramizooRetailLocation->save();

        try
        {
            oxTiramizoo_Api::getApiInstance( $sApiToken )->getRemoteConfiguration();
            $oTiramizooConfig->synchronizeAll( $sApiToken );

            $this->addMessage('success', oxRegistry::getLang()->translateString('oxTiramizoo_add_location_success', oxRegistry::getLang()->getTplLanguage(), true));

        } catch (oxTiramizoo_ApiException $e) {
            $oTiramizooRetailLocation->delete();

            $this->addMessage('error', oxRegistry::getLang()->translateString('oxTiramizoo_add_location_error', oxRegistry::getLang()->getTplLanguage(), true));
        }
    }

    /**
     * Remove retail location and redirect back to tiramizoo settings.
     *
     * @return mixed
     */
    public function removeLocation()
    {
        $sApiToken = trim($this->getConfig()->getRequestParameter('api_token'));

        $oRetailLocation = oxNew('oxTiramizoo_RetailLocation');
        $sOxid = $oRetailLocation->getIdByApiToken($sApiToken);

        if ($sOxid)
        {
            $oRetailLocation->load($sOxid);
            $oRetailLocation->delete();

            $this->addMessage('success', oxRegistry::getLang()->translateString('oxTiramizoo_remove_location_success', oxRegistry::getLang()->getTplLanguage(), true));
        }
    }

    /**
     * Validate configuration enabling
     *
     * @return array
     */
    public function validateEnable()
    {
        $aConfStrs = $this->getConfig()->getRequestParameter( "confstrs" );
        $aPayments = $this->getConfig()->getRequestParameter( "payment" );

        $errors = array();

        if (!trim($aConfStrs['oxTiramizoo_api_url'])) {
            $errors[] = oxRegistry::getLang()->translateString('oxTiramizoo_settings_api_url_label', oxRegistry::getLang()->getTplLanguage(), true) . ' ' . oxRegistry::getLang()->translateString('oxTiramizoo_is_required', oxRegistry::getLang()->getTplLanguage(), true);
        }

        if (!trim($aConfStrs['oxTiramizoo_shop_url'])) {
            $errors[] = oxRegistry::getLang()->translateString('oxTiramizoo_settings_shop_url_label', oxRegistry::getLang()->getTplLanguage(), true) . ' ' . oxRegistry::getLang()->translateString('oxTiramizoo_is_required', oxRegistry::getLang()->getTplLanguage(), true);
        }

        $paymentsAreValid = 0;
        foreach ($aPayments as $paymentName => $paymentIsEnable)
        {
            if ($paymentIsEnable) {
               $paymentsAreValid = 1;
            }
        }

        if (!$paymentsAreValid) {
            $errors[] = oxRegistry::getLang()->translateString('oxTiramizoo_payments_required_error', oxRegistry::getLang()->getTplLanguage(), true);
        }

        return $errors;
    }

    /**
     * Adds message to view
     *
     * @param $sType string type
     * @param $sDescription string message
     *
     * @return void
     */
    protected function addMessage($sType, $sDescription)
    {
        if (!isset($this->_aViewData['aMessages'])) {
            $this->_aViewData['aMessages'] = array();
        }

        $this->_aViewData['aMessages'][] = array('type' => $sType, 'description' => $sDescription);
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        return isset($this->_aViewData['aMessages']) ? $this->_aViewData['aMessages'] : array();
    }
}
