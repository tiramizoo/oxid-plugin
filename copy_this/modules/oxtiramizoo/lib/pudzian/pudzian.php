<?php 

class pudzian 
{
    protected $_items = array();

    protected $_root = array('width' => 0, 
                             'height' => 0, 
                             'length' => 0, 
                             'weight' => 0,
                             'x' => 0, 
                             'y' => 0, 
                             'z' => 0);

    public function __construct($width, $height, $length, $weight)
    {
        $this->_root['width'] = $width;
        $this->_root['height'] = $height;
        $this->_root['length'] = $length;
        $this->_root['weight'] = $weight; 

    }

    public function insertItem($bin, $item) 
    {
        $item['x'] = $bin['x'];
        $item['y'] = $bin['y'];
        $item['z'] = $bin['z'];
        $items[] = $item;

        return $item;
    }

    public function rotate($bin, $item) 
    {
        if ($item['width'] === $item['height'] && $item['height'] === $item['length']) return false;

        $position1 = array('width' => $item['length'], 'height' => $item['height'], 'length' => $item['width']);
        $position2 = array('width' => $item['width'], 'height' => $item['length'], 'length' => $item['height']);
        $position3 = array('width' => $item['height'], 'height' => $item['width'], 'length' => $item['length']);

        $item['non_rotatable'] = isset($item['non_rotatable']) ? $item['non_rotatable'] : false;

        if ($this->checkDimensions($bin, $position1)) {
            return $this->rotateItem($item, $position1);
        }
        else if (!$item['non_rotatable'] && $this->checkDimensions($bin, $position2)) {
            return $this->rotateItem($item, $position2);
        }
        else if (!$item['non_rotatable'] && $this->checkDimensions($bin, $position3)) {
            return $this->rotateItem($item, $position3);
        }
        else {
            return false;
        }
    }

    public function rotateItem($item, $dimensions) 
    {
        $item['original_dimensions'] = array('width' => $item['width'], 'height' =>  $item['height'], 'length' =>  $item['length']);
        $item['width'] = $dimensions['width'];
        $item['height'] = $dimensions['height'];
        $item['length'] = $dimensions['length'];
        return $item;
    }

    public function checkDimensions($bin, $dimensions) 
    {
        return ($dimensions['width'] <= $bin['width'] && $dimensions['height'] <= $bin['height'] && $dimensions['length'] <= $bin['length']);
    }

    public function findBin($bin, $item) 
    {
        if (isset($bin['used']) && $bin['used']) {
            return $this->findBin($bin['right'], $item) || $this->findBin($bin['front'], $item) || $this->findBin($bin['up'], $item);
        }
        else if ($item['width'] <= $bin['width'] && $item['height'] <= $bin['height'] && $item['length'] <= $bin['length']) {
            return $bin;
        }
        else if ($this->rotate($bin, $item)) {
            return $bin;
        }
        else {
            return null;
        }
    }

    public function splitBin($bin, $width, $height, $length) 
    {
        $bin['used'] = true;
        $bin['front']  = array('x' => $bin['x'], 'y' => $bin['y'], 'z' => $bin['z'] + $length, 'width' => $bin['width'], 'height' => $bin['height'], 'length' => $bin['length'] - 'length' );
        $bin['right'] = array('x' => $bin['x'] + $width, 'y' => $bin['y'], 'z' => $bin['z'], 'width' => $bin['width'] - $width, 'height' => $bin['height'], 'length' => $length);
        $bin['up'] = array('x' => $bin['x'], 'y' => $bin['y'] + $height, 'z' => $bin['z'], 'width' => $width, 'height' => $bin['height'] - $height, 'length' => $length);
        return $bin;
    }

    public function sortItems($_items, $sortingMethod = 'footprint') 
    {
        $sortingMethod = !$sortingMethod ? "footprint" : $sortingMethod;

        switch ($sortingMethod) {
        case "footprint":
            usort($_items, function ($a, $b) {
                return $a['width'] * $a['length'] < $b['width'] * $b['length'];
            });
            break;
        case "volume":
            usort($_items, function ($a, $b) {
                return $a['width'] * $a['height'] * $a['length'] < $b['width'] * $b['height'] * $b['length'];
            });
            break;
        }
        return $_items;
    }

    public function checkItemsVolume() 
    {
        return $this->getItemsVolume() <= $this->getBinVolume();
    }

    public function checkItemsWeight() 
    {
        return $this->getItemsWeight() <= $this->_root['weight'];
    }

    public function fit($items = array(), $sortingMethod = 'footprint')
    {
        $this->_items = $items;

        if (!$this->checkItemsVolume()) return false;
        if (!$this->checkItemsWeight()) return false;

        $_items = $this->sortItems($this->_items, $sortingMethod);

        for ($i=0; $i < count($this->_items); $i++) {
            $item = $this->_items[$i];
            if (($bin = $this->findBin($this->_root, $item))) {
                $this->splitBin($bin, $item['width'], $item['height'], $item['length']);
                $this->insertItem($bin, $item);
            }
            else {
                return false;
            }
        };
        return true;
    }

    public function getBin()
    {
        return $this->_root;
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function getBinVolume()
    {
        return $this->_root['width'] * $this->_root['height'] * $this->_root['length'];
    }

    public function getItemsVolume()
    {
        return array_reduce($this->_items, function($sum, $item) {
            return $sum += $item['width'] * $item['height'] * $item['length'];
        });
    }

    public function getItemsWeight()
    {
        return array_reduce($this->_items, function($sum, $item) {
            return $sum += $item['weight'];
        });
    }

}