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
 * Time window class hold data.
 *
 * @package oxTiramizoo
 */
class oxTiramizoo_TimeWindow
{
    /**
     * Array where store time window data
     *
     * @var array
     */
	protected $_aData = array();

    /**
     * Class constructor
     *
     * @return null
     */
	public function __construct($aData)
	{
		$this->_aData = $aData;
	}

    /**
     * Returns delivery from
     *
     * @return string
     */
	public function getDeliveryFrom()
	{
		return $this->_aData['delivery']['from'];
	}

    /**
     * Returns delivery to
     *
     * @return string
     */
	public function getDeliveryTo()
	{
		return $this->_aData['delivery']['to'];
	}

    /**
     * Returns pickup from
     *
     * @return string
     */
	public function getPickupFrom()
	{
		return $this->_aData['pickup']['from'];
	}

    /**
     * Returns pickup to
     *
     * @return string
     */
	public function getPickupTo()
	{
		return $this->_aData['pickup']['to'];
	}

    /**
     * Returns delivery from as object
     *
     * @return oxTiramizoo_Date
     */
    public function getDeliveryFromDate()
    {
        return new oxTiramizoo_Date($this->_aData['delivery']['from']);
    }

    /**
     * Returns delivery to as object
     *
     * @return oxTiramizoo_Date
     */
    public function getDeliveryToDate()
    {
        return new oxTiramizoo_Date($this->_aData['delivery']['to']);
    }

    /**
     * Returns pickup from as object
     *
     * @return oxTiramizoo_Date
     */
    public function getPickupFromDate()
    {
        return new oxTiramizoo_Date($this->_aData['pickup']['from']);
    }

    /**
     * Returns pickup to as object
     *
     * @return oxTiramizoo_Date
     */
    public function getPickupToDate()
    {
        return new oxTiramizoo_Date($this->_aData['pickup']['to']);
    }

    /**
     * Returns cut off
     *
     * @return string
     */
	public function getCutOff()
	{
		return $this->_aData['cut_off'];
	}

    /**
     * Returns delivery type
     *
     * @return string
     */
	public function getDeliveryType()
	{
		return $this->_aData['delivery_type'];
	}

    /**
     * Returns all data
     *
     * @return array
     */
	public function getAsArray()
	{
		return $this->_aData;
	}

    /**
     * Generate and retrieve hash for instance
     *
     * @return string
     */
	public function getHash()
	{
		return md5(serialize($this->_aData));
	}

    /**
     * Get hash in output context
     *
     * @return string
     */
	public function __toString()
	{
		return $this->getHash();
	}

    /**
     * Get formatted delivery time window using oxlang entries.
     *
     * @return string
     */
	public function getFormattedDeliveryTimeWindow()
	{
        $sReturn = '';
        $oLang = oxRegistry::getLang();

        $sTplLangugage = oxRegistry::getLang()->getTplLanguage();

        if ($this->isToday()) {
            $sReturn  = $oLang->translateString('oxTiramizoo_Today', $sTplLangugage, false);
            $sReturn .= ' ' . $this->getDeliveryHoursFormated($this->_aData);
        } else if ($this->isTomorrow()){
            $sReturn  = $oLang->translateString('oxTiramizoo_Tomorrow', $sTplLangugage, false);
            $sReturn .= ' ' .  $this->getDeliveryHoursFormated($this->_aData);
        } else {
            $sFormat = $oLang->translateString('oxTiramizoo_time_window_date_format', $sTplLangugage, false);
            $sReturn  = $this->getDeliveryFromDate()->get($sFormat);
            $sReturn .= ' ' . $this->getDeliveryHoursFormated($this->_aData);
        }

        return $sReturn;
	}

    /**
     * Get formatted delivery time window hours.
     *
     * @return string
     */
    public function getDeliveryHoursFormated()
    {
        return $this->getDeliveryFromDate()->get('H:i') . ' - ' . $this->getDeliveryToDate()->get('H:i');
    }

    /**
     * Check if time window is valid according to current datetime.
     *
     * @return bool
     */
    public function isValid()
    {
        $blReturn = false;
        $oDueDate = oxnew('oxTiramizoo_Date');

        if ($iMinutes = $this->_aData['cut_off']) {
            $oDueDate->modify('+' . $iMinutes . ' minutes');
        }

        if ($this->getPickupFromDate()->isLaterThan($oDueDate)
            && $this->getDeliveryFromDate()->isLaterThan($oDueDate)
        ) {
            $blReturn = true;
        }

        return $blReturn;
    }

    /**
     * Check if time window is today according to current datetime.
     *
     * @return bool
     */
    public function isToday()
    {
        return  $this->getPickupFromDate()->isToday()
                && $this->getPickupToDate()->isToday()
                && $this->getDeliveryFromDate()->isToday()
                && $this->getDeliveryToDate()->isToday();
    }

    /**
     * Check if time window is tommorow according to current datetime.
     *
     * @return bool
     */
    public function isTomorrow()
    {
        return  $this->getPickupFromDate()->isTomorrow()
                && $this->getPickupToDate()->isTomorrow()
                && $this->getDeliveryFromDate()->isTomorrow()
                && $this->getDeliveryToDate()->isTomorrow();
    }

    /**
     * Check if time window has specified hours.
     *
     * @param array $aHours array of hours
     * @return bool
     */
    public function hasHours($aHours)
    {
        return  $this->getPickupFromDate()->isOnTime($aHours['pickup_after'])
                && $this->getPickupToDate()->isOnTime($aHours['pickup_before'])
                && $this->getDeliveryFromDate()->isOnTime($aHours['delivery_after'])
                && $this->getDeliveryToDate()->isOnTime($aHours['delivery_before']);
    }
}
