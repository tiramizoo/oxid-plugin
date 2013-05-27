<?php


class oxTiramizoo_ArticleExtendedExposed extends oxTiramizoo_ArticleExtended
{
    public function _getParentsCategoryTree($oCategory, $returnCategories = array())
    {
        return parent::_getParentsCategoryTree($oCategory, $returnCategories);
    }
}

class Unit_Application_Models_oxTiramizoo_ArticleExtendedTest extends OxidTestCase
{

    public function testGetIdByArticleId()
    {
        $oArticleExtended = oxNew('oxTiramizoo_ArticleExtended');
        $oArticleExtended->oxtiramizooarticleextended__oxarticleid = new oxField('oxarticle_id');
        $oArticleExtended->save();

        $this->assertEquals($oArticleExtended->oxtiramizooarticleextended__oxid->value, $oArticleExtended->getIdByArticleId('oxarticle_id'));

        $oArticleExtended->delete();
    }


    public function testGetArticle()
    {
    	$oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'loadArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('loadArticle')
                         ->will($this->returnValue(oxNew('oxArticle')));

    	$this->assertInstanceOf('oxArticle', $oArticleExtended->getArticle());
    }

    public function testLoadArticle()
    {
        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getId'))->getMock();

        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct', 'load'))->getMock();

        oxTestModules::addModuleObject('oxArticle', $oArticle);

        $this->assertInstanceOf('oxArticle', $oArticleExtended->getArticle());
    }

    public function testIsEnabled1()
    {
    	$oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();

    	$oArticleExtended->oxtiramizooarticleextended__tiramizoo_enable = new oxField(-1);

    	$this->assertEquals(false, $oArticleExtended->isEnabled());
    }

    public function testIsEnabled2()
    {
    	$oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData'))->getMock();
		$oArticleExtended->expects($this->any())
						 ->method('getArticleInheritData')
						 ->will($this->returnValue(array('tiramizoo_enable' => false)));


    	$oArticleExtended->oxtiramizooarticleextended__tiramizoo_enable = new oxField(1);

    	$this->assertEquals(false, $oArticleExtended->isEnabled());
    }

    public function testIsEnabled3()
    {
    	$effectiveData = new stdClass;
    	$effectiveData->weight = 2;
    	$effectiveData->width = 50;
    	$effectiveData->height = 20;
    	$effectiveData->length = 0;


    	$oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData', 'buildArticleEffectiveData'))->getMock();
		$oArticleExtended->expects($this->any())
						 ->method('getArticleInheritData')
						 ->will($this->returnValue(array('tiramizoo_enable' => true)));
		$oArticleExtended->expects($this->any())
						 ->method('buildArticleEffectiveData')
						 ->will($this->returnValue($effectiveData));


    	$oArticleExtended->oxtiramizooarticleextended__tiramizoo_enable = new oxField(1);

    	$this->assertEquals(false, $oArticleExtended->isEnabled());
    }

    public function testIsEnabled4()
    {
    	$effectiveData = new stdClass;
    	$effectiveData->weight = 2;
    	$effectiveData->width = 50;
    	$effectiveData->height = 20;
    	$effectiveData->length = 10;


    	$oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData', 'buildArticleEffectiveData'))->getMock();
		$oArticleExtended->expects($this->any())
						 ->method('getArticleInheritData')
						 ->will($this->returnValue(array('tiramizoo_enable' => true)));
		$oArticleExtended->expects($this->any())
						 ->method('buildArticleEffectiveData')
						 ->will($this->returnValue($effectiveData));

    	$oArticleExtended->oxtiramizooarticleextended__tiramizoo_enable = new oxField(1);

    	$this->assertEquals(true, $oArticleExtended->isEnabled());
    }

    public function testHasIndividualPackage1()
    {
    	$oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData'))->getMock();
		$oArticleExtended->oxtiramizooarticleextended__tiramizoo_use_package = new oxField(1);
    	
    	$this->assertEquals(false, $oArticleExtended->hasIndividualPackage());
    }

    public function testHasIndividualPackage2()
    {
    	$oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData'))->getMock();
		$oArticleExtended->expects($this->any())
						 ->method('getArticleInheritData')
						 ->will($this->returnValue(array('tiramizoo_use_package' => 1)));

    	$this->assertEquals(false, $oArticleExtended->hasIndividualPackage());
    }

    public function testHasIndividualPackage3()
    {
    	$oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData'))->getMock();
		$oArticleExtended->expects($this->any())
						 ->method('getArticleInheritData')
						 ->will($this->returnValue(array()));

    	$this->assertEquals(true, $oArticleExtended->hasIndividualPackage());
    }

