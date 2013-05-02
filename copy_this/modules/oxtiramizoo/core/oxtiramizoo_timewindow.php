<?php

class oxTiramizoo_TimeWindow
{
	protected $_aData = array();

	public function __construct($aData)
	{
		$this->_aData = $aData;
	}

	public function getDeliveryFrom()
	{
		return $this->_aData['delivery']['from'];
	}

	public function getDeliveryTo()
	{
		return $this->_aData['delivery']['to'];
	}

	public function getPickupFrom()
	{
		return $this->_aData['pickup']['from'];
	}

	public function getPickupTo()
	{
		return $this->_aData['pickup']['to'];
	}

    public function getDeliveryFromDate()
    {
        return new oxTiramizoo_Date($this->_aData['delivery']['from']);
    }

    public function getDeliveryToDate()
    {
        return new oxTiramizoo_Date($this->_aData['delivery']['to']);
    }

    public function getPickupFromDate()
    {
        return new oxTiramizoo_Date($this->_aData['pickup']['from']);
    }

    public function getPickupToDate()
    {
        return new oxTiramizoo_Date($this->_aData['pickup']['to']);
    }

	public function getCutOff()
	{
		return $this->_aData['cut_off'];
	}

	public function getDeliveryType()
	{
		return $this->_aData['delivery_type'];
	}

	public function getAsArray()
	{
		return $this->_aData;
	}

	public function getHash()
	{
		return md5(serialize($this->_aData));
	}

	public function __toString()
	{
		return $this->getHash();
	}

	public function getFormattedDeliveryTimeWindow()
	{
        if ($this->isToday()) {
            return oxRegistry::getLang()->translateString('oxTiramizoo_Today', oxRegistry::getLang()->getBaseLanguage(), false) . ' ' . $this->getDeliveryHoursFormated($this->_aData);
        } else if ($this->isTomorrow()){
            return oxRegistry::getLang()->translateString('oxTiramizoo_Tomorrow', oxRegistry::getLang()->getBaseLanguage(), false) . ' ' .  $this->getDeliveryHoursFormated($this->_aData);
        } else {
            return  $this->getDeliveryFromDate()->get(oxRegistry::getLang()->translateString('oxTiramizoo_time_window_date_format', oxRegistry::getLang()->getBaseLanguage(), false)) . ' ' . $this->getDeliveryHoursFormated($this->_aData);
        }

	}

    public function getDeliveryHoursFormated()
    {
        return $this->getDeliveryFromDate()->get('H:i') . ' - ' . $this->getDeliveryToDate()->get('H:i');
    }

    public function isValid()
    {
        $oDueDate = new oxTiramizoo_Date();

        if ($iMinutes = $this->_aData['cut_off']) {
            $oDueDate->modify('+' . $iMinutes . ' minutes');
        }

        if ($this->getPickupFromDate()->isLaterThan($oDueDate) && $this->getDeliveryFromDate()->isLaterThan($oDueDate)) {
            return true;
        }

        return false;
    }

    public function isToday()
    {
        return $this->getPickupFromDate()->isToday() && 
               $this->getPickupToDate()->isToday() && 
               $this->getDeliveryFromDate()->isToday() && 
               $this->getDeliveryToDate()->isToday();
    }

    public function isTomorrow()
    {
        return $this->getPickupFromDate()->isTomorrow() && 
               $this->getPickupToDate()->isTomorrow() && 
               $this->getDeliveryFromDate()->isTomorrow() && 
               $this->getDeliveryToDate()->isTomorrow();
    }

    public function hasHours($aHours)
    {
        return $this->getPickupFromDate()->isOnTime($aHours['pickup_after']) && 
               $this->getPickupToDate()->isOnTime($aHours['pickup_before']) && 
               $this->getDeliveryFromDate()->isOnTime($aHours['delivery_after']) && 
               $this->getDeliveryToDate()->isOnTime($aHours['delivery_before']);
    }

}