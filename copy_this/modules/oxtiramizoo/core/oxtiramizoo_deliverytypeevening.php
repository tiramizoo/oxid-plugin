<?php

class oxTiramizoo_DeliveryTypeEvening extends oxTiramizoo_DeliveryType
{
	protected $_sType = 'evening';

	public function isAvailable()
	{
		if (parent::isAvailable() == false) {
			return false;
		}

		$oTimeWindow = $this->getEveningTimeWindow();

		if ($oTimeWindow === null) {
			return false;
		}

		return true;
	}

    public function getEveningTimeWindow()
    {
    	$aPresetHours = $this->getRetailLocation()->getConfVar('time_window_preset');

		if (!$aPresetHours) {
			return null;
		}

        foreach ($this->_aTimeWindows as $aTimeWindow) 
        {
        	$oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindow);

            if ($oTimeWindow->isValid() && $oTimeWindow->hasHours($aPresetHours) && $oTimeWindow->isToday()) {
                return $oTimeWindow;
            }
        }

        return null;
    }

	public function getDefaultTimeWindow()
	{
		return $this->getEveningTimeWindow();
	}

	public function hasTimeWindow($sTimeWindow)
	{
		$oEveningTimeWindow = $this->getEveningTimeWindow();

        return $oEveningTimeWindow->getHash() == $sTimeWindow;
	}	
}