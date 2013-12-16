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
 * Tiramizoo Delivery Special class
 *
 * @extends oxTiramizoo_DeliveryType
 * @package oxTiramizoo
 */
class oxTiramizoo_DeliveryTypeSpecial extends oxTiramizoo_DeliveryType
{
	/**
	 * Delivery type name
	 *
	 * @var string
	 */
	protected $_sType = 'special';

    /**
     * After this hour this delivery type should display next possible day's time windows
     *
     * @var string
     */
    protected $_sLimitHour = '14:00';

    /**
     * Array of time windows available in this delivery type, use for lazy load
     *
     * @var mixed
     */
    protected $_aAvailableTimeWindows = null;

	/**
	 * Checks if is available depends on current time
     *
     * @extend oxTiramizoo_DeliveryType::isAvailable()
     *
	 * @return bool
	 */
	public function isAvailable()
	{
        $blReturn = true;

		if (parent::isAvailable() == false) {
            $blReturn = false;
        } elseif (!oxRegistry::get('oxTiramizoo_Config')->getShopConfVar('oxTiramizoo_delivery_special')) {
            $blReturn = false;
        } else{
            $oTimeWindow = $this->getSpecialTimeWindow();

            if ($oTimeWindow === null) {
                $blReturn = false;
            }
        }

		return $blReturn;
	}


	/**
	 * Retrieve first next Time window if is today and preset hours are equal
	 *
	 * @return oxTiramizoo_TimeWindow|null
	 */
    public function getSpecialTimeWindow()
    {
        $oReturn = null;

        foreach ($this->getAvailableTimeWindows() as $oTimeWindow)
        {
            if ($oTimeWindow->isValid()) {
                $oReturn = $oTimeWindow;
                break;
            }
        }

        return $oReturn;
    }

	/**
	 * Returns default (evening) time window
	 *
	 * @return oxTiramizoo_TimeWindow|null
	 */
	public function getDefaultTimeWindow()
	{
		return $this->getSpecialTimeWindow();
	}

	/**
	 * Checks if time window is in available time windows
	 *
	 * @return bool
	 */
	public function hasTimeWindow($sTimeWindow)
	{
        $oReturn = false;

        foreach ($this->getAvailableTimeWindows() as $oTimeWindow)
        {
            if ($oTimeWindow->getHash() == $sTimeWindow) {
                $oReturn = true;
                break;
            }
        }

        return $oReturn;
	}

    /**
     * Parse time windows from service areas and valid each item
     *
     * @return mixed available time windows
     */
    public function getAvailableTimeWindows()
    {
        if ($this->_aAvailableTimeWindows === null) {

            $this->_aAvailableTimeWindows = array();
            $oNextDayDate = null;

            if (is_array($this->_aTimeWindows)) {
                foreach ($this->_aTimeWindows as $aTimeWindow)
                {
                    $oTimeWindow = oxNew('oxTiramizoo_TimeWindow', $aTimeWindow);

                    //if not specified date and it is not today
                    if ($oNextDayDate === null && !$oTimeWindow->isToday()) {
                        //next date have to later than current data
                        if ($oTimeWindow->getPickupFromDate()->isLaterThan(oxNew('oxTiramizoo_Date'))) {
                            $sNextDayDate = $oTimeWindow->getPickupFromDate()->get('Y-m-d');
                            $oNextDayDate = oxNew('oxTiramizoo_Date', $sNextDayDate);
                        }
                    }

                    if ($oTimeWindow->isValid()
                        && ($oTimeWindow->getDeliveryType() == 'standard')
                        && $this->isTimeWindowValidForCurrentTime($oTimeWindow, $oNextDayDate)
                    ) {
                        $this->_aAvailableTimeWindows[] = $oTimeWindow;
                    }
                }
            }
        }

        return $this->_aAvailableTimeWindows;
    }

    /**
     * Check if time window is valid for delivery type special
     *
     * @param  oxTiramizoo_TimeWindow $oTimeWindow  time window to validate
     * @param  mixed                 $oNextDayDate next possible date
     * @return boolean time window is valid
     */
    public function isTimeWindowValidForCurrentTime(oxTiramizoo_TimeWindow $oTimeWindow, $oNextDayDate)
    {
        $blReturn = false;

        $oCurrentDate = oxNew('oxTiramizoo_Date');
        $sLimitDate = oxTiramizoo_Date::date('Y-m-d') . ' ' . $this->_sLimitHour . ':00';
        $oLimitTodayOnlyDate = oxNew('oxTiramizoo_Date', $sLimitDate);

        if ($oTimeWindow->isToday()) {
            $blReturn = true;
        }

        if ($oNextDayDate && $oCurrentDate->isLaterOrEqualTo($oLimitTodayOnlyDate)) {
            if ($oTimeWindow->isOnDate($oNextDayDate)) {
                $blReturn = true;
            }
        }

        return $blReturn;
    }
}
