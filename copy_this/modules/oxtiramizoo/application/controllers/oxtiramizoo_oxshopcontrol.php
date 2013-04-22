<?php

class oxTiramizoo_oxShopControl extends oxTiramizoo_oxShopControl_parent
{
    public function _process( $sClass = null, $sFunction = null, $aParams = null, $aViewsChain = null )
    {
		$oTiramizooConfig = oxRegistry::get('oxTiramizooConfig');
		$oScheduleJobManager = oxRegistry::get('oxTiramizoo_ScheduleJobManager');

		if (!$oTiramizooConfig->getShopConfVar('cron_is_enabled') && !$oScheduleJobManager->isFinished()) {
			$oScheduleJobManager->addTasks();
			$oScheduleJobManager->runJobs();
		}

		parent::_process( $sClass, $sFunction, $aParams, $aViewsChain );
	}
}
