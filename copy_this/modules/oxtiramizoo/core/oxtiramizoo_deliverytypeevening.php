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

	public function hasPresetHours()
	{
    	$aPresetHours = $this->getPresetHours();
    	
    	return is_array($aPresetHours) && count($aPresetHours);
	}

	public function getPresetHours()
	{
    	return $this->getRetailLocation()->getConfVar('time_window_preset');
	}

    public function getEveningTimeWindow()
    {
		if (!$this->hasPresetHours()) {
			return null;
		}

    	$aPresetHours = $this->getPresetHours();

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