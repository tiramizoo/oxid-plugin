<?php

class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_ArticleInheritedDataTest extends OxidTestCase
{

    public function testGetParentsCategoryTree()
    {
        $oParentCategory = $this->getMock('oxcategory', array('__construct', 'getParentCategory'), array(), '', false);
        $oParentCategory->oxcategories__oxid = new oxField('some parent id');
        $oParentCategory->oxcategories__oxtitle = new oxField('some parent title');
        $oParentCategory->oxcategories__oxsort = new oxField(1);
        $oParentCategory->expects($this->any())
                        ->method('getParentCategory')
                        ->will($this->returnValue(null));

        $oCategory = $this->getMock('oxcategory', array('__construct', 'getParentCategory'), array(), '', false);
        $oCategory->oxcategories__oxid = new oxField('some category id');
        $oCategory->oxcategories__oxtitle = new oxField('some category title');
        $oCategory->oxcategories__oxsort = new oxField(2);
        $oCategory->expects($this->any())
                  ->method('getParentCategory')
                  ->will($this->returnValue($oParentCategory));

        $oCategoryExtended = $this->getMock('oxTiramizoo_CategoryExtended', array('__construct', 'getIdByCategoryId', 'load'), array(), '', false);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_use_package = new oxField(1);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_enable = new oxField(1);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_weight = new oxField(1);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_width = new oxField(40);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_height = new oxField(50);
        $oCategoryExtended->oxtiramizoocategoryextended__tiramizoo_length = new oxField(20);

        oxTestModules::addModuleObject('oxTiramizoo_CategoryExtended', $oCategoryExtended);

        $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');

        $aExpectedCategoryData = array(array('oxid' => 'some parent id',
                                             'oxtitle' => 'some parent title',
                                             'oxsort' => 1,
                                             'tiramizoo_use_package' => 1,
                                             'tiramizoo_enable' => 1,
                                             'tiramizoo_weight' => 1,
                                             'tiramizoo_width' => 40,
                                             'tiramizoo_height' => 50,
                                             'tiramizoo_length' => 20),
                                       array('oxid' => 'some category id',
                                             'oxtitle' => 'some category title',
                                             'oxsort' => 2,
                                             'tiramizoo_use_package' => 1,
                                             'tiramizoo_enable' => 1,
                                             'tiramizoo_weight' => 1,
                                             'tiramizoo_width' => 40,
                                             'tiramizoo_height' => 50,
                                             'tiramizoo_length' => 20));

        $this->assertEquals($aExpectedCategoryData, $oArticleInheritedData->getParentsCategoryTree($oCategory, array()));
    }

    public function testGetGlobalEffectiveData()
    {
        $aExpectedData['tiramizoo_enable'] = true;
        $aExpectedData['tiramizoo_use_package'] = false;
        $aExpectedData['weight'] = 1;
        $aExpectedData['width'] = 2;
        $aExpectedData['height'] = 3;
        $aExpectedData['length'] = 4;
        $aExpectedData['tiramizoo_enable_inherited_from'] = 'global';
        $aExpectedData['tiramizoo_use_package_inherited_from'] = 'global';
        $aExpectedData['tiramizoo_dimensions_inherited_from'] = 'global';

        $oTiramizooConfig = $this->getMock('oxTiramizooConfig', array('__construct', 'getShopConfVar'), array(), '', false);

        $oTiramizooConfig->expects($this->any())
                         ->method('getShopConfVar')
                         ->will($this->returnCallback(function(){
                            $valueMap = array(
                                array('oxTiramizoo_package_strategy', 0),
                                array('oxTiramizoo_global_weight', 1),
                                array('oxTiramizoo_global_width', 2),
                                array('oxTiramizoo_global_height', 3),
                                array('oxTiramizoo_global_length', 4),
                            );

                            return returnValueMap($valueMap, func_get_args());
                         }));

        oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

        $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');

        $this->assertEquals($aExpectedData, $oArticleInheritedData->getGlobalEffectiveData());
    }

