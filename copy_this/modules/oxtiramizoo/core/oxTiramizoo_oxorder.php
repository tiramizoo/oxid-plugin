<?php

class oxTiramizoo_oxOrder extends oxTiramizoo_oxOrder_parent
{
 
 
    public function getShippingSetList()
    {   
        // in which country we deliver
        if ( !( $sShipId = $this->oxorder__oxdelcountryid->value ) ) {
            $sShipId = $this->oxorder__oxbillcountryid->value;
        }

        $oBasket = $this->_getOrderBasket( false );


        // unsetting bundles
        $oOrderArticles = $this->getOrderArticles();
        foreach ( $oOrderArticles as $sItemId => $oItem ) {
            if ( $oItem->isBundle() ) {
                $oOrderArticles->offsetUnset( $sItemId );
            }
        }
        // add this order articles to basket and recalculate basket
        $this->_addOrderArticlesToBasket( $oBasket, $oOrderArticles );

        // recalculating basket
        $oBasket->calculateBasket( true );

        // load fitting deliveries list
        $oDeliveryList = oxNew( "oxDeliveryList", "core" );
        $oDeliveryList->setCollectFittingDeliveriesSets( true );

        return $oDeliveryList->getDeliveryList( $oBasket, $this->getOrderUser(), $sShipId );
    }
}
