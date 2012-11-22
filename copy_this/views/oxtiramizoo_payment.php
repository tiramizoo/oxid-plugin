<?php
class oxTiramizoo_Payment extends oxTiramizoo_Payment_parent
{


    public function getAllSets()
    {
        //@TODO: 

        if ( $this->_aAllSets === null ) {
            $this->_aAllSets = false;

            if ($this->getPaymentList()) {
                return $this->_aAllSets;
            }
        }

        //@TODO: check if remove Tiramizoo
        if (!$this->tiramizooCanShow()) {
            unset($this->_aAllSets['Tiramizoo']);
        }

        return $this->_aAllSets;
    }


    /**
     * Changes shipping set to chosen one. Sets basket status to not up-to-date, which later
     * forces to recalculate it
     *
     * @return null
     */
    public function changeshipping()
    {
        $mySession = $this->getSession();

        $oBasket = $mySession->getBasket();
        $oBasket->setShipping( null );
        $oBasket->onUpdate();
        oxSession::setVar( 'sShipSet', oxConfig::getParameter( 'sShipSet' ) );

        if (oxConfig::getParameter( 'sTiramizooTimeWindow' )) {
            oxSession::setVar( 'sTiramizooTimeWindow', oxConfig::getParameter( 'sTiramizooTimeWindow' ) );
        }
    }

    /**
     * Executes parent::render(), checks if this connection secure
     * (if not - redirects to secure payment page), loads user object
     * (if user object loading was not successfull - redirects to start
     * page), loads user delivery/shipping information. According
     * to configuration in admin, user profile data loads delivery sets,
     * and possible payment methods. Returns name of template to render
     * payment::_sThisTemplate.
     *
     * @return  string  current template file name
     */





    public function getTiramizooTimeWindow()
    {
        return oxSession::getVar( 'sTiramizooTimeWindow' );
    }


    public function isTiramizooCurrentShiippingMethod()
    {
        $oBasket = $this->getSession()->getBasket();
        return  $oBasket->getShippingId() == 'Tiramizoo';
    }

    public function getAvailableDeliveryHours()
    {
        $oxTiramizooConfig = $this->getConfig();

        $aAvailableDeliveryHours = array();
        $aAvailablePickupHours = $this->getAvailablePickupHours();

        $orderOffsetTime = (int)$oxTiramizooConfig->getShopConfVar('oxTiramizoo_order_pickup_offset');
        $deliveryOffsetTime = (int)$oxTiramizooConfig->getShopConfVar('oxTiramizoo_pickup_del_offset');

        $dateTime = date('Y-m-d H:i');

        $itertator = 0;

        while ($itertator++ < 3)
        {
            $dateTime = $this->getNextAvailableDate($dateTime);

            $aAvailableDeliveryHours[$dateTime] = $this->getLabelDeliveryWindow($dateTime);

            if (($itertator == 1) && !oxSession::hasVar( 'sTiramizooTimeWindow' )) {
                oxSession::setVar( 'sTiramizooTimeWindow',  $dateTime);
            }
        }



        return $aAvailableDeliveryHours;
    }

    public function getLabelDeliveryWindow($dateTime)
    {
        $oxTiramizooConfig = $this->getConfig();

        $orderOffsetTime = (int)$oxTiramizooConfig->getShopConfVar('oxTiramizoo_order_pickup_offset');
        $deliveryOffsetTime = (int)$oxTiramizooConfig->getShopConfVar('oxTiramizoo_pickup_del_offset');


        if (strtotime(date('Y-m-d', strtotime($dateTime))) ==  strtotime(date('Y-m-d'))) {
            return oxLang::getInstance()->translateString('oxTiramizoo_Today', oxLang::getInstance()->getBaseLanguage(), false) . strtotime('Y-m-d', strtotime($dateTime)) . ' ' . date('H:i', strtotime($dateTime)) . ' - ' . date('H:i', strtotime('+' . $deliveryOffsetTime . 'minutes', strtotime($dateTime)));
        } else if (strtotime(date('Y-m-d', strtotime($dateTime))) ==  strtotime(date('Y-m-d', strtotime('+1days', strtotime(date('Y-m-d'))))))
        {
            return oxLang::getInstance()->translateString('oxTiramizoo_Tomorrow', oxLang::getInstance()->getBaseLanguage(), false) . strtotime('Y-m-d', strtotime($dateTime)) . ' ' . date('H:i', strtotime($dateTime)) . ' - ' . date('H:i', strtotime('+' . $deliveryOffsetTime . 'minutes', strtotime($dateTime)));

        } else {
            return $dateTime . ' - ' . date('H:i', strtotime('+' . $deliveryOffsetTime . 'minutes', strtotime($dateTime)));
        }
    }


    public function getNextAvailableDate($fromDateTime)
    {
        $oxTiramizooConfig = $this->getConfig();

        $orderOffsetTime = (int)$oxTiramizooConfig->getShopConfVar('oxTiramizoo_order_pickup_offset');

        $fromDateTime = date('Y-m-d H:i', strtotime('+' . $orderOffsetTime . ' minutes', strtotime($fromDateTime)));
        $fromHour = date('H:i', strtotime($fromDateTime));
        $fromDate = date('Y-m-d', strtotime($fromDateTime));
        $fromDayNum = date('w', strtotime($fromDateTime));

        if (in_array($fromDayNum, range(1, 5))) {
            foreach ($this->getAvailablePickupHours() as $sAvailablePickupHour) 
            {
                if (strtotime($fromHour) < strtotime($sAvailablePickupHour)) {
                    return $fromDate . ' ' . $sAvailablePickupHour;
                }
            }
        } 

        if (in_array($fromDayNum, array(6))) {
            $nextDateTime = date('Y-m-d', strtotime('+2days', strtotime($fromDateTime))) . ' 00:00';
            return $this->getNextAvailableDate($nextDateTime);
        } else {
            $nextDateTime = date('Y-m-d', strtotime('+1days', strtotime($fromDateTime))) . ' 00:00';
            return $this->getNextAvailableDate($nextDateTime);
        }
    }

    public function getAvailablePickupHours()
    {
        $aAvailablePickupHours = array();

        $oxTiramizooConfig = $this->getConfig();

        for ($i = 1; $i <= 3 ; $i++) 
        {
            if ($pickupHour = $oxTiramizooConfig->getShopConfVar('oxTiramizoo_shop_pickup_hour_' . $i)) {
                $aAvailablePickupHours[] = $pickupHour;
            }

        }
        return $aAvailablePickupHours;
    }


    public function tiramizooCanShow() 
    {
        $oBasket = $this->getSession()->getBasket();

        $oOrder = oxNew( 'oxorder' );
        $address = $oOrder->getDelAddressInfo();

        $oUser = $this->getUser();

        $sZipCode = $address ? $address->oxaddress__oxzip->value : $oUser->oxuser__oxzip->value;

        if (!count($this->getAvailablePickupHours())) {
            return false;
        }

        //check if Tiramizoo can deliver this basket
        $data = new stdClass();
        $data->pickup_postal_code = $this->getConfig()->getConfigParam('oxTiramizoo_shop_postal_code');
        $data->delivery_postal_code = $sZipCode;
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

        require_once getShopBasePath() . '/modules/oxtiramizoo/core/TiramizooApi/oxTiramizooApi.php';


        $result = oxTiramizooApi::getInstance()->getQuotes($data, true);

        //echo json_encode($data);
        //var_dump($result);

        if (!in_array($result['http_status'], array(200, 201))) {
             //return false;
        }

        return true;
    }


}