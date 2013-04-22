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
            return oxLang::getInstance()->translateString('oxTiramizoo_Today', oxLang::getInstance()->getBaseLanguage(), false) . ' ' . $this->getDeliveryHoursFormated($this->_aData);
        } else if ($this->isTomorrow()){
            return oxLang::getInstance()->translateString('oxTiramizoo_Tomorrow', oxLang::getInstance()->getBaseLanguage(), false) . ' ' .  $this->getDeliveryHoursFormated($this->_aData);
        } else {
            return oxUtilsDate::getInstance()->formatDBDate( date('Y-m-d', strtotime($this->_aData['delivery']['from'])) ) . ' ' . $this->getDeliveryHoursFormated($this->_aData);
        }

		return $this->_aData['delivery']['from'] . '-' . $this->_aData['delivery']['to'];
	}

    public function getDeliveryHoursFormated()
    {
        return date('H:i', strtotime($this->_aData['delivery']['from'])) . ' - ' . date('H:i', strtotime($this->_aData['delivery']['to']));
    }

    public function isValid($sDate = null)
    {
        $sDueDate = $sDate ? $sDate : strtotime('now');

        if ($iMinutes = $this->_aData['cut_off']) {
            $sDueDate = strtotime('+' . $iMinutes . ' minutes');
        }

        if ((strtotime($this->_aData['pickup']['from']) >= $sDueDate) && (strtotime($this->_aData['delivery']['from']) >= $sDueDate)) {
            return true;
        }

        return false;
    }

    public function isToday()
    {
        $sToday = date('Y-m-d');
        $sPickupFromDay = date('Y-m-d', strtotime($this->_aData['pickup']['from']));
        $sPickupToDay = date('Y-m-d', strtotime($this->_aData['pickup']['to']));
        $sDeliveryFromDay = date('Y-m-d', strtotime($this->_aData['delivery']['from']));
        $sDeliveryToDay = date('Y-m-d', strtotime($this->_aData['delivery']['to']));

        return ($sToday == $sPickupFromDay) && 
               ($sToday == $sPickupToDay) && 
               ($sToday == $sDeliveryFromDay) && 
               ($sToday == $sDeliveryToDay);
    }

    public function isTomorrow()
    {
        $sTomorrow = strtotime('+1 days', date('Y-m-d'));
        $sPickupFromDay = date('Y-m-d', strtotime($this->_aData['pickup']['from']));
        $sPickupToDay = date('Y-m-d', strtotime($this->_aData['pickup']['to']));
        $sDeliveryFromDay = date('Y-m-d', strtotime($this->_aData['delivery']['from']));
        $sDeliveryToDay = date('Y-m-d', strtotime($this->_aData['delivery']['to']));

        return ($sTomorrow == $sPickupFromDay) && 
               ($sTomorrow == $sPickupToDay) && 
               ($sTomorrow == $sDeliveryFromDay) && 
               ($sTomorrow == $sDeliveryToDay);
    }

    public function hasHours($aHours)
    {
        $sPickupFromHours = date('H:i', strtotime($this->_aData['pickup']['from']));
        $sPickupToHours = date('H:i', strtotime($this->_aData['pickup']['to']));
        $sDeliveryFromHours = date('H:i', strtotime($this->_aData['delivery']['from']));
        $sDeliveryToHours = date('H:i', strtotime($this->_aData['delivery']['to']));

        $sPickupFromPresetHours = $aHours['pickup_after'];
        $sPickupToPresetHours = $aHours['pickup_before'];
        $sDeliveryFromPresetHours = $aHours['delivery_after'];
        $sDeliveryToPresetHours = $aHours['delivery_before'];

		return ($sPickupFromPresetHours == $sPickupFromHours) && 
               ($sPickupToPresetHours == $sPickupToHours) && 
               ($sDeliveryFromPresetHours == $sDeliveryFromHours) && 
               ($sDeliveryToPresetHours == $sDeliveryToHours);
    }

}