<?php

class oxTiramizoo_DeliveryTypeImmediate extends oxTiramizoo_DeliveryType
{
	protected $_sType = 'immediate';

	public function isAvailable()
	{
		if (parent::isAvailable() == false) {
			return false;
		}

		if (!$this->getRetailLocation()->getConfVar('immediate_time_window_enabled')) {
			return false;
		}

		$oTimeWindow = $this->getImmediateTimeWindow();

		if ($oTimeWindow === null) {
			return false;
		}

		return true;
	}

    public function getImmediateTimeWindow()
    {
        foreach ($this->_aTimeWindows as $aTimeWindow) 
        {
        	$oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindow);

            if ($oTimeWindow->isValid() && $oTimeWindow->isToday()) {
                return $oTimeWindow;
            }
        }

        return null;
    }

	public function getDefaultTimeWindow()
	{
		return $this->getImmediateTimeWindow();
	}

	public function hasTimeWindow($sTimeWindow)
	{
		$oImmediateTimeWindow = $this->getImmediateTimeWindow();

        return $oImmediateTimeWindow->getHash() == $sTimeWindow;
	}	
}