<?php


/**
 * Extends oxbasket class. Overrides method to calculate price
 */
class oxTiramizoo_oxbasket extends oxTiramizoo_oxbasket_parent
{
    protected function _calcDeliveryCost()
    {
        $oDeliveryPrice = parent::_calcDeliveryCost();

        if (($this->getShippingId() == oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID)) {

            $oTiramizooDeliverySet = oxRegistry::get('oxTiramizoo_DeliverySet');
            $oTiramizooDeliverySet->init($this->getUser(), oxNew( 'oxorder' )->getDelAddressInfo());

            if ($oTiramizooDeliverySet->isTiramizooAvailable()) {
                $oTiramizooDeliveryPrice = oxNew('oxTiramizoo_DeliveryPrice'); 
                
                $oDeliveryPrice = $oTiramizooDeliveryPrice->calculateDeliveryPrice($oTiramizooDeliverySet, $this->getUser(), $this, $oDeliveryPrice);

                return $oDeliveryPrice;
            }
        }
        
        return $oDeliveryPrice;
    }
}