<?php


class Unit_Admin_oxTiramizoo_Article_TabTest extends OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testRender()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();

        $map = array(
          array('oxid', false, 'some article id'),
        );

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($map));
    	
        $oTiramizooArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->getMock();
        
        $effectiveData = new stdClass();
        $effectiveData->weight = 1;
        $effectiveData->width = 2;
        $effectiveData->height = 3;
        $effectiveData->length = 0;

        $oTiramizooArticleExtended->expects($this->any())
                                  ->method('buildArticleEffectiveData')
                                  ->will($this->returnValue($effectiveData));
        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oTiramizooArticleExtended);

    	$oArticleTab = $this->getMockBuilder('oxTiramizoo_Article_Tab')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oArticleTab->expects($this->any())
                    ->method('getConfig')
                    ->will($this->returnValue($oConfig));

        $this->assertEquals('oxTiramizoo_article_tab.tpl', $oArticleTab->render());
    }

    public function testSave()
    {
        $oConfig = $this->getMockBuilder('oxConfig')->disableOriginalConstructor()->setMethods(array('getRequestParameter'))->getMock();

        $map = array(
          array('oxid', false, 'some article id'),
          array('oxTiramizooArticleExtended', false, array()),
        );

        $oConfig->expects($this->any())
                ->method('getRequestParameter')
                ->will($this->returnValueMap($map));
    	
        $oTiramizooArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('load', 'assign', 'save'))->getMock();
        $oTiramizooArticleExtended->expects($this->once())
                    			  ->method('load');
        $oTiramizooArticleExtended->expects($this->once())
                    			  ->method('assign');
        $oTiramizooArticleExtended->expects($this->once())
                    			  ->method('save');
        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oTiramizooArticleExtended);

    	$oArticleTab = $this->getMockBuilder('oxTiramizoo_Article_Tab')->disableOriginalConstructor()->setMethods(array('__construct', 'getConfig'))->getMock();
        $oArticleTab->expects($this->any())
                    ->method('getConfig')
                    ->will($this->returnValue($oConfig));

        $oArticleTab->save();
    }
}