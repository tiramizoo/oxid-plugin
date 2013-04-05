<?php


/**
 * This class contains static methods used for calculating pickup and delivery hours
 *
 * @package: oxTiramizoo
 */
class oxTiramizooArticleHelper extends oxSuperCfg
{
    /**
     * Singleton instance
     * 
     * @var oxTiramizooApi
     */
    protected static $_instance = null;

    /**
     * Get the instance of class
     * 
     * @return oxTiramizooHelper
     */
    public static function getInstance()
    {
        if ( !self::$_instance instanceof oxTiramizooArticleHelper ) {
                self::$_instance = new oxTiramizooArticleHelper();
        }

        return self::$_instance;
    }

    /**
     * Get product data (enable, weight, dimensions) from main category or parents
     * 
     * @param  oxArticle $oArticle
     * @return array
     */
    public function getArticleInheritData($oArticle)
    {
        //set the defaults
        $oxTiramizooInheritedData = array();

        $oxTiramizooInheritedData['tiramizoo_use_package'] = 1;
        $oxTiramizooInheritedData['tiramizoo_enable'] = 0;
        $oxTiramizooInheritedData['weight'] = 0;
        $oxTiramizooInheritedData['width'] = 0;
        $oxTiramizooInheritedData['height'] = 0;
        $oxTiramizooInheritedData['length'] = 0;

        $oxConfig = oxConfig::getInstance();

        // get from tiramizoo settings centimeters and kilograms
        $oxTiramizooInheritedData['tiramizoo_enable'] = ($oxConfig->getShopConfVar('oxTiramizoo_enable_immediate') == 'on') || ($oxConfig->getShopConfVar('oxTiramizoo_enable_evening') == 'on');
        $oxTiramizooInheritedData['weight'] = floatval($oxConfig->getShopConfVar('oxTiramizoo_global_weight'));
        $oxTiramizooInheritedData['width'] = floatval($oxConfig->getShopConfVar('oxTiramizoo_global_width'));
        $oxTiramizooInheritedData['height'] = floatval($oxConfig->getShopConfVar('oxTiramizoo_global_height'));
        $oxTiramizooInheritedData['length'] = floatval($oxConfig->getShopConfVar('oxTiramizoo_global_length'));

        $oCategory = $oArticle->getCategory();

        // if article has no assigned categories return only global settings
        if (!$oCategory) {
            return  $oxTiramizooInheritedData;
        }

        $aCheckCategories = $this->_getParentsCategoryTree($oCategory);

        foreach ($aCheckCategories as $aCategoryData) 
        {   
            //if some category in category parent tree is disabled the whole subtree is disabled
            if (($aCategoryData['tiramizoo_enable']) == -1) {
                $oxTiramizooInheritedData['tiramizoo_enable'] = 0;
            }

            //if some category in category parent tree disabled use std package the whole subtree don't use std package
            if (($aCategoryData['tiramizoo_use_package']) == -1) {
                $oxTiramizooInheritedData['tiramizoo_use_package'] = 0;
            }

            //category can override dimensions and weight but only all or nothing
            if ($aCategoryData['tiramizoo_weight'] && $aCategoryData['tiramizoo_width'] && $aCategoryData['tiramizoo_height'] && $aCategoryData['tiramizoo_length']) {
                $oxTiramizooInheritedData['weight'] = $aCategoryData['tiramizoo_weight'];
                $oxTiramizooInheritedData['width'] = $aCategoryData['tiramizoo_width'];
                $oxTiramizooInheritedData['height'] = $aCategoryData['tiramizoo_height'];
                $oxTiramizooInheritedData['length'] = $aCategoryData['tiramizoo_length'];
            }                                    
        }

        return $oxTiramizooInheritedData;
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
        $oxTiramizooCategoryData = array();
        $oxTiramizooCategoryData['oxid'] = $oCategory->oxcategories__oxid->value;
        $oxTiramizooCategoryData['oxtitle'] = $oCategory->oxcategories__oxtitle->value;
        $oxTiramizooCategoryData['oxsort'] = $oCategory->oxcategories__oxsort->value;
        $oxTiramizooCategoryData['tiramizoo_enable'] = $oCategory->oxcategories__tiramizoo_enable->value;
        $oxTiramizooCategoryData['tiramizoo_weight'] = $oCategory->oxcategories__tiramizoo_weight->value;
        $oxTiramizooCategoryData['tiramizoo_width'] = $oCategory->oxcategories__tiramizoo_width->value;
        $oxTiramizooCategoryData['tiramizoo_height'] = $oCategory->oxcategories__tiramizoo_height->value;
        $oxTiramizooCategoryData['tiramizoo_length'] = $oCategory->oxcategories__tiramizoo_length->value;

        array_unshift($returnCategories, $oxTiramizooCategoryData);

        if ($parentCategory = $oCategory->getParentCategory()) {
            $returnCategories = $this->_getParentsCategoryTree($parentCategory, $returnCategories);
        }

        return $returnCategories;
    }

    public function buildArticleEffectiveData($oArticle, $item = null)
    {
        if (!$item) {
            $item = new stdClass();
        }

        //get the data from categories hierarchy
        $inheritedData = $this->getArticleInheritData($oArticle);

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
            $item->weight = isset($inheritedData['weight']) && $inheritedData['weight'] ? $inheritedData['weight'] : 0;
            $item->width = isset($inheritedData['width']) && $inheritedData['width'] ? $inheritedData['width'] : 0;
            $item->height = isset($inheritedData['height']) && $inheritedData['height'] ? $inheritedData['height'] : 0;
            $item->length = isset($inheritedData['length']) && $inheritedData['length'] ? $inheritedData['length'] : 0;
        }

        return $item;
    }

    public function getDisabledCategory($oArticle) 
    {
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


    public function getInheritedCategory($oArticle) 
    {
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
            $oCategory->load( $oxTiramizooCategoryData['oxid'] );
            return $oCategory;
        }

        return null;
    }




}