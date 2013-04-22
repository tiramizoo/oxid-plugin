<?php


/**
 * Tiramizoo Order view. Extends to proccess Tiramizoo delivery
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_order extends oxTiramizoo_order_parent
{

    public function init()
    {
        parent::init();

        $oTiramizooDeliverySet = oxRegistry::get('oxTiramizoo_DeliverySet');
        $oTiramizooDeliverySet->init($this->getUser(), oxNew( 'oxorder' )->getDelAddressInfo());

        //redirect to payment if tiramizoo is not available
        if (!$this->getTiramizooDeliverySet()->isTiramizooAvailable() && (oxSession::getVar( 'sShipSet') == oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID)) {
            oxUtils::getInstance()->redirect( oxConfig::getInstance()->getShopHomeURL() .'cl=payment', true, 302 );
        }

    }

    public function getTiramizooDeliverySet()
    {
        return oxRegistry::get('oxTiramizoo_DeliverySet');
    }

    /**
     * Executes parent::render(), pass variable to template to check
     * if tiramizoo module is running now
     * 
     * @return string template file
     */
    public function render()
    {

        if ( oxSession::getVar('sShipSet') == oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID ) {

            $oTiramizooDeliverySet = $this->getTiramizooDeliverySet();

            $oTiramizooWindow = $oTiramizooDeliverySet->getSelectedTimeWindow();

            $this->_aViewData['sFormattedTiramizooTimeWindow'] = $oTiramizooWindow->getFormattedDeliveryTimeWindow();
        }

        return parent::render();
    }

    /**
     * Execute save order with tiramizoo shipping
     * 
     * @return string
     */
    public function execute()
    {
        try {
            return parent::execute();
        } catch ( oxTiramizoo_SendOrderException $oEx ) {
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
        }
    }
}