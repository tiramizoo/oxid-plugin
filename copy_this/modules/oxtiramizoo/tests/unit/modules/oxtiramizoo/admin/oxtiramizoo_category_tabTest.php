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
                        array('oxTiramizooCategoryExtended', array('oxtiramizoocategoryextended__tiramizoo_enable' => 1,
                                                                   'oxtiramizoocategoryextended__tiramizoo_length' => 2,
                                                                   'oxtiramizoocategoryextended__tiramizoo_width' => 3,
                                                                   'oxtiramizoocategoryextended__tiramizoo_height' => 4,
                                                                   'oxtiramizoocategoryextended__tiramizoo_weight' => 5,
                                                                   'oxtiramizoocategoryextended__tiramizoo_use_package' => 1
                        )),
                    );

                    $parameters = func_get_args();
                    $parameterCount = count($parameters);

                    return returnValueMap($valueMap, func_get_args());
                }));

    	$oCategoryTab = $this->getMock('oxTiramizoo_Category_Tab', array('__construct', 'getConfig'), array(), '', false);
        $oCategoryTab->expects($this->any())
                    ->method('getConfig')
                    ->will($this->returnValue($oConfig));

        $oCategoryTab->save();

        $this->assertEquals('some category id', $oCategoryTab->getTiramizooCategoryExtended()->oxtiramizoocategoryextended__oxcategoryid->value);
        $this->assertEquals(1, $oCategoryTab->getTiramizooCategoryExtended()->oxtiramizoocategoryextended__tiramizoo_enable->value);
        $this->assertEquals(2, $oCategoryTab->getTiramizooCategoryExtended()->oxtiramizoocategoryextended__tiramizoo_length->value);
        $this->assertEquals(3, $oCategoryTab->getTiramizooCategoryExtended()->oxtiramizoocategoryextended__tiramizoo_width->value);
        $this->assertEquals(4, $oCategoryTab->getTiramizooCategoryExtended()->oxtiramizoocategoryextended__tiramizoo_height->value);
        $this->assertEquals(5, $oCategoryTab->getTiramizooCategoryExtended()->oxtiramizoocategoryextended__tiramizoo_weight->value);
        $this->assertEquals(1, $oCategoryTab->getTiramizooCategoryExtended()->oxtiramizoocategoryextended__tiramizoo_use_package->value);

        $oCategoryTab->getTiramizooCategoryExtended()->delete();
    }
}