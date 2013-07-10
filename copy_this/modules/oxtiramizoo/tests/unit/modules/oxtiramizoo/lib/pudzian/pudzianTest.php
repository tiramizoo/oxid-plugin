<?php



class pudzianExposed extends pudzian
{
    public $_root = array('width' => 0, 
                             'height' => 0, 
                             'length' => 0, 
                             'weight' => 0,
                             'x' => 0, 
                             'y' => 0, 
                             'z' => 0);
    public $_items = array();

}

class Unit_Modules_oxTiramizoo_Lib_pudzian_pudzianTest extends PHPUnit_Framework_TestCase
{
    protected $_pudzian = array();

    public function setUp()
    {
        $this->_pudzian = new pudzian(200, 200, 80, 100);
    }

    protected function tearDown()
    {
        unset($this->_pudzian);
    }

    public function testShouldNotFitItemBiggerThanMaxWeight()
    {
        $this->assertEquals(0, $this->_pudzian->fit(array(
                array('width' =>  10, 'height' =>  10, 'length' =>  10, 'weight' =>  50 ),
                array('width' =>  10, 'height' =>  10, 'length' =>  10, 'weight' =>  51 )
            )
        ));
    }

    public function testShouldFitItemSmallerThanBin()
    {
        $this->assertEquals(1, $this->_pudzian->fit(array(
                array('width' =>  99, 'height' =>  88, 'length' =>  77, 'weight' =>  99 )
            )
        ));
    }

    public function testShouldNotFitItemBiggerThanBin()
    {
        $this->assertEquals(0, $this->_pudzian->fit(array(
                array('width' =>  99, 'height' =>  88, 'length' =>  101, 'weight' =>  99 )
            )
        ));
    }

    public function testShouldFitItemsTotalEqualToBin()
    {
        $this->assertEquals(1, $this->_pudzian->fit(array(
                array('width' =>  30, 'height' =>  100, 'length' =>  20, 'weight' =>  10 ),
                array('width' =>  80, 'height' =>  90, 'length' =>  20, 'weight' =>  10 ),
                array('width' =>  90, 'height' =>  10, 'length' =>  40, 'weight' =>  80 ),
            )
        ));
    }

    public function testShouldNotFitItemsTotalBiggerThanBin()
    {
        $this->assertEquals(0, $this->_pudzian->fit(array(
                array('width' =>  30, 'height' =>  101, 'length' =>  20, 'weight' =>  10 ),
                array('width' =>  80, 'height' =>  90, 'length' =>  22, 'weight' =>  11 ),
                array('width' =>  91, 'height' =>  10, 'length' =>  40, 'weight' =>  80 ),
            )
        ));
    }

    public function testShouldFit1000SmallItems()
    {
        $items = array();

        for($i = 0; $i < 1000; $i++)
        {
            $items[] = array('width' => 20, 'height' =>  20, 'length' => 8, 'weight' => 0.1 );
        }

        $this->assertEquals(1, $this->_pudzian->fit($items));
    }

    public function testShouldNotFit1001SmallItems()
    {
        $items = array();

        for($i = 0; $i < 1001; $i++)
        {
            $items[] = array('width' => 20, 'height' =>  20, 'length' => 8, 'weight' => 0.01 );
        }

        $this->assertEquals(0, $this->_pudzian->fit($items));
    }

    public function testShouldBeAbleFitItemsIfRotated()
    {
        $this->assertEquals(1, $this->_pudzian->fit(array(
                array('width' =>  100, 'height' =>  100, 'length' =>  40, 'weight' =>  50 ),
                array('width' =>  100, 'height' =>  40, 'length' =>  100, 'weight' =>  50 )
            )
        ));
    }

    public function testShouldNotBeAbleFitItemsIfNonRotated()
    {
        $this->assertEquals(0, $this->_pudzian->fit(array(
                array('width' =>  100, 'height' =>  100, 'length' =>  40, 'weight' =>  50 ),
                array('width' =>  100, 'height' =>  40, 'length' =>  100, 'weight' =>  50, 'non_rotatable' => true )
            )
        ));
    }

