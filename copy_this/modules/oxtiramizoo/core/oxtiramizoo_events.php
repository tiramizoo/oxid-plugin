<?php

    if ( !class_exists('oxTiramizooConfig') ) {
        require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_config.php';
    }

    if ( !class_exists('oxTiramizooSetup') ) {
        require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_setup.php';
    }

    if ( !class_exists('oxTiramizooHelper') ) {
        require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_helper.php';
    }

class oxTiramizooEvents
{
	public static function onActivate() 
	{
		$oxTiramizooSetup = new oxTiramizoo_setup();
        $oxTiramizooSetup->install();
	}

	public static function onDeactivate() 
	{

	}	
}