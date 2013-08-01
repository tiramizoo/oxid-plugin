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
 * Order manager. Extends checkout proccess with Tiramizoo delivery
 *
 * @extend order
 * @package oxTiramizoo
 */
class oxTiramizoo_order extends oxTiramizoo_order_parent
{
    /**
     * Executes parent::init(), initialize oxTiramizooDeliverySet
     * object and redirect back to the payment step
     * if Tiramizoo Delivery is not available
     * 
     * @extend order::init()
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

        $oOrder = oxNew( 'oxorder' );

        $oTiramizooDeliverySet = $this->getTiramizooDeliverySet();
        $oTiramizooDeliverySet->init($this->getUser(), $oOrder->getDelAddressInfo());

        //redirect to payment if tiramizoo is not available
        if (!$this->getTiramizooDeliverySet()->isTiramizooAvailable() && ($this->getSession()->getVariable( 'sShipSet') == oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID)) {
            oxRegistry::get('oxUtils')->redirect( $this->getConfig()->getShopHomeURL() .'cl=payment', true, 302 );
        }
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
     * Executes parent::render(), pass variable to template to check
     * if tiramizoo module is running now
     * 
     * @extend order::render()
     *
     * @return string template file
     */
    public function render()
    {
        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            parent::render();
        }
        // @codeCoverageIgnoreEnd

        if ( $this->getSession()->getVariable('sShipSet') == oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID ) {
            $oTiramizooDeliverySet = $this->getTiramizooDeliverySet();
            $oTiramizooWindow = $oTiramizooDeliverySet->getSelectedTimeWindow();
            $this->_aViewData['sFormattedTiramizooTimeWindow'] = $oTiramizooWindow->getFormattedDeliveryTimeWindow();
        }

        return $this->_sThisTemplate;
    }

    /**
     * Executes parent::execute(), show errors 
     * if exception throwed
     * 
     * @extend order::execute()
     *
     * @return string
     */
    public function execute()
    {
        try {
            return parent::execute();
        } catch ( oxTiramizoo_SendOrderException $oEx ) {
            oxRegistry::get('oxUtilsView')->addErrorToDisplay( $oEx );
        }
    }
}
