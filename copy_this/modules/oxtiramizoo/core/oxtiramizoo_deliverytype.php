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
 * Tiramizoo Delivery type class, provides all basic functions,
 * needed for describe Tiramizoo Delivery.
 *
 * @package oxTiramizoo
 */
abstract class oxTiramizoo_DeliveryType
{
	/**
	 * Delivery type name
	 *
	 * @var string
	 */
	protected $_sType = null;

	/**
	 * Array od available time windows in this type
	 *
	 * @var array
	 */
	protected $_aTimeWindows = null;

	/**
	 * Instance of Retail location object
	 *
	 * @var oxTiramizoo_RetailLocation
	 */
	protected $_oRetailLocation = null;

	/**
	 * Class constructor, assign retail location object and available time windows.
	 *
	 * @param oxTiramizoo_RetailLocation $oRetailLocation Retail Location object
	 *
	 * @return oxTiramizoo
	 */
	public function __construct($oRetailLocation)
	{
		$this->_aTimeWindows = $oRetailLocation->getAvailableTimeWindows();
		$this->_oRetailLocation = $oRetailLocation;
	}

	/**
	 * Returns Retail location object
	 *
	 * @return oxTiramizoo_RetailLocation
	 */
	public function getRetailLocation()
	{
		return $this->_oRetailLocation;
	}

	/**
	 * Returns type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->_sType;
	}

	/**
	 * Returns Tiramizoo delivery name
	 *
	 * @return string
	 */
	public function getName()
	{
		return oxRegistry::getLang()->translateString('oxTiramizoo_delivery_type_' . $this->_sType .'_name');
	}

	/**
	 * Returns available time windows
	 *
	 * @return array
	 */
	public function getTimeWindows()
	{
		return $this->_aTimeWindows;
	}

	/**
	 * Returns default time window
	 *
	 * @return oxTiramizoo_TimeWindow|null
	 */
	public function getDefaultTimeWindow()
	{
		$oReturn = null;

		if (count($this->_aTimeWindows)) {
			$aKeys = array_keys($this->_aTimeWindows);
			$oReturn = oxNew('oxTiramizoo_TimeWindow', $this->_aTimeWindows[array_shift($aKeys)]);
		}

		return $oReturn;
	}

	/**
	 * Basic checks if Tiramizoo delivery is available
	 *
	 * @return bool
	 */
	public function isAvailable()
	{
		$aPickupContact = $this->getRetailLocation()->getConfVar('pickup_contact');

		$aPickupContact = array_merge(
			array(	'address_line' => null,
					'postal_code' => null,
					'country_code' => null,
					'name' => null,
					'phone_number' => null),
			(array)$aPickupContact
		);

		$blReturn = true;

		if (!($aPickupContact['address_line'])
			|| !$aPickupContact['postal_code']
			|| !$aPickupContact['country_code']
			|| !$aPickupContact['name']
			|| !$aPickupContact['phone_number']
		) {
			$blReturn = false;
		}

		return $blReturn;
	}

	/**
	 * Checks if time window is in available time windows
	 *
	 * @return bool
	 */
	public function hasTimeWindow($aTimeWindow)
	{
		return false;
	}
}
