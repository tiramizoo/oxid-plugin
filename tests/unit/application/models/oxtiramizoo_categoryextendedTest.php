<?php
require_once dirname(__FILE__) . '/../../TiramizooTestCase.php';

class Unit_Application_Models_oxTiramizoo_CategoryExtendedTest extends TiramizooTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testGetIdByCategoryId()
    {
        $oCategoryExtended = oxNew('oxTiramizoo_CategoryExtended');
        $oCategoryExtended->oxtiramizoocategoryextended__oxcategoryid = new oxField('oxcategory_id');
        $oCategoryExtended->save();

        $this->assertEquals($oCategoryExtended->oxtiramizoocategoryextended__oxid->value, $oCategoryExtended->getIdByCategoryId('oxcategory_id'));

        $oCategoryExtended->delete();
    }
}