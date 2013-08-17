<?php



class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_TimeWindowTest extends OxidTestCase
{
	protected $_oSubj = null;
	protected $_aDataTodayWindow = array();

	public function setUp()
	{
        parent::setUp();

        oxTiramizoo_Date::changeCurrentTime('2013-04-01 09:00:00');

        $this->_aDataTodayWindow = array('delivery_type' => 'express',
                			   			  'cut_off' => '0',
                			   		      'pickup' => array('from' => '2013-04-01T12:00:00Z',
                			   					   			  'to' => '2013-04-01T14:00:00Z'),

                                          'delivery' => array('from' => '2013-04-01T12:00:00Z',
                                                              'to' => '2013-04-01T14:00:00Z'));

        $this->_oSubj = new oxTiramizoo_TimeWindow($this->_aDataTodayWindow);
	}


    public function tearDown()
    {
        parent::tearDown();        

        oxRegistry::set('oxLang', null);        
    }

    public function testIsToday()
    {
        $this->assertEquals(true, $this->_oSubj->isToday());
    }

    public function testIsTomorrow()
    {
        oxTiramizoo_Date::changeCurrentTime('2013-03-31 09:00:00');
        $this->assertEquals(true, $this->_oSubj->isTomorrow());
    }

    public function testHasHours()
    {
        // daylight saving time
        if (date('I') == '1') { 
            $aHours = array('pickup_after' => '14:00', 
                            'pickup_before' => '16:00', 
                            'delivery_after' => '14:00', 
                            'delivery_before' => '16:00');
        } else {
            $aHours = array('pickup_after' => '13:00', 
                            'pickup_before' => '15:00', 
                            'delivery_after' => '13:00', 
                            'delivery_before' => '15:00');
        }

        $this->assertEquals(true, $this->_oSubj->hasHours($aHours));
    }

    public function testIsValid()
    {
        oxTiramizoo_Date::changeCurrentTime('2013-04-01 09:00:00');
        $this->assertEquals(true, $this->_oSubj->isValid());
    }

    public function testIsNotValid()
    {
        oxTiramizoo_Date::changeCurrentTime('2013-04-03 09:00:00');
        $this->assertEquals(false, $this->_oSubj->isValid());
    }

    public function testGetDeliveryHoursFormated()
    {
        // daylight saving time
        if (date('I') == '1') { 
            $sExpectedString = '14:00 - 16:00';
        } else {
            $sExpectedString = '13:00 - 15:00';
        }

        $this->assertEquals($sExpectedString, $this->_oSubj->getDeliveryHoursFormated());
    }

    public function testGetHash()
    {
        $this->assertEquals('c16f5ea1f0a860c7ebcfe5467fe216f0', $this->_oSubj->getHash());
    }

    public function testToString()
    {
        $this->assertEquals('c16f5ea1f0a860c7ebcfe5467fe216f0', $this->_oSubj->__toString());
    }

    public function testGetDeliveryFrom()
    {
        $this->assertEquals('2013-04-01T12:00:00Z', $this->_oSubj->getDeliveryFrom());
    }

    public function testGetDeliveryTo()
    {
        $this->assertEquals('2013-04-01T14:00:00Z', $this->_oSubj->getDeliveryTo());
    }

    public function testGetPickupFrom()
    {
        $this->assertEquals('2013-04-01T12:00:00Z', $this->_oSubj->getPickupFrom());
    }

    public function testGetPickupTo()
    {
        $this->assertEquals('2013-04-01T14:00:00Z', $this->_oSubj->getPickupTo());
    }

    public function testGetCutOff()
    {
        $this->assertEquals('0', $this->_oSubj->getCutOff());
    }

    public function testGetDeliveryType()
    {
        $this->assertEquals('express', $this->_oSubj->getDeliveryType());
    }

    public function testGetAsArray()
    {
        $this->assertEquals($this->_aDataTodayWindow, $this->_oSubj->getAsArray());
    }

    public function testGetFormattedDeliveryTimeWindowIfIsToday()
    {
        $oLang = $this->getMock('oxLang', array('translateString'), array(), '', false);
        $oLang->expects($this->any())->method('translateString')->will($this->returnValue('Today'));

        oxRegistry::set('oxLang', $oLang);

        //if isToday
        oxTiramizoo_Date::changeCurrentTime('2013-04-01 09:00:00');

        if (date('I') == '1') { 
            $sExpectedString = 'Today 14:00 - 16:00';
        } else {
            $sExpectedString = 'Today 13:00 - 15:00';
        }

        $this->assertEquals($sExpectedString, $this->_oSubj->getFormattedDeliveryTimeWindow());
    }

    public function testGetFormattedDeliveryTimeWindowIfIsTomorrow()
    {
        $oLang = $this->getMock('oxLang', array('translateString'), array(), '', false);
        $oLang->expects($this->any())->method('translateString')->will($this->returnValue('Tomorrow'));

        oxRegistry::set('oxLang', $oLang);
        //if isTomorrow
        oxTiramizoo_Date::changeCurrentTime('2013-03-31 09:00:00');

        if (date('I') == '1') { 
            $sExpectedString = 'Tomorrow 14:00 - 16:00';
        } else {
            $sExpectedString = 'Tomorrow 13:00 - 15:00';
        }

        $this->assertEquals($sExpectedString, $this->_oSubj->getFormattedDeliveryTimeWindow());
    }

    public function testGetFormattedDeliveryTimeWindowIfIsOtherDay()
    {
        $oLang = $this->getMock('oxLang', array('translateString'), array(), '', false);
        $oLang->expects($this->any())->method('translateString')->will($this->returnValue('Y/m/d'));

        oxRegistry::set('oxLang', $oLang);

        //if isTomorrow
        oxTiramizoo_Date::changeCurrentTime('2013-03-30 09:00:00');

        if (date('I') == '1') { 
            $sExpectedString = '2013/04/01 14:00 - 16:00';
        } else {
            $sExpectedString = '2013/04/01 13:00 - 15:00';
        }

        $mock = $this->getMock('oxLang', array('translateString'));

        $mock->expects($this->any())
             ->method('translateString')
             ->will($this->returnValue('Y/m/d'));

        oxRegistry::set('oxLang', $mock);

        $this->assertEquals($sExpectedString, $this->_oSubj->getFormattedDeliveryTimeWindow());
    }
}
