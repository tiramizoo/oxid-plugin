<?php

class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_DeliveryTypeSpecialTest extends OxidTestCase
{
	protected function setUp()
	{
	    $this->_oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);
	}

	public function testIsAvailable()
	{
		$this->_oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => 'me',
             								 'phone_number' => '5553333666')));

		$this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue(array()));

	    $this->_oSubj = $this->getMock('oxTiramizoo_DeliveryTypeSpecial', array('getSpecialTimeWindow'), array($this->_oRetailLocation));

	    $this->_oSubj->expects($this->any())
	    	 ->method('getSpecialTimeWindow')
	    	 ->will($this->returnValue(true));


	    $this->assertEquals(true, $this->_oSubj->isAvailable());
	}

	public function testIsNotAvailable()
	{
		$this->_oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array('address_line' => 'test',
             								 'postal_code' => '80639',
             								 'country_code' =>'de',
             								 'name' => 'me',
             								 'phone_number' => '5553333666')));

		$this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue(array()));

	    $this->_oSubj = $this->getMock('oxTiramizoo_DeliveryTypeSpecial', array('getSpecialTimeWindow'), array($this->_oRetailLocation));

	    $this->_oSubj->expects($this->any())
	    	 ->method('getSpecialTimeWindow')
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

	    $this->_oSubj = $this->getMock('oxTiramizoo_DeliveryTypeSpecial', array('getSpecialTimeWindow'), array( $this->_oRetailLocation));

	    $this->_oSubj->expects($this->any())
	    	 ->method('getSpecialTimeWindow')
	    	 ->will($this->returnValue(null));

	    $this->assertEquals(false, $this->_oSubj->isAvailable());
	}

	public function testGetSpecialTimeWindow()
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

        oxTiramizoo_Date::changeCurrentTime('2013-04-01T06:00:00Z');

        $this->_oSubj = new oxTiramizoo_DeliveryTypeSpecial($this->_oRetailLocation);

	    $this->assertEquals(new oxTiramizoo_TimeWindow($aTimeWindows[0]), $this->_oSubj->getSpecialTimeWindow());
	    $this->assertNotEquals(new oxTiramizoo_TimeWindow($aTimeWindows[1]), $this->_oSubj->getSpecialTimeWindow());
	    $this->assertEquals(new oxTiramizoo_TimeWindow($aTimeWindows[0]), $this->_oSubj->getDefaultTimeWindow());



	    $oTiramizoo_TimeWindow = new oxTiramizoo_TimeWindow($aTimeWindows[1]);

	    $this->assertEquals(true, $this->_oSubj->hasTimeWindow($oTiramizoo_TimeWindow->getHash()));

	    // not exists time window
	    $aTmpTimeWindow = $aTimeWindows[1];
	    $aTmpTimeWindow['cut_off'] = 5;
	    $oTimeWindow = new oxTiramizoo_TimeWindow($aTmpTimeWindow);

	    $this->assertEquals(false, $this->_oSubj->hasTimeWindow($oTimeWindow->getHash()));


	    // not preset hours
	    $this->_oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);
		$this->_oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue(array()));

        $this->_oSubj = new oxTiramizoo_DeliveryTypeSpecial($this->_oRetailLocation);


	    $this->assertEquals(null, $this->_oSubj->getSpecialTimeWindow());
	}

	public function testGetSpecialTimeWindowNull()
	{
		if (date('I')) {
			$aPresetHours = array('delivery_after' 	=> '18:00',
 								 'delivery_before' 	=> '20:00',
 								 'pickup_after' 	=> '18:00',
 								 'pickup_before' 	=> '20:00');
		} else {
			$aPresetHours = array('delivery_after' 	=> '17:00',
 								 'delivery_before' 	=> '19:00',
 								 'pickup_after' 	=> '17:00',
 								 'pickup_before' 	=> '19:00');
		}

		$this->_oRetailLocation->expects($this->any())
             ->method('getConfVar')
             ->will($this->returnValue($aPresetHours));

		$this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue(array()));

		$this->_oSubj = new oxTiramizoo_DeliveryTypeSpecial($this->_oRetailLocation);

	    $this->assertEquals(false, $this->_oSubj->getSpecialTimeWindow());
	}

    public function testIsTimeWindowValidForCurrentTime()
    {
        $this->_oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);

        $this->_oSubj = new oxTiramizoo_DeliveryTypeSpecial($this->_oRetailLocation);

        oxTiramizoo_Date::changeCurrentTime('2013-04-01T09:20:00Z');
        $oNextDayDate = new oxTiramizoo_Date('2013-04-02T05:00:00Z');

        $aTimeWindows = array(
            0 => array('delivery_type'  => 'standard',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-01T10:00:00Z',
                                                 'to'   => '2013-04-01T12:00:00Z'),
                      'pickup'          => array('from' => '2013-04-01T10:00:00Z',
                                                 'to'   => '2013-04-01T12:00:00Z'),
                      ),
            1 => array('delivery_type'  => 'standard',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-01T16:00:00Z',
                                                 'to'   => '2013-04-01T18:00:00Z'),
                      'pickup'          => array('from' => '2013-04-01T16:00:00Z',
                                                 'to'   => '2013-04-01T18:00:00Z'),
                       ),
            2 => array('delivery_type'  => 'standard',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-02T16:00:00Z',
                                                 'to'   => '2013-04-02T18:00:00Z'),
                      'pickup'          => array('from' => '2013-04-02T16:00:00Z',
                                                 'to'   => '2013-04-02T18:00:00Z'),
                       ),
            3 => array('delivery_type'  => 'standard',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-03T16:00:00Z',
                                                 'to'   => '2013-04-03T18:00:00Z'),
                      'pickup'          => array('from' => '2013-04-03T16:00:00Z',
                                                 'to'   => '2013-04-03T18:00:00Z'),
                       )
        );

        $oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindows[0]);
        $this->assertEquals(true, $this->_oSubj->isTimeWindowValidForCurrentTime($oTimeWindow, $oNextDayDate));

        $oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindows[1]);
        $this->assertEquals(true, $this->_oSubj->isTimeWindowValidForCurrentTime($oTimeWindow, $oNextDayDate));

        $oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindows[2]);
        $this->assertEquals(false, $this->_oSubj->isTimeWindowValidForCurrentTime($oTimeWindow, $oNextDayDate));

        //check if after 14:00
        oxTiramizoo_Date::changeCurrentTime('2013-04-01T16:20:00Z');

        $oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindows[2]);
        $this->assertEquals(true, $this->_oSubj->isTimeWindowValidForCurrentTime($oTimeWindow, $oNextDayDate));


        $oTimeWindow = new oxTiramizoo_TimeWindow($aTimeWindows[3]);
        $this->assertEquals(false, $this->_oSubj->isTimeWindowValidForCurrentTime($oTimeWindow, $oNextDayDate));
    }

    public function testGetAvailableTimeWindows()
    {

        oxTiramizoo_Date::changeCurrentTime('2013-04-01T08:20:00Z');
        $oNextDayDate = new oxTiramizoo_Date('2013-04-02T05:00:00Z');

        $aTimeWindows = array(
            0 => array('delivery_type'  => 'standard',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-01T10:00:00Z',
                                                 'to'   => '2013-04-01T12:00:00Z'),
                      'pickup'          => array('from' => '2013-04-01T10:00:00Z',
                                                 'to'   => '2013-04-01T12:00:00Z'),
                      ),
            1 => array('delivery_type'  => 'express',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-01T16:00:00Z',
                                                 'to'   => '2013-04-01T18:00:00Z'),
                      'pickup'          => array('from' => '2013-04-01T16:00:00Z',
                                                 'to'   => '2013-04-01T18:00:00Z'),
                       ),
            2 => array('delivery_type'  => 'standard',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-01T18:00:00Z',
                                                 'to'   => '2013-04-01T20:00:00Z'),
                      'pickup'          => array('from' => '2013-04-01T18:00:00Z',
                                                 'to'   => '2013-04-01T20:00:00Z'),
                       ),
            3 => array('delivery_type'  => 'standard',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-02T12:00:00Z',
                                                 'to'   => '2013-04-02T14:00:00Z'),
                      'pickup'          => array('from' => '2013-04-02T12:00:00Z',
                                                 'to'   => '2013-04-02T14:00:00Z'),
                       ),
            4 => array('delivery_type'  => 'standard',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-02T16:00:00Z',
                                                 'to'   => '2013-04-02T18:00:00Z'),
                      'pickup'          => array('from' => '2013-04-02T16:00:00Z',
                                                 'to'   => '2013-04-02T18:00:00Z'),
                       ),
            5 => array('delivery_type'  => 'standard',
                      'cut_off'         => 60,
                      'delivery'        => array('from' => '2013-04-03T16:00:00Z',
                                                 'to'   => '2013-04-03T18:00:00Z'),
                      'pickup'          => array('from' => '2013-04-03T16:00:00Z',
                                                 'to'   => '2013-04-03T18:00:00Z'),
                       )
        );

        $this->_oRetailLocation->expects($this->any())
             ->method('getAvailableTimeWindows')
             ->will($this->returnValue($aTimeWindows));

        $this->_oSubj = new oxTiramizoo_DeliveryTypeSpecial($this->_oRetailLocation);


        $aExceptedTimeWindows = array(
            new oxTiramizoo_TimeWindow($aTimeWindows[0]),
            new oxTiramizoo_TimeWindow($aTimeWindows[2]),
        );

        $this->assertEquals($aExceptedTimeWindows, $this->_oSubj->getAvailableTimeWindows());

        //check if after 14:00
        $this->_oSubj = new oxTiramizoo_DeliveryTypeSpecial($this->_oRetailLocation);

        oxTiramizoo_Date::changeCurrentTime('2013-04-01T15:20:00Z');

        $aExceptedTimeWindows = array(
            new oxTiramizoo_TimeWindow($aTimeWindows[2]),
            new oxTiramizoo_TimeWindow($aTimeWindows[3]),
            new oxTiramizoo_TimeWindow($aTimeWindows[4]),
        );

        $this->assertEquals($aExceptedTimeWindows, $this->_oSubj->getAvailableTimeWindows());


    }

}
