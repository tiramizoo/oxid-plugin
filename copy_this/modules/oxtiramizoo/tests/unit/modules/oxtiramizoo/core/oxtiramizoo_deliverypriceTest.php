<?php



class Unit_Core_oxTiramizoo_DeliveryPriceTest extends OxidTestCase
{

	protected $_oSubj = null;

	public function setUp()
	{
        parent::setUp();
		$this->_oSubj = new oxTiramizoo_DeliveryPrice();
	}

	public function testCalculateDeliveryPrice()
	{
		$oTiramizooDeliverySet = $this->getMock('oxTiramizoo_DeliverySet');
		$oUser = oxRegistry::get('oxuser');
		$oBasket = $this->getMock('oxBasket');
		$oDeliveryPrice = oxNew('oxPrice');
		$oDeliveryPrice->setPrice(12.45);

		$this->assertEquals(12.45, $this->_oSubj->calculateDeliveryPrice($oTiramizooDeliverySet, $oUser, $oBasket, $oDeliveryPrice)->getPrice());

	}
}