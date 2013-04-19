<?php


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
        $resetShippingMethod = false;

        if (!oxTiramizooHelper::getInstance()->isTiramizooAvailable()) {
            unset($this->_aAllSets['Tiramizoo']);
            $unsetTiramizoo = true;
        } else {
            if (!oxTiramizooHelper::getInstance()->isTiramizooImmediateAvailable()) {
                unset($this->_aAllSets['Tiramizoo']);
                if (oxSession::getVar( 'sShipSet') == 'Tiramizoo') {
                    $resetShippingMethod = true;
                }
            }
            if (!oxTiramizooHelper::getInstance()->isTiramizooEveningAvailable()) {
                unset($this->_aAllSets['TiramizooEvening']);
                if (oxSession::getVar( 'sShipSet') == 'TiramizooEvening') {
                    $resetShippingMethod = true;
                } 
            }
        }

        if ($unsetTiramizoo && in_array(oxSession::getVar( 'sShipSet'), array('Tiramizoo')) || $resetShippingMethod)  {

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

        $oxTiramizooHelper->setUser($this->getUser());

        $sDeliveryPostalCode = $this->getUser()->oxuser__oxzip->value;
        $oOrder = oxNew( 'oxorder' );
        $oDeliveryAddress = $oOrder->getDelAddressInfo();

        if ($oDeliveryAddress) {
            $sDeliveryPostalCode = $oDeliveryAddress->oxaddress__oxzip->value;
        }

        $oxTiramizooHelper->setDeliveryPostalCode($sDeliveryPostalCode);



        $this->_aViewData['sCurrentShipSet'] = oxSession::getVar('sShipSet');

        if ($oxTiramizooHelper->isTiramizooAvailable()) {

            $oBasket = $this->getSession()->getBasket();

            //$this->_aViewData['isTiramizooCurrentShippingMethod'] = $oBasket->getShippingId() == 'Tiramizoo';
            //$this->_aViewData['aTiramizooAvailableDeliveryHours'] = $oxTiramizooHelper->getAvailableDeliveryHours();
            //$this->_aViewData['sTiramizooSelectedDeliveryTime'] = $oxTiramizooHelper->getSelectedTimeWindow();
            $this->_aViewData['isTiramizooSelectTimeShippingMethod'] = $oBasket->getShippingId() == 'TiramizooSelectTime';

            $this->_aViewData['sTiramizooTimeWindow'] = oxSession::getVar('sTiramizooTimeWindow');
            $this->_aViewData['sTiramizooSelectedDate'] = $sTiramizooSelectedDate = oxSession::getVar('sTiramizooTimeWindow') ? date('Y-m-d', strtotime(oxSession::getVar('sTiramizooTimeWindow'))) : null;
            

            if (($oBasket->getShippingId() == 'Tiramizoo') && $oxTiramizooHelper->isTiramizooImmediateAvailable()) {
               // $dateTime = $oxTiramizooHelper->getNextAvailableDate( date('Y-m-d H:i:s'));
                //@ToDo: change it test only
                $dateTime = date('Y-m-d H:i:s');
                oxSession::setVar( 'sTiramizooTimeWindow',  $dateTime);
            } else if (($oBasket->getShippingId() == 'TiramizooEvening') && $oxTiramizooHelper->isTiramizooEveningAvailable()) {
                oxSession::setVar( 'sTiramizooTimeWindow',  date('Y-m-d') . ' ' . $this->getConfig()->getShopConfVar('oxTiramizoo_evening_window'));
            }
        }

        return parent::render();
    }
}