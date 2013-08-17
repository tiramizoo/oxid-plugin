<?php


class Unit_Modules_oxTiramizoo_Admin_oxTiramizoo_Category_TabTest extends OxidTestCase
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
                        array('oxid', 'some category id'),
                    );
                    
                    return returnValueMap($valueMap, func_get_args());
                }));    	

    	$oCategoryTab = $this->getMock('oxTiramizoo_Category_Tab', array('__construct', 'getConfig'), array(), '', false);
        $oCategoryTab->expects($this->any())
                    ->method('getConfig')
                    ->will($this->returnValue($oConfig));

        $this->assertEquals('oxTiramizoo_category_tab.tpl', $oCategoryTab->render());
    }

    public function testSave()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnCallback(function(){
                    $valueMap = array(
                        array('oxid', 'some category id'),
                        array('oxTiramizooCategoryExtended', array()),
                    );
                    
                    $parameters = func_get_args();
                    $parameterCount = count($parameters);

                    return returnValueMap($valueMap, func_get_args());
                }));    

        $oTiramizooCategoryExtended = $this->getMock('oxTiramizoo_CategoryExtended', array('load', 'assign', 'save'), array(), '', false);
        $oTiramizooCategoryExtended->expects($this->once())
                    			   ->method('load');
        $oTiramizooCategoryExtended->expects($this->once())
                    			   ->method('assign');
        $oTiramizooCategoryExtended->expects($this->once())
                    			   ->method('save');
        oxTestModules::addModuleObject('oxTiramizoo_CategoryExtended', $oTiramizooCategoryExtended);

    	$oCategoryTab = $this->getMock('oxTiramizoo_Category_Tab', array('__construct', 'getConfig'), array(), '', false);
        $oCategoryTab->expects($this->any())
                    ->method('getConfig')
                    ->will($this->returnValue($oConfig));

        $oCategoryTab->save();
    }
}