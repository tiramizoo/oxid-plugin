<?php

class oxTiramizoo_ScheduleJobManager
{
	const MAX_RUNNING_JOBS_PER_REQUEST = 5;

	private $_isFinished = false;

	public function isFinished()
	{
		return $this->_isFinished;
	}

	public function getJobsForRun()
	{
        $oScheduleJobList = oxNew('oxTiramizoo_ScheduleJobList');
        $oScheduleJobList->loadToRun(self::MAX_RUNNING_JOBS_PER_REQUEST);
    
        return $oScheduleJobList;
	}

	public function runJobs()
	{
		foreach ($this->getJobsForRun() as $oScheduleJob) 
		{
			$oScheduleJob->run();
		}

		$this->_isFinished = true;
	}

	public function addTasks()
	{
		$this->syncConfigDaily();
	}

	public function syncConfigDaily()
	{
		$oSyncConfigJob = oxnew('oxTiramizoo_SyncConfigJob');

		if ($oSyncConfigJob->getIdTodayByType('synchronize_configuration')) {
			return false;
		} else {
            $oSyncConfigJob->save();
            return true;
		}
	}
}