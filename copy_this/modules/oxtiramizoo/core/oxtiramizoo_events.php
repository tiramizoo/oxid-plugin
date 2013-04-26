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
			die($e->getMessage());
		}
	}
}