<?php

class Unit_Core_oxTiramizoo_ScheduleJobManagerTest extends OxidTestCase
{
	public function testIsFinished()
	{
		$oScheduleJobManager = new oxTiramizoo_ScheduleJobManager();

		$this->assertEquals(false, $oScheduleJobManager->isFinished());		
	}

	public function testRunJobsWithNoJobs()
	{
	    $oScheduleJobManager = $this->getMock('oxTiramizoo_ScheduleJobManager', array('getJobsForRun'));
	    $oScheduleJobManager->expects($this->any())->method('getJobsForRun')->will($this->returnValue(array()));
		$oScheduleJobManager->runJobs();

		$this->assertEquals(true, $oScheduleJobManager->isFinished());		
	}

	public function testRunJobsWith2Jobs()
	{
		$oJob1 = $this->getMockBuilder('oxTiramizoo_ScheduleJob')->disableOriginalConstructor()->setMethods(array('run'))->getMock();
		$oJob1->expects($this->exactly(1))->method('run');

		$oJob2 = $this->getMockBuilder('oxTiramizoo_ScheduleJob')->disableOriginalConstructor()->setMethods(array('run'))->getMock();
		$oJob2->expects($this->exactly(1))->method('run');

	    $aJobs = array($oJob1, $oJob2);

	    $oScheduleJobManager = $this->getMock('oxTiramizoo_ScheduleJobManager', array('getJobsForRun'));
	    $oScheduleJobManager->expects($this->any())->method('getJobsForRun')->will($this->returnValue($aJobs));
		$oScheduleJobManager->runJobs();

		$this->assertEquals(true, $oScheduleJobManager->isFinished());		
	}

	public function testAddTasks()
	{
	    $oScheduleJobManager = $this->getMock('oxTiramizoo_ScheduleJobManager', array('syncConfigDaily'));
	    $oScheduleJobManager->expects($this->exactly(1))->method('syncConfigDaily');
		
		$oScheduleJobManager->addTasks();
	}

	public function testSyncConfigDailyWithAlreadyExists()
	{
		$oSyncConfigJob = $this->getMockBuilder('oxTiramizoo_SyncConfigJob')->disableOriginalConstructor()->setMethods(array('getIdTodayByType', 'save'))->getMock();
	    $oSyncConfigJob->expects($this->any())->method('getIdTodayByType')->will($this->returnValue(null));
	    $oSyncConfigJob->expects($this->exactly(1))->method('save');

        oxTestModules::addModuleObject( "oxTiramizoo_SyncConfigJob", $oSyncConfigJob );

		$oScheduleJobManager = new oxTiramizoo_ScheduleJobManager();
		
		$this->assertEquals(true, $oScheduleJobManager->syncConfigDaily());		
	}

	public function testSyncConfigDailyWithoutAlreadyExists()
	{
		$oSyncConfigJob = $this->getMockBuilder('oxTiramizoo_SyncConfigJob')->disableOriginalConstructor()->setMethods(array('getIdTodayByType', 'save'))->getMock();
	    $oSyncConfigJob->expects($this->any())->method('getIdTodayByType')->will($this->returnValue(1));

        oxTestModules::addModuleObject( "oxTiramizoo_SyncConfigJob", $oSyncConfigJob );

		$oScheduleJobManager = new oxTiramizoo_ScheduleJobManager();
		
		$this->assertEquals(false, $oScheduleJobManager->syncConfigDaily());		
	}

	public function testGetJobsForRun()
	{
		$oScheduleJobList = $this->getMockBuilder('oxTiramizoo_ScheduleJobList')->disableOriginalConstructor()->setMethods(array('loadToRun'))->getMock();
	    $oScheduleJobList->expects($this->exactly(1))->method('loadToRun');

        oxTestModules::addModuleObject( "oxTiramizoo_ScheduleJobList", $oScheduleJobList );

		$oScheduleJobManager = new oxTiramizoo_ScheduleJobManager();
		
		$oScheduleJobManager->getJobsForRun();
	}

}
