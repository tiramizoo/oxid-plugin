<?php


class Unit_Modules_oxTiramizoo_Application_Controllers_oxTiramizoo_OrderTest extends OxidTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();

        oxUtilsObject::resetClassInstances();        
        oxRegistry::set('oxLang', null);
        oxRegistry::set('oxTiramizoo_DeliverySet', null);
        oxRegistry::set('oxUtils', null);
        oxRegistry::set('oxUtilsView', null);
    }

    public function testInit()
    {
        $oTiramizooDeliverySet = $this->getMockBuilder('oxTiramizoo_DeliverySet')->disableOriginalConstructor()->getMock();
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('isTiramizooAvailable')
                              ->will($this->returnValue(false));
        $oTiramizooDeliverySet->expects($this->once())
                              ->method('init');

        oxRegistry::set('oxTiramizoo_DeliverySet', $oTiramizooDeliverySet);

        $oSession = $this->getMockBuilder('oxSession')->disableOriginalConstructor()->getMock();
        $map = array(array('sShipSet', 'Tiramizoo'));   
        $oSession->expects($this->any())->method('getVariable')->will($this->returnValueMap($map));

        $oUtils = $this->getMockBuilder('oxUtils')->disableOriginalConstructor()->getMock();
        $oUtils->expects($this->once())
               ->method('redirect');

        oxRegistry::set('oxUtils', $oUtils);


        $oTiramizooOrder = $this->getMock('oxTiramizoo_Order', array('getSession', 'getUser'));
        $oTiramizooOrder->expects($this->any())
                        ->method('getSession')
                        ->will($this->returnValue($oSession));
        $oTiramizooOrder->init();
    }

    public function testGetTiramizooDeliverySet()
    {
        $oTiramizooOrder = oxNew('oxTiramizoo_Order');
        $this->assertInstanceOf('oxTiramizoo_DeliverySet', $oTiramizooOrder->getTiramizooDeliverySet());
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
                              ->method('getSelectedTimeWindow')
                              ->will($this->returnValue($oTimeWindow));
        oxRegistry::set('oxTiramizoo_DeliverySet', $oTiramizooDeliverySet);

        $oSession = $this->getMockBuilder('oxSession')->disableOriginalConstructor()->getMock();
        $oSession->expects($this->any())
                 ->method('getVariable')
                 ->will($this->returnValue('Tiramizoo'));

        $oxLang = $this->getMockBuilder('oxLang')->disableOriginalConstructor()->setMethods(array('translateString', 'getBaseLanguage'))->getMock();
        $map = array(
          array('oxTiramizoo_Today', null, false, 'Today')
        );

        $oxLang->expects($this->any())
               ->method('translateString')
               ->will($this->returnValueMap($map));

        oxRegistry::set('oxLang', $oxLang);

        $oTiramizooOrder = $this->getMock('oxTiramizoo_Order', array('getSession'));
        $oTiramizooOrder->expects($this->any())
                        ->method('getSession')
                        ->will($this->returnValue($oSession));

        $oTiramizooOrder->render();

        if (date('I') == '1') { 
            $sExpectedString = 'Today 14:00 - 16:00';
        } else {
            $sExpectedString = 'Today 13:00 - 15:00';
        }

        $this->assertEquals($sExpectedString, $oTiramizooOrder->getViewDataElement('sFormattedTiramizooTimeWindow'));
    }

    public function testExecute()
    {
        $oUtilsView = $this->getMockBuilder('oxUtilsView')->disableOriginalConstructor()->getMock();
        $oUtilsView->expects($this->once())
                   ->method('addErrorToDisplay');

        oxRegistry::set('oxUtilsView', $oUtilsView);

        $oTiramizooOrder = $this->getMock('oxTiramizoo_Order', array('getSession'));
        $oTiramizooOrder->expects($this->any())
                        ->method('getSession')
                        ->will($this->throwException(new oxTiramizoo_SendOrderException));

        $oTiramizooOrder->execute();
    }


}