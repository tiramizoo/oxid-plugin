<?php


class Unit_Modules_oxTiramizoo_Application_Models_oxTiramizoo_RetailLocationConfigTest extends OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testGetIdByRetailLocationIdAndVarName()
    {
        $oRetailLocationConfig = oxNew('oxTiramizoo_RetailLocationConfig');
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxretaillocationid = new oxField('some retail id');
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname = new oxField('some var name');
        $oRetailLocationConfig->save();

        $this->assertEquals($oRetailLocationConfig->getId(), $oRetailLocationConfig->getIdByRetailLocationIdAndVarName('some retail id', 'some var name'));

        $oRetailLocationConfig->delete();
    }
}