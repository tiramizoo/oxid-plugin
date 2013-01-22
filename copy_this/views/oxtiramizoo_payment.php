<?php

if ( !class_exists('oxTiramizooHelper') ) {
    require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_helper.php';
}

/**
 * Tiramizoo Payment view. Extends to proccess Tiramizoo delivery
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_Payment extends oxTiramizoo_Payment_parent
{
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

        if (!oxTiramizooHelper::getInstance()->isTiramizooAvailable()) {
            unset($this->_aAllSets['Tiramizoo']);
            unset($this->_aAllSets['TiramizooEvening']);
            $unsetTiramizoo = true;
        } else {
            if (!oxTiramizooHelper::getInstance()->isTiramizooImmediateAvailable()) {
                unset($this->_aAllSets['Tiramizoo']);
                $unsetTiramizoo = true;
            }
            if (!oxTiramizooHelper::getInstance()->isTiramizooEveningAvailable()) {
                unset($this->_aAllSets['TiramizooEvening']);
                $unsetTiramizoo = true;
            }
        }

        if ($unsetTiramizoo && in_array(oxSession::getVar( 'sShipSet'), array('Tiramizoo', 'TiramizooEvening'))) {

            $sNewShippingMethod = key($this->_aAllSets);

            oxSession::setVar( 'sShipSet', $sNewShippingMethod );
            $oBasket = $this->getSession()->getBasket();

            $oBasket->setShipping( $sNewShippingMethod );
            $oBasket->onUpdate();

            oxUtils::getInstance()->redirect( oxConfig::getInstance()->getShopHomeURL() .'cl=payment', true, 302 );
        }

        return $this->_aAllSets;
    }

    /**
     * Changes shipping set to chosen one. If selected Tiramizoo set session variable
     * sTiramizooTimeWindow
     *
     * @return null
     */
    public function changeshipping()
    {
        parent::changeshipping();

        // set the session variable with selected delivery time
        if (oxConfig::getParameter( 'sTiramizooTimeWindow' )) {
            oxSession::setVar( 'sTiramizooTimeWindow', oxConfig::getParameter( 'sTiramizooTimeWindow' ) );
        }
    }

    /**
     * Executes parent::render(), check if tiramizoo is available if yes pass vars to templates
     *
     * @return string
     */
    public function render()
    {
        $oxTiramizooHelper = oxTiramizooHelper::getInstance();

        if ($oxTiramizooHelper->isTiramizooAvailable()) {
            $oBasket = $this->getSession()->getBasket();
            


            //$this->_aViewData['isTiramizooCurrentShippingMethod'] = $oBasket->getShippingId() == 'Tiramizoo';
            //$this->_aViewData['aTiramizooAvailableDeliveryHours'] = $oxTiramizooHelper->getAvailableDeliveryHours();
            //$this->_aViewData['sTiramizooSelectedDeliveryTime'] = $oxTiramizooHelper->getSelectedTimeWindow();
            
            if (($oBasket->getShippingId() == 'Tiramizoo') && $oxTiramizooHelper->isTiramizooImmediateAvailable()) {
                $dateTime = $oxTiramizooHelper->getNextAvailableDate( date('Y-m-d H:i:s'));
                oxSession::setVar( 'sTiramizooTimeWindow',  $dateTime);
            } else if (($oBasket->getShippingId() == 'TiramizooEvening') && $oxTiramizooHelper->isTiramizooEveningAvailable()) {
                oxSession::setVar( 'sTiramizooTimeWindow',  date('Y-m-d') . ' ' . $this->getConfig()->getShopConfVar('oxTiramizoo_evening_window'));
            }
            
        }

        return parent::render();
    }
}