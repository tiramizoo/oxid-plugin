<?php


class oxTiramizoo_CreateOrderDataExposed extends oxTiramizoo_CreateOrderData
{
    const TIRAMIZOO_SALT = 'oxTiramizoo';

    public $_sApiWebhookUrl = '/modules/oxtiramizoo/api.php';

    public $_oTiramizooData = null;

    public $_oTimeWindow = null;
    public $_oBasket = null;
    public $_oRetailLocation = null;


    public $_sExternalId = '';

    public $_aPackages = array();

    public $_oPickup = null;
    public $_oDelivery = null;

}


class unit_core_TiramizooApi_oxTiramizooCreateOrderDataTest extends OxidTestCase
{
	protected function tearDown()
	{
        parent::tearDown();
		oxUtilsObject::resetClassInstances();
	}

	public function testCreateTiramizooOrderDataObject()
	{
		$oExpectedTiramizooData = new stdClass();

        $oExpectedTiramizooData->description = 'some description';
        $oExpectedTiramizooData->external_id = 'some external id';
        $oExpectedTiramizooData->web_hook_url = 'webhook url';
        $oExpectedTiramizooData->pickup = oxNew('oxAddress');
        $oExpectedTiramizooData->delivery = oxNew('oxAddress');
        $oExpectedTiramizooData->packages = array();

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('getDescription', 'getExternalId', 'getWebhookUrl'))->getMock();
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getDescription')
             					  ->will($this->returnValue('some description'));
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getExternalId')
             					  ->will($this->returnValue('some external id'));
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getWebhookUrl')
             					  ->will($this->returnValue('webhook url'));
		$oTiramizooCreateOrderData->_oPickup = oxNew('oxAddress');
		$oTiramizooCreateOrderData->_oDelivery = oxNew('oxAddress');
		$oTiramizooCreateOrderData->_aPackages = array();

		$this->assertEquals($oExpectedTiramizooData, $oTiramizooCreateOrderData->createTiramizooOrderDataObject());

