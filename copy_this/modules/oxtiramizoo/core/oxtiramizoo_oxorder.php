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
 * Extends oxorder class. Overrides _loadFromBasket method to allow send order to the API
 *
 * @extends oxorder
 * @package oxTiramizoo
 */
class oxTiramizoo_oxorder extends oxTiramizoo_oxorder_parent
{
    /**
     * Execute parent::_loadFromBasket(), prepare object to send order, 
     * save tiramizoo order extended information after sending order to the API.
     * 
     * @extend oxorder::_loadFromBasket()
     *
     * @param oxBasket $oBasket Shopping basket object
     *
     * @return null
     */
    protected function _loadFromBasket( oxBasket $oBasket )
    {
        parent::_loadFromBasket( $oBasket );

        if ($oBasket->getShippingId() == oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID) {

            $oTiramizooDeliverySet = oxRegistry::get('oxTiramizoo_DeliverySet');

            $oTiramizooWindow = $oTiramizooDeliverySet->getSelectedTimeWindow();

            $oRetailLocation = $oTiramizooDeliverySet->getRetailLocation();
            $oUser = $this->getUser();
            $oDeliveryAddress = $this->getDelAddressInfo();

            $oCreateOrderData = oxnew('oxTiramizoo_CreateOrderData', $oTiramizooWindow, $oBasket, $oRetailLocation);
            $oCreateOrderData->buildPickup();
            $oCreateOrderData->buildDelivery($oUser, $oDeliveryAddress);
            $oCreateOrderData->buildItems();
            $oTiramizooData = $oCreateOrderData->getCreatedTiramizooOrderDataObject();

            $tiramizooResult = $oTiramizooDeliverySet->getTiramizooApi()->sendOrder($oTiramizooData);

            if (!in_array($tiramizooResult['http_status'], array(201))) {

                $oSendOrderJob = oxNew('oxTiramizoo_SendOrderJob');
                $oSendOrderJob->setExternalId($this->getId());
                $oSendOrderJob->setParams(array('api_token' => $oTiramizooDeliverySet->getApiToken()));            
                $oSendOrderJob->save();

                if ($tiramizooResult['errno'] == oxTiramizoo_Api::CURLE_OPERATION_TIMEDOUT) {
                    $oEmail = oxNew( 'oxEmail' );

                    $oShop = $oEmail->getConfig()->getActiveShop();

                    $oEmail->setFrom( $oShop->oxshops__oxowneremail->value );
                    $oEmail->setSmtp( $oShop );
                    $oEmail->setBody(print_r($oTiramizooData, true));
                    $oEmail->setSubject( 'Sending order timeout, API token: ' . $oTiramizooDeliverySet->getApiToken());
                    $oEmail->setRecipient( 'developers@tiramizoo.com', 'Developers team' );
                    $oEmail->setReplyTo( $oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue() );

                    // @codeCoverageIgnoreStart
                    if (!defined('OXID_PHP_UNIT')) {
                        $oEmail->send();
                    }
                    // @codeCoverageIgnoreEnd
                }
            }

            $oTiramizooOrderExtended = $this->getOrderExtended();

            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_response = new oxField(base64_encode(serialize($tiramizooResult)), oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_request_data = new oxField(base64_encode(serialize($oTiramizooData)), oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_status = new oxField($tiramizooResult['response']->state, oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_external_id = new oxField($oTiramizooData->external_id, oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_tracking_url = new oxField($tiramizooResult['response']->tracking_url . '?locale=' . oxLang::getInstance()->getLanguageAbbr(), oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__oxorderid = new oxField($this->getId());

            oxSession::deleteVar('sTiramizooTimeWindow');
            oxSession::deleteVar('sTiramizooDeliveryType');

            $oTiramizooOrderExtended->save();
        }
    }

    /**
     * Returns tiramizoo order extended related to current order record
     *
     * @return oxTiramizoo_OrderExtended
     */
    public function getOrderExtended()
    {
        $oTiramizooOrderExtended = oxNew('oxTiramizoo_OrderExtended');
        $sOxId = $oTiramizooOrderExtended->getIdByOrderId($this->getId());
        $oTiramizooOrderExtended->load($sOxId);

        return $oTiramizooOrderExtended;
    }
}
