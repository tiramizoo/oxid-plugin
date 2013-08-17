<?php



class Unit_Modules_oxTiramizoo_Admin_oxTiramizoo_settingsTest extends OxidTestCase
{

    protected function setUp()
    {
        parent::setUp();

        $oxLang = $this->getMock('oxLang', array('translateString', 'getBaseLanguage'), array(), '', false);

        $oxLang->expects($this->any())
               ->method('translateString')
               ->will($this->returnCallback(function(){
                    $valueMap = array(
                      array('oxTiramizoo_settings_api_url_label', null, true, 'oxTiramizoo_settings_api_url_label'),
                      array('oxTiramizoo_settings_shop_url_label', null, true, 'oxTiramizoo_settings_shop_url_label'),
                      array('oxTiramizoo_payments_required_error', null, true, 'oxTiramizoo_payments_required_error'),
                      array('oxTiramizoo_is_required', null, true, 'oxTiramizoo_is_required'),
                    );
                    
                    return returnValueMap($valueMap, func_get_args());
                }));

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
        $oTiramizooSetup = $this->getMock('oxTiramizoo_setup', array('install'), array(), '', false);
        $oTiramizooSetup->expects($this->once())->method('install');

        oxTestModules::addModuleObject('oxTiramizoo_setup', $oTiramizooSetup);

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct'), array(), '', false);
        $oTiramizooSettings->init();
    }

