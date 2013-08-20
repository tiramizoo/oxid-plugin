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
 * Extends oxbasket class. Overrides method to calculate price
 *
 * @extends oxbasket
 * @package oxTiramizoo
 */
class oxTiramizoo_oxbasket extends oxTiramizoo_oxbasket_parent
{

    /**
     * Calculates basket delivery costs if oxTiramizoo_DeliveryPrice
     * has overrided method
     *
     * @extend oxbasket::_calcDeliveryCost()
     *
     * @see oxTiramizoo_DeliveryPrice
     *
     * @return oxPrice
     */
    protected function _calcDeliveryCost()
    {
        $oDeliveryPrice = parent::_calcDeliveryCost();

        if (($this->getShippingId() == oxTiramizoo_DeliverySet::TIRAMIZOO_DELIVERY_SET_ID)) {

            $oTiramizooDeliverySet = oxRegistry::get('oxTiramizoo_DeliverySet');
            $oTiramizooDeliverySet->init($this->getUser(), oxNew( 'oxorder' )->getDelAddressInfo());

            if ($oTiramizooDeliverySet->isTiramizooAvailable()) {
                $oTiramizooDeliveryPrice = oxNew('oxTiramizoo_DeliveryPrice');

                $oDeliveryPrice = $oTiramizooDeliveryPrice->calculateDeliveryPrice($oTiramizooDeliverySet, $this->getUser(), $this, $oDeliveryPrice);

                return $oDeliveryPrice;
            }
        }

        return $oDeliveryPrice;
    }

    /**
     * Check if items in basket are valid to Tiramizoo Delivery
     *
     * @return bool
     */
    public function isValid()
    {
        if (!count($this->getBasketArticles())) {
            return false;
        }

        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        foreach ($this->getBasketArticles() as $key => $oArticle)
        {
            //check if deliverable is set for articles with stock > 0
            if ($oTiramizooConfig->getShopConfVar('oxTiramizoo_articles_stock_gt_0')) {
                if ($oArticle->oxarticles__oxstock->value <= 0) {
                    return false;
                }
            }

            //NOTICE if article is only variant of parent article then load parent product as article
            if ($oArticle->oxarticles__oxparentid->value) {
                $parentArticleId = $oArticle->oxarticles__oxparentid->value;

                $oArticleParent = oxNew( 'oxarticle' );
                $oArticleParent->load($parentArticleId);
                $oArticle = $oArticleParent;
            }

            $oArticleExtended = oxnew('oxTiramizoo_ArticleExtended');
            $oArticleExtended->loadByArticle($oArticle);

            if (!$oArticleExtended->isEnabled()) {
                return false;
            }
        }

        return true;
    }
}
