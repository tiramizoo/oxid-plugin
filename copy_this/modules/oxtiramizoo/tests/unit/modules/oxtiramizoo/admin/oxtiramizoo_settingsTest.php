<?php


class Unit_Admin_oxTiramizoo_settingsTest extends OxidTestCase
{

    protected function setUp()
    {
        parent::setUp();

        $oxLang = $this->getMockBuilder('oxLang')->disableOriginalConstructor()->setMethods(array('translateString', 'getBaseLanguage'))->getMock();
        $map = array(
          array('oxTiramizoo_settings_api_url_label', null, true, 'oxTiramizoo_settings_api_url_label'),
          array('oxTiramizoo_settings_shop_url_label', null, true, 'oxTiramizoo_settings_shop_url_label'),
          array('oxTiramizoo_payments_required_error', null, true, 'oxTiramizoo_payments_required_error'),
          array('oxTiramizoo_is_required', null, true, 'oxTiramizoo_is_required'),
        );

        $oxLang->expects($this->any())
               ->method('translateString')
               ->will($this->returnValueMap($map));

        oxRegistry::set('oxLang', $oxLang);
    }

    protected function tearDown()
    {
        parent::tearDown();

        oxUtilsObject::resetClassInstances();
        oxRegistry::set('oxLang', null);      
    }

    public function testInit()
    {
        $oTiramizooSetup = $this->getMockBuilder('oxTiramizoo_setup')->disableOriginalConstructor()->setMethods(array('install'))->getMock();
        $oTiramizooSetup->expects($this->once())->method('install');

        oxTestModules::addModuleObject('oxTiramizoo_setup', $oTiramizooSetup);

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();
        $oTiramizooSettings->init();
    }

