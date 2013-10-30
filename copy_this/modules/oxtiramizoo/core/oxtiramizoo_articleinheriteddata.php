<?php
/**
 * This file is part of the oxTiramizoo OXID eShop plugin.
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  module
 * @package   oxTiramizoo
 * @author    Tiramizoo GmbH <support@tiramizoo.com>
 * @copyright Tiramizoo GmbH
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Used for getting effective values like dimensions and weight,
 * enable property and indivual package info for product and category
 *
 * @extends oxConfig
 * @package oxTiramizoo
 */
class oxTiramizoo_ArticleInheritedData
{
    /**
     * Inherited by global value
     */
    const INHERITED_BY_GLOBAL = 'global';

    /**
     * Inherited by category value
     */
    const INHERITED_BY_CATEGORY = 'category';

    /**
     * Mo inherited
     */
    const INHERITED_BY_SELF = 'self';

    /**
     * Recursive method for getting array of arrays product data (enable, weight, dimensions)
     *
     * @param  oxCategory $oCategory
     * @param  array  $returnCategories
     *
     * @return array Array of categories hierarchy
     */
    public function getParentsCategoryTree($oCategory, $returnCategories = array())
    {
        $oTiramizooCategoryExtended = oxNew('oxTiramizoo_CategoryExtended');
        $oTiramizooCategoryExtended->load($oTiramizooCategoryExtended->getIdByCategoryId($oCategory->getId()));

        $aTiramizooCategoryData = array();
        $aTiramizooCategoryData['oxid'] = $oCategory->oxcategories__oxid->value;
        $aTiramizooCategoryData['oxtitle'] = $oCategory->oxcategories__oxtitle->value;
        $aTiramizooCategoryData['oxsort'] = $oCategory->oxcategories__oxsort->value;

        $aTiramizooCategoryData['tiramizoo_use_package'] = $oTiramizooCategoryExtended
                                                            ->oxtiramizoocategoryextended__tiramizoo_use_package
                                                            ->value;

        $aTiramizooCategoryData['tiramizoo_enable'] = $oTiramizooCategoryExtended
                                                        ->oxtiramizoocategoryextended__tiramizoo_enable
                                                        ->value;

        $aTiramizooCategoryData['tiramizoo_weight'] = $oTiramizooCategoryExtended
                                                        ->oxtiramizoocategoryextended__tiramizoo_weight
                                                        ->value;

        $aTiramizooCategoryData['tiramizoo_width'] = $oTiramizooCategoryExtended
                                                        ->oxtiramizoocategoryextended__tiramizoo_width
                                                        ->value;

        $aTiramizooCategoryData['tiramizoo_height'] = $oTiramizooCategoryExtended
                                                        ->oxtiramizoocategoryextended__tiramizoo_height
                                                        ->value;

        $aTiramizooCategoryData['tiramizoo_length'] = $oTiramizooCategoryExtended
                                                        ->oxtiramizoocategoryextended__tiramizoo_length
                                                        ->value;

        array_unshift($returnCategories, $aTiramizooCategoryData);

        if ($parentCategory = $oCategory->getParentCategory()) {
            $returnCategories = $this->getParentsCategoryTree($parentCategory, $returnCategories);
        }

        return $returnCategories;
    }

    /**
     * Returns global data array with values from settings.
     *
     * @return array
     */
    public function getGlobalEffectiveData()
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        $aData = array();

        $aData['tiramizoo_enable'] = true;
        $aData['tiramizoo_use_package'] = ($oTiramizooConfig->getShopConfVar('oxTiramizoo_package_strategy') != 0);

        // get from tiramizoo settings centimeters and kilograms
        $aData['weight'] = floatval($oTiramizooConfig->getShopConfVar('oxTiramizoo_global_weight'));
        $aData['width'] = floatval($oTiramizooConfig->getShopConfVar('oxTiramizoo_global_width'));
        $aData['height'] = floatval($oTiramizooConfig->getShopConfVar('oxTiramizoo_global_height'));
        $aData['length'] = floatval($oTiramizooConfig->getShopConfVar('oxTiramizoo_global_length'));

