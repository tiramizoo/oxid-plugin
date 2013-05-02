<?php

class oxTiramizoo_Events
{
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