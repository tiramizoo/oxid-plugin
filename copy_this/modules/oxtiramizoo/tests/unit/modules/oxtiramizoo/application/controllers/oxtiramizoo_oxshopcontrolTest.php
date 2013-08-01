<?php


class Unit_Modules_oxTiramizoo_Application_Controllers_oxTiramizoo_oxShopControlTest extends OxidTestCase
{
    protected function setUp()
    {
        parent::setUp();
        modDb::getInstance()->cleanup();
    }

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testProcess()
    {
		$oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array(), array(), '', false);
		$oTiramizooConfig->expects($this->any())
					     ->method('getShopConfVar')
					     ->will($this->returnValue(0));

		oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oScheduleJobManager = $this->getMock('oxTiramizoo_ScheduleJobManager', array(), array(), '', false);
		$oScheduleJobManager->expects($this->any())
					     	->method('isFinished')
					     	->will($this->returnValue(0));
		$oScheduleJobManager->expects($this->once())
					     	->method('isFinished');
		$oScheduleJobManager->expects($this->once())
					     	->method('runJobs');
		oxRegistry::set('oxTiramizoo_ScheduleJobManager', $oScheduleJobManager);

		$oShopControl = oxNew('oxTiramizoo_oxShopControl');
		$oShopControl->_process();
    }
}