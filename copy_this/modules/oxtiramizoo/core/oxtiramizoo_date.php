<?php

class oxTiramizoo_Date
{
    protected $_sDate;

    protected static $_sCurrentTime = 'now';
    protected static $_sFormat = 'Y-m-d H:i:s';

    public function __construct($sDate = null)
    {
        $this->_sDate = strtotime($sDate, strtotime(self::$_sCurrentTime)) ? strtotime($sDate, strtotime(self::$_sCurrentTime)) : strtotime(self::$_sCurrentTime);
    }

    public static function changeCurrentTime($sCurrentTime = 'now')
    {
        if ( !defined( 'OXID_PHP_UNIT' ) ) {
            return;
        }    	

        self::$_sCurrentTime = $sCurrentTime;
    }

    public static function resetCurrentTime()
    {
    	self::$_sCurrentTime = 'now';
    }

	public static function date($sFormat = null)
	{
		$oDate = new oxTiramizoo_Date();
		return $oDate->get($sFormat); 
	}

    public function get($sFormat = null)
    {
    	$sFormat = $sFormat ? $sFormat : self::$_sFormat;
    	return date($sFormat, $this->_sDate);
    }

    public function getForRestApi()
    {
        $oDateForApi = new oxTiramizoo_Date($this->get());
        $sSign = strpos($oDateForApi->get('P'), '+') == 0 ? '-' : '+';

        $oDateForApi->modify($sSign . (intval($oDateForApi->get('Z')) / 3600) . ' hours');

        return $oDateForApi->get('Y-m-d\TH:i:s\Z');
    }

    public function getTimestamp()
    {
        return $this->_sDate;
    }

    public function isToday()
    {
    	$oToday = new oxTiramizoo_Date();
    	return $this->get('Y-m-d') == $oToday->get('Y-m-d');
    }

    public function isTomorrow()
    {
    	$oTomorrow = new oxTiramizoo_Date('+1 days');
    	return $this->get('Y-m-d') == $oTomorrow->get('Y-m-d');
    }

    public function isOnTime($sTime)
    {
    	$aTimeFormats = array('H', 'H:i', 'H:i:s');
    	$sFormat = isset($aTimeFormats[substr_count($sTime, ':')]) ? $aTimeFormats[substr_count($sTime, ':')] : 'H:i:s';

    	return $this->get($sFormat) == $sTime;
    }

    public function modify($sModify)
    {
    	$this->_sDate = strtotime($sModify, $this->_sDate);
    	return $this;
    }

    public function __toString()
    {
    	return $this->get();
    }

    public function isEqualTo(oxTiramizoo_Date $oDate)
    {
        return $this->getTimestamp() == $oDate->getTimestamp();
    }

    public function isLaterThan(oxTiramizoo_Date $oDate)
    {
    	return $this->getTimestamp() > $oDate->getTimestamp();
    }

    public function isLaterOrEqualTo(oxTiramizoo_Date $oDate)
    {
        return $this->isLaterThan($oDate) || $oDate->isEqualTo($oDate);
    }

    public function isEarlierThan(oxTiramizoo_Date $oDate)
    {
        return $this->getTimestamp() < $oDate->getTimestamp();
    }

    public function isEarlierOrEqualTo(oxTiramizoo_Date $oDate)
    {
        return $this->isEarlierThan($oDate) || $oDate->isEqualTo($oDate);
    }


}