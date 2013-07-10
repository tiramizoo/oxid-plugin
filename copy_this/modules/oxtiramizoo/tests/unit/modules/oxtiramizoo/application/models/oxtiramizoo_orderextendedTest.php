<?php


class Unit_Modules_oxTiramizoo_Application_Models_oxTiramizoo_OrderExtendedTest extends OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testGetIdByOrderId()
    {
        $oOrderExtended = oxNew('oxTiramizoo_OrderExtended');
        $oOrderExtended->oxtiramizooorderextended__oxorderid = new oxField('order_id');
        $oOrderExtended->save();

        $this->assertEquals($oOrderExtended->oxtiramizooorderextended__oxid->value, $oOrderExtended->getIdByOrderId('order_id'));

        $oOrderExtended->delete();
    }

    public function testGetSetTiramizooData()
    {
        $aData = array('somedata' => 'somevalue');

        $oOrderExtended = oxNew('oxTiramizoo_OrderExtended');
        $oOrderExtended->setTiramizooData($aData);

        $this->assertEquals($aData, $oOrderExtended->getTiramizooData());
    }

    public function testGetTrackingUrl()
    {
        $aData = array('somedata' => 'somevalue');

        $oOrderExtended = oxNew('oxTiramizoo_OrderExtended');
        $oOrderExtended->oxtiramizooorderextended__tiramizoo_tracking_url = new oxField('tracking URL');
        
        $this->assertEquals('tracking URL', $oOrderExtended->getTrackingUrl());
    }
}