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
 * Admin order extended tiramizoo parameters manager.
 * Collects extended order tiramizoo properties ( such as
 * weight, dimensions, enable tiramizoo delivery).
 * Admin Menu: Administer Orders -> Orders -> Tiramizoo.
 *
 * @extend oxAdminDetails
 * @package oxTiramizoo
 */
class oxTiramizoo_Order_Tab extends oxAdminDetails
{
    /**
     * @var oxOrder
     */
    protected $_oOrder = null;

    /**
     * @var oxTiramizoo_OrderExtended
     */
    protected $_oTiramizooOrderExtended = null;

    /**
     * @var array
     */
    protected $_aTiramizooWebhookResponse = null;

    /**
     * @var array
     */
    protected $_aTiramizooResponse = null;

    /**
     * @var array
     */
    protected $_aTiramizooRequest = null;

    /**
     * Getter method, returns oxTiramizoo_CategoryExtended object
     *
     * @return oxTiramizoo_CategoryExtended
     */
    public function getOrder()
    {
        return $this->_oOrder;
    }

    /**
     * Getter method, returns oxTiramizoo_CategoryExtended object
     *
     * @return oxTiramizoo_OrderExtended
     */
    public function getTiramizooOrderExtended()
    {
        return $this->_oTiramizooOrderExtended;
    }

    /**
     * Getter method, returns decoded webhook response
     *
     * @return array
     */
    public function getTiramizooWebhookResponse()
    {
        return $this->_aTiramizooWebhookResponse;
    }

    /**
     * Getter method, returns decoded response data
     *
     * @return array
     */
    public function getTiramizooResponse()
    {
        return $this->_aTiramizooResponse;
    }

    /**
     * Getter method, returns decoded request data
     *
     * @return array
     */
    public function getTiramizooRequest()
    {
        return $this->_aTiramizooRequest;
    }


    /**
     * Executes parent method parent::render(), creates oxorder and
     * oxuserpayment objects, passes data to Smarty engine and returns
     * name of template file "order_main.tpl".
     *
     * @extend oxAdminDetails::render
     *
     * @return string
     */
    public function render()
    {
        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            parent::render();
        }
        // @codeCoverageIgnoreEnd

        $soxId = $this->getConfig()->getRequestParameter( "oxid");

        if ( $soxId != "-1" && isset( $soxId ) ) {
            $oOrder = oxNew( "oxorder" );

            $this->_oOrder = $oOrder;

            $this->_oTiramizooOrderExtended = oxNew('oxTiramizoo_OrderExtended');
            $this->_oTiramizooOrderExtended->load($this->_oTiramizooOrderExtended->getIdByOrderId($soxId));

            $this->_aTiramizooWebhookResponse = unserialize(
                base64_decode($this->_oTiramizooOrderExtended
                                        ->oxtiramizooorderextended__tiramizoo_webhook_response
                                        ->value
                )
            );

            $this->_aTiramizooResponse = unserialize(
                base64_decode($this->_oTiramizooOrderExtended
                                        ->oxtiramizooorderextended__tiramizoo_response
                                        ->value
                )
            );

            $this->_aTiramizooRequest = unserialize(
                base64_decode($this->_oTiramizooOrderExtended
                                        ->oxtiramizooorderextended__tiramizoo_request_data
                                        ->value
                )
            );
        }

        return "oxTiramizoo_order_tab.tpl";
    }
}
