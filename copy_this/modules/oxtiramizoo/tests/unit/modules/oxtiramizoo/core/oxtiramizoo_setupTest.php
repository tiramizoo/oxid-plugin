<?php

class _oxTiramizoo_Setup extends oxTiramizoo_Setup
{
    const VERSION = '0.9.0';
    public $_migrationErrors = array();

    public function migration_0_0_5() {}
    public function migration_0_8_5() {}
    public function migration_1_0_0() {}

    public function executeSQL($sql) 
    {
    	return parent::executeSQL($sql);
    }

    public function addColumnToTable($tableName, $columnName, $columnData)
    {
    	parent::addColumnToTable($tableName, $columnName, $columnData);
    }

    public function columnExistsInTable($columnName, $tableName)
    {
    	return parent::columnExistsInTable($columnName, $tableName);
    }

    public function getMigrationMethods($class = __CLASS__)
    {
    	return parent::getMigrationMethods($class);
    }
}

class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_SetupTest extends OxidTestCase
{

	public function tearDown()
	{
		parent::tearDown();

        oxRegistry::set('oxTiramizoo_Config', new oxTiramizoo_Config());
        modDb::getInstance()->cleanup();
	}

	// if not installed
	public function testInstall1()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock(array('getShopConfVar'));
        $oTiramizooConfig->expects($this->at(0))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_version'))->will($this->returnValue(false));
        $oTiramizooConfig->expects($this->at(1))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_is_installed'))->will($this->returnValue(false));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('runMigrations'));
        $oTiramizooSetup->expects($this->once())->method('runMigrations');

