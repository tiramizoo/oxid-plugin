<?php


class unit_core_TiramizooApi_oxTiramizooApiTest extends OxidTestCase
{	
	protected function setUp()
	{
        parent::setUp();
	}

	public function testSendOrder()
	{
		$aResult = array('soma_api_result');

		$oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->setMethods(array('request'))->getMock();
		$oTiramizooApi->expects($this->any())->method('request')->will($this->returnValue($aResult));

		$this->assertEquals($aResult, $oTiramizooApi->sendOrder('someData'));
	}

	public function testGetAvailableServiceAreas()
	{
		$aResult = array('soma_api_result');

		$oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->setMethods(array('requestGet'))->getMock();
		$oTiramizooApi->expects($this->any())->method('requestGet')->will($this->returnValue($aResult));

		$this->assertEquals($aResult, $oTiramizooApi->getAvailableServiceAreas('someData'));
	}

	public function testGetRemoteConfiguration1()
	{
		$aResult = array('soma_api_result', 'http_status' => 200);

		$oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->setMethods(array('requestGet'))->getMock();
		$oTiramizooApi->expects($this->any())->method('requestGet')->will($this->returnValue($aResult));

		$this->assertEquals($aResult, $oTiramizooApi->getRemoteConfiguration('someData'));
	}

	public function testGetRemoteConfiguration2()
	{
		$this->setExpectedException('oxTiramizoo_ApiException');

		$aResult = array('soma_api_result', 'http_status' => 500);

		$oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->setMethods(array('requestGet'))->getMock();
		$oTiramizooApi->expects($this->any())->method('requestGet')->will($this->returnValue($aResult));

		$this->assertNotEquals($aResult, $oTiramizooApi->getRemoteConfiguration('someData'));
	}
}
