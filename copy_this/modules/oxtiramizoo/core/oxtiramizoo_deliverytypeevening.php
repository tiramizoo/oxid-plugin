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
 * Tiramizoo Delivery Evening class
 *
 * @extends oxTiramizoo_DeliveryType
 * @package oxTiramizoo
 */
class oxTiramizoo_DeliveryTypeEvening extends oxTiramizoo_DeliveryType
{
	/**
	 * Delivery type name
	 *
	 * @var string
	 */
	protected $_sType = 'evening';

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
		} else{
    		$oTimeWindow = $this->getEveningTimeWindow();

    		if ($oTimeWindow === null) {
    			$blReturn = false;
    		}
        }

		return $blReturn;
	}

	/**
	 * Check if preset hours exists
	 *
	 * @return bool
	 */
	public function hasPresetHours()
	{
    	$aPresetHours = $this->getPresetHours();

    	return is_array($aPresetHours) && count($aPresetHours);
	}

	/**
	 * Returns time window preset
	 *
	 * @return string
	 */
	public function getPresetHours()
	{
    	return $this->getRetailLocation()->getConfVar('time_window_preset');
	}

	/**
	 * Retrieve first next Time window if is today and preset hours are equal
	 *
	 * @return oxTiramizoo_TimeWindow|null
	 */
    public function getEveningTimeWindow()
    {
        $oReturn = null;

		if ($this->hasPresetHours()) {
        	$aPresetHours = $this->getPresetHours();

            foreach ($this->_aTimeWindows as $aTimeWindow)
            {
            	$oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindow);

                if ($oTimeWindow->isValid()
                    && $oTimeWindow->hasHours($aPresetHours)
                    && $oTimeWindow->isToday()
                ) {
                    $oReturn = $oTimeWindow;
                    break;
                }
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
		return $this->getEveningTimeWindow();
	}

	/**
	 * Checks if time window is in available time windows
	 *
	 * @return bool
	 */
	public function hasTimeWindow($sTimeWindow)
	{
		$oEveningTimeWindow = $this->getEveningTimeWindow();

        return $oEveningTimeWindow->getHash() == $sTimeWindow;
	}
}
