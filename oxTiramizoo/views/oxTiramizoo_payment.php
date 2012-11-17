<?php
class oxTiramizoo_Payment extends oxTiramizoo_Payment_parent
{
    /**
     * Template variable getter. Returns paymentlist
     * http://wiki.oxidforge.org/Tutorials/en/Disable_Payment_Method 
     * @return object
     */
    public function getPaymentList()
    {
        if ( $this->_oPaymentList === null ) {
            $this->_oPaymentList = false;

            $sActShipSet = oxConfig::getParameter( 'sShipSet' );
            if ( !$sActShipSet ) {
                 $sActShipSet = oxSession::getVar( 'sShipSet' );
            }

            $oBasket = $this->getSession()->getBasket();

            // load sets, active set, and active set payment list
            list( $aAllSets, $sActShipSet, $aPaymentList ) = oxDeliverySetList::getInstance()->getDeliverySetData( $sActShipSet, $this->getUser(), $oBasket );

            $oBasket->setShipping( $sActShipSet );

            // calculating payment expences for preview for each payment
            $this->_setDeprecatedValues( $aPaymentList, $oBasket );
            $this->_oPaymentList = $aPaymentList;
            $this->_aAllSets     = $aAllSets;

        }
        return $this->_oPaymentList;
    }

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
        unset($this->_aAllSets['1b842e732a23255b1.91207750']);
        $this->checkIfTiramizooShow();

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
    public function render()
    {
        return parent::render();
    }

    public function getAvailableDeliveryHours()
    {
        $aAvailableDeliveryHours = array();
        $aAvailablePickupHours = $this->getAvailablePickupHours();

        $orderOffsetTime = 30;
        $deliveryOffsetTime = 90;

        $dateTime = date('Y-m-d H:i');

        $itertator = 1;

        while ($itertator++ <= 3)
        {
            $dateTime = $this->getNextAvailableDate($dateTime);

            if (strtotime('Y-m-d', strtotime($dateTime)) ==  date('Y-m-d')) {
                $aAvailableDeliveryHours[$dateTime] = oxLang::getInstance()->translateString('oxTiramizoo_Today', oxLang::getInstance()->getBaseLanguage(), false) . strtotime('Y-m-d', strtotime($dateTime)) . ' - ' . date('H:i', strtotime('+' . $deliveryOffsetTime . 'minutes', strtotime($dateTime)));
            } else if (strtotime('Y-m-d', strtotime($dateTime)) ==  date('Y-m-d', strtotime('+1days', strtotime(date('Y-m-d')))))
            {
                $aAvailableDeliveryHours[$dateTime] = oxLang::getInstance()->translateString('oxTiramizoo_Tomorrow', oxLang::getInstance()->getBaseLanguage(), false) . strtotime('Y-m-d', strtotime($dateTime)) . ' - ' . date('H:i', strtotime('+' . $deliveryOffsetTime . 'minutes', strtotime($dateTime)));

            } else {
                $aAvailableDeliveryHours[$dateTime] = $dateTime . ' - ' . date('H:i', strtotime('+' . $deliveryOffsetTime . 'minutes', strtotime($dateTime)));
            } 

        }

        return $aAvailableDeliveryHours;
    }

    public function getNextAvailableDate($fromDateTime)
    {
        $orderOffsetTime = 30;

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


    public function checkIfTiramizooShow() 
    {
        $oBasket = $this->getSession()->getBasket();

        foreach ($oBasket->getBasketArticles() as $key => $oArticle) {
            // echo $oArticle->oxarticles__oxweight->value;
            // echo $oArticle->oxarticles__oxwidth->value;
            // echo $oArticle->oxarticles__oxlength->value;
            // echo $oArticle->oxarticles__oxheight->value;
            echo $oArticle->oxarticles__oxtiramizooenable->value;
            
            //print_r($oArticle);

        }


        //print_r($oBasket->getBasketArticles());

    }


}