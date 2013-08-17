<?php



class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_ConfigTest extends OxidTestCase
{



	public function testSynchronizeAll()
	{
		$oTiramizooApi = $this->getMock('oxTiramizoo_Api', array(), array(), '', false);

        oxTestModules::addModuleObject('oxTiramizoo_Api', $oTiramizooApi);

		$oRetailLocation = $this->getMock('oxTiramizoo_RetailLocation', array(), array(), '', false);
		$oRetailLocation->expects($this->exactly(2))->method('synchronizeConfiguration');

        oxTestModules::addModuleObject('oxTiramizoo_RetailLocation', $oRetailLocation);

        $oTiramizooConfig = new oxTiramizoo_Config();
        $oTiramizooConfig->synchronizeAll();
	}

	public function testGetTiramizooConfVars()
	{
        $aValues = array('confbools' => array(),
                         'confstrs' => array(),
                         'confarrs' => array(),
                         'confaarrs' => array(),
                         'confselects' => array(),
                         'confints' => array('oxTiramizooVariable' => 1));

        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getConfigParam'), array(), '', false);

        $oResource = new modResource();
        $oResource->recordCount = 1;
        $oResource->eof = false;
        $oResource->fields = array('oxTiramizooVariable', 'int', 1);

        $oDb = $this->getMock('stdClass', array('Execute'));
        $oDb->expects($this->any())->method('Execute')->will($this->returnValue($oResource));

        modDb::getInstance()->modAttach($oDb);

		$this->assertEquals($aValues, $oTiramizooConfig->getTiramizooConfVars());

		modDb::getInstance()->cleanup();
	}

	public function testGetShopConfVar()
	{
        $oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getConfigParam'), array(), '', false);

        $oTiramizooConfig->init();
        $sShopId  = $oTiramizooConfig->getShopId();
        $sConfKey = $oTiramizooConfig->getConfigParam( 'sConfigKey' );
        $sVar 	  = 'oxTiramizoo_version';
        $sModule  =	'oxTiramizoo';

        $oDb = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );

        $sQ = "select DECODE( oxvarvalue, '{$sConfKey}') from oxconfig where oxshopid='{$sShopId}' and oxmodule = '{$sModule}' and  oxvarname='{$sVar}'";
        $this->assertEquals( $oDb->getOne( $sQ ), $oTiramizooConfig->getShopConfVar( $sVar, $sShopId, $sModule ));
	}
}
