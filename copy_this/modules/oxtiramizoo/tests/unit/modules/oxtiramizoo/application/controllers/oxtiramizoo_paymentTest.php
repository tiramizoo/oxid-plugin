<?php


class oxTiramizoo_PaymentExposed extends oxTiramizoo_Payment
{
	public $_aAllSets = null;
}

class Unit_Modules_oxTiramizoo_Application_Controllers_oxTiramizoo_PaymentTest extends OxidTestCase
{
    public function testInit()
    {
		$oTiramizooDeliverySet = $this->getMockBuilder('oxTiramizoo_DeliverySet')->disableOriginalConstructor()->getMock();
		$oTiramizooDeliverySet->expects($this->once())
					     	  ->method('init');

		oxRegistry::set('oxTiramizoo_DeliverySet', $oTiramizooDeliverySet);

		$oTiramizooPayment = oxNew('oxTiramizoo_Payment');
		$oTiramizooPayment->init();
    }

    public function testGetTiramizooDeliverySet()
    {
		$oTiramizooPayment = oxNew('oxTiramizoo_Payment');
		$this->assertInstanceOf('oxTiramizoo_DeliverySet', $oTiramizooPayment->getTiramizooDeliverySet());
    }

    public function testGetAllSetsTiramizooIsNotAvailable()
    {
  		$oTiramizooDeliverySet = $this->getMockBuilder('oxTiramizoo_DeliverySet')->disableOriginalConstructor()->getMock();
  		$oTiramizooDeliverySet->expects($this->any())
  					     	  ->method('isTiramizooAvailable')
  					     	  ->will($this->returnValue(false));

  		oxRegistry::set('oxTiramizoo_DeliverySet', $oTiramizooDeliverySet);


        $oUtils = $this->getMockBuilder('oxUtils')->disableOriginalConstructor()->getMock();
        oxRegistry::set('oxUtils', $oUtils);


        $oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->getMock();

    		$oSession = $this->getMockBuilder('oxSession')->disableOriginalConstructor()->getMock();
    		$map = array(array('sShipSet', 'Tiramizoo'));	
        $oSession->expects($this->any())->method('getVariable')->will($this->returnValueMap($map));
        $oSession->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));

        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->getMock();

    		$oTiramizooPayment = $this->getMock('oxTiramizoo_PaymentExposed', array('getSession'));
    		$oTiramizooPayment->expects($this->any())
          					      ->method('getSession')
          					      ->will($this->returnValue($oSession));
        $oTiramizooPayment->expects($this->any())
                          ->method('getConfig')
                          ->will($this->returnValue($oConfig));

    		$oTiramizooPayment->_aAllSets = array('some delivery' => oxNew('oxDeliverySet'), 'some delivery 2' => oxNew('oxDeliverySet'), 'Tiramizoo' => oxNew('oxDeliverySet'));

    		$this->assertNotContains('Tiramizoo', array_keys($oTiramizooPayment->getAllSets()));
    }

    public function testGetAllSetsTiramizooIsAvailable()
    {
        $oTiramizooDeliverySet = $this->getMockBuilder('oxTiramizoo_DeliverySet')->disableOriginalConstructor()->getMock();
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('isTiramizooAvailable')
                              ->will($this->returnValue(true));

        oxRegistry::set('oxTiramizoo_DeliverySet', $oTiramizooDeliverySet);


        $oUtils = $this->getMockBuilder('oxUtils')->disableOriginalConstructor()->getMock();
        oxRegistry::set('oxUtils', $oUtils);


        $oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->getMock();

        $oSession = $this->getMockBuilder('oxSession')->disableOriginalConstructor()->getMock();
        $map = array(array('sShipSet', 'Tiramizoo'));   
        $oSession->expects($this->any())->method('getVariable')->will($this->returnValueMap($map));
        $oSession->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));

        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->getMock();

        $oTiramizooPayment = $this->getMock('oxTiramizoo_PaymentExposed', array('getSession'));
        $oTiramizooPayment->expects($this->any())
                          ->method('getSession')
                          ->will($this->returnValue($oSession));
        $oTiramizooPayment->expects($this->any())
                          ->method('getConfig')
                          ->will($this->returnValue($oConfig));

        $oTiramizooPayment->_aAllSets = array('some delivery' => oxNew('oxDeliverySet'), 'some delivery 2' => oxNew('oxDeliverySet'), 'Tiramizoo' => oxNew('oxDeliverySet'));

        $this->assertContains('Tiramizoo', array_keys($oTiramizooPayment->getAllSets()));
    }

    public function testChangeshipping()
    {
        oxTiramizoo_Date::changeCurrentTime('2013-04-01 09:00:00');

        $aDataTodayWindow = array('delivery_type' => 'express',
                                  'cut_off' => '0',
                                  'pickup' => array('from' => '2013-04-01T12:00:00Z',
                                                      'to' => '2013-04-01T14:00:00Z'),

                                  'delivery' => array('from' => '2013-04-01T12:00:00Z',
                                                      'to' => '2013-04-01T14:00:00Z'));

        $oTimeWindow = oxNew('oxTiramizoo_TimeWindow', $aDataTodayWindow);

        $oTiramizooDeliveryTypeImmediate = $this->getMockBuilder('oxTiramizoo_DeliveryTypeImmediate')->disableOriginalConstructor()->getMock();
        $oTiramizooDeliveryTypeImmediate->expects($this->any())
                                        ->method('getDefaultTimeWindow')
                                        ->will($this->returnValue($oTimeWindow));
        $oTiramizooDeliveryTypeImmediate->expects($this->any())
                                        ->method('hasTimeWindow')
                                        ->will($this->returnValue(true));

        $oTiramizooDeliverySet = $this->getMockBuilder('oxTiramizoo_DeliverySet')->disableOriginalConstructor()->getMock();
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('isTiramizooAvailable')
                              ->will($this->returnValue(false));
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('getTiramizooDeliveryTypeObject')
                              ->will($this->returnValue($oTiramizooDeliveryTypeImmediate));
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('setSelectedTimeWindow')
                              ->will($this->throwException(new oxTiramizoo_InvalidTimeWindowException));

        oxRegistry::set('oxTiramizoo_DeliverySet', $oTiramizooDeliverySet);

        $oUtilsView = $this->getMockBuilder('oxUtilsView')->disableOriginalConstructor()->getMock();
        oxRegistry::set('oxUtilsView', $oUtilsView);

        $oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->getMock();

        $oSession = $this->getMockBuilder('oxSession')->disableOriginalConstructor()->getMock();
        $map = array(array('sShipSet', 'Tiramizoo'));   
        $oSession->expects($this->any())->method('getVariable')->will($this->returnValueMap($map));
        $oSession->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));

        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->getMock();
        $map = array(array('sTiramizooDeliveryType', false, 'immediate'),
                     array('sTiramizooTimeWindow', false, $oTimeWindow->getHash()));   
        $oConfig->expects($this->any())->method('getRequestParameter')->will($this->returnValueMap($map));


        $oTiramizooPayment = $this->getMock('oxTiramizoo_PaymentExposed', array('getSession', 'getConfig'));
        $oTiramizooPayment->expects($this->any())
                          ->method('getSession')
                          ->will($this->returnValue($oSession));
        $oTiramizooPayment->expects($this->any())
                          ->method('getConfig')
                          ->will($this->returnValue($oConfig));

        $oTiramizooPayment->_aAllSets = array('some delivery' => oxNew('oxDeliverySet'), 'some delivery 2' => oxNew('oxDeliverySet'), 'Tiramizoo' => oxNew('oxDeliverySet'));

        $oTiramizooPayment->changeshipping();
    }




    public function testRender()
    {
        oxTiramizoo_Date::changeCurrentTime('2013-04-01 09:00:00');

        $aDataTodayWindow = array('delivery_type' => 'express',
                                  'cut_off' => '0',
                                  'pickup' => array('from' => '2013-04-01T12:00:00Z',
                                                      'to' => '2013-04-01T14:00:00Z'),

                                  'delivery' => array('from' => '2013-04-01T12:00:00Z',
                                                      'to' => '2013-04-01T14:00:00Z'));

        $oTimeWindow = oxNew('oxTiramizoo_TimeWindow', $aDataTodayWindow);

        $oTiramizooDeliverySet = $this->getMockBuilder('oxTiramizoo_DeliverySet')->disableOriginalConstructor()->getMock();
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('isTiramizooAvailable')
                              ->will($this->returnValue(true));
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('getTiramizooDeliveryType')
                              ->will($this->returnValue('immediate'));
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('getSelectedTimeWindow')
                              ->will($this->returnValue($oTimeWindow));
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('getAvailableDeliveryTypes')
                              ->will($this->returnValue(array('immediate', 'evening')));

        oxRegistry::set('oxTiramizoo_DeliverySet', $oTiramizooDeliverySet);


        $oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->getMock();
        $oBasket->expects($this->any())
                ->method('getShippingId')
                ->will($this->returnValue('Tiramizoo'));

        $oSession = $this->getMockBuilder('oxSession')->disableOriginalConstructor()->getMock();
        $oSession->expects($this->any())
                 ->method('getBasket')
                 ->will($this->returnValue($oBasket));

        $oTiramizooPayment = $this->getMock('oxTiramizoo_Payment', array('getSession'));
        $oTiramizooPayment->expects($this->any())
                          ->method('getSession')
                          ->will($this->returnValue($oSession));


        $oTiramizooPayment->render();

        $this->assertEquals('Tiramizoo', $oTiramizooPayment->getViewDataElement('sCurrentShipSet'));
        $this->assertEquals('immediate', $oTiramizooPayment->getViewDataElement('sTiramizooDeliveryType'));
        $this->assertEquals($oTimeWindow->getHash(), $oTiramizooPayment->getViewDataElement('sSelectedTimeWindow'));
        $this->assertContains('immediate', $oTiramizooPayment->getViewDataElement('aAvailableDeliveryTypes'));
        $this->assertContains('evening', $oTiramizooPayment->getViewDataElement('aAvailableDeliveryTypes'));
    }
}