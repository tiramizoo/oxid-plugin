<?php
/**
 * This file is part of the module oxTiramizoo for OXID eShop.
 *
 * The module oxTiramizoo for OXID eShop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free Software Foundation
 * either version 3 of the License, or (at your option) any later version.
 *
 * The module oxTiramizoo for OXID eShop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. 
 *  
 * See the GNU General Public License for more details <http://www.gnu.org/licenses/>
 *
 * @copyright: Tiramizoo GmbH
 * @author: Krzysztof Kowalik <kowalikus@gmail.com>
 * @package: oxTiramizoo
 * @license: http://www.gnu.org/licenses/
 * @version: 1.0.0
 * @link: http://tiramizoo.com
 */

require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_helper.php';

/**
 * Tiramizoo Payment view. Extends to proccess Tiramizoo delivery
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_Payment extends oxTiramizoo_Payment_parent
{
    /**
     * @var integer
     */
    protected $_isTiramizooCanShow = -1;

    /**
     * Get all delivery sets, remove Tiramizoo if basket couldn't be delivered
     * 
     * @return array
     */
    public function getAllSets()
    {
        if ( $this->_aAllSets === null ) {
            $this->_aAllSets = false;

            if ($this->getPaymentList()) {
                return $this->_aAllSets;
            }
        }

        //check if remove Tiramizoo
        if (!$this->tiramizooCanShow()) {
            unset($this->_aAllSets['Tiramizoo']);
        }

        return $this->_aAllSets;
    }

    /**
     * Changes shipping set to chosen one. Sets basket status to not up-to-date, which later
     * forces to recalculate it
     *
     * @return null
     */
    public function changeshipping()
    {
        $mySession = $this->getSession();

        $oBasket = $mySession->getBasket();
        $oBasket->setShipping( null );
        $oBasket->onUpdate();
        oxSession::setVar( 'sShipSet', oxConfig::getParameter( 'sShipSet' ) );

        if (oxConfig::getParameter( 'sTiramizooTimeWindow' )) {
            oxSession::setVar( 'sTiramizooTimeWindow', oxConfig::getParameter( 'sTiramizooTimeWindow' ) );
        }
    }

    /**
     * Executes parent::render(), pass variable to template to check
     * if tiramioo module is running now
     *
     * @return  string  current template file name
     */

    public function render()
    {
        $this->_aViewData['isTiramizooPaymentView'] = 1;
        return parent::render();
    }

    /**
     * Get tiramizoo deliver time window selected by user 
     * 
     * @return string
     */
    public function getTiramizooTimeWindow()
    {
        return oxSession::getVar( 'sTiramizooTimeWindow' );
    }

    /**
     * Check if tiramizoo is selected
     * 
     * @return boolean
     */
    public function isTiramizooCurrentShiippingMethod()
    {
        if ($this->tiramizooCanShow()) {
            $oBasket = $this->getSession()->getBasket();
            return  $oBasket->getShippingId() == 'Tiramizoo';
        }

        return false;
    }

    /**
     * Get available delivery windows to present in cart
     * 
     * @return array
     */
    public function getAvailableDeliveryHours()
    {
        $oxConfig = $this->getConfig();

        $aAvailableDeliveryHours = array();
        
        $orderOffsetTime = (int)$oxConfig->getShopConfVar('oxTiramizoo_order_pickup_offset');
        $deliveryOffsetTime = (int)$oxConfig->getShopConfVar('oxTiramizoo_pickup_del_offset');

        $dateTime = date('Y-m-d H:i');

        $itertator = 0;
        while ($itertator++ < 6)
        {
            $dateTime = $this->getNextAvailableDate($dateTime);

            $aAvailableDeliveryHours[$dateTime] = oxTiramizooHelper::getLabelDeliveryWindow($dateTime);

            if (($itertator == 1) && !oxSession::hasVar( 'sTiramizooTimeWindow' )) {
                oxSession::setVar( 'sTiramizooTimeWindow',  $dateTime);
            }
        }

        return $aAvailableDeliveryHours;
    }

    /**
     * Get available pikup hours from config
     * 
     * @return array Array of datetimes
     */
    public function getAvailablePickupHours()
    {
        $aAvailablePickupHours = array();

        $oxConfig = oxConfig::getInstance();

        for ($i = 1; $i <= 6 ; $i++) 
        {
            if ($pickupHour = $oxConfig->getShopConfVar('oxTiramizoo_shop_pickup_hour_' . $i)) {
                $aAvailablePickupHours[] = $pickupHour;
            }
        }

        return $aAvailablePickupHours;
    }

    /**
     * Get next available pickup date excluding weekends
     * 
     * @param  string $dateTime Date time
     * @return string Next date time
     */
    public function getNextAvailableDate($fromDateTime)
    {
        $oxConfig = $this->getConfig();

        $orderOffsetTime = (int)$oxConfig->getShopConfVar('oxTiramizoo_order_pickup_offset');

        $fromDateTime = date('Y-m-d H:i', strtotime('+' . $orderOffsetTime . ' minutes', strtotime($fromDateTime)));
        $fromHour = date('H:i', strtotime($fromDateTime));
        $fromDate = date('Y-m-d', strtotime($fromDateTime));
        $fromDayNum = date('w', strtotime($fromDateTime));

        if (in_array($fromDayNum, range(1, 5))) {
            foreach ($this->getAvailablePickupHours() as $sAvailablePickupHour) 
            {
                if (strtotime($fromHour) < strtotime($sAvailablePickupHour)) {
                    return $fromDate . ' ' . $sAvailablePickupHour;
                }
            }
        } 

        if (in_array($fromDayNum, array(6))) {
            $nextDateTime = date('Y-m-d', strtotime('+2days', strtotime($fromDateTime))) . ' 00:00';
            return $this->getNextAvailableDate($nextDateTime);
        } else {
            $nextDateTime = date('Y-m-d', strtotime('+1days', strtotime($fromDateTime))) . ' 00:00';
            return $this->getNextAvailableDate($nextDateTime);
        }
    }

    /**
     * Validate basket data to decide if can be delivered by tiramizoo 
     * 
     * @return bool
     */
    public function tiramizooCanShow() 
    {
        if ($this->_isTiramizooCanShow === -1) {

            $oBasket = $this->getSession()->getBasket();
            $oxConfig = $this->getConfig();

            $oOrder = oxNew( 'oxorder' );
            $address = $oOrder->getDelAddressInfo();

            $oUser = $this->getUser();

            $sZipCode = $address ? $address->oxaddress__oxzip->value : $oUser->oxuser__oxzip->value;

            if (!$this->getConfig()->getConfigParam('oxTiramizoo_enable_module')) {
                return $this->_isTiramizooCanShow = 0;
            }

            if (!count($this->getAvailablePickupHours())) {
                return $this->_isTiramizooCanShow = 0;
            }

            //check if Tiramizoo can deliver this basket
            $data = new stdClass();
            $data->pickup_postal_code = $this->getConfig()->getConfigParam('oxTiramizoo_shop_postal_code');
            $data->delivery_postal_code = $sZipCode;
            $data->items = array();


            require_once getShopBasePath() . '/modules/oxtiramizoo/core/TiramizooApi/oxTiramizooApi.php';

            $data->items = oxTiramizooApi::getInstance()->buildItemsData($oBasket);

            $result = oxTiramizooApi::getInstance()->getQuotes($data, false);

            if (!in_array($result['http_status'], array(200, 201))) {
                
                // Uncomment to debug
                // echo '<div>';
                // echo json_encode($data);
                // echo json_encode($result);
                // echo '</div>';

                return $this->_isTiramizooCanShow = 0;
            }
        }

        return $this->_isTiramizooCanShow = 1;
    }
}