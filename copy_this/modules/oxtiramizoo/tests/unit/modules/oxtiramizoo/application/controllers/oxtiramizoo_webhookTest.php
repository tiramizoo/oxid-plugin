<?php
class MockPhpStream
{
    protected $index = 0;
    protected $length = null;
    protected $data = '';

    public $context;

    function __construct()
    {
        if(file_exists($this->buffer_filename()))
        {
            $this->data = file_get_contents($this->buffer_filename());
        }else{
            $this->data = '';
        }

        $this->index = 0;
        $this->length = strlen($this->data);
    }

    protected function buffer_filename()
    {
        return sys_get_temp_dir().'\php_input.txt';
    }

    function stream_open($path, $mode, $options, &$opened_path)
    {
        return true;
    }

    function stream_close()
    {
    }

    function stream_stat()
    {
        return array();
    }

    function stream_flush()
    {
        return true;
    }

    function stream_read($count)
    {
        if(is_null($this->length) === TRUE){
            $this->length = strlen($this->data);
        }
        $length = min($count, $this->length - $this->index);
        $data = substr($this->data, $this->index);
        $this->index = $this->index + $length;
        return $data;
    }

    function stream_eof()
    {
        return ($this->index >= $this->length ? TRUE : FALSE);
    }

    function stream_write($data)
    {
        return file_put_contents($this->buffer_filename(), $data);
    }

    function unlink()
    {
        if(file_exists($this->buffer_filename())){
            unlink($this->buffer_filename());
        }
        $this->data = '';
        $this->index = 0;
        $this->length = 0;
    }
}


class Unit_Application_Controllers_oxTiramizoo_WebhookTest extends OxidTestCase
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

        $oDb = $this->getMock('oxDb', array('Execute'));
        modDb::getInstance()->modAttach($oDb);

        $oUtils = $this->getMockBuilder('oxUtils')->disableOriginalConstructor()->getMock();
        $oUtils->expects($this->once())
               ->method('showMessageAndExit');

        oxRegistry::set('oxUtils', $oUtils);

        $oTiramizooWebhook->saveOrderStatus($oApiResponse);
    }
}