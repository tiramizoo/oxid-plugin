<?php 

class oxTiramizoo_DeliveryPrice
{
	public function calculateDeliveryPrice($oTiramizooDeliverySet, $oUser, $oBasket, $oDeliveryPrice)
	{
		// Create Your own delivery price calculation and set the price
		// e.g. The delivery price is depended on time window delivery type and tiramizoo's account prices
		
		/*
			$oRetailLocation = $oTiramizooDeliverySet->getRetailLocation();

			$iStandardPrice = intval($oRetailLocation->getConfVar('standard_price')) / 100;
			$iExpressPrice = intval($oRetailLocation->getConfVar('express_price')) / 100;

			if ($iWeekendSurchargePercent = intval($oRetailLocation->getConfVar('weekend_surcharge_percent'))) {
				$iStandardWeekendPrice = $iStandardPrice * ((100 + $iWeekendSurchargePercent) / 100);
				$iExpressWeekendPrice = $iExpressPrice * ((100 + $iWeekendSurchargePercent) / 100);
			}

			$oTimeWindow = $oTiramizooDeliverySet->getSelectedTimeWindow();

			if (($oTimeWindow->getDeliveryType() == 'standard') && isset($iStandardPrice)) {
				$iPrice = $iStandardPrice;
			} else if (($oTimeWindow->getDeliveryType() == 'express') && isset($iExpressPrice)) {
				$iPrice = $iExpressPrice;
			} else if (($oTimeWindow->getDeliveryType() == 'standard_weekend') && isset($iStandardWeekendPrice)) {
				$iPrice = $iStandardWeekendPrice;
			} else if (($oTimeWindow->getDeliveryType() == 'express_weekend') && isset($iExpressWeekendPrice)) {
				$iPrice = $iExpressWeekendPrice;
			}

			if (isset($iPrice)) {
				$oDeliveryPrice->setPrice($iPrice);
			}

		*/
		
		return $oDeliveryPrice;
	}
}