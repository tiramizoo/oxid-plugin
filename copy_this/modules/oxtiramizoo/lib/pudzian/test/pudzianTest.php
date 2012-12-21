<?php

require_once('../pudzian.php');

class pudzianTest extends PHPUnit_Framework_TestCase
{
    protected $_pudzian = array();

    public function setUp()
    {
        $this->_pudzian = new pudzian(200, 200, 80, 100);
    }

    public function tearDown()
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
}
