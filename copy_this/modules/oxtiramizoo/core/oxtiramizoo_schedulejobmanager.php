<?php

class oxTiramizoo_ScheduleJobManager
{
	private $_aJobTypes = array('send_order' => 'oxTiramizoo_SendOrderJob',
							    'synchronize_configuration' => 'oxTiramizoo_SyncConfigJob');

	private $_isFinished = false;

	public function isFinished()
	{
		return $this->_isFinished;
	}

	public function runJobs()
	{
		foreach ($this->_aJobTypes as $sJobType => $sClassName) 
		{
			$oScheduleJob = new $sClassName;

			$oScheduleJob->initialize();

			if ($oScheduleJob->jobExists()) {
				$oScheduleJob->run();
			}
		}	
	}

	public function activateJob(oxTiramizoo_ScheduleJob $oScheduleJob)
	{
		$oScheduleJob->activate();
	}
}