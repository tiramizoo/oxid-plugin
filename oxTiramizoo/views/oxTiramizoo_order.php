<?php

class oxTiramizoo_order extends oxTiramizoo_order_parent
{
    /**
     * Template variable getter. Returns shipping set
     *
     * @return object
     */
    public function getShipSet()
    {
        if ( $this->_oShipSet === null ) {
            $this->_oShipSet = false;
            if ( $oBasket = $this->getBasket() ) {
                $oShipSet = oxNew( 'oxdeliveryset' );
                if ( $oShipSet->load( $oBasket->getShippingId() )) {
                    $this->_oShipSet = $oShipSet;
                }
            }
        }
        return $this->_oShipSet;
    }

}