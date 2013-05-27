<?php

class _oxTiramizoo_DeliverySet extends oxTiramizoo_DeliverySet
{
	public $_sDeliveryPostalcode = null;
	public $_sCurrentApiToken = null;
    public $_aDeliveryTypes = array('immediate', 'evening');
	public $_aAvailableDeliveryTypes = null;
	public $_sTiramizooDeliveryType = null;
	public $_oSelectedTimeWindow = null;
    public $_isInitialized = false;

}

class oxTiramizoo_DeliveryTypeTestIsNotAvailable extends oxTiramizoo_DeliveryType
{
	public function isAvailable() { return false; }
}

class oxTiramizoo_DeliveryTypeTestIsAvailable extends oxTiramizoo_DeliveryType
{
	public function isAvailable() { return true; }
}

class Unit_Core_oxTiramizoo_DeliverySetTest extends OxidTestCase
{
	public function setUp()
	{
        parent::setUp();
	}

	public function tearDown()
	{
        parent::tearDown();
        oxSession::deleteVar('sTiramizooDeliveryType');
        oxSession::deleteVar('sTiramizooTimeWindow');
	}


	// test if class has already initialized
	public function testInit1()
	{
		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('refreshDeliveryPostalCode'));
        $oTiramizooDeliverySet->expects($this->never())->method('refreshDeliveryPostalCode');
		$oTiramizooDeliverySet->_isInitialized = true;

