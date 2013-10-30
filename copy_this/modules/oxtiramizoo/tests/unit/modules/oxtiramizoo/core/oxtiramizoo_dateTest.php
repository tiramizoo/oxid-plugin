<?php



class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_DateTest extends OxidTestCase
{
	public function setUp()
	{
        parent::setUp();
	}

	public function testGetYmdHis()
	{
		$oDate = new oxTiramizoo_Date('2013-04-01 05:04:02');
		$this->assertEquals('2013-04-01 05:04:02', $oDate->get('Y-m-d H:i:s'));
	}

	public function testGetYmd()
	{
		$oDate = new oxTiramizoo_Date('2013-04-01 05:04:02');
		$this->assertEquals('2013-04-01', $oDate->get('Y-m-d'));
	}

	public function testGetForRestApi()
	{
		$oDate = new oxTiramizoo_Date('2013-04-01 05:04:02');

        $sExpectedDate = '2013-04-01T03:04:02Z';

		$this->assertEquals($sExpectedDate, $oDate->getForRestApi());
	}

	public function testCreationDate()
	{
        oxTiramizoo_Date::changeCurrentTime(date('Y-m-d H:i:s'));

		$oDate = new oxTiramizoo_Date();
		$sDate = date('Y-m-d H:i:s');
		$this->assertEquals($sDate, $oDate->get('Y-m-d H:i:s'));
	}

	public function testToString()
	{
		$oDate = new oxTiramizoo_Date('2013-04-01 05:04:02');
		$this->assertEquals('2013-04-01 05:04:02', $oDate->__toString());
	}

	public function testCreationDateWithChangedCurrentTime()
	{
        oxTiramizoo_Date::changeCurrentTime('2013-04-01 09:00:00');

		$oDate = new oxTiramizoo_Date();
		$sDate = '2013-04-01 09:00:00';
		$this->assertEquals($sDate, $oDate->get('Y-m-d H:i:s'));
	}

	public function testIsToday()
	{
        oxTiramizoo_Date::changeCurrentTime('2013-04-01 09:00:00');

		$oDate = new oxTiramizoo_Date('2013-04-01 05:04:02');
		$this->assertEquals(true, $oDate->isToday());
	}

	public function testIsTomorrowDirectly()
	{
        oxTiramizoo_Date::changeCurrentTime('2013-04-01 09:00:00');

		$oDate = new oxTiramizoo_Date('2013-04-02 05:04:02');
		$this->assertEquals(true, $oDate->isTomorrow());
	}

	public function testIsNotTomorrowDirectly()
	{
        oxTiramizoo_Date::changeCurrentTime('2013-04-02 09:00:00');

		$oDate = new oxTiramizoo_Date('2013-04-02 05:04:02');
		$this->assertEquals(false, $oDate->isTomorrow());
	}

	public function testIsTomorrowByAddOneDay()
	{
        oxTiramizoo_Date::resetCurrentTime();

		$oDate = new oxTiramizoo_Date('+1 Day');
		$this->assertEquals(true, $oDate->isTomorrow());
	}

	public function testIsOnTimeHis()
	{
		$oDate = new oxTiramizoo_Date('2013-04-02 05:04:02');
		$this->assertEquals(true, $oDate->isOnTime('05:04:02'));
	}

	public function testIsOnTimeHi()
	{
		$oDate = new oxTiramizoo_Date('2013-04-02 05:04:02');
		$this->assertEquals(true, $oDate->isOnTime('05:04'));
	}

	public function testIsOnTimeH()
	{
		$oDate = new oxTiramizoo_Date('2013-04-02 05:04:02');
		$this->assertEquals(true, $oDate->isOnTime('05'));
	}

	public function testModify1Minute()
	{
		$oDate = new oxTiramizoo_Date('2013-04-02 05:04:02');
		$oDate->modify('+1 minute');

		$this->assertEquals('2013-04-02 05:05:02', $oDate->get());
	}

    public function testIsEqualTo()
    {
		$oDate1 = new oxTiramizoo_Date('2013-04-02 05:04:02');
		$oDate2 = new oxTiramizoo_Date('2013-04-02 05:04:02');

		$this->assertEquals(true, $oDate1->isEqualTo($oDate2));
    }

    public function testIsNotEqualTo()
    {
		$oDate1 = new oxTiramizoo_Date('2013-04-02 05:04:02');
		$oDate2 = new oxTiramizoo_Date('2013-04-02 05:04:03');

		$this->assertEquals(false, $oDate1->isEqualTo($oDate2));
    }

    public function testIsLaterThan()
    {
		$oDate1 = new oxTiramizoo_Date('2013-04-02 05:04:04');
		$oDate2 = new oxTiramizoo_Date('2013-04-02 05:04:03');

		$this->assertEquals(true, $oDate1->isLaterThan($oDate2));
    }

    public function testIsLaterOrEqualTo()
    {
		$oDate1 = new oxTiramizoo_Date('2013-04-02 05:04:04');
		$oDate2 = new oxTiramizoo_Date('2013-04-02 05:04:03');

		$this->assertEquals(true, $oDate1->isLaterOrEqualTo($oDate2));
    }

    public function testIsEarlierThan()
    {
		$oDate1 = new oxTiramizoo_Date('2013-04-02 05:04:02');
		$oDate2 = new oxTiramizoo_Date('2013-04-02 05:04:03');

		$this->assertEquals(true, $oDate1->isEarlierThan($oDate2));
    }

    public function testIsEarlierOrEqualTo()
    {
		$oDate1 = new oxTiramizoo_Date('2013-02-02 15:04:04');
		$oDate2 = new oxTiramizoo_Date('2013-04-02 05:04:03');

		$this->assertEquals(true, $oDate1->isEarlierOrEqualTo($oDate2));
    }
}