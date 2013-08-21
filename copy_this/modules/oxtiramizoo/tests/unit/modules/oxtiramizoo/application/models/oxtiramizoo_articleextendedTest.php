<?php


class oxTiramizoo_ArticleExtendedExposed extends oxTiramizoo_ArticleExtended
{
    public function _getParentsCategoryTree($oCategory, $returnCategories = array())
    {
        return parent::_getParentsCategoryTree($oCategory, $returnCategories);
    }
}

class Unit_Modules_oxTiramizoo_Application_Models_oxTiramizoo_ArticleExtendedTest extends OxidTestCase
{

    public function testGetIdByArticleId()
    {
        $oArticleExtended = oxNew('oxTiramizoo_ArticleExtended');
        $oArticleExtended->oxtiramizooarticleextended__oxarticleid = new oxField('oxarticle_id');
        $oArticleExtended->save();

        $this->assertEquals($oArticleExtended->oxtiramizooarticleextended__oxid->value, $oArticleExtended->getIdByArticleId('oxarticle_id'));

        $oArticleExtended->delete();
    }

    public function testLoadByArticle()
    {
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getIdByArticleId', 'load'), array(), '', false);
        $oArticleExtended->expects($this->any())
                         ->method('getIdByArticleId')
                         ->will($this->returnValue('some oxid'));

        $oArticle = $this->getMock('oxArticle', array('__construct', 'getId'), array(), '', false);
        $oArticle->expects($this->any())
                 ->method('getCategory')
                 ->will($this->returnValue(new oxCategory()));


        $oArticleExtended->expects($this->exactly(1))
                         ->method('load');

        $oArticleExtended->loadByArticle($oArticle);
    }

    public function testGetArticle()
    {
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getIdByArticleId', 'load'), array(), '', false);

        $oArticleExtended->expects($this->any())
                         ->method('getIdByArticleId')
                         ->will($this->returnValue('some oxid'));

        $oArticle = $this->getMock('oxArticle', array(), array(), '', false);

        $oArticleExtended->expects($this->exactly(1))
                         ->method('load');

        $oArticleExtended->loadByArticle($oArticle);

        $this->assertEquals($oArticle, $oArticleExtended->getArticle());
    }

    public function testIsEnabledIfEffectiveDataEnableFalse()
    {
    	$oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getEffectiveDataValue'), array(), '', false);

        $oArticleExtended->expects($this->at(0))
                         ->method('getEffectiveDataValue')
                         ->will($this->returnValue(false));


    	$this->assertEquals(false, $oArticleExtended->isEnabled());
    }

    public function testIsEnabledIfEffectiveDataDimensions()
    {
        //all dimensions and weight property equal 0
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getEffectiveDataValue'), array(), '', false);

        $oArticleExtended->expects($this->any())
                         ->method('getEffectiveDataValue')
                         ->will($this->returnCallback(function(){
                                    $valueMap = array(
                                        array('tiramizoo_enable', true),
                                        array('weight', 0),
                                        array('width', 0),
                                        array('height', 0),
                                        array('length', 0),
                                    );

                                    $parameters = func_get_args();
                                    $parameterCount = count($parameters);

                                    return returnValueMap($valueMap, func_get_args());
                                }));

        $this->assertEquals(false, $oArticleExtended->isEnabled());


        //weight is non zero property
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getEffectiveDataValue'), array(), '', false);

        $oArticleExtended->expects($this->any())
                         ->method('getEffectiveDataValue')
                         ->will($this->returnCallback(function(){
                                    $valueMap = array(
                                        array('tiramizoo_enable', true),
                                        array('weight', 1),
                                        array('width', 0),
                                        array('height', 0),
                                        array('length', 0),
                                    );

                                    return returnValueMap($valueMap, func_get_args());
                                }));

        $this->assertEquals(false, $oArticleExtended->isEnabled());


        //weight and width are non zero property
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getEffectiveDataValue'), array(), '', false);

        $oArticleExtended->expects($this->any())
                         ->method('getEffectiveDataValue')
                         ->will($this->returnCallback(function(){
                                    $valueMap = array(
                                        array('tiramizoo_enable', true),
                                        array('weight', 1),
                                        array('width', 1),
                                        array('height', 0),
                                        array('length', 0),
                                    );

                                    return returnValueMap($valueMap, func_get_args());
                                }));


        $this->assertEquals(false, $oArticleExtended->isEnabled());


        //weight, width and height are non zero property
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getEffectiveDataValue'), array(), '', false);

        $oArticleExtended->expects($this->any())
                         ->method('getEffectiveDataValue')
                         ->will($this->returnCallback(function(){
                                    $valueMap = array(
                                        array('tiramizoo_enable', true),
                                        array('weight', 1),
                                        array('width', 1),
                                        array('height', 1),
                                        array('length', 0),
                                    );

                                    return returnValueMap($valueMap, func_get_args());
                                }));

        $this->assertEquals(false, $oArticleExtended->isEnabled());


        //all properties have non zero value
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getEffectiveDataValue'), array(), '', false);

        $oArticleExtended->expects($this->any())
                         ->method('getEffectiveDataValue')
                         ->will($this->returnCallback(function(){
                                    $valueMap = array(
                                        array('tiramizoo_enable', true),
                                        array('weight', 1),
                                        array('width', 2),
                                        array('height', 1),
                                        array('length', 1),
                                    );

                                    return returnValueMap($valueMap, func_get_args());
                                }));

        $this->assertEquals(true, $oArticleExtended->isEnabled());
    }


