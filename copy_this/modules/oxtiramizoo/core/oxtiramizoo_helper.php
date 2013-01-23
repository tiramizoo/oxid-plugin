<?php

if ( !class_exists('oxTiramizooConfig') ) {
    require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_config.php';
}

if ( !class_exists('oxTiramizooApi') ) {
    require_once getShopBasePath() . '/modules/oxtiramizoo/core/TiramizooApi/oxTiramizooApi.php';
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

    protected $_sDeliveryPostalcode = '';


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

    public static function getExcludeDates()
    {
        $sExcludeDates = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_exclude_days');

        return $sExcludeDates ? explode(',', $sExcludeDates) : array();
    }

    public static function getIncludeDates()
    {
        $sIncludeDates = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_include_days');

        return $sIncludeDates ? explode(',', $sIncludeDates) : array();
    }

    public static function getShopAvailableDates()
    {
        $iCountNextDates = 7;
        $aAvailableDates = array();

        $start = strtotime(date('Y-m-d'));
        $dates=array();

        for($i = 1; $i<=$iCountNextDates; $i++)
        {
            array_push($aAvailableDates, date('Y-m-d', strtotime("+$i day", $start)));
        }

        //skip dates with day of week not checked in settings
        $aShopAvailableDaysOfWeek = oxTiramizooHelper::getShopAvailableDaysOfWeek();
        foreach ($aAvailableDates as $key => $sDate) 
        {
            if (!in_array(date('w', strtotime($sDate)), $aShopAvailableDaysOfWeek)) {
                unset($aAvailableDates[$key]);
            }
        }

        //exclude dates from exclude dates list
        $aExcludeDates = oxTiramizooHelper::getExcludeDates();
        foreach ($aAvailableDates as $key => $sDate) 
        {
            if (in_array($sDate, $aExcludeDates)) {
                unset($aAvailableDates[$key]);
            }
        }

        //Include Additional dates from include list
        $aIncludeDates = oxTiramizooHelper::getIncludeDates();
        foreach ($aIncludeDates as $key => $sDate) 
        {
            if ((strtotime($sDate) >= $start) && (strtotime($sDate) <= strtotime("+$iCountNextDates day", $start)) && !in_array($sDate, $aAvailableDates)) {
                array_push($aAvailableDates, $sDate);
            }
        }

        sort($aAvailableDates);

        return $aAvailableDates;
    }

    public static function getShopAvailableDaysOfWeek()
    {
        $oxConfig = oxConfig::getInstance();
        
        $aAvailableDayOfWeek = array();

        if ($oxConfig->getShopConfVar('oxTiramizoo_works_mon')) {
            $aAvailableDaysOfWeek[] = 1;
        }

        if ($oxConfig->getShopConfVar('oxTiramizoo_works_tue')) {
            $aAvailableDaysOfWeek[] = 2;
        }

        if ($oxConfig->getShopConfVar('oxTiramizoo_works_wed')) {
            $aAvailableDaysOfWeek[] = 3;
        }

        if ($oxConfig->getShopConfVar('oxTiramizoo_works_thu')) {
            $aAvailableDaysOfWeek[] = 4;
        }

        if ($oxConfig->getShopConfVar('oxTiramizoo_works_fri')) {
            $aAvailableDaysOfWeek[] = 5;
        }

        if ($oxConfig->getShopConfVar('oxTiramizoo_works_sat')) {
            $aAvailableDaysOfWeek[] = 6;
        }

        if ($oxConfig->getShopConfVar('oxTiramizoo_works_sun')) {
            $aAvailableDaysOfWeek[] = 0;
        }

        return $aAvailableDaysOfWeek;
    }

    public function setDeliveryPostalCode($sDeliveryPostalcode)
    {
        $this->_sDeliveryPostalcode = $sDeliveryPostalcode;
    }

    public function getTiramizooAvailableWorkingHours()
    {
        $oxConfig = oxConfig::getInstance();
        $result = oxTiramizooApi::getInstance()->getAvailableWorkingHours($oxConfig->getShopConfVar('oxTiramizoo_shop_country_code'), $oxConfig->getShopConfVar('oxTiramizoo_shop_postal_code'), $this->_sDeliveryPostalcode);

        return (array)$result['response'];
    }

    public function isTimeWindowAvailable($dateTime)
    {
        $oxConfig = $this->getConfig();

        $sPickupHour = date('H:i', strtotime($dateTime));
        $sDate = date('Y-m-d', strtotime($dateTime));

        if (!$this->isDateWindowAvailable($sDate)) {
            return false;
        }

        $aTiramizooWorkingHours = $this->getTiramizooAvailableWorkingHours();
        $aTiramizooWorkingHoursThisDay = $aTiramizooWorkingHours[$sDate];


        $maximumDeliveryHour = oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour');
        $minimumDeliveryHour = oxTiramizooConfig::getInstance()->getConfigParam('minimumDeliveryHour');

        if (isset($aTiramizooWorkingHoursThisDay->to) && isset($aTiramizooWorkingHoursThisDay->to->hours) && isset($aTiramizooWorkingHoursThisDay->to->minutes)) {
            $maximumTiramizooWorkingHour = $aTiramizooWorkingHoursThisDay->to->hours . ':' . $aTiramizooWorkingHoursThisDay->to->minutes;

            if(strtotime($maximumDeliveryHour) > strtotime($maximumTiramizooWorkingHour)) {
                $maximumDeliveryHour = $maximumTiramizooWorkingHour;
            }
        }

        if (isset($aTiramizooWorkingHoursThisDay->from) && isset($aTiramizooWorkingHoursThisDay->from->hours) && isset($aTiramizooWorkingHoursThisDay->from->minutes)) {
            $minimumTiramizooWorkingHour = $aTiramizooWorkingHoursThisDay->from->hours . ':' . $aTiramizooWorkingHoursThisDay->from->minutes;
            if(strtotime($minimumDeliveryHour) > strtotime($minimumTiramizooWorkingHour)) {
                $minimumDeliveryHour = $minimumTiramizooWorkingHour;
            }        
        }

        $minimumDeliveryLengthInMinutes = (strtotime(oxTiramizooConfig::getInstance()->getConfigParam('minimumDeliveryWindowLength')) - strtotime('00:00')) / 60;

        $minimumDeliveryBeforeTime = strtotime('+' . $minimumDeliveryLengthInMinutes . 'minutes', strtotime($sPickupHour));

        //check if not exceed the maximum delivery hour
        if ($minimumDeliveryBeforeTime > strtotime($maximumDeliveryHour)) {
            return false;
        }

        //check if not earlier than minimum delivery hour
        if (strtotime($sPickupHour) < strtotime($minimumDeliveryHour)) {
            return false;
        }

        return true;
    }

    public function isDateWindowAvailable($sDate)
    {
        $aTiramizooWorkingHours = $this->getTiramizooAvailableWorkingHours();

        if (!in_array($sDate, array_keys($aTiramizooWorkingHours))) {
            return false;
        }

        if (in_array($sDate, oxTiramizooHelper::getIncludeDates())) {
            return true;
        }

        if (!in_array(date('w', strtotime($sDate)), oxTiramizooHelper::getShopAvailableDaysOfWeek())) {
            return false;
        }

        if (in_array($sDate, oxTiramizooHelper::getExcludeDates())) {
            return false;
        }

        return true;
    }


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
            return oxUtilsDate::getInstance()->formatDBDate( $dateTime ) . ' - ' . $deliveryBefore;
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

            if (!$dateTime) {
                break;
            }

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

        if ($this->isDateWindowAvailable($fromDate)) {
            foreach ($this->getAvailablePickupHours() as $sAvailablePickupHour) 
            {
                $newDateTime = $fromDate . ' ' . $sAvailablePickupHour;

                if ($this->isTimeWindowAvailable($newDateTime) && (strtotime($fromHour) < strtotime($sAvailablePickupHour))) {
                    return $newDateTime;
                }
            }
        }

        //break if checking more than 14 days because of infinite loop
        if (strtotime(date('Y-m-d', strtotime('+1days', strtotime(date('Y-m-d'))))) < strtotime($fromDate)) {
            return false;
        }

        $nextDateTime = date('Y-m-d', strtotime('+1days', strtotime($fromDateTime))) . ' 00:00';
        return $this->getNextAvailableDate($nextDateTime);
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

            //chack if exists delivery hours
            if (!count($this->getAvailableDeliveryHours())) {
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
}