    public function testGetArticleInheritData1()
    {
        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct', 'getCategory'))->getMock();
        $oArticle->expects($this->any())
                 ->method('getCategory')
                 ->will($this->returnValue(null));

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));


        $oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->setMethods(array('__construct', 'getShopConfVar'))->getMock();
        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnValue(1));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $aExpectedInheritedData = array('tiramizoo_use_package' => 1,
                                        'tiramizoo_enable' => 1,
                                        'weight' => 1,
                                        'width' => 1,
                                        'height' => 1,
                                        'length' => 1);

        $this->assertEquals($aExpectedInheritedData, $oArticleExtended->getArticleInheritData());
    }

    public function testGetArticleInheritData2()
    {
        $aCategoryData = array(array('tiramizoo_enable' => -1, 
                                     'tiramizoo_use_package' => -1,
                                     'tiramizoo_weight' => 1,
                                     'tiramizoo_width' => 10,
                                     'tiramizoo_height' => 10,
                                     'tiramizoo_length' => 10,));

        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct', 'getCategory'))->getMock();

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', '_getParentsCategoryTree'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));
        $oArticleExtended->expects($this->any())
                         ->method('_getParentsCategoryTree')
                         ->will($this->returnValue($aCategoryData));

        $aExpectedInheritedData = array('tiramizoo_use_package' => 0,
                                        'tiramizoo_enable' => 0,
                                        'weight' => 1,
                                        'width' => 10,
                                        'height' => 10,
                                        'length' => 10);

        $this->assertEquals($aExpectedInheritedData, $oArticleExtended->getArticleInheritData());
    }

    public function testBuildArticleEffectiveDataFromArticle()
    {
        $oExpectedEffectiveData = new stdClass;
        $oExpectedEffectiveData->weight = floatval(1);
        $oExpectedEffectiveData->width = floatval(10);
        $oExpectedEffectiveData->height = floatval(15);
        $oExpectedEffectiveData->length = floatval(20);
        $oExpectedEffectiveData->quantity = floatval(0);

        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();
        $oArticle->oxarticles__oxweight = new oxField(1);
        $oArticle->oxarticles__oxwidth = new oxField(0.1);
        $oArticle->oxarticles__oxheight = new oxField(0.15);
        $oArticle->oxarticles__oxlength = new oxField(0.20);

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));

        $this->assertEquals($oExpectedEffectiveData, $oArticleExtended->buildArticleEffectiveData());
    }

    public function testBuildArticleEffectiveDataFromInherit()
    {
        $oExpectedEffectiveData = new stdClass;
        $oExpectedEffectiveData->weight = floatval(1);
        $oExpectedEffectiveData->width = floatval(10);
        $oExpectedEffectiveData->height = floatval(10);
        $oExpectedEffectiveData->length = floatval(10);
        $oExpectedEffectiveData->quantity = floatval(0);

        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();
        $oArticle->oxarticles__oxweight = new oxField(1);
        $oArticle->oxarticles__oxwidth = new oxField(0);
        $oArticle->oxarticles__oxheight = new oxField(0);
        $oArticle->oxarticles__oxlength = new oxField(0);

        $aInheritedData = array('weight' => 1,
                               'width' => 10,
                               'height' => 10,
                               'length' => 10);

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));
        $oArticleExtended->expects($this->any())
                         ->method('getArticleInheritData')
                         ->will($this->returnValue($aInheritedData));

        $this->assertEquals($oExpectedEffectiveData, $oArticleExtended->buildArticleEffectiveData());
    }

    public function testGetDisabledCategoryWithNoCategory()
    {
        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct', 'getCategory'))->getMock();
        $oArticle->expects($this->any())
                 ->method('getCategory')
                 ->will($this->returnValue(null));

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));

        $this->assertEquals(null, $oArticleExtended->getDisabledCategory());
    }

    public function testGetDisabledCategoryWithDisabledCategory()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxid = new oxField('some category id');

        oxTestModules::addModuleObject('oxcategory', $oCategory);

        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct', 'getCategory'))->getMock();
        $oArticle->expects($this->any())
                 ->method('getCategory')
                 ->will($this->returnValue($oCategory));

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', '_getParentsCategoryTree', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));
        $oArticleExtended->expects($this->any())
                         ->method('_getParentsCategoryTree')
                         ->will($this->returnValue(array(array('tiramizoo_enable' => -1))));

        $this->assertEquals($oCategory, $oArticleExtended->getDisabledCategory());
    }

    public function testGetDisabledCategoryWithoutDisablesCategory()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxid = new oxField('some category id');

        oxTestModules::addModuleObject('oxcategory', $oCategory);

        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct', 'getCategory'))->getMock();
        $oArticle->expects($this->any())
                 ->method('getCategory')
                 ->will($this->returnValue($oCategory));

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', '_getParentsCategoryTree', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));
        $oArticleExtended->expects($this->any())
                         ->method('_getParentsCategoryTree')
                         ->will($this->returnValue(array(array('tiramizoo_enable' => 1))));

        $this->assertEquals(null, $oArticleExtended->getDisabledCategory());
    }

    public function testGetInheritedCategoryWithoutAnyCategory()
    {
        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct', 'getCategory'))->getMock();
        $oArticle->expects($this->any())
                 ->method('getCategory')
                 ->will($this->returnValue(null));

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticleInheritData', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));

        $this->assertEquals(null, $oArticleExtended->getInheritedCategory());
    }

    public function testGetInheritedCategoryWithInheritedCategories()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxid = new oxField('some category id');

        oxTestModules::addModuleObject('oxcategory', $oCategory);

        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct', 'getCategory'))->getMock();
        $oArticle->expects($this->any())
                 ->method('getCategory')
                 ->will($this->returnValue($oCategory));

        $aCategoryData = array(array('oxid' => 'some category id',
                                     'tiramizoo_enable' => -1, 
                                     'tiramizoo_use_package' => -1,
                                     'tiramizoo_weight' => 1,
                                     'tiramizoo_width' => 10,
                                     'tiramizoo_height' => 10,
                                     'tiramizoo_length' => 10,));


        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', '_getParentsCategoryTree', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));
        $oArticleExtended->expects($this->any())
                         ->method('_getParentsCategoryTree')
                         ->will($this->returnValue($aCategoryData));

        $this->assertEquals($oCategory, $oArticleExtended->getInheritedCategory());
    }

    public function testGetInheritedCategoryWithBlankInheritedCategories()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxid = new oxField('some category id');

        oxTestModules::addModuleObject('oxcategory', $oCategory);

        $oArticle = $this->getMockBuilder('oxArticle')->disableOriginalConstructor()->setMethods(array('__construct', 'getCategory'))->getMock();
        $oArticle->expects($this->any())
                 ->method('getCategory')
                 ->will($this->returnValue($oCategory));

        $aCategoryData = array(array('oxid' => 'some category id',
                                     'tiramizoo_enable' => -1, 
                                     'tiramizoo_use_package' => -1,
                                     'tiramizoo_weight' => 0,
                                     'tiramizoo_width' => 0,
                                     'tiramizoo_height' => 0,
                                     'tiramizoo_length' => 0,));

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->setMethods(array('__construct', '_getParentsCategoryTree', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));
        $oArticleExtended->expects($this->any())
                         ->method('_getParentsCategoryTree')
                         ->will($this->returnValue($aCategoryData));

        $this->assertEquals(null, $oArticleExtended->getInheritedCategory());
    }

    public function testGetParentsCategoryTree()
    {
        $oParentCategory = $this->getMockBuilder('oxcategory')->disableOriginalConstructor()->setMethods(array('__construct', 'getParentCategory'))->getMock();
        $oParentCategory->oxcategories__oxid = new oxField('some parent id');
        $oParentCategory->oxcategories__oxtitle = new oxField('some parent title');
        $oParentCategory->oxcategories__oxsort = new oxField(1);
        $oParentCategory->expects($this->any())
                        ->method('getParentCategory')
                        ->will($this->returnValue(null));

        $oCategory = $this->getMockBuilder('oxcategory')->disableOriginalConstructor()->setMethods(array('__construct', 'getParentCategory'))->getMock();
        $oCategory->oxcategories__oxid = new oxField('some category id');
        $oCategory->oxcategories__oxtitle = new oxField('some category title');
        $oCategory->oxcategories__oxsort = new oxField(2);
        $oCategory->expects($this->any())
                  ->method('getParentCategory')
                  ->will($this->returnValue($oParentCategory));

        $oCategoryExtended = $this->getMockBuilder('oxTiramizoo_CategoryExtended')->disableOriginalConstructor()->setMethods(array('__construct', 'getIdByCategoryId', 'load'))->getMock();
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_enable = new oxField(1);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_weight = new oxField(1);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_width = new oxField(40);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_height = new oxField(50);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_length = new oxField(20);

        oxTestModules::addModuleObject('oxTiramizoo_CategoryExtended', $oCategoryExtended);

        $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtendedExposed')->disableOriginalConstructor()->setMethods(array('__construct', 'getArticle'))->getMock();
        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));

        $aExpectedCategoryData = array(array('oxid' => 'some parent id',
                                             'oxtitle' => 'some parent title', 
                                             'oxsort' => 1,
                                             'tiramizoo_enable' => 1,                                             
                                             'tiramizoo_weight' => 1,
                                             'tiramizoo_width' => 40,
                                             'tiramizoo_height' => 50,
                                             'tiramizoo_length' => 20),
                                       array('oxid' => 'some category id',
                                             'oxtitle' => 'some category title', 
                                             'oxsort' => 2,
                                             'tiramizoo_enable' => 1,                                             
                                             'tiramizoo_weight' => 1,
                                             'tiramizoo_width' => 40,
                                             'tiramizoo_height' => 50,
                                             'tiramizoo_length' => 20));

        $this->assertEquals($aExpectedCategoryData, $oArticleExtended->_getParentsCategoryTree($oCategory, array()));                         
    }
}