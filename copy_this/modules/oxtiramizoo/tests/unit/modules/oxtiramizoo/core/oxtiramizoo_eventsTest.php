<?php

class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_EventsTest extends OxidTestCase
{
	public function tearDown()
	{
		parent::tearDown();
		oxRegistry::set('oxTiramizoo_Setup', new oxTiramizoo_Setup());
	}


	public function testOnActivate()
	{
		$oTiramizooSetup = $this->getMockBuilder('oxTiramizoo_Setup')->disableOriginalConstructor()->getMock();
		$oTiramizooSetup->expects($this->once())->method('install');

		oxRegistry::set('oxTiramizoo_Setup', $oTiramizooSetup);

		$oTiramizooEvents = new oxTiramizoo_Events();
        $oTiramizooEvents->onActivate();
	}

	public function testOnActivateWithException()
	{		
		$oTiramizooSetup = $this->getMockBuilder('oxTiramizoo_Setup')->disableOriginalConstructor()->getMock();
		$oTiramizooSetup->expects($this->once())->method('install')->will($this->throwException(new oxException));

		oxRegistry::set('oxTiramizoo_Setup', $oTiramizooSetup);

		$oTiramizooEvents = new oxTiramizoo_Events();
        $oTiramizooEvents->onActivate();
	}

}