<?php

if ( !class_exists('oxTiramizooHelper') ) {
    require_once getShopBasePath() . '/modules/oxtiramizoo/core/oxtiramizoo_helper.php';
}

/**
 * Tiramizoo Order view. Extends to proccess Tiramizoo delivery
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_order extends oxTiramizoo_order_parent
{
    /**
     * Executes parent::render(), pass variable to template to check
     * if tiramizoo module is running now
     * 
     * @return string template file
     */
    public function render()
    {
        if (in_array(oxSession::getVar('sShipSet'), array('Tiramizoo', 'TiramizooEvening', 'TiramizooSelectTime'))) {
            $this->_aViewData['sTiramizooTimeWindow'] = oxTiramizooHelper::getLabelDeliveryWindow(oxSession::getVar( 'sTiramizooTimeWindow' ));
        }

        return parent::render();
    }

    /**
     * Execute save order with tiramizoo shipping
     * 
     * @return string
     */
    public function execute()
    {
        try {
            return parent::execute();
        } catch ( oxTiramizoo_SendOrderException $oEx ) {
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
        }
    }
}