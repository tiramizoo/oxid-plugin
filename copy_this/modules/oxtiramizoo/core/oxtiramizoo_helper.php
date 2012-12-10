<?php

if ( !class_exists('oxTiramizooConfig') ) {
    require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_config.php';
}

/**
 * This class contains static methods used for calculating pickup and delivery hours
 *
 * @package: oxTiramizoo
 */
class oxTiramizooHelper extends oxSuperCfg
{
    /**
     * Singleton instance
     * 
     * @var oxTiramizooApi
     */
    protected static $_instance = null;

    /**
     * Get the instance of class
     * 
     * @return oxTiramizooHelper
     */
    public static function getInstance()
    {
        if ( !self::$_instance instanceof oxTiramizooHelper ) {
                self::$_instance = new oxTiramizooHelper();
        }

        return self::$_instance;
    }

    /**
     * @var integer
     */
    protected $_isTiramizooAvailable = -1;

    protected $_isTiramizooImmediateAvailable = -1;
    protected $_isTiramizooEveningAvailable = -1;


    /**
     * Convert date time to more readable string
     * 
     * @param  string $dateTime Date time to convert
     * @return string Converted date 
     */
    public static function getLabelDeliveryWindow($dateTime)
    {
        $oxConfig = oxConfig::getInstance();

        $orderOffsetTime = (int)$oxConfig->getShopConfVar('oxTiramizoo_order_pickup_offset');
        $deliveryOffsetTime = (int)$oxConfig->getShopConfVar('oxTiramizoo_pickup_del_offset');

        $deliveryBefore = date('H:i', strtotime('+' . $deliveryOffsetTime . 'minutes', strtotime($dateTime)));

        // cut the delivery before if it greater than maximum delivery hour
        if (strtotime($deliveryBefore) > strtotime(oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour'))) {
            $deliveryBefore = oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour');
        }

        if (strtotime(date('Y-m-d', strtotime($dateTime))) ==  strtotime(date('Y-m-d'))) {
            return oxLang::getInstance()->translateString('oxTiramizoo_Today', oxLang::getInstance()->getBaseLanguage(), false) . strtotime('Y-m-d', strtotime($dateTime)) . ' ' . date('H:i', strtotime($dateTime)) . ' - ' . $deliveryBefore;
        } else if (strtotime(date('Y-m-d', strtotime($dateTime))) ==  strtotime(date('Y-m-d', strtotime('+1days', strtotime(date('Y-m-d')))))){
            return oxLang::getInstance()->translateString('oxTiramizoo_Tomorrow', oxLang::getInstance()->getBaseLanguage(), false) . strtotime('Y-m-d', strtotime($dateTime)) . ' ' . date('H:i', strtotime($dateTime)) . ' - ' . $deliveryBefore;

        } else {
            return $dateTime . ' - ' . $deliveryBefore;
        }
    }

    /**
     * Get tiramizoo deliver time window selected by user 
     * 
     * @return string
     */
    public function getSelectedTimeWindow()
    {
        return oxSession::getVar( 'sTiramizooTimeWindow' );
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

        $dateTime = date('Y-m-d H:i', strtotime('+' . $orderOffsetTime . ' minutes', strtotime(date('Y-m-d H:i'))));
        
        $itertator = 0;
        while ($itertator++ < 6)
        {
            $dateTime = $this->getNextAvailableDate($dateTime);

            $aAvailableDeliveryHours[$dateTime] = oxTiramizooHelper::getLabelDeliveryWindow($dateTime);

            //set as default time window if not setted before or the time window was expired
            if (($itertator == 1) && (!oxSession::hasVar( 'sTiramizooTimeWindow' ) || strtotime(oxSession::getVar( 'sTiramizooTimeWindow' )) < strtotime($dateTime))) {
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

        $fromHour = date('H:i', strtotime($fromDateTime));
        $fromDate = date('Y-m-d', strtotime($fromDateTime));
        $fromDayNum = date('w', strtotime($fromDateTime));

        //check if not exceed the maximum delivery hour
        $minimumDeliveryLengthInMinutes = (strtotime(oxTiramizooConfig::getInstance()->getConfigParam('minimumDeliveryWindowLength')) - strtotime('00:00')) / 60;

        $goToNextDate = false;

        if (in_array($fromDayNum, range(1, 5))) {
            foreach ($this->getAvailablePickupHours() as $sAvailablePickupHour) 
            {
                $minimumDeliveryBeforeTime = strtotime('+' . $minimumDeliveryLengthInMinutes . 'minutes', strtotime($sAvailablePickupHour));
                
                //check if not exceed the maximum delivery hour
                if ($minimumDeliveryBeforeTime > strtotime(oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour'))) {
                    $goToNextDate = true;
                    break;
                }

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
    public function isTiramizooAvailable() 
    {
        if ($this->_isTiramizooAvailable === -1) {

            $oBasket = $this->getSession()->getBasket();
            $oxConfig = $this->getConfig();

            $oOrder = oxNew( 'oxorder' );
            $address = $oOrder->getDelAddressInfo();

            $oUser = $this->getUser();

            $sZipCode = $address ? $address->oxaddress__oxzip->value : $oUser->oxuser__oxzip->value;

            if (!$this->getConfig()->getConfigParam('oxTiramizoo_enable_module')) {
                return $this->_isTiramizooAvailable = 0;
            }

            if (!count($this->getAvailablePickupHours())) {
                return $this->_isTiramizooAvailable = 0;
            }

            //check if Tiramizoo can deliver this basket
            $data = new stdClass();
            $data->pickup_postal_code = $this->getConfig()->getConfigParam('oxTiramizoo_shop_postal_code');
            $data->delivery_postal_code = $sZipCode;
            $data->items = array();

            require_once getShopBasePath() . '/modules/oxtiramizoo/core/TiramizooApi/oxTiramizooApi.php';

            $data->items = oxTiramizooApi::getInstance()->buildItemsData($oBasket);

            $result = oxTiramizooApi::getInstance()->getQuotes($data);

            if (!in_array($result['http_status'], array(200))) {
                
                // Uncomment to debug
                // echo '<div>';
                // echo json_encode($data);
                // echo json_encode($result);
                // echo '</div>';

                return $this->_isTiramizooAvailable = 0;
            }

            $this->_isTiramizooAvailable = 1;
        }

        return $this->_isTiramizooAvailable;
    }


    /**
     * Validate basket data to decide if can be delivered by tiramizoo 
     * 
     * @return bool
     */
    public function isTiramizooImmediateAvailable() 
    {
        if ($this->_isTiramizooImmediateAvailable === -1) {

            if (!$this->getConfig()->getShopConfVar('oxTiramizoo_enable_immediate')) {
                return $this->_isTiramizooImmediateAvailable = 0;
            }
            
            $hour = date('H:i', strtotime($this->getNextAvailableDate( date('Y-m-d H:i:s') )));

            if ($this->isTiramizooEveningAvailable() && (strtotime($hour) == strtotime($this->getConfig()->getShopConfVar('oxTiramizoo_evening_window')))) {
                return $this->_isTiramizooImmediateAvailable = 0;
            }


            $this->_isTiramizooImmediateAvailable = 1;
        }

        return $this->_isTiramizooImmediateAvailable;
    }

    /**
     * Validate basket data to decide if can be delivered by tiramizoo 
     * 
     * @return bool
     */
    public function isTiramizooEveningAvailable() 
    {
        if ($this->_isTiramizooEveningAvailable === -1) {

            if (!$this->getConfig()->getShopConfVar('oxTiramizoo_enable_evening') && $this->getConfig()->getShopConfVar('oxTiramizoo_evening_window')) {
                return $this->_isTiramizooEveningAvailable = 0;
            }

            $this->_isTiramizooEveningAvailable = 1;
        }

        return $this->_isTiramizooEveningAvailable;
    }

}