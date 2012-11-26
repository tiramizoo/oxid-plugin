<?php
/**
 * This file is part of the module oxTiramizoo for OXID eShop.
 *
 * The module oxTiramizoo for OXID eShop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free Software Foundation
 * either version 3 of the License, or (at your option) any later version.
 *
 * The module oxTiramizoo for OXID eShop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. 
 *  
 * See the GNU General Public License for more details <http://www.gnu.org/licenses/>
 *
 * @copyright: Tiramizoo GmbH
 * @author: Krzysztof Kowalik <kowalikus@gmail.com>
 * @package: oxTiramizoo
 * @license: http://www.gnu.org/licenses/
 * @version: 1.0.0
 * @link: http://tiramizoo.com
 */

require_once 'TiramizooApi.php';

/**
 * Tiramizoo API class
 *
 * @package: oxTiramizoo
 */
class oxTiramizooApi extends TiramizooApi
{
    protected static $_instance = null;

    protected function __construct()
    {
        $tiramizooApiUrl = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_api_url');
        $tiramizooApiToken = oxConfig::getInstance()->getShopConfVar('oxTiramizoo_api_token');
        parent::__construct($tiramizooApiUrl, $tiramizooApiToken);
    }

    public static function getInstance()
    {
        if ( !self::$_instance instanceof oxTiramizooApi ) {
                self::$_instance = new oxTiramizooApi();
        }

        return self::$_instance;
    }

    public function getQuotes($data, $cache = false)
    {
        $result = null;

        if ($cache) {
            $cachedDataKey = md5(json_encode($data));
            $cachedesultVarName = 'oxTiramizooQuote_' . $cachedDataKey;

            if ($result = oxSession::hasVar($cachedesultVarName)) {
                return oxSession::getVar($cachedesultVarName);
            }
        }

        $this->request('quotes', $data, $result);

        if ($cache && in_array($result['http_status'], array(200, 201))) {
            oxSession::setVar('oxTiramizooQuote_' . $cachedDataKey, $result);
        }

        return $result;
    }

    public function setOrder($data)
    {
        $result = null;
        $this->request('orders', $data, $result);
        return $result;
    }

    public function buildItemsData($oBasket)
    {
        $items = array();

        foreach ($oBasket->getBasketArticles() as $key => $oArticle) 
        {
            $item = new stdClass();
            $item->weight = null;
            $item->width = null;
            $item->height = null;
            $item->length = null;

            $inheritedData = $this->_getArticleInheritData($oArticle);

            //article is disabled return false
            if ($oArticle->oxarticles__tiramizoo_enable->value == -1) {
                return false;
            }

            if ($oArticle->oxarticles__tiramizoo_enable->value == 0) {
                if (isset($inheritedData['tiramizoo_enable']) && ($inheritedData['tiramizoo_enable'] == -1)) {
                    return false;
                }            
            }

            if ($oArticle->oxarticles__oxweight->value) {
                $item->weight = $oArticle->oxarticles__oxweight->value;
            } else {
                $item->weight = isset($inheritedData['weight']) && $inheritedData['weight'] ? $inheritedData['weight'] : 0;
            }

            if ($oArticle->oxarticles__oxwidth->value) {
                $item->width = $oArticle->oxarticles__oxwidth->value * 100;
            } else {
                $item->width = isset($inheritedData['width']) && $inheritedData['width'] ? $inheritedData['width'] : 0;
            }

            if ($oArticle->oxarticles__oxheight->value) {
                $item->height = $oArticle->oxarticles__oxheight->value * 100;
            } else {
                $item->height = isset($inheritedData['height']) && $inheritedData['height'] ? $inheritedData['height'] : 0;
            }

            if ($oArticle->oxarticles__oxlength->value) {
                $item->length = $oArticle->oxarticles__oxlength->value * 100;
            } else {
                $item->length = isset($inheritedData['length']) && $inheritedData['length'] ? $inheritedData['length'] : 0;
            }

            $item->quantity = $oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value);


            $item->weight = floatval($item->weight);
            $item->width = floatval($item->width);
            $item->height = floatval($item->height);
            $item->length = floatval($item->length);
            $item->quantity = floatval($item->quantity);


            $items[] = $item;
        }

        return $items;
    }

    //@todo:change to not hierarachical transparent
    protected function _getArticleInheritData($oArticle)
    {
        $oCategory = $oArticle->getCategory();

        $aCheckCategories = $this->_getParentsCategoryTree($oCategory);

        $oxTiramizooInheritedData = array();

        $allCategoryIsEnabled = true;

        foreach ($aCheckCategories as $aCategoryData) 
        {
            if (!isset($aCategoryData['tiramizoo_enable']) || !$aCategoryData['tiramizoo_enable']) {
                $allCategoryIsEnabled = false;
                break;
            }
        }

        $oxTiramizooInheritedData['tiramizoo_enable'] = $allCategoryIsEnabled;

        $oxTiramizooInheritedData['weight'] = $oCategory->oxcategories__tiramizoo_weight->value;
        $oxTiramizooInheritedData['width'] = $oCategory->oxcategories__tiramizoo_width->value;
        $oxTiramizooInheritedData['height'] = $oCategory->oxcategories__tiramizoo_height->value;
        $oxTiramizooInheritedData['length'] = $oCategory->oxcategories__tiramizoo_length->value;

        return $oxTiramizooInheritedData;
    }

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
}