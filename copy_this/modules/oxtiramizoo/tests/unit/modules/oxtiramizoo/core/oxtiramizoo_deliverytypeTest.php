<?php

class oxTiramizoo_DeliveryTypeExposed extends oxTiramizoo_DeliveryType {}

class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_DeliveryTypeTest extends OxidTestCase
{
	protected function setUp()
	{
		$this->_aTimeWindows = array();
	    $this->_oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);

	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($this->_oRetailLocation));
	}

	public function testIsAvailable()
	{
		$oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);
		$oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => 'me', 
             								 'phone_number' => '5553333666')));
	    
	    $oxTiramizooDeliveryType = new oxTiramizoo_DeliveryTypeExposed($oRetailLocation);
	    $this->assertEquals(true, $oxTiramizooDeliveryType->isAvailable());
	}

	public function testIsNotAvailable()
	{

	    $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);

		$oRetailLocation->expects($this->at(0))
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => '',
             								 'postal_code' => '',
             								 'country_code' =>'',
             								 'name' => '', 
             								 'phone_number' => '')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($oRetailLocation));
	    $this->assertEquals(true, !$this->_oSubj->isAvailable());


	    $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);

		$oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '',
             								 'country_code' =>'',
             								 'name' => '', 
             								 'phone_number' => '')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($oRetailLocation));
	    $this->assertEquals(true, !$this->_oSubj->isAvailable());


	    $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);

		$oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'',
             								 'name' => '', 
             								 'phone_number' => '')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($oRetailLocation));
	    $this->assertEquals(true, !$this->_oSubj->isAvailable());




	    $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);

		$oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => '', 
             								 'phone_number' => '')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($oRetailLocation));
	    $this->assertEquals(true, !$this->_oSubj->isAvailable());


	    $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);

		$oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => 'test', 
             								 'phone_number' => '')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($oRetailLocation));
	    $this->assertEquals(true, !$this->_oSubj->isAvailable());
	}

	public function testGetRetailLocation()
	{
		$oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);
		$oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => 'me', 
             								 'phone_number' => '5553333666')));
	    
	    $oxTiramizooDeliveryType = new oxTiramizoo_DeliveryTypeExposed($oRetailLocation);

		$this->assertEquals($oRetailLocation, $oxTiramizooDeliveryType->getRetailLocation());	
	}

	public function testGetType()
	{
		$this->assertEquals($this->_sType, $this->_oSubj->getType());	
	}

	public function testGetName()
	{
		$oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);
	    $oxTiramizooDeliveryType = new oxTiramizoo_DeliveryTypeExposed($oRetailLocation);
		$this->assertEquals('oxTiramizoo_delivery_type__name', $oxTiramizooDeliveryType->getName());	
	}

	public function testGetTimeWindows()
	{
		$aTimeWindows = array(
						0 => array('delivery_type' 	=> 'standard',
								  'cut_off' 		=> 60,
								  'delivery'		=> array('from' => '2013-04-01T10:00:00Z',
								  							 'to' 	=> '2013-04-01T12:00:00Z'),
								  'pickup'			=> array('from' => '2013-04-01T10:00:00Z',
								  							 'to' 	=> '2013-04-01T12:00:00Z'),
								  ),
						1 => array('delivery_type' 	=> 'standard',
								  'cut_off' 		=> 60,
								  'delivery'		=> array('from' => '2013-04-01T16:00:00Z',
								  							 'to' 	=> '2013-04-01T18:00:00Z'),
								  'pickup'			=> array('from' => '2013-04-01T16:00:00Z',
								  							 'to' 	=> '2013-04-01T18:00:00Z'),
								  ));

		$oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);
		$oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue($aTimeWindows));

	    
	    $oxTiramizooDeliveryType = new oxTiramizoo_DeliveryTypeExposed($oRetailLocation);

		$this->assertEquals($aTimeWindows, $oxTiramizooDeliveryType->getTimeWindows());
	}


	public function testGetDefaultTimeWindow()
	{
		$aTimeWindows = array(
						0 => array('delivery_type' 	=> 'standard',
								  'cut_off' 		=> 60,
								  'delivery'		=> array('from' => '2013-04-01T10:00:00Z',
								  							 'to' 	=> '2013-04-01T12:00:00Z'),
								  'pickup'			=> array('from' => '2013-04-01T10:00:00Z',
								  							 'to' 	=> '2013-04-01T12:00:00Z'),
								  ),
						1 => array('delivery_type' 	=> 'standard',
								  'cut_off' 		=> 60,
								  'delivery'		=> array('from' => '2013-04-01T16:00:00Z',
								  							 'to' 	=> '2013-04-01T18:00:00Z'),
								  'pickup'			=> array('from' => '2013-04-01T16:00:00Z',
								  							 'to' 	=> '2013-04-01T18:00:00Z'),
								  ));

	    $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array('getAvailableTimeWindows'), array(), '', false);

		$oRetailLocation->expects($this->at(0))
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue($aTimeWindows));

	    $oxTiramizooDeliveryType = new oxTiramizoo_DeliveryTypeExposed($oRetailLocation);


	    $this->assertEquals((new oxTiramizoo_TimeWindow($aTimeWindows[0])), $oxTiramizooDeliveryType->getDefaultTimeWindow());

	    $oxTiramizooDeliveryType = new oxTiramizoo_DeliveryTypeExposed($oRetailLocation);

	    $this->assertEquals(null, $oxTiramizooDeliveryType->getDefaultTimeWindow());
	}

	public function testHasTimeWindow()
	{
		$aTimeWindows = array(
						0 => array('delivery_type' 	=> 'standard',
								  'cut_off' 		=> 60,
								  'delivery'		=> array('from' => '2013-04-01T10:00:00Z',
								  							 'to' 	=> '2013-04-01T12:00:00Z'),
								  'pickup'			=> array('from' => '2013-04-01T10:00:00Z',
								  							 'to' 	=> '2013-04-01T12:00:00Z'),
								  ),
						1 => array('delivery_type' 	=> 'standard',
								  'cut_off' 		=> 60,
								  'delivery'		=> array('from' => '2013-04-01T16:00:00Z',
								  							 'to' 	=> '2013-04-01T18:00:00Z'),
								  'pickup'			=> array('from' => '2013-04-01T16:00:00Z',
								  							 'to' 	=> '2013-04-01T18:00:00Z'),
								  ));

	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($this->_oRetailLocation));

	    $this->assertEquals(false, $this->_oSubj->hasTimeWindow($aTimeWindows[0]));
	}

}