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

        //@TODO: 
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