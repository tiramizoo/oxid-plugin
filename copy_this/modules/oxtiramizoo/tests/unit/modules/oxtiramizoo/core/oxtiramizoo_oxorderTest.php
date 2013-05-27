<?php


class oxTiramizoo_oxorderExposed extends oxTiramizoo_oxorder
{
    public function _loadFromBasket( oxBasket $oBasket )
    {
        parent::_loadFromBasket( $oBasket );
    }
}


class Unit_Core_oxTiramizoo_oxorderTest extends OxidTestCase
{
	protected function tearDown()
	{
		parent::tearDown();

	}

	public function testLoadFromBasket1()
	{
	    $oBasket = $this->getMock('oxbasket', array('getShippingId'));
	    $oBasket->expects($this->any())
             	->method('getShippingId')
             	->will($this->returnValue(oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID));

        $oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->getMock();
		$oTiramizooApi->expects($this->any())
	             	  ->method('sendOrder')
	             	  ->will($this->returnValue(array('http_status' => 201)));

        $oDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getSelectedTimeWindow', 'getRetailLocation', 'getTiramizooApi'));
		$oDeliverySet->expects($this->any())
	             	 ->method('getTiramizooApi')
	             	 ->will($this->returnValue($oTiramizooApi));

        oxRegistry::set('oxTiramizoo_DeliverySet', $oDeliverySet);


		$oCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderData')->disableOriginalConstructor()->getMock();
        oxTestModules::addModuleObject('oxTiramizoo_CreateOrderData', $oCreateOrderData);


        $oUser = $this->getMockBuilder('oxuser')->disableOriginalConstructor()->getMock();

     	$oOrder = $this->getMockBuilder('oxTiramizoo_oxorderExposed')->disableOriginalConstructor()->setMethods(array('getUser', 'getDelAddressInfo'))->getMock();
	    $oOrder->expects($this->any())
               ->method('getUser')
               ->will($this->returnValue($oUser));

        $oOrder->_loadFromBasket($oBasket);
	}

	public function testLoadFromBasket2()
	{
	    $oBasket = $this->getMock('oxbasket', array('getShippingId'));
	    $oBasket->expects($this->any())
             	->method('getShippingId')
             	->will($this->returnValue(oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID));

        $oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->getMock();
		$oTiramizooApi->expects($this->any())
	             	  ->method('sendOrder')
	             	  ->will($this->returnValue(array('http_status' => 500)));

        $oDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getSelectedTimeWindow', 'getRetailLocation', 'getTiramizooApi', 'getApiToken'));
		$oDeliverySet->expects($this->any())
	             	 ->method('getTiramizooApi')
	             	 ->will($this->returnValue($oTiramizooApi));

        oxRegistry::set('oxTiramizoo_DeliverySet', $oDeliverySet);


		$oCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderData')->disableOriginalConstructor()->getMock();
        oxTestModules::addModuleObject('oxTiramizoo_CreateOrderData', $oCreateOrderData);

		$oSendOrderJob = $this->getMockBuilder('oxTiramizoo_SendOrderJob')->disableOriginalConstructor()->getMock();
        oxTestModules::addModuleObject('oxTiramizoo_SendOrderJob', $oSendOrderJob);


        $oUser = $this->getMockBuilder('oxuser')->disableOriginalConstructor()->getMock();

     	$oOrder = $this->getMockBuilder('oxTiramizoo_oxorderExposed')->disableOriginalConstructor()->setMethods(array('getUser', 'getDelAddressInfo'))->getMock();
	    $oOrder->expects($this->any())
               ->method('getUser')
               ->will($this->returnValue($oUser));

        $oOrder->_loadFromBasket($oBasket);
	}

	public function testLoadFromBasket3()
	{
	    $oBasket = $this->getMock('oxbasket', array('getShippingId'));
	    $oBasket->expects($this->any())
             	->method('getShippingId')
             	->will($this->returnValue(oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID));

        $oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->getMock();
		$oTiramizooApi->expects($this->any())
	             	  ->method('sendOrder')
	             	  ->will($this->returnValue(array('http_status' => 0, 'errno' => oxTiramizoo_Api::CURLE_OPERATION_TIMEDOUT)));

        $oDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getSelectedTimeWindow', 'getRetailLocation', 'getTiramizooApi', 'getApiToken'));
		$oDeliverySet->expects($this->any())
	             	 ->method('getTiramizooApi')
	             	 ->will($this->returnValue($oTiramizooApi));
		$oDeliverySet->expects($this->any())
	             	 ->method('getApiToken')
	             	 ->will($this->returnValue('some api token'));

        oxRegistry::set('oxTiramizoo_DeliverySet', $oDeliverySet);


		$oCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderData')->disableOriginalConstructor()->getMock();
        oxTestModules::addModuleObject('oxTiramizoo_CreateOrderData', $oCreateOrderData);

		$oSendOrderJob = $this->getMockBuilder('oxTiramizoo_SendOrderJob')->disableOriginalConstructor()->getMock();
        oxTestModules::addModuleObject('oxTiramizoo_SendOrderJob', $oSendOrderJob);

		$oEmail = $this->getMock('oxEmail', array('send', 'setBody'));
		$oEmail->expects($this->once())
	           ->method('setBody');
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        $oUser = $this->getMockBuilder('oxuser')->disableOriginalConstructor()->getMock();

     	$oOrder = $this->getMockBuilder('oxTiramizoo_oxorderExposed')->disableOriginalConstructor()->setMethods(array('getUser', 'getDelAddressInfo'))->getMock();
	    $oOrder->expects($this->any())
               ->method('getUser')
               ->will($this->returnValue($oUser));

        $oOrder->_loadFromBasket($oBasket);
	}

	public function testGetOrderExtendedIfExists()
	{
        $oTiramizooOrderExtended = $this->getMockBuilder('oxTiramizoo_OrderExtended')->disableOriginalConstructor()->setMethods(array('getIdByOrderId', 'load'))->getMock();
        $oTiramizooOrderExtended->_sOXID = 1;

        oxTestModules::addModuleObject('oxTiramizoo_OrderExtended', $oTiramizooOrderExtended);

     	$oOrder = $this->getMockBuilder('oxTiramizoo_oxorder')->disableOriginalConstructor()->setMethods(array('getId'))->getMock();

        $this->assertEquals($oTiramizooOrderExtended, $oOrder->getOrderExtended());
        $this->assertEquals(1, $oOrder->getOrderExtended()->getId());
	}

	public function testGetOrderExtendedIfNotExists()
	{
        $oTiramizooOrderExtended = $this->getMockBuilder('oxTiramizoo_OrderExtended')->disableOriginalConstructor()->setMethods(array('getIdByOrderId', 'load'))->getMock();

        oxTestModules::addModuleObject('oxTiramizoo_OrderExtended', $oTiramizooOrderExtended);

     	$oOrder = $this->getMockBuilder('oxTiramizoo_oxorder')->disableOriginalConstructor()->setMethods(array('getId'))->getMock();

        $this->assertEquals($oTiramizooOrderExtended, $oOrder->getOrderExtended());
        $this->assertEquals(null, $oOrder->getOrderExtended()->getId());

	}

}
