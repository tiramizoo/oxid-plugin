<?php


/**
 * Extends oxbasket class. Overrides method to calculate price
 */
class oxTiramizoo_oxbasket extends oxTiramizoo_oxbasket_parent
{
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

    public function isValid()
    {
        if (!count($this->getBasketArticles())) {
            return false;
        }

        $oTiramizooConfig = oxRegistry::get('oxTiramizooConfig');

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

            $oArticleExtended = oxTiramizoo_ArticleExtended::findOneByFiltersOrCreate(array('oxarticleid' => $oArticle->oxarticles__oxid->value));

            if (!$oArticleExtended->isEnabled()) {
                return false;
            }
        }

        return true;
    }
}