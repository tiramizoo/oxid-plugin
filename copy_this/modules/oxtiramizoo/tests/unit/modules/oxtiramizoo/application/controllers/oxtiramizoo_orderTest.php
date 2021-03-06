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
        $oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array(), array(), '', false);
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('isTiramizooAvailable')
                              ->will($this->returnValue(false));
        $oTiramizooDeliverySet->expects($this->once())
                              ->method('init');

        oxRegistry::set('oxTiramizoo_DeliverySet', $oTiramizooDeliverySet);

        $oSession = $this->getMock('oxSession', array(), array(), '', false);

        $oSession->expects($this->any())
                 ->method('getVariable')
                 ->will($this->returnCallback(function(){
                    $valueMap = array(
                        array('sShipSet', 'Tiramizoo')
                    );

                    return returnValueMap($valueMap, func_get_args());
                 }));

        $oUtils = $this->getMock('oxUtils', array(), array(), '', false);
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
        $this->asserttrue($oTiramizooOrder->getTiramizooDeliverySet() instanceof oxTiramizoo_DeliverySet);
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

        $oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array(), array(), '', false);
        $oTiramizooDeliverySet->expects($this->any())
                              ->method('getSelectedTimeWindow')
                              ->will($this->returnValue($oTimeWindow));
        oxRegistry::set('oxTiramizoo_DeliverySet', $oTiramizooDeliverySet);

        $oSession = $this->getMock('oxSession', array(), array(), '', false);
        $oSession->expects($this->any())
                 ->method('getVariable')
                 ->will($this->returnValue('Tiramizoo'));

        $oxLang = $this->getMock('oxLang', array('translateString', 'getBaseLanguage'), array(), '', false);

        $oxLang->expects($this->any())
               ->method('translateString')
               ->will($this->returnCallback(function(){
                    $valueMap = array(
                        array('oxTiramizoo_Today', null, false, 'Today')
                    );

                    return returnValueMap($valueMap, func_get_args());
               }));

        oxRegistry::set('oxLang', $oxLang);

        $oTiramizooOrder = $this->getMock('oxTiramizoo_Order', array('getSession'));
        $oTiramizooOrder->expects($this->any())
                        ->method('getSession')
                        ->will($this->returnValue($oSession));

        $oTiramizooOrder->render();

        $sExpectedString = 'Today 14:00 - 16:00';

        $this->assertEquals($sExpectedString, $oTiramizooOrder->getFormattedTiramizooTimeWindow());
    }

    public function testExecute()
    {
        $oUtilsView = $this->getMock('oxUtilsView', array(), array(), '', false);
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