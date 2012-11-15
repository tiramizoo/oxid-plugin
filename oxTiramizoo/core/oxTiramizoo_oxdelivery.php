<?php

class oxTiramizoo_oxDelivery extends oxTiramizoo_oxDelivery_parent
{

    /**
     * Returns oxprice object for delivery costs
     *
     * @param double $dVat delivery vat
     *
     * @return oxPrice
     */
    public function getDeliveryPrice( $dVat = null )
    {
        if ( $this->_oPrice === null ) {
            // loading oxprice object for final price calculation
            $this->_oPrice = oxNew( 'oxPrice' );

            if ( !$this->_blDelVatOnTop ) {
                $this->_oPrice->setBruttoPriceMode();
            } else {
                $this->_oPrice->setNettoPriceMode();
            }

            $this->_oPrice->setVat( $dVat );

            // if article is free shipping, price for delivery will be not calculated
            if ( $this->_blFreeShipping ) {
                return $this->_oPrice;
            }

            // calculating base price value
            switch ( $this->oxdelivery__oxaddsumtype->value ) {
                case 'abs':

                    $dAmount = 0;

                    if ( $this->oxdelivery__oxfixed->value == 0 ) {
                        // 1. Once per Cart
                        $dAmount = 1;
                    } elseif ( $this->oxdelivery__oxfixed->value == 1 ) {
                        // 2. Once per Product overall
                        $dAmount = $this->_iProdCnt;
                    } elseif ( $this->oxdelivery__oxfixed->value == 2 ) {
                        // 3. Once per Product in Cart
                        $dAmount = $this->_iItemCnt;
                    }

                    $oCur = $this->getConfig()->getActShopCurrencyObject();
                    $this->_oPrice->add( $this->oxdelivery__oxaddsum->value * $oCur->rate );
                    $this->_oPrice->multiply( $dAmount );
                    break;
                case '%':

                    $this->_oPrice->add( $this->_dPrice /100 * $this->oxdelivery__oxaddsum->value );
                    break;
            }
        }

        if ($this->oxdelivery__oxtitle->value == 'Tiramizoo') {
            $oBasket = $this->getSession()->getBasket();
            //@todo: api
            $this->_oPrice->setPrice(5000);
        }


        // calculating total price
        return $this->_oPrice;
    }
 
}
