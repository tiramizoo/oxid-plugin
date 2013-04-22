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
		return oxTiramizoo_ScheduleJob::findAllToRun(self::MAX_RUNNING_JOBS_PER_REQUEST);
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
		if ($oSyncConfigJob = oxTiramizoo_ScheduleJob::findDailyByType('synchronize_configuration')) {
			return false;
		} else {
			$oSyncConfigJob = new oxTiramizoo_SyncConfigJob();
            $oSyncConfigJob->save();
		}
	}
}