    public function testRender()
    {
        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getPaymentsList'))->getMock();

        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('getShopConfVar'))->getMock();
        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnValue(1));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $this->assertEquals('oxTiramizoo_settings.tpl', $oTiramizooSettings->render());
    }

    public function testGetPaymentsList()
    {
        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();
        
        $oPayment1 = oxNew('oxPayment');
        $oPayment1->oxpayments__oxid = new oxField(1);
        $oPayment1->oxpayments__oxdesc = new oxField('some payment 1');

        $oPayment2 = oxNew('oxPayment');
        $oPayment2->oxpayments__oxid = new oxField(2);
        $oPayment2->oxpayments__oxdesc = new oxField('some payment 2');

        $aPaymentList = array($oPayment1, $oPayment2);

        $oPaymentList = $this->getMockBuilder('Payment_List')->setMethods(array('getItemList', 'init'))->getMock();
        $oPaymentList->expects($this->any())
                     ->method('getItemList')
                     ->will($this->returnValue($aPaymentList));

        oxTestModules::addModuleObject('Payment_List', $oPaymentList);

        $oDb = $this->getMock('stdClass', array('getOne', 'quote'));

        $oDb->expects($this->at(0))->method('getOne')->will($this->returnValue(null));
        $oDb->expects($this->at(5))->method('getOne')->will($this->returnValue(1));

        modDb::getInstance()->modAttach($oDb);

        $aExpectedPaymentList = array(1 => array('desc' => 'some payment 1', 'checked' => false ),
                                      2 => array('desc' => 'some payment 2', 'checked' => true ));

        $this->assertEquals($aExpectedPaymentList, $oTiramizooSettings->getPaymentsList());
    }


    public function testAssignPaymentsToTiramizoo()
    {
        $aRequestParameters = array('oxidpayment1' => 0, 'oxidpayment2' => 1, 'oxidpayment3' => 1);

        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue($aRequestParameters));

        $oObject2Payment = $this->getMockBuilder('oxbase')->disableOriginalConstructor()->getMock();
        oxTestModules::addModuleObject('oxbase', $oObject2Payment);

        $oDb = $this->getMock('stdClass', array('Execute', 'quote', 'getOne'));
        $oDb->expects($this->exactly(2))->method('getOne');
        $oDb->expects($this->exactly(1))->method('Execute');

        modDb::getInstance()->modAttach($oDb);

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $oTiramizooSettings->assignPaymentsToTiramizoo();
    }

    public function testSaveConfVars()
    {
        $aConfBools = array('somename' => 'somevalue');
        $aConfStrs  = array('somename' => 'somevalue');
        $aConfArrs  = array('somename' => 'somevalue');
        $aConfAarrs = array('somename' => 'somevalue');
        $aConfInts  = array('somename' => 'somevalue');

        $aMap = array(array('confbools', false, $aConfBools),
                      array('confstrs', false, $aConfStrs),
                      array('confarrs', false, $aConfArrs),
                      array('confaarrs', false, $aConfAarrs),
                      array('confints', false, $aConfInts));

        $oConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($aMap));

        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('saveShopConfVar'))->getMock();

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $oTiramizooSettings->saveConfVars();
    }

    public function testTiramizooApiUrlHasChangedExpectTrue()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue(array('oxTiramizoo_api_url' => 'http://someapiurl')));

        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('getShopConfVar'))->getMock();
        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnValue('http://someapiurl2'));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $this->assertEquals(true, $oTiramizooSettings->tiramizooApiUrlHasChanged());
    }

    public function testTiramizooApiUrlHasChangedExpectFalse()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue(array('oxTiramizoo_api_url' => 'http://someapiurl')));

        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('getShopConfVar'))->getMock();
        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnValue('http://someapiurl'));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $this->assertEquals(false, $oTiramizooSettings->tiramizooApiUrlHasChanged());
    }

    public function testSaveEnableShippingMethod()
    {
        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('getShopId'))->getMock();
        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnValue('oxbaseshopid1'));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'validateEnable'))->getMock();
        $oTiramizooSettings->expects($this->at(0))
                           ->method('validateEnable')
                           ->will($this->returnValue(array('first error', 'second error')));

        $oDb = $this->getMock('stdClass', array('Execute'));
        modDb::getInstance()->modAttach($oDb);

        $oTiramizooSettings->saveEnableShippingMethod();
    }

    public function testSynchronize()
    {
        $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('getApiToken'))->getMock();

        $oRetailLocationList = $this->getMock('stdClass', array('loadAll', 'getArray'));
        $oRetailLocationList->expects($this->any())
                            ->method('getArray')
                            ->will($this->returnValue(array($oRetailLocation)));

        oxTestModules::addModuleObject('oxTiramizoo_RetailLocationList', $oRetailLocationList);

        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->setMethods(array('synchronizeAll'))->getMock();
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();

        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->synchronize());


        //set exception

        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->setMethods(array('synchronizeAll'))->getMock();
        $oTiramizooConfig->expects($this->any())
                         ->method('synchronizeAll')
                         ->will($this->throwException(new oxTiramizoo_ApiException('')));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $this->assertEquals('oxTiramizoo_settings.tpl', $oTiramizooSettings->synchronize());
    }


    public function testSave()
    {
        $oRetailLocation1 = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('getApiToken', 'delete'))->getMock();
        $oRetailLocation1->expects($this->once())
                         ->method('delete');
        $oRetailLocation1->expects($this->any())
                         ->method('getApiToken')
                         ->will($this->returnValue('some api save'));

        $oRetailLocation2 = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('getApiToken', 'delete'))->getMock();
        $oRetailLocation2->expects($this->never())
                         ->method('delete');

        $oRetailLocationList = $this->getMock('stdClass', array('loadAll', 'getArray'));
        $oRetailLocationList->expects($this->any())
                            ->method('getArray')
                            ->will($this->returnValue(array($oRetailLocation1, $oRetailLocation2)));

        oxTestModules::addModuleObject('oxTiramizoo_RetailLocationList', $oRetailLocationList);

        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->setMethods(array('synchronizeAll'))->getMock();
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->setMethods(array('getRemoteConfiguration'))->getMock();
        $oTiramizooApi->expects($this->at(0))
                      ->method('getRemoteConfiguration')
                      ->will($this->throwException(new oxTiramizoo_ApiException));

        oxTestModules::addModuleObject('oxTiramizoo_Api', $oTiramizooApi);

        //test if tiramizoo api url has not changed
        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'tiramizooApiUrlHasChanged', 'saveConfVars', 'saveEnableShippingMethod', 'assignPaymentsToTiramizoo'))->getMock();
        $oTiramizooSettings->expects($this->at(0))
                           ->method('tiramizooApiUrlHasChanged')
                           ->will($this->returnValue(false));
        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->save());


        //test if tiramizoo api url has changed
        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'tiramizooApiUrlHasChanged', 'saveConfVars', 'saveEnableShippingMethod', 'assignPaymentsToTiramizoo'))->getMock();
        $oTiramizooSettings->expects($this->at(0))
                           ->method('tiramizooApiUrlHasChanged')
                           ->will($this->returnValue(true));
        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->save());
    }

    public function testAddNewLocation()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue('someApiToken2'));

        $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('getApiToken', 'delete', 'save', 'getIdByApiToken', 'load'))->getMock();
        $oRetailLocation->expects($this->any())
                        ->method('getIdByApiToken')
                        ->will($this->returnValue('someOxid'));
        $oRetailLocation->expects($this->exactly(2))
                        ->method('save');
        $oRetailLocation->expects($this->exactly(1))
                        ->method('delete');


        oxTestModules::addModuleObject('oxTiramizoo_RetailLocation', $oRetailLocation);

        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->setMethods(array('synchronizeAll', 'getShopId'))->getMock();
        $oTiramizooConfig->expects($this->never())
                         ->method('synchronizeAll');
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooApi = $this->getMockBuilder('oxTiramizoo_Api')->disableOriginalConstructor()->setMethods(array('getRemoteConfiguration'))->getMock();
        $oTiramizooApi->expects($this->at(0))
                      ->method('getRemoteConfiguration')
                      ->will($this->throwException(new oxTiramizoo_ApiException));

        oxTestModules::addModuleObject('oxTiramizoo_Api', $oTiramizooApi);

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->addNewLocation());


        //test without exception in getting remote configuration
        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->setMethods(array('synchronizeAll', 'getShopId'))->getMock();
        $oTiramizooConfig->expects($this->once())
                        ->method('synchronizeAll');
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->addNewLocation());
    }

    public function testRemoveLocation()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue('someApiToken2'));

        $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('getApiToken', 'delete', 'save', 'getIdByApiToken', 'load'))->getMock();
        $oRetailLocation->expects($this->any())
                        ->method('getIdByApiToken')
                        ->will($this->returnValue('someOxid'));
        $oRetailLocation->expects($this->exactly(1))
                        ->method('delete');
        oxTestModules::addModuleObject('oxTiramizoo_RetailLocation', $oRetailLocation);

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->removeLocation());
    }

    public function testValidateEnable1()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();

        $map = array(
          array('confstrs', false, array()),
          array('payment', false, array()),
        );

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($map));

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));


        $aExpectdErrors = array('oxTiramizoo_settings_api_url_label oxTiramizoo_is_required', 'oxTiramizoo_settings_shop_url_label oxTiramizoo_is_required', 'oxTiramizoo_payments_required_error');

        $this->assertEquals($aExpectdErrors, $oTiramizooSettings->validateEnable());
    }

    public function testValidateEnable2()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();

        $map = array(
          array('confstrs', false, array('oxTiramizoo_api_url' => 'some api url')),
          array('payment', false, array()),
        );

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($map));

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));


        $aExpectdErrors = array('oxTiramizoo_settings_shop_url_label oxTiramizoo_is_required', 'oxTiramizoo_payments_required_error');

        $this->assertEquals($aExpectdErrors, $oTiramizooSettings->validateEnable());
    }

    public function testValidateEnable3()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();

        $map = array(
          array('confstrs', false, array('oxTiramizoo_api_url' => 'some api url', 'oxTiramizoo_shop_url' => 'some shop url')),
          array('payment', false, array()),
        );

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($map));

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));


        $aExpectdErrors = array('oxTiramizoo_payments_required_error');

        $this->assertEquals($aExpectdErrors, $oTiramizooSettings->validateEnable());
    }

    public function testValidateEnable4()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();

        $map = array(
          array('confstrs', false, array('oxTiramizoo_api_url' => 'some api url', 'oxTiramizoo_shop_url' => 'some shop url')),
          array('payment', false, array('payment name 1' => 0, 'payment name 2' => 1)),
        );

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($map));

        $oTiramizooSettings = $this->getMockBuilder('oxTiramizoo_settings')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));


        $aExpectdErrors = array();

        $this->assertEquals($aExpectdErrors, $oTiramizooSettings->validateEnable());
    }
}