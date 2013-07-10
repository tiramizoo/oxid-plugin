<?php


class oxTiramizoo_RetailLocationExposed extends oxTiramizoo_RetailLocation
{
    public $_aConfigVars = null;
}

class Unit_Modules_oxTiramizoo_Application_Models_oxTiramizoo_RetailLocationTest extends OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testGetIdByApiToken()
    {
        $oRetailLocation = oxNew('oxTiramizoo_RetailLocation');
        $oRetailLocation->oxtiramizooretaillocation__oxapitoken = new oxField('some api token');
        $oRetailLocation->oxtiramizooretaillocation__oxshopid = new oxField(oxRegistry::getConfig()->getShopId());
        $oRetailLocation->save();

        $this->assertEquals($oRetailLocation->getId(), $oRetailLocation->getIdByApiToken('some api token'));

        $oRetailLocation->delete();
    }

    public function testRefreshConfigVars()
    {
        $oRetailLocationConfig1 = oxNew('oxTiramizoo_RetailLocationConfig');
        $oRetailLocationConfig1->oxtiramizooretaillocationconfig__oxvarname = new oxField('config_1');
        $oRetailLocationConfig1->oxtiramizooretaillocationconfig__oxvarvalue = new oxField( base64_encode( serialize( 'config value 1' ) ) );

        $oRetailLocationConfig2 = oxNew('oxTiramizoo_RetailLocationConfig');
        $oRetailLocationConfig2->oxtiramizooretaillocationconfig__oxvarname = new oxField('config_2');
        $oRetailLocationConfig2->oxtiramizooretaillocationconfig__oxvarvalue = new oxField( base64_encode( serialize( 'config value 2' ) ) );

        $aRetailLocationConfigs = array($oRetailLocationConfig1, $oRetailLocationConfig2);

        $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocationExposed')->disableOriginalConstructor()->setMethods(array('__construct', 'getRetailLocationConfigs'))->getMock();
        $oRetailLocation->expects($this->any())
                        ->method('getRetailLocationConfigs')
                        ->will($this->returnValue($aRetailLocationConfigs));
        
        $oRetailLocation->refreshConfigVars();

        $aExpectedRetailLocationConfigs = array('config_1' => $oRetailLocationConfig1, 'config_2' => $oRetailLocationConfig2);

        $this->assertEquals($aExpectedRetailLocationConfigs, $oRetailLocation->_aConfigVars);
    }

    public function testGetConfVar()
    {
        $oRetailLocationConfig1 = oxNew('oxTiramizoo_RetailLocationConfig');
        $oRetailLocationConfig1->oxtiramizooretaillocationconfig__oxvarname = new oxField('config_1');
        $oRetailLocationConfig1->oxtiramizooretaillocationconfig__oxvarvalue = new oxField( base64_encode( serialize( 'config value 1' ) ) );

        $oRetailLocationConfig2 = oxNew('oxTiramizoo_RetailLocationConfig');
        $oRetailLocationConfig2->oxtiramizooretaillocationconfig__oxvarname = new oxField('config_2');
        $oRetailLocationConfig2->oxtiramizooretaillocationconfig__oxvarvalue = new oxField( base64_encode( serialize( 'config value 2' ) ) );

        $aRetailLocationConfigs = array($oRetailLocationConfig1, $oRetailLocationConfig2);

        $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('__construct', 'getRetailLocationConfigs'))->getMock();
        $oRetailLocation->expects($this->any())
                        ->method('getRetailLocationConfigs')
                        ->will($this->returnValue($aRetailLocationConfigs));
        
        $oRetailLocation->refreshConfigVars();

        $this->assertEquals('config value 1', $oRetailLocation->getConfVar('config_1'));
        $this->assertEquals(null, $oRetailLocation->getConfVar('config_3_no_exists'));
    }

    public function testGetApiToken()
    {
        $oRetailLocation = oxNew('oxTiramizoo_RetailLocation');
        $oRetailLocation->oxtiramizooretaillocation__oxapitoken = new oxField('some api token');
        
        $this->assertEquals('some api token', $oRetailLocation->getApiToken());
    }

    public function testSynchronizeConfiguration1()
    {
        $this->setExpectedException('oxTiramizoo_ApiException');

        $aRemoteConfiguration['http_status'] = 500;

        $oRetailLocation = oxNew('oxTiramizoo_RetailLocation');
        $oRetailLocation->synchronizeConfiguration($aRemoteConfiguration);
    }

    public function testSynchronizeConfiguration2()
    {
        $aRemoteConfiguration = array();
        $aRemoteConfiguration['http_status'] = 200;
        $aRemoteConfiguration['response'] = new stdClass;
        $aRemoteConfiguration['response']->some_param1 = 'some value 1';
        $aRemoteConfiguration['response']->some_param_array = array('some value 2');

        $oRetailLocationConfig = $this->getMockBuilder('oxTiramizoo_RetailLocationConfig')->disableOriginalConstructor()->getMock();

        $oRetailLocationConfig->expects($this->exactly(2))
                              ->method('save');

        oxTestModules::addModuleObject('oxTiramizoo_RetailLocationConfig', $oRetailLocationConfig);

        $oRetailLocation = oxNew('oxTiramizoo_RetailLocation');
        $oRetailLocation->synchronizeConfiguration($aRemoteConfiguration);
    }


    public function testDelete()
    {
        $oRetailLocationConfig1 = $this->getMockBuilder('oxTiramizoo_RetailLocationConfig')->disableOriginalConstructor()->getMock();
        $oRetailLocationConfig1->expects($this->once())
                               ->method('delete');

        $oRetailLocationConfig2 = $this->getMockBuilder('oxTiramizoo_RetailLocationConfig')->disableOriginalConstructor()->getMock();
        $oRetailLocationConfig2->expects($this->once())
                               ->method('delete');

        $aRetailLocationConfigs = array($oRetailLocationConfig1, $oRetailLocationConfig2);

        $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('__construct', 'getRetailLocationConfigs'))->getMock();
        $oRetailLocation->expects($this->any())
                        ->method('getRetailLocationConfigs')
                        ->will($this->returnValue($aRetailLocationConfigs));

        $oRetailLocation->delete();
    }

    public function testGetAvailableTimeWindows()
    {
        $aTimeWindows = array(0 => array('delivery_type' => 'standard',
                                        'cut_off' => '0',
                                        'pickup' => array('from' => '2013-04-02T12:00:00Z',
                                                          'to'   => '2013-04-02T14:00:00Z'),

                                        'delivery' => array('from' => '2013-04-02T12:00:00Z',
                                                            'to'   => '2013-04-02T14:00:00Z')),
                              1 => array('delivery_type' => 'express',
                                         'cut_off' => '0',
                                         'pickup' => array('from' => '2013-04-01T12:00:00Z',
                                                           'to'   => '2013-04-01T14:00:00Z'),

                                         'delivery' => array('from' => '2013-04-01T12:00:00Z',
                                                             'to'   => '2013-04-01T14:00:00Z')));

        $aExpectedTimeWindows = array('1364817600' => array('delivery_type' => 'express',
                                                            'cut_off' => '0',
                                                            'pickup' => array('from' => '2013-04-01T12:00:00Z',
                                                                              'to'   => '2013-04-01T14:00:00Z'),

                                                            'delivery' => array('from' => '2013-04-01T12:00:00Z',
                                                                                'to'   => '2013-04-01T14:00:00Z')),
                                     '1364904000' => array('delivery_type' => 'standard',
                                                           'cut_off' => '0',
                                                           'pickup' => array('from' => '2013-04-02T12:00:00Z',
                                                                             'to'   => '2013-04-02T14:00:00Z'),

                                                           'delivery' => array('from' => '2013-04-02T12:00:00Z',
                                                                               'to'   => '2013-04-02T14:00:00Z')));


        $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfVar'))->getMock();
        $oRetailLocation->expects($this->any())
                        ->method('getConfVar')
                        ->will($this->returnValue($aTimeWindows));

        $this->assertEquals($aExpectedTimeWindows, $oRetailLocation->getAvailableTimeWindows());
    }

    public function testGetTimeWindowByHash()
    {
        $aTimeWindows = array('1364817600' => array('delivery_type' => 'express',
                                                    'cut_off' => '0',
                                                    'pickup' => array('from' => '2013-04-01T12:00:00Z',
                                                                      'to'   => '2013-04-01T14:00:00Z'),

                                                    'delivery' => array('from' => '2013-04-01T12:00:00Z',
                                                                        'to'   => '2013-04-01T14:00:00Z')),
                              '1364904000' => array('delivery_type' => 'standard',
                                                    'cut_off' => '0',
                                                    'pickup' => array('from' => '2013-04-02T12:00:00Z',
                                                                      'to'   => '2013-04-02T14:00:00Z'),

                                                    'delivery' => array('from' => '2013-04-02T12:00:00Z',
                                                                        'to'   => '2013-04-02T14:00:00Z')));

        $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('__construct', 'getAvailableTimeWindows'))->getMock();
        $oRetailLocation->expects($this->any())
                        ->method('getAvailableTimeWindows')
                        ->will($this->returnValue($aTimeWindows));

        $oExpectedTimeWindow = oxNew('oxTiramizoo_TimeWindow', $aTimeWindows['1364817600']);

        $this->assertEquals($oExpectedTimeWindow, $oRetailLocation->getTimeWindowByHash('c16f5ea1f0a860c7ebcfe5467fe216f0'));
        //test random hash
        $this->assertEquals(null, $oRetailLocation->getTimeWindowByHash('234b5ea1f0a860c7ebcfe5467fe216f0'));
    }

    public function testobjectToArray()
    {
        $oRetailLocation = new oxTiramizoo_RetailLocation();

        $oObject = new stdClass();
        $oObject->some_property_1 = 'value 1';
        $oObject->some_property_2 = 'value 2';
        $oObject->some_property_3 = array('property 3' => 'value 3', 'property 4' => 'value 4');

        $aExpectedArray = array(
            'some_property_1'   => 'value 1',
            'some_property_2'   => 'value 2',
            'some_property_3'   => array('property 3' => 'value 3', 'property 4' => 'value 4')
        );

        $this->assertEquals($aExpectedArray, $oRetailLocation->objectToArray($oObject));
    }
}