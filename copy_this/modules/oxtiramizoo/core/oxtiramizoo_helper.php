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



    function objectToArray($data)
    {
        if (is_array($data) || is_object($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->objectToArray($value);
            }
            return $result;
        }
        return $data;
    }

    public function getAvailableTimeWindows()
    {
        //@TODO: Change postal code to properly variable
        $aAvailableServiceAreas = oxTiramizooApi::getInstance()->getAvailableServiceAreas(80639);

        $aTimeWindows = $this->objectToArray($aAvailableServiceAreas['response']->time_windows);

        //sort by delivery from date
        foreach ($aTimeWindows as $oldKey => $aTimeWindow) 
        {
            $aTimeWindows[strtotime($aTimeWindow['delivery']['from'])] = $aTimeWindow;
            unset($aTimeWindows[$oldKey]);
        }

        ksort($aTimeWindows);

        return $aTimeWindows ? $aTimeWindows : array();
    }

    public function getNext7DaysAvailableWindows()
    {
        if ($this->_next7DaysAvailableWindows == null) {

            $oxConfig = oxConfig::getInstance();


            $aTimeWindows = $this->getAvailableTimeWindows();


            $aNext7DaysAvailableWindows = array();
            $deliveryOffsetTime = (int)$oxConfig->getShopConfVar('oxTiramizoo_pickup_del_offset');

            $sCurrentDate = null;

            foreach ($aTimeWindows as $aTimeWindow) 
            {
                $sNewCurrentDate = date('Y-m-d', strtotime($aTimeWindow['delivery']['from']));

                if($sCurrentDate != $sNewCurrentDate) {
                    $aNext7DaysAvailableWindows[] = array('date' => $sNewCurrentDate, 'label' => $sNewCurrentDate, 'timeWindows' => array());

                    if (strtotime(date('Y-m-d', strtotime($sNewCurrentDate))) ==  strtotime(date('Y-m-d'))) {
                        $aNext7DaysAvailableWindows[count($aNext7DaysAvailableWindows) - 1]['label'] = oxLang::getInstance()->translateString('oxTiramizoo_Today', oxLang::getInstance()->getBaseLanguage(), false);
                    } else if (strtotime(date('Y-m-d', strtotime($sNewCurrentDate))) ==  strtotime(date('Y-m-d', strtotime('+1days', strtotime(date('Y-m-d')))))){
                        $aNext7DaysAvailableWindows[count($aNext7DaysAvailableWindows) - 1]['label'] = oxLang::getInstance()->translateString('oxTiramizoo_Tomorrow', oxLang::getInstance()->getBaseLanguage(), false);
                    }
                }

                $sCurrentDate = $sNewCurrentDate;

                $aTimeWindow['timeWindowLabel'] = date('H:i', strtotime($aTimeWindow['delivery']['from'])) . ' - ' . date('H:i', strtotime($aTimeWindow['delivery']['to']));
                $aTimeWindow['enable'] = true;

                //@TODO: Change valid procedure
                if ((strtotime($aTimeWindow['delivery']['to']) < strtotime("now")) && (strtotime($aTimeWindow['pickup']['to']) < strtotime("now"))) {
                    $aTimeWindow['enable'] = false;
                }

                $aNext7DaysAvailableWindows[count($aNext7DaysAvailableWindows) - 1]['timeWindows'][] = $aTimeWindow;
            }


            $this->_next7DaysAvailableWindows = $aNext7DaysAvailableWindows;
        }

        return $this->_next7DaysAvailableWindows;    
    }

    public function getFirstAvailableTimeWindow()
    {
        $aTimeWindows = $this->getAvailableTimeWindows();

        foreach ($aTimeWindows as $aTimeWindow) 
        {
            if ((strtotime($aTimeWindow['delivery']['to']) >= strtotime("now")) && (strtotime($aTimeWindow['pickup']['to']) >= strtotime("now"))) {
                return $aTimeWindow['delivery']['from'];
            }
        }

        return null;
    }

    public function isTimeWindowDeliveryFromAvailable($sDate)
    {
        $aTimeWindows = $this->getAvailableTimeWindows();

        foreach ($aTimeWindows as $aTimeWindow) 
        {
            if (strtotime($sDate) == strtotime($aTimeWindow['delivery']['from'])) {
                return true;
            }
        }

        return false;
    }

    public function getEveningTimeWindow()
    {
        //@TODO: Rewrite when this can get by API 
        return $this->getFirstAvailableTimeWindow();
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

            if (!count($this->getAvailableDeliveryHours())) {
                return $this->_isTiramizooImmediateAvailable = 0;
            }

            if ($this->isTiramizooEveningAvailable()) {
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
        $iMaximumPackageSizes = oxTiramizooConfig::getInstance()->getConfigParam('iMaximumPackageSizes', 6);

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