    public function testSortItemsByVolume()
    {
        $items = array(
            array('width' =>  13, 'height' =>  13, 'length' =>  13, 'weight' =>  1 ),
            array('width' =>  11, 'height' =>  11, 'length' =>  11, 'weight' =>  1 ),
            array('width' =>  12, 'height' =>  12, 'length' =>  12, 'weight' =>  1 ),
        );

        $expectedItems = array(
            array('width' =>  13, 'height' =>  13, 'length' =>  13, 'weight' =>  1 ),
            array('width' =>  12, 'height' =>  12, 'length' =>  12, 'weight' =>  1 ),
            array('width' =>  11, 'height' =>  11, 'length' =>  11, 'weight' =>  1 ),
        );

        $this->assertEquals($expectedItems, $this->_pudzian->sortItems($items, 'volume'));
    }


    public function testRotate()
    {
        $pudzian = $this->getMock('pudzian', array('checkDimensions'), array(200, 200, 80, 100));
        $pudzian->expects($this->at(0))
                ->method('checkDimensions')
                ->will($this->returnValue(true));

        $bin = array();
        $bin['right'] = array('x' => 10, 'y' => 10, 'z' => 10, 'width' => 20, 'height' => 10, 'length' => 10);
        $bin['front']  = array('x' => 10, 'y' =>10, 'z' => 10, 'width' => 20, 'height' => 40, 'length' => 10 );
        $bin['up'] = array('x' => 10, 'y' => 10, 'z' => 10, 'width' =>10, 'height' => 10, 'length' => 10);

        $item = array('width' => 20, 'height' => 50, 'length' => 10);
        $expectedItem = array('width' => 10, 'height' => 50, 'length' => 20, 'non_rotatable' => false, 'original_dimensions' => $item);

        $this->assertEquals($expectedItem, $pudzian->rotate($bin, $item));



        $pudzian = $this->getMock('pudzian', array('checkDimensions'), array(200, 200, 80, 100));
        $pudzian->expects($this->at(0))
                ->method('checkDimensions')
                ->will($this->returnValue(false));
        $pudzian->expects($this->at(2))
                ->method('checkDimensions')
                ->will($this->returnValue(true));


        $item = array('width' => 20, 'height' => 50, 'length' => 10, 'non_rotatable' => false);
        $expectedItem = array('width' => 50, 'height' => 20, 'length' => 10, 'non_rotatable' => false, 'original_dimensions' => array('width' => 20, 'height' => 50, 'length' => 10, ));

        $this->assertEquals($expectedItem, $pudzian->rotate($bin, $item));
    }

    public function testFindBin()
    {
        $bin = array();
        $bin['used'] = true;
        $bin['right'] = array('x' => 10, 'y' => 10, 'z' => 10, 'width' => 10, 'height' => 10, 'length' => 10);
        $bin['front']  = array('x' => 10, 'y' =>10, 'z' => 10, 'width' => 10, 'height' => 10, 'length' => 10 );
        $bin['up'] = array('x' => 10, 'y' => 10, 'z' => 10, 'width' =>10, 'height' => 10, 'length' => 10);

        $this->assertEquals(true, $this->_pudzian->findBin($bin, array('width' =>  5, 'height' =>  5, 'length' =>  5, 'weight' =>  1)));
    }

    public function testGetBin()
    {
        $expectedRoot = array(  'width' => 1, 
                                'height' => 2, 
                                'length' => 0, 
                                'weight' => 0,
                                'x' => 0, 
                                'y' => 0, 
                                'z' => 0);

        $pudzianExposed = new pudzianExposed(200, 200, 80, 100);
        $pudzianExposed->_root = $expectedRoot;

        $this->assertEquals($expectedRoot, $pudzianExposed->getBin());
    }

    public function testGetItems()
    {
        $expectedItems = array(
                array('width' =>  30, 'height' =>  100, 'length' =>  20, 'weight' =>  10 ),
                array('width' =>  80, 'height' =>  90, 'length' =>  20, 'weight' =>  10 ),
                array('width' =>  90, 'height' =>  10, 'length' =>  40, 'weight' =>  80 ),
            );

        $pudzianExposed = new pudzianExposed(200, 200, 80, 100);
        $pudzianExposed->_items = $expectedItems;

        $this->assertEquals($expectedItems, $pudzianExposed->getitems());
    }



}
