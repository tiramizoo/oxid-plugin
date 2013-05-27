<?php


class Unit_Application_Models_oxTiramizoo_RetailLocationListTest extends OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testLoadAll()
    {
        $oRetailLocation = oxNew('oxTiramizoo_RetailLocation');
        $oRetailLocation->oxtiramizooretaillocation__oxapitoken = new oxField('some api token');
        $oRetailLocation->oxtiramizooretaillocation__oxshopid = new oxField(oxRegistry::getConfig()->getShopId());
        $oRetailLocation->save();

        $oRetailLocationList = oxNew('oxTiramizoo_RetailLocationList');
        $oRetailLocationList->loadAll();

        $this->assertContains($oRetailLocation->getId(), array_keys($oRetailLocationList->getArray()));

        $oRetailLocation->delete();
    }
}