    public function testGetCategoryEffectiveData()
    {
        $aCategoryData = array(array('oxid' => 'some parent id',
                                     'oxtitle' => 'some parent title',
                                     'oxsort' => 1,
                                     'tiramizoo_use_package' => 0,
                                     'tiramizoo_enable' => 1,
                                     'tiramizoo_weight' => 1,
                                     'tiramizoo_width' => 40,
                                     'tiramizoo_height' => 50,
                                     'tiramizoo_length' => 20),
                               array('oxid' => 'some middle id',
                                     'oxtitle' => 'some middle title',
                                     'oxsort' => 1,
                                     'tiramizoo_use_package' => -1,
                                     'tiramizoo_enable' => -1,
                                     'tiramizoo_weight' => 1,
                                     'tiramizoo_width' => 40,
                                     'tiramizoo_height' => 50,
                                     'tiramizoo_length' => 20),
                               array('oxid' => 'some category id',
                                     'oxtitle' => 'some category title',
                                     'oxsort' => 2,
                                     'tiramizoo_use_package' => 1,
                                     'tiramizoo_enable' => 1,
                                     'tiramizoo_weight' => 1,
                                     'tiramizoo_width' => 20,
                                     'tiramizoo_height' => 30,
                                     'tiramizoo_length' => 40));

        $aGlobalData = array();

        $aGlobalData['tiramizoo_enable'] = true;
        $aGlobalData['tiramizoo_use_package'] = false;
        $aGlobalData['weight'] = 1;
        $aGlobalData['width'] = 2;
        $aGlobalData['height'] = 3;
        $aGlobalData['length'] = 4;
        $aGlobalData['tiramizoo_enable_inherited_from'] = 'global';
        $aGlobalData['tiramizoo_use_package_inherited_from'] = 'global';
        $aGlobalData['tiramizoo_dimensions_inherited_from'] = 'global';

        $oCategory = $this->getMock('oxcategory', array('__construct', 'getId'), array(), '', false);
        $oCategory->expects($this->any())
                  ->method('getId')
                  ->will($this->returnValue('some category id'));

        $oArticleInheritedData = $this->getMock('oxTiramizoo_ArticleInheritedData', array('getGlobalEffectiveData', 'getParentsCategoryTree'), array(), '', false);
        $oArticleInheritedData->expects($this->any())
                              ->method('getGlobalEffectiveData')
                              ->will($this->returnValue($aGlobalData));
        $oArticleInheritedData->expects($this->any())
                              ->method('getParentsCategoryTree')
                              ->will($this->returnValue($aCategoryData));

        $aExpectedData = array();
        $aExpectedData['tiramizoo_enable'] = true;
        $aExpectedData['tiramizoo_use_package'] = true;
        $aExpectedData['weight'] = 1;
        $aExpectedData['width'] = 20;
        $aExpectedData['height'] = 30;
        $aExpectedData['length'] = 40;
        $aExpectedData['tiramizoo_enable_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_use_package_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_dimensions_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_enable_inherited_from_category_oxid'] = 'some category id';
        $aExpectedData['tiramizoo_enable_inherited_from_category_title'] = 'some category title';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_oxid'] = 'some category id';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_title'] = 'some category title';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_oxid'] = 'some category id';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_title'] = 'some category title';

        $this->assertEquals($aExpectedData, $oArticleInheritedData->getCategoryEffectiveData($oCategory, true));


        $aCategoryData = array(array('oxid' => 'some parent id',
                                     'oxtitle' => 'some parent title',
                                     'oxsort' => 1,
                                     'tiramizoo_use_package' => 0,
                                     'tiramizoo_enable' => 0,
                                     'tiramizoo_weight' => 1,
                                     'tiramizoo_width' => 40,
                                     'tiramizoo_height' => 50,
                                     'tiramizoo_length' => 20),
                               array('oxid' => 'some middle id',
                                     'oxtitle' => 'some middle title',
                                     'oxsort' => 1,
                                     'tiramizoo_use_package' => -1,
                                     'tiramizoo_enable' => 0,
                                     'tiramizoo_weight' => 1,
                                     'tiramizoo_width' => 40,
                                     'tiramizoo_height' => 50,
                                     'tiramizoo_length' => 20),
                               array('oxid' => 'some category id',
                                     'oxtitle' => 'some category title',
                                     'oxsort' => 2,
                                     'tiramizoo_use_package' => 1,
                                     'tiramizoo_enable' => 0,
                                     'tiramizoo_weight' => 0,
                                     'tiramizoo_width' => 40,
                                     'tiramizoo_height' => 50,
                                     'tiramizoo_length' => 20));

        $oArticleInheritedData = $this->getMock('oxTiramizoo_ArticleInheritedData', array('getGlobalEffectiveData', 'getParentsCategoryTree'), array(), '', false);
        $oArticleInheritedData->expects($this->any())
                              ->method('getGlobalEffectiveData')
                              ->will($this->returnValue($aGlobalData));
        $oArticleInheritedData->expects($this->any())
                              ->method('getParentsCategoryTree')
                              ->will($this->returnValue($aCategoryData));

        $aExpectedData = array();
        $aExpectedData['tiramizoo_enable'] = true;
        $aExpectedData['tiramizoo_use_package'] = true;
        $aExpectedData['weight'] = 1;
        $aExpectedData['width'] = 40;
        $aExpectedData['height'] = 50;
        $aExpectedData['length'] = 20;
        $aExpectedData['tiramizoo_enable_inherited_from'] = 'global';
        $aExpectedData['tiramizoo_use_package_inherited_from'] = 'category';
        $aExpectedData['tiramizoo_dimensions_inherited_from'] = 'category';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_oxid'] = 'some middle id';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_title'] = 'some middle title';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_oxid'] = 'some category id';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_title'] = 'some category title';

        $this->assertEquals($aExpectedData, $oArticleInheritedData->getCategoryEffectiveData($oCategory));
    }

    public function testBuildCategoryEffectiveDataEnableTrue()
    {
        $aCategoryData = array( 'oxid' => 'some parent id',
                                'oxtitle' => 'some parent title',
                                'oxsort' => 1,
                                'tiramizoo_use_package' => 1,
                                'tiramizoo_enable' => 1,
                                'tiramizoo_weight' => 1,
                                'tiramizoo_width' => 40,
                                'tiramizoo_height' => 50,
                                'tiramizoo_length' => 20);

        $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');

        $oCategory = $this->getMock('oxcategory', array('__construct', 'getId'), array(), '', false);
        $oCategory->expects($this->any())
                  ->method('getId')
                  ->will($this->returnValue('some category id'));


        $aExpectedData = array();
        $aExpectedData['tiramizoo_enable'] = true;
        $aExpectedData['tiramizoo_enable_inherited_from'] = 'category';
        $aExpectedData['tiramizoo_enable_inherited_from_category_oxid'] = 'some parent id';
        $aExpectedData['tiramizoo_enable_inherited_from_category_title'] = 'some parent title';

        $this->assertEquals($aExpectedData, $oArticleInheritedData->buildCategoryEffectiveDataEnable(array(), $oCategory, $aCategoryData, true));
    }

    public function testBuildCategoryEffectiveDataEnableFalse()
    {
        $aCategoryData = array( 'oxid' => 'some parent id',
                                'oxtitle' => 'some parent title',
                                'oxsort' => 1,
                                'tiramizoo_use_package' => 1,
                                'tiramizoo_enable' => -1,
                                'tiramizoo_weight' => 1,
                                'tiramizoo_width' => 40,
                                'tiramizoo_height' => 50,
                                'tiramizoo_length' => 20);

        $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');

        $oCategory = $this->getMock('oxcategory', array('__construct', 'getId'), array(), '', false);
        $oCategory->expects($this->any())
                  ->method('getId')
                  ->will($this->returnValue('some parent id'));


        $aExpectedData = array();
        $aExpectedData['tiramizoo_enable'] = false;
        $aExpectedData['tiramizoo_enable_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_enable_inherited_from_category_oxid'] = 'some parent id';
        $aExpectedData['tiramizoo_enable_inherited_from_category_title'] = 'some parent title';

        $this->assertEquals($aExpectedData, $oArticleInheritedData->buildCategoryEffectiveDataEnable(array(), $oCategory, $aCategoryData, true));
    }

    public function testBuildCategoryEffectiveDataUsePackageTrue()
    {
        $aCategoryData = array( 'oxid' => 'some parent id',
                                'oxtitle' => 'some parent title',
                                'oxsort' => 1,
                                'tiramizoo_use_package' => 1,
                                'tiramizoo_enable' => 1,
                                'tiramizoo_weight' => 1,
                                'tiramizoo_width' => 40,
                                'tiramizoo_height' => 50,
                                'tiramizoo_length' => 20);

        $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');

        $oCategory = $this->getMock('oxcategory', array('__construct', 'getId'), array(), '', false);
        $oCategory->expects($this->any())
                  ->method('getId')
                  ->will($this->returnValue('some category id'));


        $aExpectedData = array();
        $aExpectedData['tiramizoo_use_package'] = true;
        $aExpectedData['tiramizoo_use_package_inherited_from'] = 'category';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_oxid'] = 'some parent id';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_title'] = 'some parent title';

        $this->assertEquals($aExpectedData, $oArticleInheritedData->buildCategoryEffectiveDataUsePackage(array(), $oCategory, $aCategoryData, true));
    }

    public function testBuildCategoryEffectiveDataUsePackageFalse()
    {
        $aCategoryData = array( 'oxid' => 'some parent id',
                                'oxtitle' => 'some parent title',
                                'oxsort' => 1,
                                'tiramizoo_use_package' => -1,
                                'tiramizoo_enable' => 1,
                                'tiramizoo_weight' => 1,
                                'tiramizoo_width' => 40,
                                'tiramizoo_height' => 50,
                                'tiramizoo_length' => 20);

        $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');

        $oCategory = $this->getMock('oxcategory', array('__construct', 'getId'), array(), '', false);
        $oCategory->expects($this->any())
                  ->method('getId')
                  ->will($this->returnValue('some parent id'));


        $aExpectedData = array();
        $aExpectedData['tiramizoo_use_package'] = false;
        $aExpectedData['tiramizoo_use_package_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_oxid'] = 'some parent id';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_title'] = 'some parent title';

        $this->assertEquals($aExpectedData, $oArticleInheritedData->buildCategoryEffectiveDataUsePackage(array(), $oCategory, $aCategoryData, true));
    }

    public function testBuildCategoryEffectiveDataDimensions()
    {
        $aCategoryData = array( 'oxid' => 'some parent id',
                                'oxtitle' => 'some parent title',
                                'oxsort' => 1,
                                'tiramizoo_use_package' => 1,
                                'tiramizoo_enable' => 1,
                                'tiramizoo_weight' => 1,
                                'tiramizoo_width' => 40,
                                'tiramizoo_height' => 50,
                                'tiramizoo_length' => 20);

        $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');

        $oCategory = $this->getMock('oxcategory', array('__construct', 'getId'), array(), '', false);
        $oCategory->expects($this->any())
                  ->method('getId')
                  ->will($this->returnValue('some parent id'));


        $aExpectedData = array();
        $aExpectedData['weight'] = 1;
        $aExpectedData['width'] = 40;
        $aExpectedData['height'] = 50;
        $aExpectedData['length'] = 20;
        $aExpectedData['tiramizoo_dimensions_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_oxid'] = 'some parent id';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_title'] = 'some parent title';

        $this->assertEquals($aExpectedData, $oArticleInheritedData->buildCategoryEffectiveDataDimensions(array(), $oCategory, $aCategoryData, true));
    }


    public function testGetArticleEffectiveData()
    {
        $aGlobalData = array();

        $aGlobalData['tiramizoo_enable'] = true;
        $aGlobalData['tiramizoo_use_package'] = false;
        $aGlobalData['weight'] = 1;
        $aGlobalData['width'] = 2;
        $aGlobalData['height'] = 3;
        $aGlobalData['length'] = 4;
        $aGlobalData['tiramizoo_enable_inherited_from'] = 'global';
        $aGlobalData['tiramizoo_use_package_inherited_from'] = 'global';
        $aGlobalData['tiramizoo_dimensions_inherited_from'] = 'global';

        $aCategoryData = array(array('oxid' => 'some parent id',
                                     'oxtitle' => 'some parent title',
                                     'oxsort' => 1,
                                     'tiramizoo_use_package' => 0,
                                     'tiramizoo_enable' => 0,
                                     'tiramizoo_weight' => 1,
                                     'tiramizoo_width' => 40,
                                     'tiramizoo_height' => 50,
                                     'tiramizoo_length' => 20),
                               array('oxid' => 'some middle id',
                                     'oxtitle' => 'some middle title',
                                     'oxsort' => 1,
                                     'tiramizoo_use_package' => -1,
                                     'tiramizoo_enable' => 0,
                                     'tiramizoo_weight' => 1,
                                     'tiramizoo_width' => 40,
                                     'tiramizoo_height' => 50,
                                     'tiramizoo_length' => 20),
                               array('oxid' => 'some category id',
                                     'oxtitle' => 'some category title',
                                     'oxsort' => 2,
                                     'tiramizoo_use_package' => 1,
                                     'tiramizoo_enable' => 0,
                                     'tiramizoo_weight' => 0,
                                     'tiramizoo_width' => 40,
                                     'tiramizoo_height' => 50,
                                     'tiramizoo_length' => 20));

        $oCategory = $this->getMock('oxcategory', array('__construct', 'getId'), array(), '', false);
        $oCategory->expects($this->any())
                  ->method('getId')
                  ->will($this->returnValue('some category id'));

        $oArticle = $this->getMock('oxArticle', array('__construct', 'getCategory'), array(), '', false);
        $oArticle->oxarticles__oxweight = new oxField(3);
        $oArticle->oxarticles__oxwidth = new oxField(0.11);
        $oArticle->oxarticles__oxheight = new oxField(0.12);
        $oArticle->oxarticles__oxlength = new oxField(0.13);
        $oArticle->expects($this->any())
                 ->method('getCategory')
                 ->will($this->returnValue($oCategory));

        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct'), array(), '', false);
        $oArticleExtended->oxtiramizooarticleextended__tiramizoo_enable = new oxField(1);
        $oArticleExtended->oxtiramizooarticleextended__tiramizoo_use_package = new oxField(1);

        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);

        $oArticleInheritedData = $this->getMock('oxTiramizoo_ArticleInheritedData', array('getGlobalEffectiveData', 'getParentsCategoryTree'), array(), '', false);
        $oArticleInheritedData->expects($this->any())
                              ->method('getGlobalEffectiveData')
                              ->will($this->returnValue($aGlobalData));
        $oArticleInheritedData->expects($this->any())
                              ->method('getParentsCategoryTree')
                              ->will($this->returnValue($aCategoryData));

        $aExpectedData = array();
        $aExpectedData['tiramizoo_enable'] = true;
        $aExpectedData['tiramizoo_use_package'] = true;
        $aExpectedData['weight'] = 3;
        $aExpectedData['width'] = 11;
        $aExpectedData['height'] = 12;
        $aExpectedData['length'] = 13;
        $aExpectedData['tiramizoo_enable_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_use_package_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_dimensions_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_oxid'] = 'some category id';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_title'] = 'some category title';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_oxid'] = 'some middle id';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_title'] = 'some middle title';

        $this->assertEquals($aExpectedData, $oArticleInheritedData->getArticleEffectiveData($oArticle));

        $oArticleInheritedData = $this->getMock('oxTiramizoo_ArticleInheritedData', array('getGlobalEffectiveData', 'getParentsCategoryTree'), array(), '', false);
        $oArticleInheritedData->expects($this->any())
                              ->method('getGlobalEffectiveData')
                              ->will($this->returnValue($aGlobalData));
        $oArticleInheritedData->expects($this->any())
                              ->method('getParentsCategoryTree')
                              ->will($this->returnValue($aCategoryData));

        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct'), array(), '', false);
        $oArticleExtended->oxtiramizooarticleextended__tiramizoo_enable = new oxField(-1);
        $oArticleExtended->oxtiramizooarticleextended__tiramizoo_use_package = new oxField(-1);

        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);

        $aExpectedData = array();
        $aExpectedData['tiramizoo_enable'] = false;
        $aExpectedData['tiramizoo_use_package'] = false;
        $aExpectedData['weight'] = 3;
        $aExpectedData['width'] = 11;
        $aExpectedData['height'] = 12;
        $aExpectedData['length'] = 13;
        $aExpectedData['tiramizoo_enable_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_use_package_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_dimensions_inherited_from'] = 'self';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_oxid'] = 'some middle id';
        $aExpectedData['tiramizoo_dimensions_inherited_from_category_title'] = 'some middle title';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_oxid'] = 'some category id';
        $aExpectedData['tiramizoo_use_package_inherited_from_category_title'] = 'some category title';

        $this->assertEquals($aExpectedData, $oArticleInheritedData->getArticleEffectiveData($oArticle));

    }



    public function testDimensionsAndWeightCanBeInherited()
    {
        $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');

        $aCategoryData = array();
        $aCategoryData['tiramizoo_weight'] = 1;
        $aCategoryData['tiramizoo_width'] = 1;
        $aCategoryData['tiramizoo_height'] = 1;
        $aCategoryData['tiramizoo_length'] = 1;

        $this->assertEquals(true, $oArticleInheritedData->dimensionsAndWeightCanBeInherited($aCategoryData));

        $aCategoryData = array();
        $aCategoryData['tiramizoo_weight'] = 1;
        $aCategoryData['tiramizoo_width'] = 0;
        $aCategoryData['tiramizoo_height'] = 1;
        $aCategoryData['tiramizoo_length'] = 1;

        $this->assertEquals(false, $oArticleInheritedData->dimensionsAndWeightCanBeInherited($aCategoryData));

    }

}