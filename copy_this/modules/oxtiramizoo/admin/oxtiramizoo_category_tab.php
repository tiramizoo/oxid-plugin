<?php

/**
 * Tiramizoo category tab
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_Category_Tab extends oxAdminDetails
{
    /**
     * Loads category object data, pases it to Smarty engine and returns
     * name of template file "category_text.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oCategory = oxNew( 'oxcategory' );

        $soxId = oxConfig::getParameter( "oxid");
        // check if we right now saved a new entry
        $sSavedID = oxConfig::getParameter( "saved_oxid");
        if ( ($soxId == "-1" || !isset( $soxId)) && isset( $sSavedID) ) {
            $soxId = $sSavedID;
            oxSession::deleteVar( "saved_oxid");
            $this->_aViewData["oxid"] =  $soxId;
            // for reloading upper frame
            $this->_aViewData["updatelist"] =  "1";
        }

        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $iCatLang = oxConfig::getParameter("catlang");

            if (!isset($iCatLang))
                $iCatLang = $this->_iEditLang;

            $this->_aViewData["catlang"] = $iCatLang;

            $oCategory->loadInLang( $iCatLang, $soxId );


            foreach ( oxLang::getInstance()->getLanguageNames() as $id => $language) {
                $oLang= new oxStdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        $this->_aViewData['oxTiramizooCategoryExtended'] = oxtiramizoocategoryextended::findOneByFiltersOrCreate(array('oxcategoryid' => $oCategory->getId()));

        return "oxTiramizoo_category_tab.tpl";
    }

    /**
     * Saves category description text to DB.
     *
     * @return mixed
     */
    public function save()
    {
        $myConfig  = $this->getConfig();

        $soxId   = oxConfig::getParameter( "oxid");
        $aParams = oxConfig::getParameter( "oxTiramizooCategoryExtended");

        $oCategory = oxNew( "oxcategory" );

        if ( $soxId != "-1" ) {
            $oCategory->load( $soxId );
            $aParams['oxcategoryid'] = $soxId;
        }

        $oxTiramizooCategoryExtended = oxtiramizoocategoryextended::findOneByFiltersOrCreate(array('oxcategoryid' => $oCategory->getId()));

        $oxTiramizooCategoryExtended->assign( $aParams );
        $oxTiramizooCategoryExtended->save();

        // set oxid if inserted
        if ( $soxId == "-1") {
            oxSession::setVar( "saved_oxid", $oCategory->oxcategories__oxid->value);
        }
    }
}
