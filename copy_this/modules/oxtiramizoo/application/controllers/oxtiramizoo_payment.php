<?php


/**
 * Tiramizoo Payment view. Extends to proccess Tiramizoo delivery
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_Payment extends oxTiramizoo_Payment_parent
{

    public function init()
    {
        parent::init();
       
        $oTiramizooDeliverySet = oxRegistry::get('oxTiramizoo_DeliverySet');
        $oTiramizooDeliverySet->init($this->getUser(), oxNew( 'oxorder' )->getDelAddressInfo());
    }

    public function getTiramizooDeliverySet()
    {
        return oxRegistry::get('oxTiramizoo_DeliverySet');
    }

    /**
     * Get all delivery sets, remove Tiramizoo delivery set if basket couldn't be delivered
     * by Tiramizoo
     * 
     * @return array
     */
    public function getAllSets()
    {
        $this->_aAllSets = parent::getAllSets();

        $unsetTiramizoo = false;

        if (!$this->getTiramizooDeliverySet()->isTiramizooAvailable()) {
            unset($this->_aAllSets[oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID]);
            $unsetTiramizoo = true;
        }

        // if tiramizoo was selected and is not available set the first delivery set
        if ($unsetTiramizoo && (oxSession::getVar( 'sShipSet') == oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID)) {

            $sNewShippingMethod = key($this->_aAllSets);

            oxSession::setVar('sShipSet', $sNewShippingMethod);
            $oBasket = $this->getSession()->getBasket();

            $oBasket->setShipping($sNewShippingMethod);
            $oBasket->onUpdate();

            oxUtils::getInstance()->redirect( oxConfig::getInstance()->getShopHomeURL() .'cl=payment', true, 302 );
        }

        return $this->_aAllSets;
    }

    /**
     * Changes shipping set to chosen one. If selected Tiramizoo set session variable
     * aTiramizooTimeWindow
     *
     * @return null
     */
    public function changeshipping()
    {
        parent::changeshipping();

        if (oxConfig::getParameter( 'sTiramizooDeliveryType' )) {
            try {
                $oTiramizooDeliverySet = $this->getTiramizooDeliverySet();
                $oTiramizooDeliverySet->setTiramizooDeliveryType(oxConfig::getParameter('sTiramizooDeliveryType'));
                
                if (oxConfig::getParameter('sTiramizooTimeWindow') && $oTiramizooDeliverySet->getTiramizooDeliveryTypeObject()->hasTimeWindow(oxConfig::getParameter('sTiramizooTimeWindow'))) {
                    $oTiramizooDeliverySet->setSelectedTimeWindow(oxConfig::getParameter('sTiramizooTimeWindow'));      
                } else if ($oDefaultTimeWindow = $oTiramizooDeliverySet->getTiramizooDeliveryTypeObject()->getDefaultTimeWindow()) {
                    $oTiramizooDeliverySet->setSelectedTimeWindow($oDefaultTimeWindow->getHash());      
                }

            } catch (oxTiramizoo_InvalidTiramizooDeliveryTypeException $oEx) {
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            } catch (oxTiramizoo_InvalidTimeWindowException $oEx) {
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            }
        }
    }

    /**
     * Executes parent::render(), check if tiramizoo is available if yes pass vars to templates
     *
     * @return string
     */
    public function render()
    {
        $oTiramizooDeliverySet = $this->getTiramizooDeliverySet();
        $oBasket = $this->getSession()->getBasket();

        $this->_aViewData['sCurrentShipSet'] = $oBasket->getShippingId();

        if ($oTiramizooDeliverySet->isTiramizooAvailable()) {
            $this->_aViewData['sTiramizooDeliveryType'] = $oTiramizooDeliverySet->getTiramizooDeliveryType();
            $this->_aViewData['sSelectedTimeWindow'] = $oTiramizooDeliverySet->getSelectedTimeWindow() ? $oTiramizooDeliverySet->getSelectedTimeWindow()->getHash() : '';
            $this->_aViewData['aAvailableDeliveryTypes'] = $oTiramizooDeliverySet->getAvailableDeliveryTypes();
        }


        return parent::render();
    }
}