<?php
require_once dirname(__FILE__) . '/../../TiramizooTestCase.php';

class Unit_Application_Models_oxTiramizoo_RetailLocationConfigListTest extends TiramizooTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testLoadByRetailLocationId()
    {
        $oRetailLocationConfig = oxNew('oxTiramizoo_RetailLocationConfig');
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxretaillocationid = new oxField('some retail id');
        $oRetailLocationConfig->oxtiramizooretaillocationconfig__oxvarname = new oxField('some var name');
        $oRetailLocationConfig->save();

        $oRetailLocationConfigList = oxNew('oxTiramizoo_RetailLocationConfigList');
        $oRetailLocationConfigList->loadByRetailLocationId('some retail id');

        $this->assertContains($oRetailLocationConfig->getId(), array_keys($oRetailLocationConfigList->getArray()));

        $oRetailLocationConfig->delete();
    }
}
