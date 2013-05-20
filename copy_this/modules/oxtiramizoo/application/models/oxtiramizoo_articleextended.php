<?php

class oxTiramizoo_ArticleExtended extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizooarticleextended';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxTiramizoo_ArticleExtended';

    protected $_aConfigVars = null;

    protected $_oArticle = null;

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizooarticleextended' );
    }

    public function getIdByArticleId($sArticleId) 
    {
        $oDb = oxDb::getDb();
        $sQ = "SELECT oxid FROM " . $this->_sCoreTbl . " WHERE OXARTICLEID = '" . $sArticleId . "';";
        return $oDb->getOne($sQ);
    }

    public function getArticle()
    {
        if ($this->_oArticle === null) {
            $this->loadArticle();
        }

        return $this->_oArticle;
    }

    protected function loadArticle()
    {
        $this->_oArticle = oxNew( 'oxarticle' );
        $this->_oArticle->load($this->getId());
    }

    public function isEnabled()
    {
        if ($this->oxtiramizooarticleextended__tiramizoo_enable->value == -1) {
            return false;
        }

        $aTiramizooInheritedData = $this->getArticleInheritData();

        if (!$aTiramizooInheritedData['tiramizoo_enable']) {
            return false;
        }

        $oItem = $this->buildArticleEffectiveData();

        if (!$oItem->weight || !$oItem->width || !$oItem->height || !$oItem->length) {
            return false;
        }

        return true;
    }


    public function hasIndividualPackage()
    {
        $aTiramizooInheritedData = $this->getArticleInheritData();

        if (isset($this->oxtiramizooarticleextended__tiramizoo_use_package->value) && $this->oxtiramizooarticleextended__tiramizoo_use_package->value) {
            return false;
        } else if (!isset($this->oxtiramizooarticleextended__tiramizoo_use_package->value) && $aTiramizooInheritedData['tiramizoo_use_package']) {
            return false;
        }

        return true;
    }


    /**
     * Get product data (enable, weight, dimensions) from main category or parents
     * 
     * @param  oxArticle $oArticle
     * @return array
     */
    public function getArticleInheritData()
    {
        //set the defaults
        $aTiramizooInheritedData = array();

        $aTiramizooInheritedData['tiramizoo_use_package'] = 1;
        $aTiramizooInheritedData['tiramizoo_enable'] = 1;
        $aTiramizooInheritedData['weight'] = 0;
        $aTiramizooInheritedData['width'] = 0;
        $aTiramizooInheritedData['height'] = 0;
        $aTiramizooInheritedData['length'] = 0;

        $oTiramizooConfig = oxTiramizooConfig::getInstance();

        // get from tiramizoo settings centimeters and kilograms
        $aTiramizooInheritedData['weight'] = floatval($oTiramizooConfig->getShopConfVar('oxTiramizoo_global_weight'));
        $aTiramizooInheritedData['width'] = floatval($oTiramizooConfig->getShopConfVar('oxTiramizoo_global_width'));
        $aTiramizooInheritedData['height'] = floatval($oTiramizooConfig->getShopConfVar('oxTiramizoo_global_height'));
        $aTiramizooInheritedData['length'] = floatval($oTiramizooConfig->getShopConfVar('oxTiramizoo_global_length'));

        $oArticle = $this->getArticle();

        $oCategory = $oArticle->getCategory();

        // if article has no assigned categories return only global settings
        if (!$oCategory) {
            return  $aTiramizooInheritedData;
        }

        $aCheckCategories = $this->_getParentsCategoryTree($oCategory);

        foreach ($aCheckCategories as $aCategoryData) 
        {   
            //if some category in category parent tree is disabled the whole subtree is disabled
            if (($aCategoryData['tiramizoo_enable']) == -1) {
                $aTiramizooInheritedData['tiramizoo_enable'] = 0;
            }

            //if some category in category parent tree disabled use std package the whole subtree don't use std package
            if (($aCategoryData['tiramizoo_use_package']) == -1) {
                $aTiramizooInheritedData['tiramizoo_use_package'] = 0;
            }

            //category can override dimensions and weight but only all or nothing
            if ($aCategoryData['tiramizoo_weight'] && $aCategoryData['tiramizoo_width'] && $aCategoryData['tiramizoo_height'] && $aCategoryData['tiramizoo_length']) {
                $aTiramizooInheritedData['weight'] = $aCategoryData['tiramizoo_weight'];
                $aTiramizooInheritedData['width'] = $aCategoryData['tiramizoo_width'];
                $aTiramizooInheritedData['height'] = $aCategoryData['tiramizoo_height'];
                $aTiramizooInheritedData['length'] = $aCategoryData['tiramizoo_length'];
            }                                    
        }

        return $aTiramizooInheritedData;
    }

    /**
     * Recursive method for getting array of arrays product data (enable, weight, dimensions)
     * 
     * @param  oxCategory $oCategory
     * @param  array  $returnCategories
     * @return array Array of categories hierarchy
     */
    protected function _getParentsCategoryTree($oCategory, $returnCategories = array())
    {
        $oTiramizooCategoryExtended = oxNew('oxTiramizoo_CategoryExtended');
        $oTiramizooCategoryExtended->load($oTiramizooCategoryExtended->getIdByCategoryId($oCategory->getId()));

        $aTiramizooCategoryData = array();
        $aTiramizooCategoryData['oxid'] = $oCategory->oxcategories__oxid->value;
        $aTiramizooCategoryData['oxtitle'] = $oCategory->oxcategories__oxtitle->value;
        $aTiramizooCategoryData['oxsort'] = $oCategory->oxcategories__oxsort->value;
        $aTiramizooCategoryData['tiramizoo_enable'] = $oTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_enable->value;
        $aTiramizooCategoryData['tiramizoo_weight'] = $oTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_weight->value;
        $aTiramizooCategoryData['tiramizoo_width'] = $oTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_width->value;
        $aTiramizooCategoryData['tiramizoo_height'] = $oTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_height->value;
        $aTiramizooCategoryData['tiramizoo_length'] = $oTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_length->value;

        array_unshift($returnCategories, $aTiramizooCategoryData);

        if ($parentCategory = $oCategory->getParentCategory()) {
            $returnCategories = $this->_getParentsCategoryTree($parentCategory, $returnCategories);
        }

        return $returnCategories;
    }

    public function buildArticleEffectiveData($item = null)
    {
        if (!$item) {
            $item = new stdClass();
        }

        $oArticle = $this->getArticle();

        //get the data from categories hierarchy
        $aTiramizooInheritedData = $this->getArticleInheritData();

        //article override dimensions and weight but only if all parameters are specified
        if ($oArticle->oxarticles__oxweight->value && 
            $oArticle->oxarticles__oxwidth->value &&
            $oArticle->oxarticles__oxheight->value && 
            $oArticle->oxarticles__oxlength->value) {
                $item->weight = $oArticle->oxarticles__oxweight->value;
                $item->width = $oArticle->oxarticles__oxwidth->value * 100;
                $item->height = $oArticle->oxarticles__oxheight->value * 100;
                $item->length = $oArticle->oxarticles__oxlength->value * 100;
        } else {
            $item->weight = isset($aTiramizooInheritedData['weight']) && $aTiramizooInheritedData['weight'] ? $aTiramizooInheritedData['weight'] : 0;
            $item->width = isset($aTiramizooInheritedData['width']) && $aTiramizooInheritedData['width'] ? $aTiramizooInheritedData['width'] : 0;
            $item->height = isset($aTiramizooInheritedData['height']) && $aTiramizooInheritedData['height'] ? $aTiramizooInheritedData['height'] : 0;
            $item->length = isset($aTiramizooInheritedData['length']) && $aTiramizooInheritedData['length'] ? $aTiramizooInheritedData['length'] : 0;
        }

        //convert to float val
        $item->weight = floatval($item->weight);
        $item->width = floatval($item->width);
        $item->height = floatval($item->height);
        $item->length = floatval($item->length);
        $item->quantity = floatval($item->quantity);

        return $item;
    }

    public function getDisabledCategory() 
    {
        $oArticle = $this->getArticle();
        $oCategory = $oArticle->getCategory();

        // if article has no assigned categories return only global settings
        if (!$oCategory) {
            return  null;
        }

        $aCheckCategories = $this->_getParentsCategoryTree($oCategory);

        foreach ($aCheckCategories as $aCategoryData) 
        {   
            //if some category in category parent tree is disabled the wole subtree is disabled
            if (($aCategoryData['tiramizoo_enable']) == -1) {
                $oCategory = oxNew( 'oxcategory' );
                $oCategory->load( $aCategoryData['oxid'] );
                return $oCategory;
            }
        }
    }


    public function getInheritedCategory() 
    {
        $oArticle = $this->getArticle();
        $oCategory = $oArticle->getCategory();

        // if article has no assigned categories return only global settings
        if (!$oCategory) {
            return  null;
        }

        $aCheckCategories = $this->_getParentsCategoryTree($oCategory);

        $inheritedCategoryId = null;

        foreach ($aCheckCategories as $aCategoryData) 
        {   
            if ($aCategoryData['tiramizoo_weight'] && $aCategoryData['tiramizoo_width'] && $aCategoryData['tiramizoo_height'] && $aCategoryData['tiramizoo_length']) {

                $inheritedCategoryId = $aCategoryData['oxid'];
            }
        }

        if ($inheritedCategoryId) {
            $oCategory = oxNew( 'oxcategory' );
            $oCategory->load( $aTiramizooCategoryData['oxid'] );
            return $oCategory;
        }

        return null;
    }



}