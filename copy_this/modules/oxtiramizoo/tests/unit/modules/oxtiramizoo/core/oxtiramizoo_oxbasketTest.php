<?php



class _oxTiramizoo_oxBasket extends oxTiramizoo_oxbasket 
{
    public function _calcDeliveryCost()
    {
    	return parent::_calcDeliveryCost();
    }
}

class Unit_Modules_oxTiramizoo_Core_oxTiramizoo_oxbasketTest extends OxidTestCase
{
	protected function setUp()
	{
		parent::setUp();
        $this->_oSubj = $this->getMock('_oxTiramizoo_oxBasket', array(), array(), '', false);
	}

	protected function tearDown()
	{
        parent::tearDown();
        oxRegistry::set('oxTiramizoo_DeliverySet', new oxTiramizoo_DeliverySet());
        oxRegistry::set('oxTiramizoo_Config', new oxTiramizoo_Config());
		oxUtilsObject::resetClassInstances();
	}

	public function testCalcDeliveryCostIfNotTiramizoo()
	{
	    $this->_oSubj = $this->getMock('_oxTiramizoo_oxbasket', array('getShippingId'), array(), '', false);

	    $this->_oSubj->expects($this->at(0))
             		 ->method('getShippingId')
             		 ->will($this->returnValue('aTiramizoo'));

	    $this->assertEquals(new oxPrice(), $this->_oSubj->_calcDeliveryCost());
	}

	public function testCalcDeliveryCostIfTiramizooIsNotAvailable()
	{
	    $oDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array(), array(), '', false);
	    $oDeliverySet->expects($this->any())->method('isTiramizooAvailable')->will($this->returnValue(false));

	    oxRegistry::set('oxTiramizoo_DeliverySet', $oDeliverySet);

	    $oBasket = $this->getMock('_oxTiramizoo_oxbasket', array('getShippingId'), array(), '', false);

	    $oBasket->expects($this->at(0))
                ->method('getShippingId')
             	->will($this->returnValue('Tiramizoo'));

