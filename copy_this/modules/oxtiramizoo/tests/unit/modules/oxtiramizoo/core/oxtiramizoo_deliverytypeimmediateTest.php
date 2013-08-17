<?php

class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_DeliveryTypeImmediateTest extends OxidTestCase
{
	protected function setUp()
	{
		$this->_aTimeWindows = array();
	    $this->_oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);
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

		$this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue(array()));


	    $this->_oSubj = $this->getMock('oxTiramizoo_DeliveryTypeImmediate', array('getImmediateTimeWindow'), array($this->_oRetailLocation));

	    $this->_oSubj->expects($this->at(0))
	    	 ->method('getImmediateTimeWindow')
	    	 ->will($this->returnValue(true));

	    $this->assertEquals(true, $this->_oSubj->isAvailable());

	}

	public function testIsNotAvailable()
	{
		$this->_oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => 'me',
             								 'phone_number' => '5553333666')));

		$this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue(array()));

	    $this->_oSubj = $this->getMock('oxTiramizoo_DeliveryTypeImmediate', array('getImmediateTimeWindow'), array($this->_oRetailLocation));

	    $this->_oSubj->expects($this->any())
	    	 ->method('getImmediateTimeWindow')
	    	 ->will($this->returnValue(null));

	    $this->assertEquals(false, $this->_oSubj->isAvailable());
	}

	public function testIsNotAvailableIfParentIsNotAvailable()
	{
	    $this->_oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);

		$this->_oRetailLocation->expects($this->at(0))
             ->method('getConfVar')
             ->will($this->returnValue(0));

		$this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue(array()));

	    $this->_oSubj = $this->getMock('oxTiramizoo_DeliveryTypeImmediate', array('getImmediateTimeWindow'), array($this->_oRetailLocation));

	    $this->_oSubj->expects($this->any())
	    	 ->method('getImmediateTimeWindow')
	    	 ->will($this->returnValue(null));

	    $this->assertEquals(false, $this->_oSubj->isAvailable());
	}

	public function testIsNotAvailableIfNotEnabled()
	{
	    $oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);

		$oRetailLocation->expects($this->at(1))
             ->method('getConfVar')
             ->with('pickup_contact')
             ->will($this->returnValue(array('address_line_1' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => 'me',
             								 'phone_number' => '5553333666')));

		$oRetailLocation->expects($this->at(2))
             ->method('getConfVar')
             ->with('immediate_time_window_enabled')
             ->will($this->returnValue(0));

		$oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue(array()));

	    $this->_oSubj = $this->getMock('oxTiramizoo_DeliveryTypeImmediate', array('getImmediateTimeWindow'), array($oRetailLocation));

	    $this->_oSubj->expects($this->any())
	    	 ->method('getImmediateTimeWindow')
	    	 ->will($this->returnValue(null));

	    $this->assertEquals(false, $this->_oSubj->isAvailable());
	}


	public function testGetImmediateTimeWindow()
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
             ->method('getConfVar')
             ->will($this->returnValue($aPresetHours));

		$this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue($aTimeWindows));


        oxTiramizoo_Date::changeCurrentTime('2013-04-01T06:00:00Z');

        $this->_oSubj = new oxTiramizoo_DeliveryTypeImmediate($this->_oRetailLocation);

	    $this->assertEquals(new oxTiramizoo_TimeWindow($aTimeWindows[0]), $this->_oSubj->getImmediateTimeWindow());
	    $this->assertNotEquals(new oxTiramizoo_TimeWindow($aTimeWindows[1]), $this->_oSubj->getImmediateTimeWindow());
	    $this->assertEquals(new oxTiramizoo_TimeWindow($aTimeWindows[0]), $this->_oSubj->getDefaultTimeWindow());

	    //change date time
		if (date('I')) {
        	oxTiramizoo_Date::changeCurrentTime('2013-04-01T12:00:00Z');
		} else {
        	oxTiramizoo_Date::changeCurrentTime('2013-04-01T11:00:00Z');
		}

	    $this->assertEquals(new oxTiramizoo_TimeWindow($aTimeWindows[1]), $this->_oSubj->getImmediateTimeWindow());
	    $this->assertNotEquals(new oxTiramizoo_TimeWindow($aTimeWindows[0]), $this->_oSubj->getImmediateTimeWindow());
	    $this->assertEquals(new oxTiramizoo_TimeWindow($aTimeWindows[1]), $this->_oSubj->getDefaultTimeWindow());


	    //has time window
	    $oTiramizoo_TimeWindow = new oxTiramizoo_TimeWindow($aTimeWindows[1]);

	    $this->assertEquals(true, $this->_oSubj->hasTimeWindow($oTiramizoo_TimeWindow->getHash()));

	    // not exists time window
	    $aTmpTimeWindow = $aTimeWindows[1];
	    $aTmpTimeWindow['cut_off'] = 5;
	    $oTimeWindow = new oxTiramizoo_TimeWindow($aTmpTimeWindow);

	    $this->assertEquals(false, $this->_oSubj->hasTimeWindow($oTimeWindow->getHash()));
	}

	public function testGetImmediateTimeWindowNull()
	{
		$this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue(array()));

		$this->_oSubj = new oxTiramizoo_DeliveryTypeImmediate($this->_oRetailLocation);

	    $this->assertEquals(false, $this->_oSubj->getImmediateTimeWindow());
	}



}