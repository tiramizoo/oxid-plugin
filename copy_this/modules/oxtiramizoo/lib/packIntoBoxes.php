<?php 

class packIntoBoxes 
{
	protected $_packages = array();
	protected $_items = array();
	protected $_individualPackages = array();
	protected $_itemsInPackages = array();
	protected $_currentK = 0;

	protected $counterLimit = 0;

	public function __construct($items, $packages) 
	{
		$this->_items = $items;

		foreach($this->_items as $key => $item) 
		{
			$itemArray = (array)$item;
			$itemArray['volume'] = $itemArray['width'] * $itemArray['height'] * $itemArray['length'];
			$itemArray['index'] = $key;
			$this->_items[$key] = $itemArray;
		}

		$this->_packages = $packages;
	}

	public function pack() 
	{
		$this->_sortItems();
		$this->_excludeNoFitItems();

		$this->packItemsIntoBoxes();
	}

	protected function _excludeNoFitItems()
	{
		foreach($this->_items as $key => $item) 
		{
			$itemNoFit = true;
			foreach($this->_packages as $package) 
			{
				$pudzianPackage = new pudzian($package['width'], $package['height'], $package['length'], $package['weight']);
                if ($pudzianPackage->fit(array($item))) {
                	$itemNoFit = false;
                	break;
                }
			}

			if ($itemNoFit) {
				$this->_individualPackages[$key] = $item;
				unset($this->_items[$key]);
			}
		}
	}

	protected function _sortItems() 
	{
        usort($this->_items, function ($a, $b) {
            return $a['volume'] < $b['volume'];
        });
	}

	public function getCombinations($array, $k)
	{
		$countItems = count($array);

		if($countItems == 0){
		    return;
		}

	    if ($k == 1){

	        $return = array();
	        foreach($array as $item){
	            $return[] = array($item);
	        }
	        return $return;

	    } else {
	        $levelLowerArray = $this->getCombinations($array, $k - 1);

	        $newCombs = array();

	        foreach ($levelLowerArray as $levelLowerArrayItem)
	        {
	            $lastEl = $levelLowerArrayItem[$k - 2];
	            $found = false;

	            foreach($array as $key => $b)
	            {
	                if($b == $lastEl){
	                    $found = true;
	                    continue;
	                }

	                if($found == true){
                        if($key < $countItems){

                            $tmp = $levelLowerArrayItem;
                            $newCombination = array_slice($tmp, 0);
                            $newCombination[] = $b;
                            $newCombs[] = array_slice($newCombination, 0);
                        }
	                }
	            }

	        }
	    }

	    return $newCombs;
	}

	public function getItemsCombinations($k)
	{

		$items = array();

		foreach ($this->_items as $item) 
		{
			$items[] = $item['index'];
		}		

		return $this->getCombinations($items, $k);
	}

	public function packItemsIntoBoxes()
	{
		if ($this->counterLimit++ > 60) exit;

		if (($this->_currentK <= 0) || ($this->_currentK > count($this->_items))) {
			$this->_currentK = count($this->_items);
		}

		$combinations = $this->getItemsCombinations($this->_currentK);

		$foundPackage = false;

		foreach ($combinations as $combination) 
		{
			$aAutoFitPackageItems = array();

			foreach ($this->_items as $item) 
			{
				if (in_array($item['index'], ($combination))) {
					$aAutoFitPackageItems[] = $item;
				}
			}

	        foreach ($this->_packages as $key => $package) 
	        {
				$pudzianPackage = new pudzian($package['width'], $package['height'], $package['length'], $package['weight']);

	            if ($pudzianPackage->fit($aAutoFitPackageItems)) {

	            	$this->_itemsInPackages[] = array('package' => $package, 'items' => $aAutoFitPackageItems);

					foreach ($this->_items as $key => $item) 
					{
						if (in_array($item['index'], $combination)) {
							unset($this->_items[$key]);
						}
					}
					$foundPackage = true;
					break;
	            }
	        }

	        if ($foundPackage) {
	        	break;
	        }
		}

		if (count($this->_items)) {
			if (!$foundPackage) {
				$this->_currentK--;
			} else {
				$this->_currentK = count($this->_items);
			}
			$this->packItemsIntoBoxes();
		}

	}

	public function getPackedItems()
	{
		return $this->_itemsInPackages;
	}

	public function getIndividualPackageItems()
	{
		return $this->_individualPackages;
	}
}