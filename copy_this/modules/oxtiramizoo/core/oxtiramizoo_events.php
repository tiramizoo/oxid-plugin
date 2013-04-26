<?php

class oxTiramizoo_Events
{
	public static function onActivate() 
	{
		$oxTiramizooSetup = new oxTiramizoo_setup();
        $oxTiramizooSetup->install();
	}
}