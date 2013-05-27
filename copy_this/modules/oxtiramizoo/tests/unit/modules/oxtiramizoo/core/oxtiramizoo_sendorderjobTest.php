<?php



class Unit_Core_oxTiramizoo_SendOrderJobTest extends OxidTestCase
{	
	protected function setUp()
	{
        parent::setUp();
		$this->_oSubj = new oxTiramizoo_SendOrderJob();
		$this->_oSubj->save();
	}

	protected function tearDown()
	{
		$this->_oSubj->delete();
	}


	public function testRefreshJob()
	{
		$this->assertEquals(0, $this->_oSubj->getRepeats());
		
		$this->_oSubj->refreshJob();
		$this->assertEquals(1, $this->_oSubj->getRepeats());

		$this->_oSubj->refreshJob();
		$this->_oSubj->refreshJob();
		$this->assertEquals(3, $this->_oSubj->getRepeats());

		$this->_oSubj->finishJob();
	}


	public function testGetApiToken()
	{
		$this->_oSubj = new oxTiramizoo_SendOrderJob();
		$this->_oSubj->setParams(array('api_token' => 'some_api_token'));

		$this->assertEquals('some_api_token', $this->_oSubj->getApiToken());
	}

	public function testNotGetApiToken()
	{
		$this->_oSubj = new oxTiramizoo_SendOrderJob();

		$this->assertEquals(null, $this->_oSubj->getApiToken());
	}

	public function testRun1()
	{
		$oSendOrderJob = $this->getMockBuilder('oxTiramizoo_SendOrderJob')->disableOriginalConstructor()->setMethods(array('getRepeats', 'closeJob'))->getMock();
		$oSendOrderJob->expects($this->any())->method('getRepeats')->will($this->returnValue('10'));
		$oSendOrderJob->expects($this->once())->method('closeJob');

		$oSendOrderJob->run();
	}

	public function testRun2()
	{
		$oSendOrderJob = $this->getMockBuilder('oxTiramizoo_SendOrderJob')->disableOriginalConstructor()->setMethods(array('getRepeats', 'closeJob', 'getExternalId'))->getMock();
		$oSendOrderJob->expects($this->any())->method('getRepeats')->will($this->returnValue(oxTiramizoo_SendOrderJob::MAX_REPEATS + 10));
		$oSendOrderJob->expects($this->any())->method('getExternalId')->will($this->returnValue('1'));
		$oSendOrderJob->expects($this->once())->method('closeJob');

		$oOrder = $this->getMockBuilder('oxorder')->disableOriginalConstructor()->setMethods(array('load'))->getMock();
		$oOrder->expects($this->once())->method('load');

        oxTestModules::addModuleObject('oxorder', $oOrder);

		$oSendOrderJob->run();
	}

	// test if send status is 500
	public function testRun3()
	{
        $aApiResult = array('http_status' => 503);

		$oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->setMethods(array('sendOrder'))->getMock();
		$oTiramizooApi->expects($this->any())->method('sendOrder')->will($this->returnValue($aApiResult));

        oxTestModules::addModuleObject('oxTiramizoo_Api', $oTiramizooApi);

		$oSendOrderJob = $this->getMockBuilder('oxTiramizoo_SendOrderJob')->disableOriginalConstructor()->setMethods(array('getRepeats', 'closeJob', 'getExternalId', 'getApiToken', 'refreshJob'))->getMock();
		$oSendOrderJob->expects($this->any())->method('getRepeats')->will($this->returnValue(oxTiramizoo_SendOrderJob::MAX_REPEATS - 4));
		$oSendOrderJob->expects($this->any())->method('getExternalId')->will($this->returnValue('1'));
		$oSendOrderJob->expects($this->any())->method('getApiToken')->will($this->returnValue('some_api_token_run_3'));
		$oSendOrderJob->expects($this->never())->method('closeJob');
		$oSendOrderJob->expects($this->once())->method('refreshJob');

		$oOrder = $this->getMockBuilder('oxorder')->disableOriginalConstructor()->setMethods(array('load'))->getMock();
		$oOrder->expects($this->once())->method('load');

        oxTestModules::addModuleObject('oxorder', $oOrder);

		$oOrderExtended = $this->getMockBuilder('oxTiramizoo_OrderExtended')->disableOriginalConstructor()->setMethods(array('getIdByOrderId', 'load', 'save'))->getMock();
		$oOrderExtended->expects($this->once())->method('load');

        oxTestModules::addModuleObject('oxTiramizoo_OrderExtended', $oOrderExtended);

		$oSendOrderJob->run();
	}

	// test if send status is 201
	public function testRun4()
	{
        $aApiResult = array('http_status' => 201);

		$oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->setMethods(array('sendOrder'))->getMock();
		$oTiramizooApi->expects($this->any())->method('sendOrder')->will($this->returnValue($aApiResult));

        oxTestModules::addModuleObject('oxTiramizoo_Api', $oTiramizooApi);

		$oSendOrderJob = $this->getMockBuilder('oxTiramizoo_SendOrderJob')->disableOriginalConstructor()->setMethods(array('getRepeats', 'closeJob', 'getExternalId', 'getApiToken', 'refreshJob', 'finishJob'))->getMock();
		$oSendOrderJob->expects($this->any())->method('getRepeats')->will($this->returnValue(1));
		$oSendOrderJob->expects($this->any())->method('getExternalId')->will($this->returnValue('1'));
		$oSendOrderJob->expects($this->any())->method('getApiToken')->will($this->returnValue('some_api_token_run_4'));
		$oSendOrderJob->expects($this->never())->method('closeJob');
		$oSendOrderJob->expects($this->never())->method('refreshJob');
		$oSendOrderJob->expects($this->once())->method('finishJob');

		$oOrder = $this->getMockBuilder('oxorder')->disableOriginalConstructor()->setMethods(array('load'))->getMock();
		$oOrder->expects($this->once())->method('load');

        oxTestModules::addModuleObject('oxorder', $oOrder);

		$oOrderExtended = $this->getMockBuilder('oxTiramizoo_OrderExtended')->disableOriginalConstructor()->setMethods(array('getIdByOrderId', 'load', 'save'))->getMock();
		$oOrderExtended->expects($this->once())->method('load');

        oxTestModules::addModuleObject('oxTiramizoo_OrderExtended', $oOrderExtended);

		$oSendOrderJob->run();
	}


	public function testFinishJob()
	{
		$this->_oSubj->setExternalId(1);

		$oUser = oxNew('oxUser');

		$oOrder = $this->getMockBuilder('oxOrder')->disableOriginalConstructor()->setMethods(array('getOrderUser'))->getMock();
		$oOrder->expects($this->any())
	           ->method('getOrderUser')
	           ->will($this->returnValue($oUser));

        oxTestModules::addModuleObject('oxOrder', $oOrder);

		$oEmail = $this->getMock('oxEmail', array('send', 'setBody'));
		$oEmail->expects($this->once())
	           ->method('setBody');
        oxTestModules::addModuleObject('oxEmail', $oEmail);

		$this->_oSubj->finishJob();

		$this->assertEquals('finished', $this->_oSubj->oxtiramizooschedulejob__oxstate->value);

	}


}