	    $this->assertEquals(new oxPrice(), $oBasket->_calcDeliveryCost());
	}


	public function testCalcDeliveryCostIfTiramizooIsAvailable()
	{
	    $oDeliverySet = $this->getMock('oxTiramizoo_DeliverySet', array(), array(), '', false);
	    $oDeliverySet->expects($this->any())->method('isTiramizooAvailable')->will($this->returnValue(true));

	    oxRegistry::set('oxTiramizoo_DeliverySet', $oDeliverySet);

	    $oPrice = oxNew('oxPrice');
	    $oPrice->setPrice(10.05);


	    $oDeliveryPrice = $this->getMock('oxTiramizoo_DeliveryPrice');
	    $oDeliveryPrice->expects($this->any())->method('calculateDeliveryPrice')->will($this->returnValue($oPrice));

        oxTestModules::addModuleObject('oxTiramizoo_DeliveryPrice', $oDeliveryPrice);

	    $oBasket = $this->getMock('_oxTiramizoo_oxbasket', array('getShippingId'), array(), '', false);

	    $oBasket->expects($this->any())
                ->method('getShippingId')
             	->will($this->returnValue('Tiramizoo'));

	    $this->assertEquals($oPrice, $oBasket->_calcDeliveryCost());
	}	

	public function testIsValidNotArticles()
	{
		$oBasket = $this->getMock('oxTiramizoo_oxbasket', array('getBasketArticles'), array(), '', false);

	    $oBasket->expects($this->any())
                ->method('getBasketArticles')
             	->will($this->returnValue(array()));

	    $this->assertEquals(false, $oBasket->isValid());
	}

	// test if only articles in stock and stock value is 0
	public function testIsValidArticles1()
	{
		$oArticle1 = oxnew('oxArticle');
		$oArticle1->oxarticles__oxstock = new oxField(5);

		$oArticle2 = oxnew('oxArticle');
		$oArticle2->oxarticles__oxstock = new oxField(0);

		$oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('isEnabled'), array(), '', false);
		$oArticleExtended->expects($this->at(0))->method('isEnabled')->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);

		$oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getShopConfVar'), array(), '', false);
	    $oTiramizooConfig->expects($this->any())->method('getShopConfVar')->will($this->returnValue(true));

	    oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oBasket = $this->getMock('_oxTiramizoo_oxbasket', array('getBasketArticles'), array(), '', false);

	    $oBasket->expects($this->any())
                ->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2)));

	    $this->assertEquals(false, $oBasket->isValid());
	}

	// test if only articles in stock and stock value is 0
	public function testIsValidArticles2()
	{
		$oArticle1 = oxnew('oxArticle');
		$oArticle1->oxarticles__oxstock = new oxField(5);

		$oArticle2 = oxnew('oxArticle');
		$oArticle2->oxarticles__oxstock = new oxField(3);

		$oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('isEnabled'), array(), '', false);
		$oArticleExtended->expects($this->at(0))->method('isEnabled')->will($this->returnValue(true));
		$oArticleExtended->expects($this->at(1))->method('isEnabled')->will($this->returnValue(false));

        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);

		$oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getShopConfVar'), array(), '', false);
	    $oTiramizooConfig->expects($this->any())->method('getShopConfVar')->will($this->returnValue(true));

	    oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oBasket = $this->getMock('_oxTiramizoo_oxbasket', array('getBasketArticles'), array(), '', false);

	    $oBasket->expects($this->any())
                ->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2)));

	    $this->assertEquals(false, $oBasket->isValid());
	}

	// test if only articles in stock and stock value is 0
	public function testIsValidArticles3()
	{
		$oArticle1 = oxnew('oxArticle');
		$oArticle1->oxarticles__oxstock = new oxField(5);

		$oArticle2 = oxnew('oxArticle');
		$oArticle2->oxarticles__oxstock = new oxField(4);

		$oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('isEnabled'), array(), '', false);
		$oArticleExtended->expects($this->at(0))->method('isEnabled')->will($this->returnValue(true));
		$oArticleExtended->expects($this->at(1))->method('isEnabled')->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);

		$oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getShopConfVar'), array(), '', false);
	    $oTiramizooConfig->expects($this->any())->method('getShopConfVar')->will($this->returnValue(true));

	    oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oBasket = $this->getMock('_oxTiramizoo_oxbasket', array('getBasketArticles'), array(), '', false);

	    $oBasket->expects($this->any())
                ->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2)));

	    $this->assertEquals(true, $oBasket->isValid());
	}

	// test if only articles in stock and stock value is 0
	public function testIsValidArticles4()
	{
		$oArticle1 = oxnew('oxArticle');
		$oArticle1->oxarticles__oxstock = new oxField(5);

		$oArticle2 = oxnew('oxArticle');
		$oArticle2->oxarticles__oxstock = new oxField(4);
		$oArticle2->oxarticles__oxparentid = new oxField(11);

		$oArticle3 = oxnew('oxArticle');
		$oArticle3->oxarticles__oxstock = new oxField(5);

		$oArticleParent = oxnew('oxArticle');
		$oArticleParent->oxarticles__oxstock = new oxField(5);

        oxTestModules::addModuleObject('oxArticle', $oArticleParent);

		$oArticleExtended = $this->getMock('oxTiramizoo_ArticleExtended', array('isEnabled'), array(), '', false);
		$oArticleExtended->expects($this->at(0))->method('isEnabled')->will($this->returnValue(true));
		$oArticleExtended->expects($this->at(1))->method('isEnabled')->will($this->returnValue(true));
		$oArticleExtended->expects($this->at(2))->method('isEnabled')->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);

		$oTiramizooConfig = $this->getMock('oxTiramizoo_Config', array('getShopConfVar'), array(), '', false);
	    $oTiramizooConfig->expects($this->any())->method('getShopConfVar')->will($this->returnValue(true));

	    oxRegistry::set('oxTiramizoo_Config', $oTiramizooConfig);

		$oBasket = $this->getMock('_oxTiramizoo_oxbasket', array('getBasketArticles'), array(), '', false);

	    $oBasket->expects($this->any())
                ->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2, $oArticle3)));

	    $this->assertEquals(true, $oBasket->isValid());
	}




}