		$oTiramizooCreateOrderData->_oPickup = null;
		$this->assertNotEquals($oExpectedTiramizooData, $oTiramizooCreateOrderData->createTiramizooOrderDataObject());
	}

	public function testGetCreatedTiramizooOrderDataObject()
	{
		$oExpectedTiramizooData = new stdClass();

        $oExpectedTiramizooData->description = 'some description';
        $oExpectedTiramizooData->external_id = 'some external id';
        $oExpectedTiramizooData->web_hook_url = 'webhook url';
        $oExpectedTiramizooData->pickup = oxNew('oxAddress');
        $oExpectedTiramizooData->delivery = oxNew('oxAddress');
        $oExpectedTiramizooData->packages = array();

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('getDescription', 'getExternalId', 'getWebhookUrl'))->getMock();
		$oTiramizooCreateOrderData->_oTiramizooData = null;
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getDescription')
             					  ->will($this->returnValue('some description'));
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getExternalId')
             					  ->will($this->returnValue('some external id'));
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getWebhookUrl')
             					  ->will($this->returnValue('webhook url'));
		$oTiramizooCreateOrderData->_oPickup = oxNew('oxAddress');
		$oTiramizooCreateOrderData->_oDelivery = oxNew('oxAddress');
		$oTiramizooCreateOrderData->_aPackages = array();

		$this->assertEquals($oExpectedTiramizooData, $oTiramizooCreateOrderData->getCreatedTiramizooOrderDataObject());
	}


	public function testBuildPickup()
	{
		$oExpectedPickup = new stdClass();
		$oExpectedPickup->address_line = 'test address line 1';
		$oExpectedPickup->city = 'test city';
		$oExpectedPickup->postal_code = 'test postal_code';
		$oExpectedPickup->country_code = 'test country_code';
		$oExpectedPickup->name = 'test name';
		$oExpectedPickup->phone_number = 'test phone_number';
		$oExpectedPickup->after = '2012-04-01T19:00:00Z';
		$oExpectedPickup->before = '2012-04-01T21:00:00Z';

		$oTimeWindow = $this->getMock('oTiramizoo_TimeWindow', array('getPickupFrom', 'getPickupTo'));
	    $oTimeWindow->expects($this->any())
             		->method('getPickupFrom')
             		->will($this->returnValue('2012-04-01T19:00:00Z'));
	    $oTimeWindow->expects($this->any())
             		->method('getPickupTo')
             		->will($this->returnValue('2012-04-01T21:00:00Z'));

	    $oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->setMethods(array('getConfVar'))->getMock();
	    $oRetailLocation->expects($this->any())
             			->method('getConfVar')
             			->will($this->returnValue((array)$oExpectedPickup));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();
		$oTiramizooCreateOrderData->_oTimeWindow = $oTimeWindow;
		$oTiramizooCreateOrderData->_oRetailLocation = $oRetailLocation;

		$this->assertEquals($oExpectedPickup, $oTiramizooCreateOrderData->buildPickup());
	}

	public function testGetDescription()
	{
		$oArticle1 = oxNew('oxArticle');
		$oArticle1->oxarticles__oxid = new oxField(1);
		$oArticle1->oxarticles__oxtitle = new oxField('Test product 1');

		$oArticle2 = oxNew('oxArticle');
		$oArticle2->oxarticles__oxid = new oxField(2);
		$oArticle2->oxarticles__oxtitle = new oxField('Test product 2');

		$oArticle3 = oxNew('oxArticle');
		$oArticle3->oxarticles__oxid = new oxField(3);
		$oArticle3->oxarticles__oxtitle = new oxField('Test product with very long title so this text should not be presented at all. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book');


		$oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->setMethods(array('getBasketArticles', 'getArtStockInBasket'))->getMock();
	    $oBasket->expects($this->any())
             	->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2, $oArticle3)));
	    $oBasket->expects($this->at(1))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(1));
	    $oBasket->expects($this->at(2))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(2));
	    $oBasket->expects($this->at(3))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(3));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('getBasket'))->getMock();
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getBasket')
             					  ->will($this->returnValue($oBasket));

		$this->assertEquals('Test product 1 (x1), Test product 2 (x2), Test product with very long title so this text should not be presented at all. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever', $oTiramizooCreateOrderData->getDescription());
	}

	public function testBuildDeliveryFromUser()
	{
		$oExpectedDelivery = new stdClass();
		$oExpectedDelivery->email = 'some.email@address.de';
		$oExpectedDelivery->address_line = 'test address line 1 number 777';
		$oExpectedDelivery->city = 'test city';
		$oExpectedDelivery->postal_code = 'test postal_code';
		$oExpectedDelivery->country_code = 'de';
		$oExpectedDelivery->phone_number = 'test phone_number';
		$oExpectedDelivery->name = 'company / fname lname';
		$oExpectedDelivery->after = '2012-04-01T19:00:00Z';
		$oExpectedDelivery->before = '2012-04-01T21:00:00Z';

		$oUser = $this->getMockBuilder('oxUser')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();

		$oUser->oxuser__oxusername = new oxField('some.email@address.de');
		$oUser->oxuser__oxstreet = new oxField('test address line 1');
		$oUser->oxuser__oxstreetnr = new oxField('number 777');
		$oUser->oxuser__oxcity = new oxField('test city');
		$oUser->oxuser__oxzip = new oxField('test postal_code');
		$oUser->oxuser__oxcountryid = new oxField('test country_code');
		$oUser->oxuser__oxfon = new oxField('test phone_number');
		$oUser->oxuser__oxfname = new oxField('fname');
		$oUser->oxuser__oxlname = new oxField('lname');
		$oUser->oxuser__oxcompany = new oxField('company');

		$oCountry = $this->getMockBuilder('oxcountry')->disableOriginalConstructor()->setMethods(array('load'))->getMock();
		$oCountry->oxcountry__oxisoalpha2 = new oxField('de');
        oxTestModules::addModuleObject('oxcountry', $oCountry);

		$oTimeWindow = $this->getMock('oTiramizoo_TimeWindow', array('getDeliveryFrom', 'getDeliveryTo'));
	    $oTimeWindow->expects($this->any())
             		->method('getDeliveryFrom')
             		->will($this->returnValue('2012-04-01T19:00:00Z'));
	    $oTimeWindow->expects($this->any())
             		->method('getDeliveryTo')
             		->will($this->returnValue('2012-04-01T21:00:00Z'));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('buildPickup'))->getMock();
		$oTiramizooCreateOrderData->_oTimeWindow = $oTimeWindow;

		$this->assertEquals($oExpectedDelivery, $oTiramizooCreateOrderData->buildDelivery($oUser, null));
	}

	public function testBuildDeliveryFromDelivery()
	{
		$oExpectedDelivery = new stdClass();
		$oExpectedDelivery->email = 'some.email@address.de';
		$oExpectedDelivery->address_line = 'test address line 1 number 777';
		$oExpectedDelivery->city = 'test city';
		$oExpectedDelivery->postal_code = 'test postal_code';
		$oExpectedDelivery->country_code = 'de';
		$oExpectedDelivery->phone_number = 'test phone_number';
		$oExpectedDelivery->name = 'company / fname lname';
		$oExpectedDelivery->after = '2012-04-01T19:00:00Z';
		$oExpectedDelivery->before = '2012-04-01T21:00:00Z';

		$oUser = $this->getMockBuilder('oxUser')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();

		$oUser->oxuser__oxusername = new oxField('some.email@address.de');
		$oUser->oxuser__oxstreet = new oxField('test address line 1');
		$oUser->oxuser__oxstreetnr = new oxField('number 777');
		$oUser->oxuser__oxcity = new oxField('test city');
		$oUser->oxuser__oxzip = new oxField('test postal_code');
		$oUser->oxuser__oxcountryid = new oxField('test country_code');
		$oUser->oxuser__oxfon = new oxField('test phone_number');
		$oUser->oxuser__oxfname = new oxField('fname');
		$oUser->oxuser__oxlname = new oxField('lname');
		$oUser->oxuser__oxcompany = new oxField('company');


		$oDeliveryAddress = $this->getMockBuilder('oxUser')->disableOriginalConstructor()->setMethods(array('__construct'))->getMock();

		$oDeliveryAddress->oxaddress__oxusername = new oxField('some.email@address.de');
		$oDeliveryAddress->oxaddress__oxstreet = new oxField('test address line 1');
		$oDeliveryAddress->oxaddress__oxstreetnr = new oxField('number 777');
		$oDeliveryAddress->oxaddress__oxcity = new oxField('test city');
		$oDeliveryAddress->oxaddress__oxzip = new oxField('test postal_code');
		$oDeliveryAddress->oxaddress__oxcountryid = new oxField('test country_code');
		$oDeliveryAddress->oxaddress__oxfon = new oxField('test phone_number');
		$oDeliveryAddress->oxaddress__oxfname = new oxField('fname');
		$oDeliveryAddress->oxaddress__oxlname = new oxField('lname');
		$oDeliveryAddress->oxaddress__oxcompany = new oxField('company');

		$oCountry = $this->getMockBuilder('oxcountry')->disableOriginalConstructor()->setMethods(array('load'))->getMock();
		$oCountry->oxcountry__oxisoalpha2 = new oxField('de');
        oxTestModules::addModuleObject('oxcountry', $oCountry);

		$oTimeWindow = $this->getMock('oTiramizoo_TimeWindow', array('getDeliveryFrom', 'getDeliveryTo'));
	    $oTimeWindow->expects($this->any())
             		->method('getDeliveryFrom')
             		->will($this->returnValue('2012-04-01T19:00:00Z'));
	    $oTimeWindow->expects($this->any())
             		->method('getDeliveryTo')
             		->will($this->returnValue('2012-04-01T21:00:00Z'));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('buildPickup'))->getMock();
		$oTiramizooCreateOrderData->_oTimeWindow = $oTimeWindow;

		$this->assertEquals($oExpectedDelivery, $oTiramizooCreateOrderData->buildDelivery($oUser, $oDeliveryAddress));
	}

	public function testBuildItems()
	{
		$oItem1 = new stdClass();
		$oItem1->weight = 2;
		$oItem1->width = 30;
		$oItem1->length = 30;
		$oItem1->height = 30;
		$oItem1->quantity = 1;
		$oItem1->description = '';

		$oItem2 = new stdClass();
		$oItem2->weight = 3;
		$oItem2->width = 3;
		$oItem2->length = 5;
		$oItem2->height = 6;
		$oItem2->quantity = 3;
		$oItem2->description = '';

		$oItem3 = new stdClass();
		$oItem3->weight = 4;
		$oItem3->width = 11;
		$oItem3->length = 11;
		$oItem3->height = 11;
		$oItem3->quantity = 2;
		$oItem3->description = '';

		$aExpectedItems = array($oItem1, $oItem2, $oItem3);

		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('getShopConfVar'))->getMock();
	    $oTiramizooConfig->expects($this->any())
             			 ->method('getShopConfVar')
             			 ->will($this->returnValue(1));

		$oArticle1 = oxNew('oxArticle');
		$oArticle1->oxarticles__oxid = new oxField(1);
		$oArticle1->oxarticles__oxtitle = new oxField('Test product 1');
		$oArticle1->oxarticles__oxstock = new oxField(3);

		$oArticle2 = oxNew('oxArticle');
		$oArticle2->oxarticles__oxid = new oxField(2);
		$oArticle2->oxarticles__oxtitle = new oxField('Test product 2');
		$oArticle2->oxarticles__oxstock = new oxField(2);

		$oArticle3 = oxNew('oxArticle');
		$oArticle3->oxarticles__oxid = new oxField(3);
		$oArticle3->oxarticles__oxtitle = new oxField('Test product 3');
		$oArticle3->oxarticles__oxstock = new oxField(4);
		$oArticle3->oxarticles__oxparentid = new oxField(4);

		$oArticle4 = oxNew('oxArticle');
		$oArticle4->oxarticles__oxid = new oxField(3);
		$oArticle4->oxarticles__oxtitle = new oxField('Test product 4');
		$oArticle4->oxarticles__oxstock = new oxField(4);

        oxTestModules::addModuleObject('oxArticle', $oArticle4);

	    $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->getMock();
	    $oArticleExtended->expects($this->any())
             			 ->method('isEnabled')
             			 ->will($this->returnValue(true));
	    $oArticleExtended->expects($this->any())
             			 ->method('hasIndividualPackage')
             			 ->will($this->returnValue(false));
	    $oArticleExtended->expects($this->at(3))
             			 ->method('buildArticleEffectiveData')
             			 ->will($this->returnValue($oItem1));
	    $oArticleExtended->expects($this->at(8))
             			 ->method('buildArticleEffectiveData')
             			 ->will($this->returnValue($oItem2));
	    $oArticleExtended->expects($this->at(13))
             			 ->method('buildArticleEffectiveData')
             			 ->will($this->returnValue($oItem3));
	    $oArticleExtended->expects($this->any())
             			 ->method('getIdByArticleId')
             			 ->will($this->returnValue('someID'));

        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);

        $oArticleExtended = oxNew('oxTiramizoo_ArticleExtended');

		$oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->setMethods(array('getBasketArticles', 'getArtStockInBasket'))->getMock();

	    $oBasket->expects($this->any())
             	->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2, $oArticle3)));
	    $oBasket->expects($this->at(1))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(1));
	    $oBasket->expects($this->at(2))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(2));
	    $oBasket->expects($this->at(3))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(3));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('getTiramizooConfig', 'getBasket'))->getMock();
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getTiramizooConfig')
             					  ->will($this->returnValue($oTiramizooConfig));
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getBasket')
             					  ->will($this->returnValue($oBasket));

		$this->assertEquals($aExpectedItems, $oTiramizooCreateOrderData->buildItems());
	}


	public function testBuildItemsPackageStrategySinglePackage()
	{
		$oItem1 = new stdClass();
		$oItem1->weight = 2;
		$oItem1->width = 30;
		$oItem1->length = 30;
		$oItem1->height = 30;
		$oItem1->quantity = 1;

		$oItem = new stdClass();
		$oItem->weight = floatval(15);
		$oItem->width = floatval(40);
		$oItem->length = floatval(120);
		$oItem->height = floatval(80);
		$oItem->description = 'Test product 4';
		$oItem->quantity = 1;

		$aExpectedItems = array($oItem);

		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->disableOriginalConstructor()->getMock();

        $map = array(
          array('oxTiramizoo_package_strategy', null, 'oxTiramizoo', 2),
          array('oxTiramizoo_std_package_width', null, 'oxTiramizoo', 40),
          array('oxTiramizoo_std_package_length', null, 'oxTiramizoo', 120),
          array('oxTiramizoo_std_package_height', null, 'oxTiramizoo', 80),
          array('oxTiramizoo_std_package_weight', null, 'oxTiramizoo', 15)
        );

	    $oTiramizooConfig->expects($this->any())
             			 ->method('getShopConfVar')
             			 ->will($this->returnValueMap($map));

		$oArticle1 = oxNew('oxArticle');
		$oArticle1->oxarticles__oxid = new oxField(1);
		$oArticle1->oxarticles__oxtitle = new oxField('Test product 1');
		$oArticle1->oxarticles__oxstock = new oxField(3);

		$oArticle2 = oxNew('oxArticle');
		$oArticle2->oxarticles__oxid = new oxField(2);
		$oArticle2->oxarticles__oxtitle = new oxField('Test product 2');
		$oArticle2->oxarticles__oxstock = new oxField(2);

		$oArticle3 = oxNew('oxArticle');
		$oArticle3->oxarticles__oxid = new oxField(3);
		$oArticle3->oxarticles__oxtitle = new oxField('Test product 3');
		$oArticle3->oxarticles__oxstock = new oxField(4);
		$oArticle3->oxarticles__oxparentid = new oxField(4);

		$oArticle4 = oxNew('oxArticle');
		$oArticle4->oxarticles__oxid = new oxField(3);
		$oArticle4->oxarticles__oxtitle = new oxField('Test product 4');
		$oArticle4->oxarticles__oxstock = new oxField(4);

        oxTestModules::addModuleObject('oxArticle', $oArticle4);

	    $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->getMock();

	    $oArticleExtended->expects($this->any())
             			 ->method('isEnabled')
             			 ->will($this->returnValue(true));

	    $oArticleExtended->expects($this->any())
             			 ->method('hasIndividualPackage')
             			 ->will($this->returnValue(false));

	    $oArticleExtended->expects($this->any())
             			 ->method('getIdByArticleId')
             			 ->will($this->returnValue('someID'));

	    $oArticleExtended->expects($this->any())
             			 ->method('buildArticleEffectiveData')
             			 ->will($this->returnValue($oItem1));

        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);

        $oArticleExtended = oxNew('oxTiramizoo_ArticleExtended');

		$oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->setMethods(array('getBasketArticles', 'getArtStockInBasket'))->getMock();

	    $oBasket->expects($this->any())
             	->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2, $oArticle3)));

	    $oBasket->expects($this->at(1))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(1));

	    $oBasket->expects($this->at(2))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(2));

	    $oBasket->expects($this->at(3))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(3));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('getTiramizooConfig', 'getBasket'))->getMock();
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getTiramizooConfig')
             					  ->will($this->returnValue($oTiramizooConfig));
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getBasket')
             					  ->will($this->returnValue($oBasket));

		$this->assertEquals($aExpectedItems, $oTiramizooCreateOrderData->buildItems());
	}

	public function testBuildItemsPackageStrategyIndividualPackage()
	{
		$oItem1 = new stdClass();
		$oItem1->weight = 2;
		$oItem1->width = 30;
		$oItem1->length = 30;
		$oItem1->height = 30;
		$oItem1->quantity = 1;
		$oItem1->description = '';

		$oItem2 = new stdClass();
		$oItem2->weight = 3;
		$oItem2->width = 3;
		$oItem2->length = 5;
		$oItem2->height = 6;
		$oItem2->quantity = 3;
		$oItem2->description = '';

		$oItem3 = new stdClass();
		$oItem3->weight = 4;
		$oItem3->width = 11;
		$oItem3->length = 11;
		$oItem3->height = 11;
		$oItem3->quantity = 2;
		$oItem3->description = '';

		$aExpectedItems = array($oItem1, $oItem2, $oItem3);

		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('getShopConfVar'))->getMock();

        $map = array(
          array('oxTiramizoo_package_strategy', null, 'oxTiramizoo', 0),
        );

	    $oTiramizooConfig->expects($this->any())
             			 ->method('getShopConfVar')
             			 ->will($this->returnValueMap($map));

		$oArticle1 = oxNew('oxArticle');
		$oArticle1->oxarticles__oxid = new oxField(1);
		$oArticle1->oxarticles__oxtitle = new oxField('Test product 1');
		$oArticle1->oxarticles__oxstock = new oxField(3);

		$oArticle2 = oxNew('oxArticle');
		$oArticle2->oxarticles__oxid = new oxField(2);
		$oArticle2->oxarticles__oxtitle = new oxField('Test product 2');
		$oArticle2->oxarticles__oxstock = new oxField(2);

		$oArticle3 = oxNew('oxArticle');
		$oArticle3->oxarticles__oxid = new oxField(3);
		$oArticle3->oxarticles__oxtitle = new oxField('Test product 3');
		$oArticle3->oxarticles__oxstock = new oxField(4);
		$oArticle3->oxarticles__oxparentid = new oxField(4);

		$oArticle4 = oxNew('oxArticle');
		$oArticle4->oxarticles__oxid = new oxField(3);
		$oArticle4->oxarticles__oxtitle = new oxField('Test product 4');
		$oArticle4->oxarticles__oxstock = new oxField(4);

        oxTestModules::addModuleObject('oxArticle', $oArticle4);

	    $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->getMock();

	    $oArticleExtended->expects($this->any())
             			 ->method('isEnabled')
             			 ->will($this->returnValue(true));

	    $oArticleExtended->expects($this->any())
             			 ->method('hasIndividualPackage')
             			 ->will($this->returnValue(true));

	    $oArticleExtended->expects($this->at(3))
             			 ->method('buildArticleEffectiveData')
             			 ->will($this->returnValue($oItem1));

	    $oArticleExtended->expects($this->at(7))
             			 ->method('buildArticleEffectiveData')
             			 ->will($this->returnValue($oItem2));

	    $oArticleExtended->expects($this->at(11))
             			 ->method('buildArticleEffectiveData')
             			 ->will($this->returnValue($oItem3));

	    $oArticleExtended->expects($this->any())
             			 ->method('getIdByArticleId')
             			 ->will($this->returnValue('someID'));

        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);

        $oArticleExtended = oxNew('oxTiramizoo_ArticleExtended');


		$oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->setMethods(array('getBasketArticles', 'getArtStockInBasket'))->getMock();

	    $oBasket->expects($this->any())
             	->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2, $oArticle3)));

	    $oBasket->expects($this->at(1))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(1));

	    $oBasket->expects($this->at(2))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(2));

	    $oBasket->expects($this->at(3))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(3));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('getTiramizooConfig', 'getBasket'))->getMock();
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getTiramizooConfig')
             					  ->will($this->returnValue($oTiramizooConfig));
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getBasket')
             					  ->will($this->returnValue($oBasket));

		$this->assertEquals($aExpectedItems, $oTiramizooCreateOrderData->buildItems());
	}

	public function testBuildItemsIfNoStockQty()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('getShopConfVar'))->getMock();
	    $oTiramizooConfig->expects($this->any())
             			 ->method('getShopConfVar')
             			 ->will($this->returnValue(1));

		$oArticle1 = oxNew('oxArticle');
		$oArticle1->oxarticles__oxid = new oxField(1);
		$oArticle1->oxarticles__oxtitle = new oxField('Test product 1');
		$oArticle1->oxarticles__oxstock = new oxField(0);

		$oArticle2 = oxNew('oxArticle');
		$oArticle2->oxarticles__oxid = new oxField(2);
		$oArticle2->oxarticles__oxtitle = new oxField('Test product 2');
		$oArticle2->oxarticles__oxstock = new oxField(0);

		$oArticle3 = oxNew('oxArticle');
		$oArticle3->oxarticles__oxid = new oxField(3);
		$oArticle3->oxarticles__oxtitle = new oxField('Test product 3');
		$oArticle3->oxarticles__oxstock = new oxField(0);
		$oArticle3->oxarticles__oxparentid = new oxField(4);

		$oArticle4 = oxNew('oxArticle');
		$oArticle4->oxarticles__oxid = new oxField(3);
		$oArticle4->oxarticles__oxtitle = new oxField('Test product 4');
		$oArticle4->oxarticles__oxstock = new oxField(0);

        oxTestModules::addModuleObject('oxArticle', $oArticle4);

	    $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->getMock();
	    $oArticleExtended->expects($this->any())
             			 ->method('isEnabled')
             			 ->will($this->returnValue(true));
	    $oArticleExtended->expects($this->any())
             			 ->method('buildArticleEffectiveData')
             			 ->will($this->returnValue($oItem2));
	    $oArticleExtended->expects($this->any())
             			 ->method('getIdByArticleId')
             			 ->will($this->returnValue('someID'));
        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);


		$oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->setMethods(array('getBasketArticles', 'getArtStockInBasket'))->getMock();
	    $oBasket->expects($this->any())
             	->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2, $oArticle3)));
	    $oBasket->expects($this->at(1))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(1));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('getTiramizooConfig', 'getBasket'))->getMock();
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getTiramizooConfig')
             					  ->will($this->returnValue($oTiramizooConfig));
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getBasket')
             					  ->will($this->returnValue($oBasket));

		$this->assertEquals(false, $oTiramizooCreateOrderData->buildItems());
	}

	public function testBuildItemsIfNotEnabled()
	{
		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('getShopConfVar'))->getMock();
	    $oTiramizooConfig->expects($this->any())
             			 ->method('getShopConfVar')
             			 ->will($this->returnValue(1));

		$oArticle1 = oxNew('oxArticle');
		$oArticle1->oxarticles__oxid = new oxField(1);
		$oArticle1->oxarticles__oxtitle = new oxField('Test product 1');
		$oArticle1->oxarticles__oxstock = new oxField(1);

		$oArticle2 = oxNew('oxArticle');
		$oArticle2->oxarticles__oxid = new oxField(2);
		$oArticle2->oxarticles__oxtitle = new oxField('Test product 2');
		$oArticle2->oxarticles__oxstock = new oxField(2);

		$oArticle3 = oxNew('oxArticle');
		$oArticle3->oxarticles__oxid = new oxField(3);
		$oArticle3->oxarticles__oxtitle = new oxField('Test product 3');
		$oArticle3->oxarticles__oxstock = new oxField(3);
		$oArticle3->oxarticles__oxparentid = new oxField(4);

		$oArticle4 = oxNew('oxArticle');
		$oArticle4->oxarticles__oxid = new oxField(3);
		$oArticle4->oxarticles__oxtitle = new oxField('Test product 4');
		$oArticle4->oxarticles__oxstock = new oxField(4);

        oxTestModules::addModuleObject('oxArticle', $oArticle4);

	    $oArticleExtended = $this->getMockBuilder('oxTiramizoo_ArticleExtended')->disableOriginalConstructor()->getMock();
	    $oArticleExtended->expects($this->any())
             			 ->method('isEnabled')
             			 ->will($this->returnValue(false));
	    $oArticleExtended->expects($this->any())
             			 ->method('buildArticleEffectiveData')
             			 ->will($this->returnValue($oItem2));
	    $oArticleExtended->expects($this->any())
             			 ->method('getIdByArticleId')
             			 ->will($this->returnValue('someID'));
        oxTestModules::addModuleObject('oxTiramizoo_ArticleExtended', $oArticleExtended);


		$oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->setMethods(array('getBasketArticles', 'getArtStockInBasket'))->getMock();
	    $oBasket->expects($this->any())
             	->method('getBasketArticles')
             	->will($this->returnValue(array($oArticle1, $oArticle2, $oArticle3)));
	    $oBasket->expects($this->at(1))
             	->method('getArtStockInBasket')
             	->will($this->returnValue(1));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('getTiramizooConfig', 'getBasket'))->getMock();
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getTiramizooConfig')
             					  ->will($this->returnValue($oTiramizooConfig));
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getBasket')
             					  ->will($this->returnValue($oBasket));

		$this->assertEquals(false, $oTiramizooCreateOrderData->buildItems());
	}

	//@Todo: test more complcated basket

	public function testGetters()
	{
		$oBasket = $this->getMockBuilder('oxBasket')->disableOriginalConstructor()->getMock();
		$oRetailLocation = $this->getMockBuilder('oxTiramizoo_RetailLocation')->disableOriginalConstructor()->getMock();
		$oTimeWindow = $this->getMockBuilder('oxTiramizoo_TimeWindow')->disableOriginalConstructor()->getMock();


	    $oTiramizooCreateOrderData = new oxTiramizoo_CreateOrderDataExposed($oTimeWindow, $oBasket, $oRetailLocation);
		$oTiramizooCreateOrderData->_oTiramizooData = new stdClass;

		$this->assertEquals(new stdClass, $oTiramizooCreateOrderData->getTiramizooDataObject());

		$this->assertEquals($oTiramizooCreateOrderData->_oBasket, $oTiramizooCreateOrderData->getBasket());

		$sExternalId = $oTiramizooCreateOrderData->getExternalId();

		$this->assertEquals($oTiramizooCreateOrderData->_sExternalId, $sExternalId);

		$this->assertInstanceOf('oxTiramizoo_Config', $oTiramizooCreateOrderData->getTiramizooConfig());




		$oTiramizooConfig = $this->getMockBuilder('oxTiramizoo_Config')->setMethods(array('getShopConfVar'))->getMock();
	    $oTiramizooConfig->expects($this->any())
             			 ->method('getShopConfVar')
             			 ->will($this->returnValue('http://someurl.de'));

	    $oTiramizooCreateOrderData = $this->getMockBuilder('oxTiramizoo_CreateOrderDataExposed')->disableOriginalConstructor()->setMethods(array('getTiramizooConfig'))->getMock();
	    $oTiramizooCreateOrderData->expects($this->any())
             					  ->method('getTiramizooConfig')
             					  ->will($this->returnValue($oTiramizooConfig));

		$this->assertEquals('http://someurl.de', $oTiramizooCreateOrderData->getShopUrl());

		$this->assertEquals('http://someurl.de/modules/oxtiramizoo/api.php', $oTiramizooCreateOrderData->getWebhookUrl());

	}

}