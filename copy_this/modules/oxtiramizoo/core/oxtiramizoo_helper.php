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
    protected $_oUser = null;

    /**
     * @var integer
     */
    protected $_isTiramizooAvailable = -1;
    protected $_isTiramizooImmediateAvailable = -1;
    protected $_isTiramizooEveningAvailable = -1;
    protected $_isTiramizooSelectTimeAvailable = -1;

    protected $_next7DaysAvailableWindows = null;

    
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

    public function setUser($oUser)
    {
        $this->_oUser = $oUser;
    }

    public function getDeliveryPostalCode()
    {
        $oUser = $this->_oUser;
        $sZipCode = $oUser->oxuser__oxzip->value;

        $sSelectedAddressId = $oUser->getSelectedAddressId();

        if($sSelectedAddressId) {
            $oDeliveryAddress = $oUser->getUserAddresses($sSelectedAddressI);
            $sZipCode = $oDeliveryAddress->oxaddress__oxzip->value;
        }
        $this->setDeliveryPostalCode($sZipCode);

        return $this->_sDeliveryPostalcode;
    }

    public function setDeliveryPostalCode($sDeliveryPostalcode)
    {
        $this->_sDeliveryPostalcode = $sDeliveryPostalcode;
    }

    public function getTiramizooAvailableWorkingHours()
    {
        $oxConfig = oxConfig::getInstance();
        $result = oxTiramizooApi::getInstance()->getAvailableWorkingHours($oxConfig->getShopConfVar('oxTiramizoo_shop_country_code'), $oxConfig->getShopConfVar('oxTiramizoo_shop_postal_code'), $this->getDeliveryPostalCode());

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

        $orderOffsetTime = (int)$oxConfig->getShopConfVar('oxTiramizoo_order_pickup_offset');


        $maximumDeliveryHour = oxTiramizooConfig::getInstance()->getConfigParam('maximumDeliveryHour');
        $minimumDeliveryHour = oxTiramizooConfig::getInstance()->getConfigParam('minimumDeliveryHour');

        //false if today earlier
        if (strtotime(date('Y-m-d')) == strtotime($sDate)) {
            if (strtotime(date('H:i')) >= (strtotime($sPickupHour) - ($orderOffsetTime * 60))) {
                return false;
            }
        }

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

            if (!$this->isTiramizooImmediateAvailable() && !$this->isTiramizooEveningAvailable() && !$this->isTiramizooSelectTimeAvailable()) {
                return $this->_isTiramizooImmediateAvailable = 0;
            }

            $oOrder = oxNew( 'oxorder' );
            $address = $oOrder->getDelAddressInfo();

            $oUser = $this->getUser();

            $sZipCode = $address ? $address->oxaddress__oxzip->value : $oUser->oxuser__oxzip->value;

            if (!count($this->getAvailablePickupHours())) {
                return $this->_isTiramizooAvailable = 0;
            }

            //check if Tiramizoo can deliver this basket
            $data = new stdClass();
            $data->pickup_postal_code = $this->getConfig()->getConfigParam('oxTiramizoo_shop_postal_code');
            $data->delivery_postal_code = $sZipCode;
            $data->items = array();

            require_once getShopBasePath() . '/modules/oxtiramizoo/core/TiramizooApi/oxTiramizooApi.php';

            try {
                $data->items = oxTiramizooApi::getInstance()->buildItemsData($oBasket);
            } catch (oxTiramizoo_NotAvailableException $e) {
                return $this->_isTiramizooAvailable = 0;
            }

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


    public function getNext7AvailableDates()
    {
        $iCountNextDates = 7;
        $aAvailableDates = array();

        $start = strtotime(date('Y-m-d'));
        $dates=array();

        for($i = 0; $i<$iCountNextDates; $i++)
        {
            array_push($aAvailableDates, date('Y-m-d', strtotime("+$i day", $start)));
        }

        return $aAvailableDates;
    }

    public function getNext7DaysAvailableWindows()
    {
        if ($this->_next7DaysAvailableWindows == null) {

            $oxConfig = oxConfig::getInstance();
            $aNext7Dates = $this->getNext7AvailableDates();
            $aNext7DaysAvailableWindows = array();
            $deliveryOffsetTime = (int)$oxConfig->getShopConfVar('oxTiramizoo_pickup_del_offset');

            foreach ($aNext7Dates as $sDate) 
            {
                if (!$this->isDateWindowAvailable($sDate)) {
                    continue;
                }

                $aDate = array('date' => $sDate, 'label' => $sDate, 'timeWindows' => array());

                if (strtotime(date('Y-m-d', strtotime($sDate))) ==  strtotime(date('Y-m-d'))) {
                    $aDate['label'] = oxLang::getInstance()->translateString('oxTiramizoo_Today', oxLang::getInstance()->getBaseLanguage(), false) . strtotime('Y-m-d', strtotime($sDate));
                } else if (strtotime(date('Y-m-d', strtotime($sDate))) ==  strtotime(date('Y-m-d', strtotime('+1days', strtotime(date('Y-m-d')))))){
                    $aDate['label'] = oxLang::getInstance()->translateString('oxTiramizoo_Tomorrow', oxLang::getInstance()->getBaseLanguage(), false) . strtotime('Y-m-d', strtotime($sDate));
                }

                foreach ($this->getAvailablePickupHours() as $sAvailablePickupHour) 
                {
                    $newDateTime = $sDate . ' ' . $sAvailablePickupHour;

                    $enable = true;
                    if (!$this->isTimeWindowAvailable($newDateTime)) {
                        $enable = false;
                    } 


                    $deliveryBefore = date('H:i', strtotime('+' . $deliveryOffsetTime . 'minutes', strtotime($sAvailablePickupHour)));

                    $aDate['timeWindows'][] = array('pickupHour' => $sAvailablePickupHour, 
                                                   'timeWindowLabel' => $sAvailablePickupHour . ' - ' . $deliveryBefore,
                                                   'timeWindowDate' => $sDate . ' ' . $sAvailablePickupHour,
                                                   'enable' => $enable);
                }

                if (count($aDate['timeWindows'])) {
                    $aNext7DaysAvailableWindows[] = $aDate;
                }
            }

            $this->_next7DaysAvailableWindows = $aNext7DaysAvailableWindows;
        }

        return $this->_next7DaysAvailableWindows;
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

            if (!count($this->getAvailableDeliveryHours())) {
                return $this->_isTiramizooImmediateAvailable = 0;
            }

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

            if (!$this->getConfig()->getShopConfVar('oxTiramizoo_enable_evening') || !$this->getConfig()->getShopConfVar('oxTiramizoo_evening_window')) {
                return $this->_isTiramizooEveningAvailable = 0;
            }

            //check if time is not earlier
            $nextAvailableTime = strtotime($this->getNextAvailableDate( date('Y-m-d H:i:s') ));
            $todayEveningAvailableTime = strtotime(date('Y-m-d') . ' ' . $this->getConfig()->getShopConfVar('oxTiramizoo_evening_window'));

            if (!count($this->getAvailableDeliveryHours())) {
                return $this->_isTiramizooEveningAvailable = 0;
            }

            if ($nextAvailableTime > $todayEveningAvailableTime) {
                return $this->_isTiramizooEveningAvailable = 0;
            }
            


            $this->_isTiramizooEveningAvailable = 1;
        }

        return $this->_isTiramizooEveningAvailable;
    }

    /**
     * Validate basket data to decide if can be delivered by tiramizoo 
     * 
     * @return bool
     */
    public function isTiramizooSelectTimeAvailable() 
    {
        if ($this->_isTiramizooSelectTimeAvailable === -1) {

            if (!$this->getConfig()->getShopConfVar('oxTiramizoo_enable_select_time')) {
                return $this->_isTiramizooSelectTimeAvailable = 0;
            }

            if (!count($this->getNext7DaysAvailableWindows())) {
                return $this->_isTiramizooSelectTimeAvailable = 0;
            }


            $this->_isTiramizooSelectTimeAvailable = 1;
        }

        return $this->_isTiramizooSelectTimeAvailable;
    }

    public function getPackageSizes()
    {
        $iMaximumPackageSizes = oxTiramizooConfig::getInstance()->getConfigParam('iMaximumPackageSizes');

        $aPackageSizes = array();

        for ($i=1; $i <= $iMaximumPackageSizes ; $i++) 
        { 
            $aPackageSize = array();
            $aPackageSize['name'] = 'oxTiramizoo_package_size_' . $i;
            $aPackageSize['value'] = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_package_size_' . $i);

            $aPackageSizeValuesArray = explode('x', $aPackageSize['value']);

            $aPackageSize['width'] = $aPackageSizeValuesArray[0];
            $aPackageSize['length'] = $aPackageSizeValuesArray[1];
            $aPackageSize['height'] = $aPackageSizeValuesArray[2];
            $aPackageSize['weight'] = $aPackageSizeValuesArray[3];

            $aPackageSizes[$i] = $aPackageSize;
        }

        return $aPackageSizes;
    }


    public function getPackageSizesSortedByVolume() 
    {
        $aPackageSizes = $this->getPackageSizes();
        $aPackageSizesSorted = array();

        foreach ($aPackageSizes as $key => $aPackageSize) {
            $volume = $aPackageSize['width'] * $aPackageSize['length'] * $aPackageSize['height'];
            $aPackageSizesSorted[$volume] = $aPackageSize;
        }

        ksort($aPackageSizesSorted);

        return $aPackageSizesSorted;
    }
}