		$oTiramizooDeliverySet->init(null, null);
	}

	// test if tiramizoo is not available
	public function testInit2()
	{
		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('refreshDeliveryPostalCode', 'isTiramizooAvailable', 'setTiramizooDeliveryType'));
        $oTiramizooDeliverySet->expects($this->any())->method('refreshDeliveryPostalCode')->will($this->returnValue(null));
        $oTiramizooDeliverySet->expects($this->any())->method('isTiramizooAvailable')->will($this->returnValue(false));
        $oTiramizooDeliverySet->expects($this->never())->method('setTiramizooDeliveryType');

		$oTiramizooDeliverySet->init(null, null);
	}

	// set is succesfull
	public function testInit3()
	{
		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('refreshDeliveryPostalCode', 'isTiramizooAvailable', 'setTiramizooDeliveryType', 'setSelectedTimeWindow', 'setDefaultDeliveryType'));
        $oTiramizooDeliverySet->expects($this->any())->method('refreshDeliveryPostalCode')->will($this->returnValue(null));
        $oTiramizooDeliverySet->expects($this->any())->method('isTiramizooAvailable')->will($this->returnValue(true));
        $oTiramizooDeliverySet->expects($this->any())->method('setTiramizooDeliveryType')->will($this->returnValue(false));
        $oTiramizooDeliverySet->expects($this->any())->method('setSelectedTimeWindow')->will($this->returnValue(false));
        $oTiramizooDeliverySet->expects($this->never())->method('setDefaultDeliveryType');

		$oTiramizooDeliverySet->init(null, null);
	}

	// delivery and time window is not specified
	public function testInit4()
	{
		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('refreshDeliveryPostalCode', 'isTiramizooAvailable', 'setTiramizooDeliveryType', 'setSelectedTimeWindow', 'setDefaultDeliveryType', 'setDefaultTimeWindow'));
        $oTiramizooDeliverySet->expects($this->any())->method('refreshDeliveryPostalCode')->will($this->returnValue(null));
        $oTiramizooDeliverySet->expects($this->any())->method('isTiramizooAvailable')->will($this->returnValue(true));
        $oTiramizooDeliverySet->expects($this->any())->method('setTiramizooDeliveryType')->will($this->throwException(new oxTiramizoo_InvalidDeliveryTypeException()));
        $oTiramizooDeliverySet->expects($this->any())->method('setSelectedTimeWindow')->will($this->throwException(new oxTiramizoo_InvalidTimeWindowException()));
        $oTiramizooDeliverySet->expects($this->any())->method('setDefaultDeliveryType')->will($this->returnValue(true));
        $oTiramizooDeliverySet->expects($this->any())->method('setDefaultTimeWindow')->will($this->returnValue(true));
        
        $oTiramizooDeliverySet->expects($this->once())->method('setDefaultDeliveryType');
        $oTiramizooDeliverySet->expects($this->once())->method('setDefaultTimeWindow');

		$oTiramizooDeliverySet->init(null, null);
	}

	// delivery and time window are not valid
	public function testInit5()
	{
		$oSession = $this->getMockBuilder('oxSession')->disableOriginalConstructor()->getMock();
        $oSession->expects($this->any())->method('getVariable')->will($this->returnValue('_someVariable'));

		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('refreshDeliveryPostalCode', 'isTiramizooAvailable', 'setTiramizooDeliveryType', 'setSelectedTimeWindow', 'setDefaultDeliveryType', 'setDefaultTimeWindow', 'getSession'));
        $oTiramizooDeliverySet->expects($this->any())->method('getSession')->will($this->returnValue($oSession));
        $oTiramizooDeliverySet->expects($this->any())->method('refreshDeliveryPostalCode')->will($this->returnValue(null));
        $oTiramizooDeliverySet->expects($this->any())->method('isTiramizooAvailable')->will($this->returnValue(true));
        $oTiramizooDeliverySet->expects($this->any())->method('setTiramizooDeliveryType')->will($this->throwException(new oxTiramizoo_InvalidDeliveryTypeException()));
        $oTiramizooDeliverySet->expects($this->any())->method('setSelectedTimeWindow')->will($this->throwException(new oxTiramizoo_InvalidTimeWindowException()));
        $oTiramizooDeliverySet->expects($this->any())->method('setDefaultDeliveryType')->will($this->returnValue(true));
        $oTiramizooDeliverySet->expects($this->any())->method('setDefaultTimeWindow')->will($this->returnValue(true));
        
        $oTiramizooDeliverySet->expects($this->once())->method('setDefaultDeliveryType');
        $oTiramizooDeliverySet->expects($this->once())->method('setDefaultTimeWindow');

        $oUtils = $this->getMock('oxUtils', array('redirect'));
        oxRegistry::set('oxUtils', $oUtils);

		$oTiramizooDeliverySet->init(null, null);
	}

	// test if not delivery types
	public function testSetDefaultDeliveryType1()
	{
		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will($this->returnValue(array()));

        $this->assertEquals(false, $oTiramizooDeliverySet->setDefaultDeliveryType());
	}

	// test if  delivery types
	public function testSetDefaultDeliveryType2()
	{
		$oDeliveryTypeTestIsAvailable = $this->getMockBuilder('oxTiramizoo_DeliveryTypeTestIsAvailable')->disableOriginalConstructor()->getMock();
        $oDeliveryTypeTestIsAvailable->expects($this->any())->method('getDefaultTimeWindow')->will($this->returnValue(null));

		$aAvailableDeliveryTypes = array('testIsAvailable' => $oDeliveryTypeTestIsAvailable);

		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes', 'setTiramizooDeliveryType'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will($this->returnValue($aAvailableDeliveryTypes));
        $oTiramizooDeliverySet->expects($this->any())->method('setTiramizooDeliveryType')->will($this->returnValue(null));

        $this->assertEquals(true, $oTiramizooDeliverySet->setDefaultDeliveryType());
	}

	// test if not delivery types
	public function testSetDefaultTimeWindow1()
	{
		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will($this->returnValue(array()));

        $this->assertEquals(false, $oTiramizooDeliverySet->setDefaultTimeWindow());
	}

	// test if has not default time window
	public function testSetDefaultTimeWindow3()
	{
		$oDeliveryTypeTestIsAvailable = $this->getMockBuilder('oxTiramizoo_DeliveryTypeTestIsAvailable')->disableOriginalConstructor()->getMock();
        $oDeliveryTypeTestIsAvailable->expects($this->any())->method('getDefaultTimeWindow')->will($this->returnValue(null));

		$aAvailableDeliveryTypes = array('testIsAvailable' => $oDeliveryTypeTestIsAvailable);

		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will($this->returnValue($aAvailableDeliveryTypes));

        $this->assertEquals(false, $oTiramizooDeliverySet->setDefaultTimeWindow());
	}

	// test if has default time window
	public function testSetDefaultTimeWindow2()
	{
		$oDeliveryTypeTestIsAvailable = $this->getMockBuilder('oxTiramizoo_DeliveryTypeTestIsAvailable')->disableOriginalConstructor()->getMock();
        $oDeliveryTypeTestIsAvailable->expects($this->any())->method('getDefaultTimeWindow')->will($this->returnValue(new oxTiramizoo_TimeWindow(array())));

		$aAvailableDeliveryTypes = array('testIsAvailable' => $oDeliveryTypeTestIsAvailable);

		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes', 'setSelectedTimeWindow'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will($this->returnValue($aAvailableDeliveryTypes));
        $oTiramizooDeliverySet->expects($this->any())->method('setSelectedTimeWindow')->will($this->returnValue(null));

        $this->assertEquals(true, $oTiramizooDeliverySet->setDefaultTimeWindow());
	}

	// test if is valid
	public function testSetSelectedTimeWindow1()
	{
		$oTimeWindow = $this->getMockBuilder('oxTiramizoo_TimeWindow')->disableOriginalConstructor()->getMock();
        $oTimeWindow->expects($this->any())->method('getHash')->will( $this->returnValue('somehash'));		
        $oTimeWindow->expects($this->any())->method('isValid')->will( $this->returnValue(true));

		$oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array('getTimeWindowByHash'));
        $oRetailLocation->expects($this->any())->method('getTimeWindowByHash')->will( $this->returnValue($oTimeWindow));

		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('getRetailLocation'));
        $oTiramizooDeliverySet->expects($this->any())->method('getRetailLocation')->will( $this->returnValue($oRetailLocation));

		$oTiramizooDeliverySet->setSelectedTimeWindow('somehash');

        $this->assertInstanceOf('oxTiramizoo_TimeWindow', $oTiramizooDeliverySet->_oSelectedTimeWindow);
	}

	// test if is not valid
	public function testSetSelectedTimeWindow2()
	{
        $this->setExpectedException('oxTiramizoo_InvalidTimeWindowException');

		$oTimeWindow = $this->getMockBuilder('oxTiramizoo_TimeWindow')->disableOriginalConstructor()->getMock();
        $oTimeWindow->expects($this->any())->method('getHash')->will( $this->returnValue('somehash'));		
        $oTimeWindow->expects($this->any())->method('isValid')->will( $this->returnValue(false));

		$oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array('getTimeWindowByHash'));
        $oRetailLocation->expects($this->any())->method('getTimeWindowByHash')->will( $this->returnValue($oTimeWindow));

		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('getRetailLocation'));
        $oTiramizooDeliverySet->expects($this->any())->method('getRetailLocation')->will( $this->returnValue($oRetailLocation));

		$oTiramizooDeliverySet->setSelectedTimeWindow('somehash');
	}

	// test if not get by hash
	public function testSetSelectedTimeWindow3()
	{
        $this->setExpectedException('oxTiramizoo_InvalidTimeWindowException');

		$oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array('getTimeWindowByHash'));
        $oRetailLocation->expects($this->any())->method('getTimeWindowByHash')->will( $this->returnValue(null));

		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('getRetailLocation'));
        $oTiramizooDeliverySet->expects($this->any())->method('getRetailLocation')->will( $this->returnValue($oRetailLocation));

		$oTiramizooDeliverySet->setSelectedTimeWindow('somehash');
	}

	// test if is valid
	public function testSetTiramizooDeliveryType1()
	{
		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('isTiramizooDeliveryTypeValid'));
        $oTiramizooDeliverySet->expects($this->any())->method('isTiramizooDeliveryTypeValid')->will( $this->returnValue(true));
        $oTiramizooDeliverySet->setTiramizooDeliveryType('immediate');

        $this->assertEquals('immediate', $oTiramizooDeliverySet->_sTiramizooDeliveryType);
	}

	// test if is not valid
	public function testSetTiramizooDeliveryType2()
	{
        $this->setExpectedException('oxTiramizoo_InvalidDeliveryTypeException');

		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('isTiramizooDeliveryTypeValid'));
        $oTiramizooDeliverySet->expects($this->any())->method('isTiramizooDeliveryTypeValid')->will( $this->returnValue(false));
        $oTiramizooDeliverySet->setTiramizooDeliveryType('immediate');
	}

	// test if isset available delivery types
	public function testGetAvailableDeliveryTypes1()
	{
		$oTiramizooDeliverySet = new _oxTiramizoo_DeliverySet();
		$oTiramizooDeliverySet->_aAvailableDeliveryTypes = array();

		$this->assertEquals(array(), $oTiramizooDeliverySet->getAvailableDeliveryTypes());
	}

	// test if one available
	public function testGetAvailableDeliveryTypes2()
	{
		$oTiramizooDeliverySet = $this->getMock('_oxTiramizoo_DeliverySet', array('getRetailLocation'));
		$oTiramizooDeliverySet->_aDeliveryTypes = array('testIsAvailable', 'testIsNotAvailable');
        $oTiramizooDeliverySet->expects($this->any())->method('getRetailLocation')->will( $this->returnValue(new oxTiramizoo_RetailLocation()));

		$oDeliveryTypeTestIsAvaialble = $this->getMockBuilder('oxTiramizoo_DeliveryTypeTestIsAvaialble')->disableOriginalConstructor()->getMock();

		$this->assertEquals(array('testIsAvailable' => new oxTiramizoo_DeliveryTypeTestIsAvailable(new oxTiramizoo_RetailLocation())), $oTiramizooDeliverySet->getAvailableDeliveryTypes());
	}

	// test if isset current api token
	public function testGetApiToken1()
	{
		$oTiramizooDeliverySet = new _oxTiramizoo_DeliverySet();
		$oTiramizooDeliverySet->_sCurrentApiToken = 'some_api_token';

		$this->assertEquals('some_api_token', $oTiramizooDeliverySet->getApiToken());
	}

	// test if postal codes match
	public function testGetApiToken2()
	{
        $oRetailLocation = new oxTiramizoo_RetailLocation();
        $oRetailLocation->setId('_test');
        $oRetailLocation->oxtiramizooretaillocation__oxapitoken = new oxField('some_api_token', oxField::T_RAW );
        $oRetailLocation->save();

        $oRetailLocationConfig = new oxTiramizoo_RetailLocationConfig();
        $oRetailLocationConfig->setId('_test');
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname = new oxField( 'postal_codes', oxField::T_RAW );
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarvalue = new oxField(base64_encode(serialize(array('111', '222', '333'))));
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxretaillocationid = new oxField( '_test', oxField::T_RAW );
        $oRetailLocationConfig->save();

        $oTiramizooDeliverySet = new _oxTiramizoo_DeliverySet();
        $oTiramizooDeliverySet->_sDeliveryPostalcode = '333';
		$this->assertEquals('some_api_token', $oTiramizooDeliverySet->getApiToken());

        $oRetailLocation->delete('_test');
	}

	// test if postal codes doesn't match
	public function testGetApiToken3()
	{
        $this->setExpectedException('oxTiramizoo_NotAvailableException');

        $oRetailLocation = new oxTiramizoo_RetailLocation();
        $oRetailLocation->setId('_test');
        $oRetailLocation->oxtiramizooretaillocation__oxapitoken = new oxField('some_api_token', oxField::T_RAW );
        $oRetailLocation->save();

        $oRetailLocationConfig = new oxTiramizoo_RetailLocationConfig();
        $oRetailLocationConfig->setId('_test');
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname = new oxField( 'postal_codes', oxField::T_RAW );
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarvalue = new oxField(base64_encode(serialize(array('123', '456', '789'))));
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxretaillocationid = new oxField( '_test', oxField::T_RAW );
        $oRetailLocationConfig->save();

        try
        {
	        $oTiramizooDeliverySet = new _oxTiramizoo_DeliverySet();
	        $oTiramizooDeliverySet->_sDeliveryPostalcode = '1111';
			$this->assertEquals('some_api_token', $oTiramizooDeliverySet->getApiToken());        	
        } catch (oxTiramizoo_NotAvailableException $oEx){
        	$oRetailLocation->delete('_test');
            throw new oxTiramizoo_NotAvailableException($oEx->getMessage());
        }
	}


	// test if retail location doesn't exist
	public function testGetRetailLocation1()
	{
        $this->setExpectedException('oxTiramizoo_NotAvailableException');

		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getApiToken'));
        $oTiramizooDeliverySet->expects($this->at(0))->method('getApiToken')->will( $this->returnValue('some_api_token'));

        $oTiramizooDeliverySet->getRetailLocation();
	}

	// test if retail location exists in database
	public function testGetRetailLocation2()
	{
        $oRetailLocation = new oxTiramizoo_RetailLocation();
        $oRetailLocation->setId('_test');
        $oRetailLocation->oxtiramizooretaillocation__oxapitoken = new oxField( 'some_api_token2', oxField::T_RAW );
        $oRetailLocation->save();


		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getApiToken'));
        $oTiramizooDeliverySet->expects($this->at(0))->method('getApiToken')->will( $this->returnValue('some_api_token2'));

        $this->assertInstanceOf('oxTiramizoo_RetailLocation', $oTiramizooDeliverySet->getRetailLocation());

        $oRetailLocation->delete('_test');
	}

	// test if not available delivery types
	public function testGetTiramizooDeliveryTypeObject1()
	{
		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will( $this->returnValue(array()));

		$this->assertEquals(null, $oTiramizooDeliverySet->getTiramizooDeliveryTypeObject());
	}

	// test if available delivery types but not exists
	public function testGetTiramizooDeliveryTypeObject2()
	{
		$oDeliveryType = $this->getMockBuilder('oxTiramizoo_DeliveryType')->disableOriginalConstructor()->getMock();

        $oDeliveryType->expects($this->at(0))->method('getType')->will( $this->returnValue('immediate'));
        $oDeliveryType->expects($this->at(1))->method('getType')->will( $this->returnValue('evening'));

		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes', 'getTiramizooDeliveryType'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will( $this->returnValue(array('immediate' 	=> $oDeliveryType ,
        																												   'evening' 	=> $oDeliveryType )));
        $oTiramizooDeliverySet->expects($this->any())->method('getTiramizooDeliveryType')->will( $this->returnValue('some_not_exists_type'));

		$this->assertEquals(null, $oTiramizooDeliverySet->getTiramizooDeliveryTypeObject());
	}

	// test if available delivery types if immediate
	public function testGetTiramizooDeliveryTypeObject3()
	{
		$oDeliveryTypeImmediate= $this->getMockBuilder('oxTiramizoo_DeliveryTypeImmediate')->disableOriginalConstructor()->getMock();
		$oDeliveryTypeEvening = $this->getMockBuilder('oxTiramizoo_DeliveryTypeEvening')->disableOriginalConstructor()->getMock();

        $oDeliveryTypeImmediate->expects($this->any())->method('getType')->will( $this->returnValue('immediate'));
        $oDeliveryTypeEvening->expects($this->any())->method('getType')->will( $this->returnValue('evening'));

		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes', 'getTiramizooDeliveryType'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will( $this->returnValue(array('immediate' 	=> $oDeliveryTypeImmediate ,
        																												   'evening' 	=> $oDeliveryTypeEvening )));
        $oTiramizooDeliverySet->expects($this->any())->method('getTiramizooDeliveryType')->will( $this->returnValue('immediate'));

		$this->assertInstanceOf('oxTiramizoo_DeliveryTypeImmediate', $oTiramizooDeliverySet->getTiramizooDeliveryTypeObject());
	}

	// test if available delivery types if evening
	public function testGetTiramizooDeliveryTypeObject4()
	{
		$oDeliveryTypeImmediate= $this->getMockBuilder('oxTiramizoo_DeliveryTypeImmediate')->disableOriginalConstructor()->getMock();
		$oDeliveryTypeEvening = $this->getMockBuilder('oxTiramizoo_DeliveryTypeEvening')->disableOriginalConstructor()->getMock();

        $oDeliveryTypeImmediate->expects($this->any())->method('getType')->will( $this->returnValue('immediate'));
        $oDeliveryTypeEvening->expects($this->any())->method('getType')->will( $this->returnValue('evening'));

		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes', 'getTiramizooDeliveryType'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will( $this->returnValue(array('immediate' 	=> $oDeliveryTypeImmediate ,
        																												   'evening' 	=> $oDeliveryTypeEvening )));
        $oTiramizooDeliverySet->expects($this->any())->method('getTiramizooDeliveryType')->will( $this->returnValue('evening'));

		$this->assertInstanceOf('oxTiramizoo_DeliveryTypeEvening', $oTiramizooDeliverySet->getTiramizooDeliveryTypeObject());
	}

	// test if no object passed
	public function testRefreshDeliveryPostalCode1()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();

		$this->assertEquals(null, $oTiramizooDeliverySet->refreshDeliveryPostalCode(null, null));
	}

	// test if only user object passed and has postal code
	public function testRefreshDeliveryPostalCodeU2()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();
		$oUser = oxnew('oxuser');
		$oUser->oxuser__oxzip = new oxField('80639');

		$this->assertEquals('80639', $oTiramizooDeliverySet->refreshDeliveryPostalCode($oUser, null));
	}

	// test if only user object passed and has not postal code
	public function testRefreshDeliveryPostalCode3()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();
		$oUser = oxnew('oxuser');

		$this->assertEquals(null, $oTiramizooDeliverySet->refreshDeliveryPostalCode($oUser, null));
	}

	// test if only address object passed and has postal code
	public function testRefreshDeliveryPostalCode4()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();
		$oDeliveryAddress = oxnew('oxAddress');
		$oDeliveryAddress->oxaddress__oxzip = new oxField('80639');

		$this->assertEquals('80639', $oTiramizooDeliverySet->refreshDeliveryPostalCode(null, $oDeliveryAddress));
	}

	// test if only address object passed and has not postal code
	public function testRefreshDeliveryPostalCode5()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();
		$oDeliveryAddress = oxnew('oxAddress');

		$this->assertEquals(null, $oTiramizooDeliverySet->refreshDeliveryPostalCode(null, $oDeliveryAddress));
	}

	// test if address and user object passed and has not postal code
	public function testRefreshDeliveryPostalCode6()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();
		$oUser = oxnew('oxuser');
		$oDeliveryAddress = oxnew('oxAddress');

		$this->assertEquals(null, $oTiramizooDeliverySet->refreshDeliveryPostalCode($oUser, $oDeliveryAddress));
	}

	// test if address and user object passed, delivery has postal code
	public function testRefreshDeliveryPostalCode7()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();
		$oUser = oxnew('oxuser');
		$oDeliveryAddress = oxnew('oxAddress');
		$oDeliveryAddress->oxaddress__oxzip = new oxField('80639');

		$this->assertEquals('80639', $oTiramizooDeliverySet->refreshDeliveryPostalCode($oUser, $oDeliveryAddress));
	}

	// test if address and user object passed, user has postal code
	public function testRefreshDeliveryPostalCode8()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();
		$oUser = oxnew('oxuser');
		$oUser->oxuser__oxzip = new oxField('80639');
		$oDeliveryAddress = oxnew('oxAddress');

		$this->assertEquals('80639', $oTiramizooDeliverySet->refreshDeliveryPostalCode($oUser, $oDeliveryAddress));
	}

	// test if address and user object passed, delivery and user has postal code
	public function testRefreshDeliveryPostalCode9()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();
		$oUser = oxnew('oxuser');
		$oUser->oxuser__oxzip = new oxField('80639');
		$oDeliveryAddress = oxnew('oxAddress');
		$oDeliveryAddress->oxaddress__oxzip = new oxField('12205');

		$this->assertEquals('12205', $oTiramizooDeliverySet->refreshDeliveryPostalCode($oUser, $oDeliveryAddress));
	}

	public function testGetTiramizooApi()
    {
		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getApiToken'));
        $oTiramizooDeliverySet->expects($this->any())->method('getApiToken')->will( $this->returnValue('api_token_123edca'));

		$this->assertInstanceOf('oxTiramizoo_Api', $oTiramizooDeliverySet->getTiramizooApi());
    }

	public function testGetSelectedTimeWindow()
    {
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();

		$this->assertEquals(null, $oTiramizooDeliverySet->getSelectedTimeWindow());
    }

	public function testGetTiramizooDeliveryType()
    {
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();

		$this->assertEquals(null, $oTiramizooDeliverySet->getTiramizooDeliveryType());
    }

	public function testIsTiramizooDeliveryTypeValid()
    {
		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getAvailableDeliveryTypes'));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will( $this->returnValue(array('immediate' => array())));

		$this->assertEquals(true, $oTiramizooDeliverySet->isTiramizooDeliveryTypeValid('immediate'));
		$this->assertEquals(false, $oTiramizooDeliverySet->isTiramizooDeliveryTypeValid('evening'));
    }

	public function testIsTiramizooAvailable()
	{
		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getRetailLocation', 'getAvailableDeliveryTypes', 'getBasket'));
        $oTiramizooDeliverySet->expects($this->any())->method('getRetailLocation')->will($this->returnValue(true));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will( $this->returnValue(array(1, 2, 3)));

        $oBasket = $this->getMock('oxBasket', array('isValid'));
        $oBasket->expects($this->any())->method('isValid')->will($this->returnValue(true));

        $oTiramizooDeliverySet->expects($this->any())->method('getBasket')->will( $this->returnValue($oBasket));

		$this->assertEquals(true, $oTiramizooDeliverySet->isTiramizooAvailable());
		$this->assertEquals(true, $oTiramizooDeliverySet->isTiramizooAvailable());
	}

	public function testIsTiramizooAvailableIfException()
	{
		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getRetailLocation', 'getAvailableDeliveryTypes', 'getBasket'));
        $oTiramizooDeliverySet->expects($this->any())->method('getRetailLocation')->will($this->throwException(new oxException));

        $oBasket = $this->getMock('oxBasket', array('isValid'));
        $oBasket->expects($this->any())->method('isValid')->will($this->returnValue(true));

        $oTiramizooDeliverySet->expects($this->any())->method('getBasket')->will( $this->returnValue($oBasket));

		$this->assertEquals(false, $oTiramizooDeliverySet->isTiramizooAvailable());
	}

	public function testIsTiramizooAvailableIfNoDeliveryTypes()
	{
		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getRetailLocation', 'getAvailableDeliveryTypes', 'getBasket'));
        $oTiramizooDeliverySet->expects($this->any())->method('getRetailLocation')->will($this->returnValue(true));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will($this->returnValue(array()));

        $oBasket = $this->getMock('oxBasket', array('isValid'));
        $oBasket->expects($this->any())->method('isValid')->will($this->returnValue(true));

        $oTiramizooDeliverySet->expects($this->any())->method('getBasket')->will( $this->returnValue($oBasket));

		$this->assertEquals(false, $oTiramizooDeliverySet->isTiramizooAvailable());
	}

	public function testIsTiramizooAvailableIfBasketInvalid()
	{
		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array('getRetailLocation', 'getAvailableDeliveryTypes', 'getBasket'));
        $oTiramizooDeliverySet->expects($this->any())->method('getRetailLocation')->will($this->returnValue(true));
        $oTiramizooDeliverySet->expects($this->any())->method('getAvailableDeliveryTypes')->will($this->returnValue(array()));

        $oBasket = $this->getMock('oxBasket', array('isValid'));
        $oBasket->expects($this->any())->method('isValid')->will($this->returnValue(false));

        $oTiramizooDeliverySet->expects($this->any())->method('getBasket')->will( $this->returnValue($oBasket));

		$this->assertEquals(false, $oTiramizooDeliverySet->isTiramizooAvailable());
	}

	public function testGetBasket()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();

		$this->assertInstanceOf('oxBasket', $oTiramizooDeliverySet->getBasket());
	}

	public function testGetSession()
	{
		$oTiramizooDeliverySet = new oxTiramizoo_DeliverySet();

		$this->assertInstanceOf('oxSession', $oTiramizooDeliverySet->getSession());
	}
}