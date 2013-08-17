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
 * Class used to recalculate delivery price
 * based on some factors (Tiramizoo, User, Basket)
 *
 * @package oxTiramizoo
 */
class oxTiramizoo_DeliveryPrice
{
    /**
     * Initialize 
     * 
     * @param oxTiramizoo_DeliverySet $oTiramizooDeliverySet  Delivery set objevt
     * @param oxUser  $oUser User object
     * @param oxBasket  $oBasket Basket object
     * @param oxDeliveryPrice $oDeliveryPrice Delivery price object
     *
     * @return oxDeliveryPrice
     */
	public function calculateDeliveryPrice($oTiramizooDeliverySet, $oUser, $oBasket, $oDeliveryPrice)
	{
		// Create Your own delivery price calculation and set the price
		// e.g. The delivery price is depended on time window delivery type and tiramizoo's account prices
		
		/*
			$oDeliveryPrice->setPrice(10);
		*/
		
		return $oDeliveryPrice;
	}
}