    public function testHasIndividualPackageInheritedUsePackageFalse()
    {
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getEffectiveDataValue'), array(), '', false);

        $oArticleExtended->expects($this->at(0))
                         ->method('getEffectiveDataValue')
                         ->will($this->returnValue(false));


        $this->assertEquals(false, $oArticleExtended->hasIndividualPackage());
    }

    public function testHasIndividualPackageUInheritedsePackageTrue()
    {
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getEffectiveDataValue'), array(), '', false);

        $oArticleExtended->expects($this->at(0))
                         ->method('getEffectiveDataValue')
                         ->will($this->returnValue(true));


        $this->assertEquals(true, $oArticleExtended->hasIndividualPackage());
    }

    public function testHasWeightAndDimensions()
    {
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct', 'getArticle'), array(), '', false);

        //all dimensions are equal 0
        $oArticle = $this->getMock('oxArticle', array('__construct'), array(), '', false);
        $oArticle->oxarticles__oxweight = new oxField(0);
        $oArticle->oxarticles__oxwidth = new oxField(0);
        $oArticle->oxarticles__oxheight = new oxField(0);
        $oArticle->oxarticles__oxlength = new oxField(0);

        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));

        $this->assertEquals(false, $oArticleExtended->hasWeightAndDimensions());

        //weight is non zero fields
        $oArticle->oxarticles__oxweight = new oxField(1);

        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));

        $this->assertEquals(false, $oArticleExtended->hasWeightAndDimensions());

        //weight and width are non zero fields
        $oArticle->oxarticles__oxweight = new oxField(1);
        $oArticle->oxarticles__oxwidth = new oxField(1);

        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));

        $this->assertEquals(false, $oArticleExtended->hasWeightAndDimensions());

        //weight, width and height are non zero fields
        $oArticle->oxarticles__oxweight = new oxField(1);
        $oArticle->oxarticles__oxwidth = new oxField(1);
        $oArticle->oxarticles__oxheight = new oxField(1);

        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));

        $this->assertEquals(false, $oArticleExtended->hasWeightAndDimensions());

        //all fields have non zero value
        $oArticle->oxarticles__oxweight = new oxField(1);
        $oArticle->oxarticles__oxwidth = new oxField(1);
        $oArticle->oxarticles__oxheight = new oxField(1);
        $oArticle->oxarticles__oxlength = new oxField(1);

        $oArticleExtended->expects($this->any())
                         ->method('getArticle')
                         ->will($this->returnValue($oArticle));

        $this->assertEquals(true, $oArticleExtended->hasWeightAndDimensions());
    }

    public function testBuildEffectiveData()
    {
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct'), array(), '', false);

        $aEffectiveValueData = array('enable' => true,
                                     'tiramizoo_use_package' => true,
                                     'width' => 1,
                                     'height' => 2,
                                     'length' => 3,
                                     'weight' => 4,
                               );

        $oArticleInheritedData = $this->getMock('oxTiramizoo_ArticleExtended', array('getArticleEffectiveData'), array(), '', false);
        $oArticleInheritedData->expects($this->any())
                              ->method('getArticleEffectiveData')
                              ->will($this->returnValue($aEffectiveValueData));

        oxTestModules::addModuleObject('oxTiramizoo_ArticleInheritedData', $oArticleInheritedData);

        $this->assertEquals($aEffectiveValueData, $oArticleExtended->getEffectiveData());
    }

    public function testGetEffectiveDataValue()
    {
        $oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('__construct'), array(), '', false);

        $aEffectiveValueData = array('enable' => true,
                                     'tiramizoo_use_package' => true,
                                     'width' => 1,
                                     'height' => 2,
                                     'length' => 3,
                                     'weight' => 4,
                               );

        $oArticleInheritedData = $this->getMock('oxTiramizoo_ArticleExtended', array('getArticleEffectiveData'), array(), '', false);
        $oArticleInheritedData->expects($this->any())
                              ->method('getArticleEffectiveData')
                              ->will($this->returnValue($aEffectiveValueData));

        oxTestModules::addModuleObject('oxTiramizoo_ArticleInheritedData', $oArticleInheritedData);

        $this->assertEquals('enable', $oArticleExtended->getEffectiveDataValue('enable'));
        $this->assertEquals('tiramizoo_use_package', $oArticleExtended->getEffectiveDataValue('tiramizoo_use_package'));
        $this->assertEquals(1, $oArticleExtended->getEffectiveDataValue('width'));
        $this->assertEquals(2, $oArticleExtended->getEffectiveDataValue('height'));
        $this->assertEquals(3, $oArticleExtended->getEffectiveDataValue('length'));
        $this->assertEquals(4, $oArticleExtended->getEffectiveDataValue('weight'));
    }
}
