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

                $this->createSendOrderJob($oTiramizooDeliverySet->getApiToken());

                if ($tiramizooResult['errno'] == oxTiramizoo_Api::CURLE_OPERATION_TIMEDOUT) {
                    $this->sendErrorEmail($oTiramizooDeliverySet->getApiToken(), $oTiramizooData);
                }
            }

            $this->saveOrderExtended($tiramizooResult, $oTiramizooData);

            oxRegistry::getSession()->deleteVariable('sTiramizooTimeWindow');
            oxRegistry::getSession()->deleteVariable('sTiramizooDeliveryType');

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

    /**
     * Save request and response data into order extended
     *
     * @param  stdClass $tiramizooResult
     * @param  stdClass $oTiramizooData
     *
     * @return null
     */
    public function saveOrderExtended($tiramizooResult, $oTiramizooData)
    {
        $oTiramizooOrderExtended = $this->getOrderExtended();

        $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_response = new oxField(
            base64_encode(serialize($tiramizooResult)),
            oxField::T_RAW
        );

        $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_request_data = new oxField(
            base64_encode(serialize($oTiramizooData)),
            oxField::T_RAW
        );

        $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_status = new oxField(
            $tiramizooResult['response']->state,
            oxField::T_RAW
        );

        $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_external_id = new oxField(
            $oTiramizooData->external_id,
            oxField::T_RAW
        );

        $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_tracking_url = new oxField(
            $tiramizooResult['response']->tracking_url . '?locale=' . oxRegistry::getLang()->getLanguageAbbr(),
            oxField::T_RAW
        );

        $oTiramizooOrderExtended->oxtiramizooorderextended__oxorderid = new oxField($this->getId());

        $oTiramizooOrderExtended->save();
    }

    /**
     * Create send order job
     *
     * @param  string $sApiToken
     *
     * @return null
     */
    public function createSendOrderJob($sApiToken)
    {
        $oSendOrderJob = oxNew('oxTiramizoo_SendOrderJob');
        $oSendOrderJob->setExternalId($this->getId());
        $oSendOrderJob->setParams(array('api_token' => $sApiToken));
        $oSendOrderJob->save();
    }

    /**
     * Send email to tiramizoo developers team if sending order proccess exceed timeout limit
     *
     * @param  string $sApiToken
     * @param  stdClass $oTiramizooData Request data
     *
     * @return null
     */
    public function sendErrorEmail($sApiToken, $oTiramizooData)
    {
        $oEmail = oxNew( 'oxEmail' );

        $oShop = $oEmail->getConfig()->getActiveShop();

        $oEmail->setFrom( $oShop->oxshops__oxowneremail->value );
        $oEmail->setSmtp( $oShop );
        $oEmail->setBody(print_r($oTiramizooData, true));
        $oEmail->setSubject('Sending order timeout, API token: ' . $sApiToken);
        $oEmail->setRecipient( 'developers@tiramizoo.com', 'Developers team' );
        $oEmail->setReplyTo($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());

        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            $oEmail->send();
        }
        // @codeCoverageIgnoreEnd
    }
}
