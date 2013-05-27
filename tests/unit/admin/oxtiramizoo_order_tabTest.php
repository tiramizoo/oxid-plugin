<?php
require_once dirname(__FILE__) . '/../TiramizooTestCase.php';

class Unit_Admin_oxTiramizoo_Order_TabTest extends TiramizooTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testRender()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();

        $map = array(
          array('oxid', false, 'some order id'),
        );

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($map));
    	
        $oTiramizooOrderExtended = $this->getMockBuilder('oxTiramizoo_OrderExtended')->disableOriginalConstructor()->getMock();
        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oTiramizooArticleExtended);

    	$oOrderTab = $this->getMockBuilder('oxTiramizoo_Order_Tab')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oOrderTab->expects($this->any())
                  ->method('getConfig')
                  ->will($this->returnValue($oConfig));

        $this->assertEquals('oxTiramizoo_order_tab.tpl', $oOrderTab->render());
    }
}
