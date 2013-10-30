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
 * Tiramizoo Delivery Immediate class
 *
 * @extends oxTiramizoo_DeliveryType
 * @package oxTiramizoo
 */
class oxTiramizoo_DeliveryTypeImmediate extends oxTiramizoo_DeliveryType
{
	/**
	 * Delivery type name
	 *
	 * @var string
	 */
	protected $_sType = 'immediate';

	/**
	 * Checks if is available depends on configuration and current time
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
		} elseif (!$this->getRetailLocation()->getConfVar('immediate_time_window_enabled')) {
			$blReturn = false;
		} else {
    		$oTimeWindow = $this->getImmediateTimeWindow();

    		if ($oTimeWindow === null) {
    			$blReturn = false;
    		}
        }

		return $blReturn;
	}

	/**
	 * Retrieve first next Time window if is today
	 *
	 * @return oxTiramizoo_TimeWindow|null
	 */
    public function getImmediateTimeWindow()
    {
        $oReturn = null;

        foreach ($this->_aTimeWindows as $aTimeWindow)
        {
        	$oTimeWindow = oxNew('oxTiramizoo_TimeWindow', $aTimeWindow);

            if ($oTimeWindow->isValid() && $oTimeWindow->isToday()) {
                $oReturn = $oTimeWindow;
                break;
            }
        }

        return $oReturn;
    }

	/**
	 * Returns default (immediate) time window
	 *
	 * @return oxTiramizoo_TimeWindow|null
	 */
	public function getDefaultTimeWindow()
	{
		return $this->getImmediateTimeWindow();
	}

	/**
	 * Checks if time window is in available time windows
	 *
	 * @return bool
	 */
	public function hasTimeWindow($sTimeWindow)
	{
		$oImmediateTimeWindow = $this->getImmediateTimeWindow();

        return $oImmediateTimeWindow->getHash() == $sTimeWindow;
	}
}
