<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2011
 * @version OXID eShop CE
 * @version   SVN: $Id: test_config.inc.php 34014 2011-03-25 14:06:07Z sarunas $
 */

// DO NOT TOUCH THIS _ INSTEAD FIX NOTICES - DODGER
error_reporting( (E_ALL ^ E_NOTICE) | E_STRICT );
ini_set('memory_limit', '2048M');
ini_set('memory_limit', '4000M');

define ('OXID_PHP_UNIT', true);

define ('oxTiramizooPATH', '/www/oxid473.dev/modules/');

date_default_timezone_set('Europe/Warsaw');

function oxTiramizooAutoload($sClass)
{
	include oxTiramizooPATH . 'oxtiramizoo/metadata.php';

	if (isset($aModule['files'][$sClass])) {
		require_once oxTiramizooPATH . $aModule['files'][$sClass];
	} 
}

// spl_autoload_register('oxTiramizooAutoload');


$_sOverridenShopBasePath = '/www/oxid473.dev/';

/**
 * Sets a path to the test shop
 *
 * @deprecated Define OX_BASE_PATH constant instead
 *
 * @param string $sPath New path to shop
 */
function overrideGetShopBasePath($sPath)
{
    //TS2012-06-06
    die("overrideGetShopBasePath() is deprecated use OX_BASE_PATH constant instead. ALWAYS.");
    global $_sOverridenShopBasePath;
    $_sOverridenShopBasePath = $sPath;
}

define( 'OX_BASE_PATH',  isset( $_sOverridenShopBasePath ) ? $_sOverridenShopBasePath : oxPATH  );

/*
function getShopBasePath()
{
    global $_sOverridenShopBasePath;
    if (isset($_sOverridenShopBasePath)) {
        return $_sOverridenShopBasePath;
    }
    return oxPATH;
}*/










function getTestsBasePath()
{
    return realpath(dirname(__FILE__).'/../');
}

require_once 'test_utils.php';



// Generic utility method file.
require_once OX_BASE_PATH . 'core/oxfunctions.php';




// As in new bootstrap to get db instance.
$oConfigFile = new OxConfigFile( OX_BASE_PATH . "config.inc.php" );

OxRegistry::set("OxConfigFile", $oConfigFile);
oxRegistry::set("oxConfig", new oxConfig());

// As in new bootstrap to get db instance.
$oDb = new oxDb();
$oDb->setConfig( $oConfigFile );
$oLegacyDb = $oDb->getDb();
OxRegistry::set( 'OxDb', $oLegacyDb );

oxConfig::getInstance();

/**
 * Useful for defining custom time
 */
class modOxUtilsDate extends oxUtilsDate
{
    protected $_sTime = null;

    public function UNITSetTime($sTime)
    {
        $this->_sTime = $sTime;
    }

    public function getTime()
    {
        if (!is_null($this->_sTime))
            return $this->_sTime;

        return parent::getTime();
    }
}

// Utility class
require_once getShopBasePath() . 'core/oxutils.php';

// Database managing class.
require_once getShopBasePath() . 'core/adodblite/adodb.inc.php';

// Session managing class.
require_once getShopBasePath() . 'core/oxsession.php';

// Database session managing class.
// included in session file if needed - require_once( getShopBasePath() . 'core/adodb/session/adodb-session.php');

// DB managing class.
//require_once( getShopBasePath() . 'core/adodb/drivers/adodb-mysql.inc.php');
require_once getShopBasePath() . 'core/oxconfig.php';

