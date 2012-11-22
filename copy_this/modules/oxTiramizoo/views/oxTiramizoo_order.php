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


    public function getTiramizooTimeWindow()
    {
        $paymentView = new oxTiramizoo_Payment();
        return $paymentView->getLabelDeliveryWindow(oxSession::getVar( 'sTiramizooTimeWindow' ));
    }

    public function execute()
    {






        $oBasket = $this->getSession()->getBasket();

        $oxTiramizooConfig = $this->getConfig();







        $oOrder = oxNew( 'oxorder' );
        $deliveryAddress = $oOrder->getDelAddressInfo();

        $oUser = $this->getUser();


        $data = new stdClass();

        $data->pickup = new stdClass();
        $data->pickup->address_line1 = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_address');
        $data->pickup->city = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_city');
        $data->pickup->postal_code = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_postal_code');
        $data->pickup->country_code = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_country_code');
        $data->pickup->name = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_contact_name');
        $data->pickup->phone_number = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_phone_number');
        $data->pickup->email = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_email_address');
        $data->pickup->before = date('c', strtotime(oxSession::getVar( 'sTiramizooTimeWindow' )));
        $data->pickup->after = date('c', strtotime(oxSession::getVar( 'sTiramizooTimeWindow' )));

        $data->delivery = new stdClass();

        $data->delivery->email = $oUser->oxuser__oxusername->value; 

        if ($deliveryAddress)  {
            $data->delivery->address_line_1 = $deliveryAddress->oxaddress__oxstreet->value . ' ' . $deliveryAddress->oxaddress__oxstreetnr->value;
            $data->delivery->city = $deliveryAddress->oxaddress__oxcity->value;
            $data->delivery->postal_code = $deliveryAddress->oxaddress__oxzip->value;
            $data->delivery->country_code = $deliveryAddress->oxaddress__oxcountry->value;
            $data->delivery->name = $deliveryAddress->oxaddress__oxfname->value . ' ' . $deliveryAddress->oxaddress__oxlname->value;
            $data->delivery->phone_number = $deliveryAddress->oxaddress__oxfon->value;
        } else {
            $data->delivery->address_line_1 = $oUser->oxuser__oxstreet->value . ' ' . $deliveryAddress->oxaddress__oxstreetnr->value;
            $data->delivery->city = $oUser->oxuser__oxcity->value;
            $data->delivery->postal_code = $oUser->oxuser__oxzip->value;
            $data->delivery->country_code = $oUser->oxuser__oxcountry->value;
            $data->delivery->name = $oUser->oxusers__oxfname->value . ' ' . $deliveryAddress->oxuser__oxlname->value;
            $data->delivery->phone_number = $oUser->oxuser__oxfon->value;
        }

        $data->description = "some articles";
        $data->external_id = 123456789;
        $data->web_hook_url = "http://oxid.dev/test";

        $data->items = array();

        foreach ($oBasket->getBasketArticles() as $key => $oArticle) 
        {
            $item = new stdClass();
            $item->weight = 2;
            $item->width = 30;
            $item->height = 30;
            $item->length = 35;

            if ($oArticle->oxarticles__oxweight->value) {
                $item->weight = $oArticle->oxarticles__oxweight->value;
            }

            if ($oArticle->oxarticles__oxwidth->value) {
                $item->width = $oArticle->oxarticles__oxwidth->value;
            }

            if ($oArticle->oxarticles__oxheight->value) {
                $item->height = $oArticle->oxarticles__oxheight->value;
            }

            if ($oArticle->oxarticles__oxlength->value) {
                $item->length = $oArticle->oxarticles__oxlength->value;
            }

            $item->quantity = $oBasket->getArtStockInBasket($oArticle->oxarticles__oxid->value);

            $data->items[] = $item;
        }




        $result = oxTiramizooApi::getInstance()->setOrder($data);

        echo json_encode($data);
        var_dump($result);



        exit;

    }

}