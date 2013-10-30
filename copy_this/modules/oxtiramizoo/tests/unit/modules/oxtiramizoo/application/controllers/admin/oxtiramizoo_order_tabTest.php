<?php


class Unit_Modules_oxTiramizoo_Admin_oxTiramizoo_Order_TabTest extends OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
    }


    public function testRender()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnCallback(function(){
                    $valueMap = array(
                        array('oxid', 'some order id'),
                    );
                    
                    return returnValueMap($valueMap, func_get_args());
                }));    	

        $oTiramizooOrderExtended = $this->getMock('oxTiramizoo_OrderExtended', array(), array(), '', false);
        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oTiramizooArticleExtended);

    	$oOrderTab = $this->getMock('oxTiramizoo_Order_Tab', array('__construct', 'getConfig'), array(), '', false);
        $oOrderTab->expects($this->any())
                  ->method('getConfig')
                  ->will($this->returnValue($oConfig));

        $this->assertEquals('oxTiramizoo_order_tab.tpl', $oOrderTab->render());
    }
}
