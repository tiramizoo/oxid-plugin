<?php
require_once dirname(__FILE__) . '/../../TiramizooTestCase.php';

class Unit_Application_Models_oxTiramizoo_RetailLocationConfigTest extends TiramizooTestCase
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