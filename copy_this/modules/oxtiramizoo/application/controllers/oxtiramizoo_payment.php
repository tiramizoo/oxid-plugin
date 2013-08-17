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
 * Payment manager. Extends with Tiramizoo delivery.
 * 
 * @extend payment
 * @package oxTiramizoo
 */
class oxTiramizoo_Payment extends oxTiramizoo_Payment_parent
{
    /**
     * Executes parent::init(), initialize 
     * oxTiramizooDeliverySet object 
     * 
     * @extend payment::init()
     *
     * @return null
     */
    public function init()
    {
        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            parent::init();
        }
        // @codeCoverageIgnoreEnd

        $oTiramizooDeliverySet = $this->getTiramizooDeliverySet();
        $oTiramizooDeliverySet->init($this->getUser(), oxNew( 'oxorder' )->getDelAddressInfo());
    }

    /**
     * Getting current oxTiramizoo_DeliverySet object
     * from registry
     * 
     * @return oxTiramizoo_DeliverySet
     */
    public function getTiramizooDeliverySet()
    {
        return oxRegistry::get('oxTiramizoo_DeliverySet');
    }

    /**
     * Get all delivery sets, remove Tiramizoo delivery set if basket 
     * couldn't be delivered by Tiramizoo. Executes parent::getAllSets()
     * 
     * @extend payment::getAllSets()
     *
     * @return array
     */
    public function getAllSets()
    {
        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            $this->_aAllSets = parent::getAllSets();
        }
        // @codeCoverageIgnoreEnd

        $unsetTiramizoo = false;

        if (!$this->getTiramizooDeliverySet()->isTiramizooAvailable()) {
            unset($this->_aAllSets[oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID]);
            $unsetTiramizoo = true;
        }

        // if tiramizoo was selected and is not available set the first delivery set
        if ($unsetTiramizoo && ($this->getSession()->getVariable( 'sShipSet') == oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID)) {

            $sNewShippingMethod = key($this->_aAllSets);

            $this->getSession()->setVariable('sShipSet', $sNewShippingMethod);
            $oBasket = $this->getSession()->getBasket();

            $oBasket->setShipping($sNewShippingMethod);
            $oBasket->onUpdate();

            oxRegistry::get('oxUtils')->redirect($this->getConfig()->getShopHomeURL() .'cl=payment', true, 302 );
        }

        return $this->_aAllSets;
    }

    /**
     * Changes shipping set to chosen one. If selected Tiramizoo set session variable
     * aTiramizooTimeWindow. Executes parent::changeshipping()
     * 
     * @extend payment::changeshipping()
     *
     * @return null
     */
    public function changeshipping()
    {
        parent::changeshipping();

        $oConfig = $this->getConfig();

        if ($oConfig->getRequestParameter( 'sTiramizooDeliveryType' )) {
            
            $sTiramizooTimeWindow = $oConfig->getRequestParameter('sTiramizooTimeWindow');

            try {
                $oTiramizooDeliverySet = $this->getTiramizooDeliverySet();
                $oTiramizooDeliverySet->setTiramizooDeliveryType($oConfig->getRequestParameter('sTiramizooDeliveryType'));
                
                $oTiramizooDeliveryTypeObject = $oTiramizooDeliverySet->getTiramizooDeliveryTypeObject();

                if ($sTiramizooTimeWindow && $oTiramizooDeliveryTypeObject->hasTimeWindow($sTiramizooTimeWindow)) {
                    $oTiramizooDeliverySet->setSelectedTimeWindow($sTiramizooTimeWindow);      
                } else if ($oDefaultTimeWindow = $oTiramizooDeliveryTypeObject->getDefaultTimeWindow()) {
                    $oTiramizooDeliverySet->setSelectedTimeWindow($oDefaultTimeWindow->getHash());      
                }
            } catch (oxTiramizoo_InvalidTiramizooDeliveryTypeException $oEx) {
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            } catch (oxTiramizoo_InvalidTimeWindowException $oEx) {
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            }
        }
    }

    /**
     * Executes parent::render(), check if tiramizoo is available 
     * if yes passes variables to template
     * 
     * @extend payment::render()
     *
     * @return string template file name
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

        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            return parent::render();
        } else {
            return $this->_sThisTemplate;
        }
        // @codeCoverageIgnoreEnd
    }
}