<?php

/**
 * Tiramizoo category tab
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_Category_Tab extends oxAdminDetails
{
    /**
     * Loads category extended object data, pases it to Smarty engine and returns
     * name of template file "oxTiramizoo_category_tab.tpl".
     *
     * @return string
     */
    public function render()
    {
        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
            parent::render();
        }
        // @codeCoverageIgnoreEnd

        $this->_aViewData['edit'] = oxNew( 'oxcategory' );

        $soxId = $this->getConfig()->getRequestParameter( "oxid");
        
        $oTiramizooCategoryExtended = oxNew('oxTiramizoo_CategoryExtended');
        $oTiramizooCategoryExtended->load($oTiramizooCategoryExtended->getIdByCategoryId($soxId));
        $this->_aViewData['oxTiramizooCategoryExtended'] = $oTiramizooCategoryExtended;

        return "oxTiramizoo_category_tab.tpl";
    }

    /**
     * Saves category extended.
     *
     * @return void
     */
    public function save()
    {
        $soxId   = $this->getConfig()->getRequestParameter( "oxid");
        $aParams = $this->getConfig()->getRequestParameter( "oxTiramizooCategoryExtended");
        
        if ( $soxId != "-1" && isset( $soxId ) ) {
            $oTiramizooCategoryExtended = oxNew('oxTiramizoo_CategoryExtended');
            $oTiramizooCategoryExtended->load($oTiramizooCategoryExtended->getIdByCategoryId($soxId));

            $oTiramizooCategoryExtended->assign( $aParams );
            $oTiramizooCategoryExtended->save();
        }
    }
}
