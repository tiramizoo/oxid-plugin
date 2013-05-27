<?php


class Unit_Admin_oxTiramizoo_Category_TabTest extends OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testRender()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();

        $map = array(
          array('oxid', false, 'some category id'),
        );

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($map));
    	

    	$oCategoryTab = $this->getMockBuilder('oxTiramizoo_Category_Tab')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oCategoryTab->expects($this->any())
                    ->method('getConfig')
                    ->will($this->returnValue($oConfig));

        $this->assertEquals('oxTiramizoo_category_tab.tpl', $oCategoryTab->render());
    }

    public function testSave()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();

        $map = array(
          array('oxid', false, 'some category id'),
          array('oxTiramizooCategoryExtended', false, array()),
        );

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($map));
    	
        $oTiramizooCategoryExtended = $this->getMockBuilder('oxTiramizoo_CategoryExtended')->disableOriginalConstructor()->setMethods(array('load', 'assign', 'save'))->getMock();
        $oTiramizooCategoryExtended->expects($this->once())
                    			   ->method('load');
        $oTiramizooCategoryExtended->expects($this->once())
                    			   ->method('assign');
        $oTiramizooCategoryExtended->expects($this->once())
                    			   ->method('save');
        oxTestModules::addModuleObject('oxTiramizoo_CategoryExtended', $oTiramizooCategoryExtended);

    	$oCategoryTab = $this->getMockBuilder('oxTiramizoo_Category_Tab')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oCategoryTab->expects($this->any())
                    ->method('getConfig')
                    ->will($this->returnValue($oConfig));

        $oCategoryTab->save();
    }
}