    public function testRender()
    {
        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getPaymentsList'), array(), '', false);

        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getShopConfVar'));
        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnValue(1));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $this->assertEquals('oxTiramizoo_settings.tpl', $oTiramizooSettings->render());
    }

    public function testGetPaymentsList()
    {
        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct'), array(), '', false);
        
        $oPayment1 = oxNew('oxPayment');
        $oPayment1->oxpayments__oxid = new oxField(1);
        $oPayment1->oxpayments__oxdesc = new oxField('some payment 1');

        $oPayment2 = oxNew('oxPayment');
        $oPayment2->oxpayments__oxid = new oxField(2);
        $oPayment2->oxpayments__oxdesc = new oxField('some payment 2');

        $aPaymentList = array($oPayment1, $oPayment2);

        $oPaymentList = $this->getMock('Payment_List', array('getItemList', 'init'));
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

        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue($aRequestParameters));

        $oObject2Payment = $this->getMock('oxbase', array(), array(), '', false);
        oxTestModules::addModuleObject('oxbase', $oObject2Payment);

        $oDb = $this->getMock('stdClass', array('Execute', 'quote', 'getOne'));
        $oDb->expects($this->exactly(2))->method('getOne');
        $oDb->expects($this->exactly(1))->method('Execute');

        modDb::getInstance()->modAttach($oDb);

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
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

        $oConfig = $this->getMock('oxTiramizoo_Config', array('getRequestParameter'), array(), '', false);
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnCallback(function(){
                    $valueMap = array(
                        array('confbools', $aConfBools),
                        array('confstrs', $aConfStrs),
                        array('confarrs', $aConfArrs),
                        array('confaarrs', $aConfAarrs),
                        array('confints', $aConfInts)                    
                    );
                    
                    return returnValueMap($valueMap, func_get_args());
                }));  

        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('saveShopConfVar'));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $oTiramizooSettings->saveConfVars();
    }

    public function testTiramizooApiUrlHasChangedExpectTrue()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue(array('oxTiramizoo_api_url' => 'http://someapiurl')));

        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getShopConfVar'));
        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnValue('http://someapiurl2'));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $this->assertEquals(true, $oTiramizooSettings->tiramizooApiUrlHasChanged());
    }

    public function testTiramizooApiUrlHasChangedExpectFalse()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue(array('oxTiramizoo_api_url' => 'http://someapiurl')));

        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getShopConfVar'));
        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnValue('http://someapiurl'));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $this->assertEquals(false, $oTiramizooSettings->tiramizooApiUrlHasChanged());
    }

    public function testSaveEnableShippingMethod()
    {
        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getShopId'));
        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnValue('oxbaseshopid1'));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'validateEnable'), array(), '', false);
        $oTiramizooSettings->expects($this->at(0))
                           ->method('validateEnable')
                           ->will($this->returnValue(array('first error', 'second error')));

        $oDb = $this->getMock('stdClass', array('Execute'));
        modDb::getInstance()->modAttach($oDb);

        $oTiramizooSettings->saveEnableShippingMethod();
    }

    public function testSynchronize()
    {
        $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array('getApiToken'), array(), '', false);

        $oRetailLocationList = $this->getMock('stdClass', array('loadAll', 'getArray'));
        $oRetailLocationList->expects($this->any())
                            ->method('getArray')
                            ->will($this->returnValue(array($oRetailLocation)));

        oxTestModules::addModuleObject('oxTiramizoo_RetailLocationList', $oRetailLocationList);

        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('synchronizeAll'), array(), '', false);
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct'), array(), '', false);

        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->synchronize());


        //set exception

        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('synchronizeAll'), array(), '', false);
        $oTiramizooConfig->expects($this->any())
                         ->method('synchronizeAll')
                         ->will($this->throwException(new oxTiramizoo_ApiException('')));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $this->assertEquals('oxTiramizoo_settings.tpl', $oTiramizooSettings->synchronize());
    }


    public function testSave()
    {
        $oRetailLocation1 = $this->getMock('oxTiramizoo_RetailLocation', array('getApiToken', 'delete'), array(), '', false);
        $oRetailLocation1->expects($this->once())
                         ->method('delete');
        $oRetailLocation1->expects($this->any())
                         ->method('getApiToken')
                         ->will($this->returnValue('some api save'));

        $oRetailLocation2 = $this->getMock('oxTiramizoo_RetailLocation', array('getApiToken', 'delete'), array(), '', false);
        $oRetailLocation2->expects($this->never())
                         ->method('delete');

        $oRetailLocationList = $this->getMock('stdClass', array('loadAll', 'getArray'));
        $oRetailLocationList->expects($this->any())
                            ->method('getArray')
                            ->will($this->returnValue(array($oRetailLocation1, $oRetailLocation2)));

        oxTestModules::addModuleObject('oxTiramizoo_RetailLocationList', $oRetailLocationList);

        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('synchronizeAll'), array(), '', false);
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooApi = $this->getMock('oxTiramizoo_Api', array('getRemoteConfiguration'), array(), '', false);
        $oTiramizooApi->expects($this->at(0))
                      ->method('getRemoteConfiguration')
                      ->will($this->throwException(new oxTiramizoo_ApiException));

        oxTestModules::addModuleObject('oxTiramizoo_Api', $oTiramizooApi);

        //test if tiramizoo api url has not changed
        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'tiramizooApiUrlHasChanged', 'saveConfVars', 'saveEnableShippingMethod', 'assignPaymentsToTiramizoo'), array(), '', false);
        $oTiramizooSettings->expects($this->at(0))
                           ->method('tiramizooApiUrlHasChanged')
                           ->will($this->returnValue(false));
        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->save());


        //test if tiramizoo api url has changed
        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'tiramizooApiUrlHasChanged', 'saveConfVars', 'saveEnableShippingMethod', 'assignPaymentsToTiramizoo'), array(), '', false);
        $oTiramizooSettings->expects($this->at(0))
                           ->method('tiramizooApiUrlHasChanged')
                           ->will($this->returnValue(true));
        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->save());
    }

    public function testAddNewLocation()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue('someApiToken2'));

        $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array('getApiToken', 'delete', 'save', 'getIdByApiToken', 'load'), array(), '', false);
        $oRetailLocation->expects($this->any())
                        ->method('getIdByApiToken')
                        ->will($this->returnValue('someOxid'));
        $oRetailLocation->expects($this->exactly(2))
                        ->method('save');
        $oRetailLocation->expects($this->exactly(1))
                        ->method('delete');


        oxTestModules::addModuleObject('oxTiramizoo_RetailLocation', $oRetailLocation);

        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('synchronizeAll', 'getShopId'), array(), '', false);
        $oTiramizooConfig->expects($this->never())
                         ->method('synchronizeAll');
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oTiramizooApi = $this->getMock('oxTiramizoo_Api', array('getRemoteConfiguration'), array(), '', false);
        $oTiramizooApi->expects($this->at(0))
                      ->method('getRemoteConfiguration')
                      ->will($this->throwException(new oxTiramizoo_ApiException));

        oxTestModules::addModuleObject('oxTiramizoo_Api', $oTiramizooApi);

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->addNewLocation());


        //test without exception in getting remote configuration
        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('synchronizeAll', 'getShopId'), array(), '', false);
        $oTiramizooConfig->expects($this->once())
                        ->method('synchronizeAll');
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->addNewLocation());
    }

    public function testRemoveLocation()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);
        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValue('someApiToken2'));

        $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array('getApiToken', 'delete', 'save', 'getIdByApiToken', 'load'), array(), '', false);
        $oRetailLocation->expects($this->any())
                        ->method('getIdByApiToken')
                        ->will($this->returnValue('someOxid'));
        $oRetailLocation->expects($this->exactly(1))
                        ->method('delete');
        oxTestModules::addModuleObject('oxTiramizoo_RetailLocation', $oRetailLocation);

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));

        $this->assertEquals('oxtiramizoo_settings', $oTiramizooSettings->removeLocation());
    }


    public function testValidateEnable1()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnCallback(function(){
                    $valueMap = array(
                      array('confstrs', array()),
                      array('payment', array()),
                    );
                    
                    return returnValueMap($valueMap, func_get_args());
                }));
    
        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));


        $aExpectdErrors = array('oxTiramizoo_settings_api_url_label oxTiramizoo_is_required', 'oxTiramizoo_settings_shop_url_label oxTiramizoo_is_required', 'oxTiramizoo_payments_required_error');

        $this->assertEquals($aExpectdErrors, $oTiramizooSettings->validateEnable());
    }

    public function testValidateEnable2()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnCallback(function(){
                    $valueMap = array(
                      array('confstrs', array('oxTiramizoo_api_url' => 'some api url')),
                      array('payment', array()),
                    );
                    
                    return returnValueMap($valueMap, func_get_args());
                }));

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));


        $aExpectdErrors = array('oxTiramizoo_settings_shop_url_label oxTiramizoo_is_required', 'oxTiramizoo_payments_required_error');

        $this->assertEquals($aExpectdErrors, $oTiramizooSettings->validateEnable());
    }

    public function testValidateEnable3()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnCallback(function(){
                    $valueMap = array(
                      array('confstrs', array('oxTiramizoo_api_url' => 'some api url', 'oxTiramizoo_shop_url' => 'some shop url')),
                      array('payment', array()),
                    );
                    
                    return returnValueMap($valueMap, func_get_args());
                }));

        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));


        $aExpectdErrors = array('oxTiramizoo_payments_required_error');

        $this->assertEquals($aExpectdErrors, $oTiramizooSettings->validateEnable());
    }

    public function testValidateEnable4()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnCallback(function(){
                    $valueMap = array(
                        array('confstrs', array('oxTiramizoo_api_url' => 'some api url', 'oxTiramizoo_shop_url' => 'some shop url')),
                        array('payment', array('payment name 1' => 0, 'payment name 2' => 1)),
                    );
                    
                    return returnValueMap($valueMap, func_get_args());
                }));  
                
        $oTiramizooSettings = $this->getMock('oxTiramizoo_settings', array('__construct', 'getConfig'), array(), '', false);
        $oTiramizooSettings->expects($this->any())
                           ->method('getConfig')
                           ->will($this->returnValue($oConfig));


        $aExpectdErrors = array();

        $this->assertEquals($aExpectdErrors, $oTiramizooSettings->validateEnable());
    }
}