        $aData['tiramizoo_enable_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_GLOBAL;
        $aData['tiramizoo_use_package_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_GLOBAL;
        $aData['tiramizoo_dimensions_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_GLOBAL;

        return $aData;
    }

    /**
     * Returns category data array containing inherited from global values and parent category values.
     *
     * @return array
     */
    public function getCategoryEffectiveData($oCategory, $bExcludeCurrentCategory = false)
    {
        $aData = $this->getGlobalEffectiveData();

        $aCheckCategories = $this->getParentsCategoryTree($oCategory);

        foreach ($aCheckCategories as $aCategoryData)
        {
            $aData = $this->buildCategoryEffectiveDataEnable($aData,
                                                             $oCategory,
                                                             $aCategoryData,
                                                             $bExcludeCurrentCategory);

            $aData = $this->buildCategoryEffectiveDataUsePackage($aData,
                                                                 $oCategory,
                                                                 $aCategoryData,
                                                                 $bExcludeCurrentCategory);

            $aData = $this->buildCategoryEffectiveDataDimensions($aData,
                                                                 $oCategory,
                                                                 $aCategoryData,
                                                                 $bExcludeCurrentCategory);
        }

        return $aData;
    }

    /**
     * Returns category effective enable value
     *
     * @param  array  $aData
     * @param  oxcategory  $oCategory
     * @param  array  $aCategoryData
     * @param  boolean $bExcludeCurrentCategory
     *
     * @return array
     */
    public function buildCategoryEffectiveDataEnable($aData, $oCategory, $aCategoryData,
        $bExcludeCurrentCategory = false
    )
    {
        if ($aCategoryData['tiramizoo_enable'] == -1) {
            $aData['tiramizoo_enable'] = false;
        } else if($aCategoryData['tiramizoo_enable'] == 1) {
            $aData['tiramizoo_enable'] = true;
        }

        if ($aCategoryData['tiramizoo_enable']) {
            $aData['tiramizoo_enable_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_CATEGORY;
            $aData['tiramizoo_enable_inherited_from_category_oxid'] = $aCategoryData['oxid'];
            $aData['tiramizoo_enable_inherited_from_category_title'] = $aCategoryData['oxtitle'];

            if ($bExcludeCurrentCategory && ($aCategoryData['oxid'] == $oCategory->getId())) {
                $aData['tiramizoo_enable_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_SELF;
            }
        }

        return $aData;
    }

    /**
     * Returns category effective use package value
     *
     * @param  array  $aData
     * @param  oxcategory  $oCategory
     * @param  array  $aCategoryData
     * @param  boolean $bExcludeCurrentCategory
     *
     * @return array
     */
    public function buildCategoryEffectiveDataUsePackage($aData, $oCategory, $aCategoryData,
        $bExcludeCurrentCategory = false
    )
    {
        if ($aCategoryData['tiramizoo_use_package'] == -1) {
            $aData['tiramizoo_use_package'] = false;
        } else if($aCategoryData['tiramizoo_use_package'] == 1) {
            $aData['tiramizoo_use_package'] = true;
        }

        if ($aCategoryData['tiramizoo_use_package']) {
            $aData['tiramizoo_use_package_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_CATEGORY;
            $aData['tiramizoo_use_package_inherited_from_category_oxid'] = $aCategoryData['oxid'];
            $aData['tiramizoo_use_package_inherited_from_category_title'] = $aCategoryData['oxtitle'];

            if ($bExcludeCurrentCategory && ($aCategoryData['oxid'] == $oCategory->getId())) {
                $aData['tiramizoo_use_package_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_SELF;
            }
        }

        return $aData;
    }

    /**
     * Returns category effective dimensions value
     *
     * @param  array  $aData
     * @param  oxcategory  $oCategory
     * @param  array  $aCategoryData
     * @param  boolean $bExcludeCurrentCategory
     *
     * @return array
     */
    public function buildCategoryEffectiveDataDimensions($aData, $oCategory, $aCategoryData,
        $bExcludeCurrentCategory = false
    )
    {
        //category can override dimensions and weight but only all or nothing
        if ($this->dimensionsAndWeightCanBeInherited($aCategoryData)) {
            $aData['weight'] = $aCategoryData['tiramizoo_weight'];
            $aData['width'] = $aCategoryData['tiramizoo_width'];
            $aData['height'] = $aCategoryData['tiramizoo_height'];
            $aData['length'] = $aCategoryData['tiramizoo_length'];

            $aData['tiramizoo_dimensions_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_CATEGORY;
            $aData['tiramizoo_dimensions_inherited_from_category_oxid'] = $aCategoryData['oxid'];
            $aData['tiramizoo_dimensions_inherited_from_category_title'] = $aCategoryData['oxtitle'];

            if ($bExcludeCurrentCategory && ($aCategoryData['oxid'] == $oCategory->getId())) {
                $aData['tiramizoo_dimensions_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_SELF;
            }
        }

        return $aData;
    }

    /**
     * Cehck if all dimensions and weight are non zero value.
     *
     * @param  array $aCategoryData
     * @return boolean
     */
    public function dimensionsAndWeightCanBeInherited($aCategoryData)
    {
        return  $aCategoryData['tiramizoo_weight']
                && $aCategoryData['tiramizoo_width']
                && $aCategoryData['tiramizoo_height']
                && $aCategoryData['tiramizoo_length'];
    }

    /**
     * Get product data (enable, weight, dimensions, individual package) from global settings or category or self
     *
     * @param  oxArticle $oArticle
     *
     * @return array
     */
    public function getArticleEffectiveData($oArticle)
    {
        //get the global data
        $aData = $this->getGlobalEffectiveData();

        $oCategory = $oArticle->getCategory();

        // if article has assigned categories get effective data from categories tree
        if ($oCategory) {
            $aData = $this->getCategoryEffectiveData($oCategory);
        }

        $oArticleExtended = oxNew('oxTiramizoo_ArticleExtended');
        $oArticleExtended->loadByArticle($oArticle);

        //check weight and dimensions
        if ($oArticleExtended->hasWeightAndDimensions()) {
            $aData['weight'] = $oArticle->oxarticles__oxweight->value;
            $aData['width'] = $oArticle->oxarticles__oxwidth->value * 100;
            $aData['height'] = $oArticle->oxarticles__oxheight->value * 100;
            $aData['length'] = $oArticle->oxarticles__oxlength->value * 100;

            $aData['tiramizoo_dimensions_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_SELF;
        }

        if ($oArticleExtended->oxtiramizooarticleextended__tiramizoo_enable->value == -1) {
            $aData['tiramizoo_enable'] = false;
            $aData['tiramizoo_enable_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_SELF;
        } else if ($oArticleExtended->oxtiramizooarticleextended__tiramizoo_enable->value == 1) {
            $aData['tiramizoo_enable'] = true;
            $aData['tiramizoo_enable_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_SELF;
        }

        if ($oArticleExtended->oxtiramizooarticleextended__tiramizoo_use_package->value == -1) {
            $aData['tiramizoo_use_package'] = false;
            $aData['tiramizoo_use_package_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_SELF;
        } else if ($oArticleExtended->oxtiramizooarticleextended__tiramizoo_use_package->value == 1) {
            $aData['tiramizoo_use_package'] = true;
            $aData['tiramizoo_use_package_inherited_from'] = oxTiramizoo_ArticleInheritedData::INHERITED_BY_SELF;
        }

        return $aData;
    }
}
