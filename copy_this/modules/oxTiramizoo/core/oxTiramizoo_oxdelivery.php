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
            //$this->_oPrice->setPrice(3000); //price: 7.90 gross



            $data = new stdClass();
            $data->pickup_postal_code = $this->getConfig()->getConfigParam('oxTiramizoo_shop_postal_code');
            $data->delivery_postal_code = "81925";
            $data->items = array();

            $data->items = array(array
                (
                  "width"=> 120,
                  "height"=> 82,
                  "length"=> 50,
                  "weight"=> 2,
                  "quantity"=> 1

                ),
                array(
                  "width"=> 40,
                  "height"=> 40,
                  "length"=> 120,
                  "weight"=> 5.4,
                  "quantity"=> 3
                ));



            foreach ($oBasket->getBasketArticles() as $key => $oArticle) 
            {
                $item = array();

                if ($oArticle->oxarticles__oxweight->value) {
                    $item['weight'] = $oArticle->oxarticles__oxweight->value;
                }

                if ($oArticle->oxarticles__oxweight->value) {
                    $item['width'] = $oArticle->oxarticles__oxwidth->value;
                }

                if ($oArticle->oxarticles__oxweight->value) {
                    $item['height'] = $oArticle->oxarticles__oxheight->value;
                }

                if ($oArticle->oxarticles__oxweight->value) {
                    $item['length'] = $oArticle->oxarticles__oxlength->value;
                }



                //$item;
                // echo $oArticle->oxarticles__oxweight->value;
                // echo $oArticle->oxarticles__oxwidth->value;
                // echo $oArticle->oxarticles__oxlength->value;
                // echo $oArticle->oxarticles__oxheight->value;
                //echo $oArticle->oxarticles__oxtiramizooenable->value;
                
                //print_r($oArticle);

            }

        }


        // calculating total price
        return $this->_oPrice;
    }
 
}
