<?php

abstract class oxTiramizoo_DeliveryType
{
	protected $_sType = null;
	protected $_aTimeWindows = null;
	protected $_oRetailLocation = null;

	public function __construct($aTimeWindows, $oRetailLocation)
	{
		$this->_aTimeWindows = $aTimeWindows;
		$this->_oRetailLocation = $oRetailLocation;
	}

	public function getRetailLocation()
	{
		return $this->_oRetailLocation;
	}


	public function getType()
	{
		return $this->_sType;
	}

	public function getName()
	{
		return oxLang::getInstance()->translateString('oxTiramizoo_delivery_type_' . $this->_sType .'_name');
	}

	public function getTimeWindows()
	{
		return $this->_aTimeWindows;
	}

	public function getDefaultTimeWindow()
	{
		if (count($this->_aTimeWindows)) {
			$aKeys = array_keys($this->_aTimeWindows);
			return (new oxTiramizoo_TimeWindow($this->_aTimeWindows[array_shift($aKeys)]));
		}

		return null;
	}

	public function isAvailable()
	{
		$aPickupContact = $this->getRetailLocation()->getConfVar('pickup_contact');

		if (!isset($aPickupContact['address_line_1']) || !$aPickupContact['address_line_1']) {
			return false;
		}

		if (!isset($aPickupContact['city']) || !$aPickupContact['city']) {
			return false;
		}

		if (!isset($aPickupContact['postal_code']) || !$aPickupContact['postal_code']) {
			return false;
		}

		if (!isset($aPickupContact['country_code']) || !$aPickupContact['country_code']) {
			return false;
		}

		if (!isset($aPickupContact['name']) || !$aPickupContact['name']) {
			return false;
		}

		if (!isset($aPickupContact['phone_number']) || !$aPickupContact['phone_number']) {
			return false;
		}

		return true;
	}

	public function hasTimeWindow($aTimeWindow)
	{
		return false;
	}


}