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
 * oxTiramizoo event class fired onActivate module.
 *
 * @package oxTiramizoo
 */
class oxTiramizoo_Events
{
	/**
	 * Execute oxTiramizoo Module setup 
	 *
	 * @return null
	 */
	public static function onActivate() 
	{
		try
		{
        	$oTiramizooSetup = oxRegistry::get('oxTiramizoo_Setup');
			$oTiramizooSetup->install();
		} catch (oxException $e) {
	        // @codeCoverageIgnoreStart
	        if ( !defined( 'OXID_PHP_UNIT' ) ) {
				die($e->getMessage());
	        }       
	        // @codeCoverageIgnoreEnd
		}
	}
}
