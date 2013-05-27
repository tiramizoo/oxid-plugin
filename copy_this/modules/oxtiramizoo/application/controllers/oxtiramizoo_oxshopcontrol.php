<?php

class oxTiramizoo_oxShopControl extends oxTiramizoo_oxShopControl_parent
{
    public function _process( $sClass = null, $sFunction = null, $aParams = null, $aViewsChain = null )
    {
    	try
    	{
			$oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');
			$oScheduleJobManager = oxRegistry::get('oxTiramizoo_ScheduleJobManager');

			if (!$oTiramizooConfig->getShopConfVar('cron_is_enabled') && !$oScheduleJobManager->isFinished()) {
				$oScheduleJobManager->addTasks();
				$oScheduleJobManager->runJobs();
			}    		
    	} catch (oxSystemComponentException $e) {
    		
    	}

        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
			parent::_process( $sClass, $sFunction, $aParams, $aViewsChain );
        }
        // @codeCoverageIgnoreEnd
	}
}
