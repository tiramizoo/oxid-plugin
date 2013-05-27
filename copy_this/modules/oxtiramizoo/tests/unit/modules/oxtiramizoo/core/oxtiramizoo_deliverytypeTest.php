<?php

class Unit_Core_oxTiramizoo_DeliveryTypeTest extends OxidTestCase
{
	protected function setUp()
	{
		$this->_aTimeWindows = array();
	    $this->_oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->getMock();

	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($this->_oRetailLocation));
	}

	public function testIsAvailable()
	{
		$this->_oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => 'me', 
             								 'phone_number' => '5553333666')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($this->_oRetailLocation));
	    $this->assertEquals(true, $this->_oSubj->isAvailable());
	}

	public function testIsNotAvailable()
	{

	    $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->getMock();

		$oRetailLocation->expects($this->at(0))
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => '',
             								 'postal_code' => '',
             								 'country_code' =>'',
             								 'name' => '', 
             								 'phone_number' => '')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($oRetailLocation));
	    $this->assertEquals(true, !$this->_oSubj->isAvailable());


	    $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->getMock();

		$oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '',
             								 'country_code' =>'',
             								 'name' => '', 
             								 'phone_number' => '')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($oRetailLocation));
	    $this->assertEquals(true, !$this->_oSubj->isAvailable());


	    $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->getMock();

		$oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'',
             								 'name' => '', 
             								 'phone_number' => '')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($oRetailLocation));
	    $this->assertEquals(true, !$this->_oSubj->isAvailable());




	    $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->getMock();

		$oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => '', 
             								 'phone_number' => '')));
	    
	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($oRetailLocation));
	    $this->assertEquals(true, !$this->_oSubj->isAvailable());


	    $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->getMock();

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
		$this->assertEquals($this->_oRetailLocation, $this->_oSubj->getRetailLocation());	
	}

	public function testGetType()
	{
		$this->assertEquals($this->_sType, $this->_oSubj->getType());	
	}

	public function testGetName()
	{
		$this->assertEquals('oxTiramizoo_delivery_type__name', $this->_oSubj->getName());	
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

		$this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue($aTimeWindows));

  	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($this->_oRetailLocation));

		$this->assertEquals($aTimeWindows, $this->_oSubj->getTimeWindows());
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

		$this->_oRetailLocation->expects($this->at(0))
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue($aTimeWindows));

	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($this->_oRetailLocation));
	    $this->assertEquals(new oxTiramizoo_TimeWindow($aTimeWindows[0]), $this->_oSubj->getDefaultTimeWindow());


	    $this->_oSubj = $this->getMockForAbstractClass('oxTiramizoo_DeliveryType', array($this->_oRetailLocation));
	    $this->assertEquals(null, $this->_oSubj->getDefaultTimeWindow());
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