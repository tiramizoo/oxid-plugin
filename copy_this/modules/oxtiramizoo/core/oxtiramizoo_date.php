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
 * Special class for date manipulating
 *
 * @package oxTiramizoo
 */
class oxTiramizoo_Date
{
    /**
     * Date as a string
     *
     * @var string
     */
    protected $_sDate;

    /**
     * Current "Real" time, all of comparisions based rely on this date
     *
     * @var string
     */
    protected static $_sCurrentTime = 'now';

    /**
     * Default date format
     *
     * @var string
     */
    protected static $_sFormat = 'Y-m-d H:i:s';

    /**
     * Class constructor. Convert and assign date from string.
     *
     * @param string $_sDate init date
     *
     * @return null
     */
    public function __construct($sDate = null)
    {
        $this->_sDate = strtotime($sDate, strtotime(self::$_sCurrentTime))
                            ? strtotime($sDate, strtotime(self::$_sCurrentTime))
                            : strtotime(self::$_sCurrentTime);
    }

    /**
     * Change current date time for unit tests.
     *
     * @param string $sCurrentTime real date time
     *
     * @return null
     */
    public static function changeCurrentTime($sCurrentTime = 'now')
    {
        // @codeCoverageIgnoreStart
        if ( !defined( 'OXID_PHP_UNIT' ) ) {
            return;
        }
        // @codeCoverageIgnoreEnd

        self::$_sCurrentTime = $sCurrentTime;
    }

    /**
     * Reset current date time to real.
     *
     * @return null
     */
    public static function resetCurrentTime()
    {
    	self::$_sCurrentTime = 'now';
    }

    /**
     * Retrieve date in specified format.
     *
     * @param string $sFormat date format
     *
     * @return string
     */
	public static function date($sFormat = null)
	{
		$oDate = new oxTiramizoo_Date();
		return $oDate->get($sFormat);
	}

    /**
     * Retrieve date in specified format.
     *
     * @param string $sFormat date format
     *
     * @return string
     */
    public function get($sFormat = null)
    {
    	$sFormat = $sFormat ? $sFormat : self::$_sFormat;
    	return date($sFormat, $this->_sDate);
    }

    /**
     * Retrieve date in REST API format.
     *
     * @return string
     */
    public function getForRestApi()
    {
        $oDateForApi = new oxTiramizoo_Date($this->get());
        $sSign = strpos($oDateForApi->get('P'), '+') == 0 ? '-' : '+';

        $oDateForApi->modify($sSign . (intval($oDateForApi->get('Z')) / 3600) . ' hours');

        return $oDateForApi->get('Y-m-d\TH:i:s\Z');
    }

    /**
     * Returns timestamp.
     *
     * @return string
     */
    public function getTimestamp()
    {
        return $this->_sDate;
    }

    /**
     * Check if is today.
     *
     * @return bool
     */
    public function isToday()
    {
    	$oToday = new oxTiramizoo_Date();
    	return $this->get('Y-m-d') == $oToday->get('Y-m-d');
    }

    /**
     * Check if is today.
     *
     * @return bool
     */
    public function isTomorrow()
    {
    	$oTomorrow = new oxTiramizoo_Date('+1 days');
    	return $this->get('Y-m-d') == $oTomorrow->get('Y-m-d');
    }

    /**
     * Check if is on date.
     *
     * @return bool
     */
    public function isOnDate($oOnDate)
    {
        return $this->get('Y-m-d') == $oOnDate->get('Y-m-d');
    }

    /**
     * Check if is time is equal to passed.
     *
     * @param string $sTime time in format (H, H:i, H:i:s)
     *
     * @return bool
     */
    public function isOnTime($sTime)
    {
    	$aTimeFormats = array('H', 'H:i', 'H:i:s');
    	$sFormat = isset($aTimeFormats[substr_count($sTime, ':')])
                        ? $aTimeFormats[substr_count($sTime, ':')]
                        : 'H:i:s';

    	return $this->get($sFormat) == $sTime;
    }

    /**
     * Modiy current time with interval.
     *
     * @return oxTiramizoo_Date
     */
    public function modify($sModify)
    {
    	$this->_sDate = strtotime($sModify, $this->_sDate);
    	return $this;
    }

    /**
     * Get date in output context
     *
     * @return string
     */
    public function __toString()
    {
    	return $this->get();
    }

    /**
     * Check if date is equal to.
     *
     * @param oxTiramizoo_Date $oDate comparision date
     *
     * @return bool
     */
    public function isEqualTo(oxTiramizoo_Date $oDate)
    {
        return $this->getTimestamp() == $oDate->getTimestamp();
    }

    /**
     * Check if date is lather than.
     *
     * @param oxTiramizoo_Date $oDate comparision date
     *
     * @return bool
     */
    public function isLaterThan(oxTiramizoo_Date $oDate)
    {
    	return $this->getTimestamp() > $oDate->getTimestamp();
    }

    /**
     * Check if date is equal to.
     *
     * @param oxTiramizoo_Date $oDate comparision date
     *
     * @return bool
     */
    public function isLaterOrEqualTo(oxTiramizoo_Date $oDate)
    {
        return $this->isLaterThan($oDate) || $this->isEqualTo($oDate);
    }

    /**
     * Check if date is earlier.
     *
     * @param oxTiramizoo_Date $oDate comparision date
     *
     * @return bool
     */
    public function isEarlierThan(oxTiramizoo_Date $oDate)
    {
        return $this->getTimestamp() < $oDate->getTimestamp();
    }

    /**
     * Check if date is earlier or equal to.
     *
     * @param oxTiramizoo_Date $oDate comparision date
     *
     * @return bool
     */
    public function isEarlierOrEqualTo(oxTiramizoo_Date $oDate)
    {
        return $this->isEarlierThan($oDate) || $this->isEqualTo($oDate);
    }
}
