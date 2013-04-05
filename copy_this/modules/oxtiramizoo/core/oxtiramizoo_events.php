<?php


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