<?php


class Unit_Modules_oxTiramizoo_Admin_oxTiramizoo_Article_TabTest extends OxidTestCase
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
                        array('oxid', 'some article id'),
                    );

                    return returnValueMap($valueMap, func_get_args());
                }));

        $oTiramizooArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array(), array(), '', false);

        $effectiveData = new stdClass();
        $effectiveData->weight = 1;
        $effectiveData->width = 2;
        $effectiveData->height = 3;
        $effectiveData->length = 0;

        $oTiramizooArticleExtended->expects($this->any())
                                  ->method('buildArticleEffectiveData')
                                  ->will($this->returnValue($effectiveData));
        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oTiramizooArticleExtended);

    	$oArticleTab = $this->getMock('oxTiramizoo_Article_Tab', array('__construct', 'getConfig'), array(), '', false);
        $oArticleTab->expects($this->any())
                    ->method('getConfig')
                    ->will($this->returnValue($oConfig));

        $this->assertEquals('oxTiramizoo_article_tab.tpl', $oArticleTab->render());
    }

    public function testSave()
    {
        $oConfig = $this->getMock('oxConfig', array('getRequestParameter'), array(), '', false);

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnCallback(function(){
                    $valueMap = array(
                        array('oxid', 'some article id'),
                        array('oxTiramizooArticleExtended', array('oxtiramizooarticleextended__tiramizoo_enable' => 1,
                                                                  'oxtiramizooarticleextended__tiramizoo_use_package' => 1
                                                            )
                        )
                    );

                    return returnValueMap($valueMap, func_get_args());
                }));

    	$oArticleTab = $this->getMock('oxTiramizoo_Article_Tab', array('__construct', 'getConfig'), array(), '', false);
        $oArticleTab->expects($this->any())
                    ->method('getConfig')
                    ->will($this->returnValue($oConfig));

        $oArticleTab->save();

        $this->assertEquals('some article id', $oArticleTab->getTiramizooArticleExtended()->oxtiramizooarticleextended__oxarticleid->value);
        $this->assertEquals(1, $oArticleTab->getTiramizooArticleExtended()->oxtiramizooarticleextended__tiramizoo_enable->value);
        $this->assertEquals(1, $oArticleTab->getTiramizooArticleExtended()->oxtiramizooarticleextended__tiramizoo_use_package->value);

        $oArticleTab->getTiramizooArticleExtended()->delete();
    }
}