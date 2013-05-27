<?php
require_once dirname(__FILE__) . '/../../TiramizooTestCase.php';

class Unit_Application_Controllers_oxTiramizoo_WebhookTest extends TiramizooTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testRender()
    {
        $oApiResponse = new stdClass;
        $oApiResponse->external_id = 'some external id';

        stream_wrapper_unregister("php");
        stream_wrapper_register("php", "MockPhpStream");
        file_put_contents('php://input', json_encode($oApiResponse));
        

        $oUtils = $this->getMockBuilder('oxUtils')->disableOriginalConstructor()->getMock();
        $oUtils->expects($this->never())
               ->method('showMessageAndExit');

        oxRegistry::set('oxUtils', $oUtils);

        $oTiramizooWebhook = $this->getMock('oxTiramizoo_Webhook', array('saveOrderStatus'));
        $oTiramizooWebhook->expects($this->once())
                          ->method('saveOrderStatus');
        
        $oTiramizooWebhook->render();

        $oUtils = $this->getMockBuilder('oxUtils')->disableOriginalConstructor()->getMock();
        $oUtils->expects($this->once())
               ->method('showMessageAndExit');

        oxRegistry::set('oxUtils', $oUtils);

        $dataEmpty = new stdClass;
        file_put_contents('php://input', json_encode($dataEmpty));
        $oTiramizooWebhook->render();

        stream_wrapper_restore("php");
    }

    public function testSaveOrderStatus()
    {
        $oApiResponse = new stdClass;
        $oApiResponse->external_id = 'some external id';

        $oTiramizooWebhook = $this->getMock('oxTiramizoo_Webhook', array('__construct'));

        $oDb = $this->getMock('stdClass', array('Execute'));
        modDb::getInstance()->modAttach($oDb);

        $oUtils = $this->getMockBuilder('oxUtils')->disableOriginalConstructor()->getMock();
        $oUtils->expects($this->once())
               ->method('showMessageAndExit');

        oxRegistry::set('oxUtils', $oUtils);

        $oTiramizooWebhook->saveOrderStatus($oApiResponse);
    }
}