		$oTiramizooSetup->install();
	}

	// current version is equal installed version
	public function testInstall2()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock(array('getShopConfVar'));
        $oTiramizooConfig->expects($this->at(0))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_version'))->will($this->returnValue('0.9.0'));
        $oTiramizooConfig->expects($this->at(1))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_is_installed'))->will($this->returnValue(true));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('runMigrations'));
        $oTiramizooSetup->expects($this->never())->method('runMigrations');

		$oTiramizooSetup->install();
	}

	// installed version is greater than current
	public function testInstall3()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock(array('getShopConfVar'));
        $oTiramizooConfig->expects($this->at(0))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_version'))->will($this->returnValue('1.0.0'));
        $oTiramizooConfig->expects($this->at(1))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_is_installed'))->will($this->returnValue(true));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('runMigrations'));
        $oTiramizooSetup->expects($this->never())->method('runMigrations');

		$oTiramizooSetup->install();
	}

	// current version is greater than installed
	public function testInstall4()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock(array('getShopConfVar'));
        $oTiramizooConfig->expects($this->at(0))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_version'))->will($this->returnValue('0.2.0'));
        $oTiramizooConfig->expects($this->at(1))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_is_installed'))->will($this->returnValue(true));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('runMigrations'));
        $oTiramizooSetup->expects($this->once())->method('runMigrations');

		$oTiramizooSetup->install();
	}

	// install and throw exception
	public function testInstall5()
	{
        $this->setExpectedException('oxException');

		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock(array('getShopConfVar'));
        $oTiramizooConfig->expects($this->at(0))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_version'))->will($this->returnValue(false));
        $oTiramizooConfig->expects($this->at(1))->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_is_installed'))->will($this->returnValue(false));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('runMigrations', 'getModule'));
        $oTiramizooSetup->expects($this->any())->method('runMigrations')->will($this->throwException(new oxException));
        $oTiramizooSetup->expects($this->any())->method('getModule')->will($this->returnValue(new oxModule));

		$oTiramizooSetup->install();
	}

	// test if instance of ox module
	public function testGetModule()
	{
		$oTiramizooSetup = new oxTiramizoo_Setup();

		$this->assertInstanceOf('oxModule', $oTiramizooSetup->getModule());
	}

	// test stop migrations if errors
	public function testStopMigrationsIfErrors()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock();
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = new _oxTiramizoo_Setup();
		$oTiramizooSetup->_migrationErrors = array();

		$this->assertEquals(false, $oTiramizooSetup->stopMigrationsIfErrors());

		$oTiramizooSetup->_migrationErrors = array(1, 2, 3);

		$this->assertEquals(true, $oTiramizooSetup->stopMigrationsIfErrors());
	}

	// Execute only 3 sql statements
	public function testMigration_0_9_0__1()
	{
        $this->setExpectedException('oxException');

		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock();
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('executeSQL'));
        $oTiramizooSetup->expects($this->at(2))->method('executeSQL')->will($this->throwException(new oxException));
        $oTiramizooSetup->expects($this->exactly(3))->method('executeSQL');

		$oTiramizooSetup->migration_0_9_0();
	}

	// Execute all statements 
	public function testMigration_0_9_0__2()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock();
        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('executeSQL', 'columnExistsInTable'));

	    $oTiramizooSetup->expects($this->any())
             			->method('columnExistsInTable')
             			->will($this->returnValue(true));

        $oTiramizooSetup->expects($this->exactly(11))->method('executeSQL');

        $oTiramizooConfig->expects($this->exactly(4))->method('saveShopConfVar');

		$oTiramizooSetup->migration_0_9_0();
	}


	public function testExecuteSQLWithoutResult()
	{
        $oDb = $this->getMock('stdClass', array('Execute'));
        $oDb->expects($this->any())->method('Execute')->will($this->returnValue(false));

        modDb::getInstance()->modAttach($oDb);

		$oTiramizooSetup = new _oxTiramizoo_Setup();

		$this->assertEquals(false, $oTiramizooSetup->executeSQL('some sql without result'));

		modDb::getInstance()->cleanup();
	}

	public function testExecuteSQLWithResult()
	{
        $oDb = $this->getMock('stdClass', array('Execute'));
        $oDb->expects($this->any())->method('Execute')->will($this->returnValue(new stdClass));

        modDb::getInstance()->modAttach($oDb);

		$oTiramizooSetup = new _oxTiramizoo_Setup();

		$this->assertInstanceOf('stdClass', $oTiramizooSetup->executeSQL('some sql with result'));

        modDb::getInstance()->cleanup();
	}

	// test if not exists
	public function testAddColumnToTable1()
	{
		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('executeSQL', 'columnExistsInTable'));
        $oTiramizooSetup->expects($this->any())->method('columnExistsInTable')->will($this->returnValue(false));
        $oTiramizooSetup->expects($this->exactly(1))->method('executeSQL');

        $oTiramizooSetup->addColumnToTable('table', 'column', 'data');
	}

	// test if exists
	public function testAddColumnToTable2()
	{
		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('executeSQL', 'columnExistsInTable'));
        $oTiramizooSetup->expects($this->any())->method('columnExistsInTable')->will($this->returnValue(true));
        $oTiramizooSetup->expects($this->never())->method('executeSQL');

        $oTiramizooSetup->addColumnToTable('table', 'column', 'data');
	}

	// test if exists
	public function testColumnExistsInTable()
	{
        $oDbResult = $this->getMock('stdClass', array('RecordCount'));
        $oDbResult->expects($this->any())->method('RecordCount')->will($this->returnValue(1));

        $oDb = $this->getMock('stdClass', array('Execute'));
        $oDb->expects($this->any())->method('Execute')->will($this->returnValue($oDbResult));

        modDb::getInstance()->modAttach($oDb);

		$oTiramizooSetup = new _oxTiramizoo_Setup();

		$this->assertEquals(true, $oTiramizooSetup->columnExistsInTable('some column', 'some table'));

        modDb::getInstance()->cleanup();
	}

	// test if exists
	public function testColumnNoExistsInTable()
	{
        $oDbResult = $this->getMock('stdClass', array('RecordCount'));
        $oDbResult->expects($this->any())->method('RecordCount')->will($this->returnValue(0));

        $oDb = $this->getMock('stdClass', array('Execute'));
        $oDb->expects($this->any())->method('Execute')->will($this->returnValue($oDbResult));

        modDb::getInstance()->modAttach($oDb);

		$oTiramizooSetup = new _oxTiramizoo_Setup();

		$this->assertEquals(false, $oTiramizooSetup->columnExistsInTable('some column', 'some table'));

        modDb::getInstance()->cleanup();
	}

	// inherited class
	public function testGetMigrationMethods1()
	{
		$oTiramizooSetup = new _oxTiramizoo_Setup();

		$aMigrationMethods = array('0.0.5'	=> 	'migration_0_0_5',
								   '0.8.5'	=> 	'migration_0_8_5',
								   '0.9.0'	=> 	'migration_0_9_0',
								   '1.0.0'	=> 	'migration_1_0_0');

		$this->assertEquals($aMigrationMethods, $oTiramizooSetup->getMigrationMethods());
	}

	// original class
	public function testGetMigrationMethods2()
	{
		$oTiramizooSetup = new oxTiramizoo_Setup();

		$aMigrationMethods = array('0.9.0'	=> 	'migration_0_9_0');

		$this->assertEquals($aMigrationMethods, $oTiramizooSetup->getMigrationMethods());
	}

	public function testRunMigrations1()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock(array('getShopConfVar', 'saveShopConfVar'));
        $oTiramizooConfig->expects($this->any())->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_version'))->will($this->returnValue(false));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('migration_0_0_5', 'migration_0_8_5', 'migration_0_9_0', 'migration_1_0_0'));
        $oTiramizooSetup->expects($this->exactly(1))->method('migration_0_0_5');
        $oTiramizooSetup->expects($this->exactly(1))->method('migration_0_8_5');
        $oTiramizooSetup->expects($this->exactly(1))->method('migration_0_9_0');
        $oTiramizooSetup->expects($this->never())->method('migration_1_0_0');

		$oTiramizooSetup->runMigrations();
	}

	public function testRunMigrations2()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock(array('getShopConfVar', 'saveShopConfVar'));
        $oTiramizooConfig->expects($this->any())->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_version'))->will($this->returnValue('0.8.2'));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMock('_oxTiramizoo_Setup', array('migration_0_0_5', 'migration_0_8_5', 'migration_0_9_0', 'migration_1_0_0'));
        $oTiramizooSetup->expects($this->never())->method('migration_0_0_5');
        $oTiramizooSetup->expects($this->exactly(1))->method('migration_0_8_5');
        $oTiramizooSetup->expects($this->exactly(1))->method('migration_0_9_0');
        $oTiramizooSetup->expects($this->never())->method('migration_1_0_0');

		$oTiramizooSetup->runMigrations();
	}

	public function testRunMigrations4()
	{
		$this->setExpectedException('oxException');

		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock(array('getShopConfVar', 'saveShopConfVar'));
        $oTiramizooConfig->expects($this->any())->method('getShopConfVar')->with($this->equalTo('oxTiramizoo_version'))->will($this->returnValue(false));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oTiramizooSetup = $this->getMockBuilder('_oxTiramizoo_Setup')->setMethods(array('stopMigrationsIfErrors', 'migration_0_0_5', 'migration_0_8_5', 'migration_0_9_0', 'migration_1_0_0'))->getMock();
        $oTiramizooSetup->expects($this->at(1))->method('stopMigrationsIfErrors')->will($this->returnValue(1));

        $oTiramizooSetup->expects($this->exactly(1))->method('migration_0_0_5');
        $oTiramizooSetup->expects($this->never())->method('migration_0_8_5');
        $oTiramizooSetup->expects($this->never())->method('migration_0_9_0');
        $oTiramizooSetup->expects($this->never())->method('migration_1_0_0');

		$oTiramizooSetup->runMigrations();
	}
}