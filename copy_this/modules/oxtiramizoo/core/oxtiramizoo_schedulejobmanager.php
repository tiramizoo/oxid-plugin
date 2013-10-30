<?php
/**
 * This file is part of the oxTiramizoo OXID eShop plugin.
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  module
 * @package   oxTiramizoo
 * @author    Tiramizoo GmbH <support@tiramizoo.com>
 * @copyright Tiramizoo GmbH
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Schedule Job Manager. Gets and runs jobs.
 *
 * @package oxTiramizoo
 */
class oxTiramizoo_ScheduleJobManager
{
	/**
	 * Maximum jobs number that could be run in one rquest
	 */
	const MAX_RUNNING_JOBS_PER_REQUEST = 5;

    /**
     * Hold information if manager finished runJobs
     *
     * @var string
     */
	protected $_isFinished = false;

    /**
     * Retrieve
     *
     * @return bool
     */
	public function isFinished()
	{
		return $this->_isFinished;
	}

    /**
     * Retrieve limited number og jobs for run in request
     *
     * @return oxTiramizoo_ScheduleJobsList
     */
	public function getJobsForRun()
	{
        $oScheduleJobList = oxNew('oxTiramizoo_ScheduleJobList');
        $oScheduleJobList->loadToRun(self::MAX_RUNNING_JOBS_PER_REQUEST);

        return $oScheduleJobList;
	}

    /**
     * Fires scheduled jobs. When finish set _isFinished to true.
     *
     * @return null
     */
	public function runJobs()
	{
		foreach ($this->getJobsForRun() as $oScheduleJob)
		{
			$oScheduleJob->run();
		}

		$this->_isFinished = true;
	}

    /**
     * Add Tasks to run
     *
     * @return null
     */
	public function addTasks()
	{
		$this->syncConfigDaily();
	}

    /**
     * Generate synchronize configuration task.
     *
     * @return bool
     */
	public function syncConfigDaily()
	{
		$oSyncConfigJob = oxnew('oxTiramizoo_SyncConfigJob');

		if ($oSyncConfigJob->getIdTodayByType('synchronize_configuration')) {
			$blReturn = false;
		} else {
            $oSyncConfigJob->save();
            $blReturn = true;
		}

        return $blReturn;
	}
}
