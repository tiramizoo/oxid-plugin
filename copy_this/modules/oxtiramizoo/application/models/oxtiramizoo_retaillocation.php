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
 * Tiramizoo Retail Location manager.
 * Performs creation assigning, updating, deleting and other order functions.
 *
 * @extends oxBase
 * @package oxTiramizoo
 */
class oxTiramizoo_RetailLocation extends oxBase
{
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

    /**
     * Current configuration variables
     * for retail location.
     *
     * @var array
     */
    protected $_aConfigVars = null;

    /**
     * Class constructor
     *
     * @extend oxBase::__construct()
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxtiramizooretaillocation' );
    }

    /**
     * Returns retail location id by API token
     *
     * @param string $sApiToken API token
     *
     * @return string
     */
    public function getIdByApiToken($sApiToken)
    {
	    $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
	    $sQ = "SELECT oxid
                    FROM " . $this->_sCoreTbl . "
                        WHERE OXSHOPID = '" . $this->getConfig()->getShopId() . "'
                            AND oxapitoken = " . $oDb->quote( $sApiToken );

        return $oDb->getOne($sQ);
    }

    /**
     * Repopulate config vars for current retail location.
     *
     * @return null
     */
    public function refreshConfigVars()
    {
        if ($this->_aConfigVars === null) {
            $this->_aConfigVars = array();

            $aRetailLocationConfigs = $this->getRetailLocationConfigs();

            foreach ($aRetailLocationConfigs as $oRetailLocationConfig)
            {
                $sVarName = $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname->value;
                $this->_aConfigVars[$sVarName] = $oRetailLocationConfig;
                $this->_aConfigVars[$sVarName]->getValue();
            }
        }
    }

    /**
     * Repopulate config vars for current retail location.
     *
     * @param $sConfVarName
     *
     * @return null
     */
    public function getConfVar($sConfVarName)
    {
        $oReturn = null;

        $this->refreshConfigVars();

        if (isset($this->_aConfigVars[$sConfVarName])) {
            $oReturn = $this->_aConfigVars[$sConfVarName]->getValue();
        }

        return $oReturn;
    }

    /**
     * Returns API token
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->oxtiramizooretaillocation__oxapitoken->value;
    }

    /**
     * Load and returns retail location config variables
     *
     * @return oxTiramizoo_RetailLocationConfigList
     */
    public function getRetailLocationConfigs()
    {
        $oRetailLocationConfigList = oxNew('oxTiramizoo_RetailLocationConfigList');
        $oRetailLocationConfigList->loadByRetailLocationId($this->getId());

        return $oRetailLocationConfigList;
    }

    /**
     * Save configuration variables for retail location
     *
     * @param mixed $response
     * @throws oxTiramizoo_ApiException if response status is not equal 200
     *
     * @return null
     */
    public function synchronizeConfiguration($response)
    {
        if ($response['http_status'] != 200) {
            throw new oxTiramizoo_ApiException("Can't connect to Tiramizoo API", 1);
        }

        $aResponse = $this->objectToArray($response['response']);

        foreach ($aResponse as $sConfigIndex => $sValue)
        {
            if(is_array($sValue)) {
                $sVarType = 'aarr';
            } else {
                $sVarType = 'str';
            }

            $oRetailLocationConfig = oxNew('oxTiramizoo_RetailLocationConfig');
            $oRetailLocationConfig->load($oRetailLocationConfig
                                  ->getIdByRetailLocationIdAndVarName($this->getId(), $sConfigIndex));

            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxretaillocationid = new oxField($this->getId());
            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname = new oxField($sConfigIndex);
            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvartype = new oxField($sVarType);

            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarvalue = new oxField(
                base64_encode( serialize( $sValue ) )
            );

            $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxlastsync = new oxField(oxTiramizoo_Date::date());

            $oRetailLocationConfig->save();
        }
    }

    /**
     * Deletes retail location with all configuration variables.
     *
     * @extend oxBase::delete()
     *
     * @param string $sOXID
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        foreach ($this->getRetailLocationConfigs() as $oRetailLocationConfig)
        {
            $oRetailLocationConfig->delete();
        }

        return parent::delete();
    }

    /**
     * Returns Time windows assigned to retail location.
     *
     * @param string $sOXID
     *
     * @return array
     */
    public function getAvailableTimeWindows()
    {
        if ($aTimeWindows = $this->getConfVar('time_windows')) {

            //sort by delivery from date
            foreach ($aTimeWindows as $oldKey => $aTimeWindow)
            {
                $oTimeWindow = oxNew('oxTiramizoo_TimeWindow', $aTimeWindow);

                $aTimeWindows[$oTimeWindow->getDeliveryFromDate()->getTimestamp()] = $aTimeWindow;
                unset($aTimeWindows[$oldKey]);
            }

            ksort($aTimeWindows);
        }

        return $aTimeWindows ? $aTimeWindows : array();
    }

    /**
     * Retrieve oxTiramizoo_TimeWindow from avbailable time windows if exists.
     *
     * @param string $sHash time window hash
     *
     * @return mixed
     */
    public function getTimeWindowByHash($sHash)
    {
        $oReturn = null;

        foreach ($this->getAvailableTimeWindows() as $aTimeWindow)
        {
            $oTimeWindow = oxNew('oxTiramizoo_TimeWindow', $aTimeWindow);

            if ($oTimeWindow->getHash() == $sHash) {
                $oReturn = $oTimeWindow;
            }
        }

        return $oReturn;
    }

    /**
     * Convert recursively stdClass object into an array.
     *
     * @param mixed $data array or stdClass object
     *
     * @return mixed
     */
    public function objectToArray($data)
    {
        $oReturn = $data;

        if (is_array($data) || is_object($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->objectToArray($value);
            }
            $oReturn = $result;
        }

        return $oReturn;
    }
}
