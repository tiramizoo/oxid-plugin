<?php


class Unit_Application_Controllers_oxTiramizoo_oxShopControlTest extends OxidTestCase
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
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock();
		$oTiramizooConfig->expects($this->any())
					     ->method('getShopConfVar')
					     ->will($this->returnValue(0));

		oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oScheduleJobManager = $this->getMockBuilder('oxTiramizoo_ScheduleJobManager')->disableOriginalConstructor()->getMock();
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