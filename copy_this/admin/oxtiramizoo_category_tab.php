<?php
/**
 * This file is part of the module oxTiramizoo for OXID eShop.
 *
 * The module oxTiramizoo for OXID eShop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free Software Foundation
 * either version 3 of the License, or (at your option) any later version.
 *
 * The module oxTiramizoo for OXID eShop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. 
 *  
 * See the GNU General Public License for more details <http://www.gnu.org/licenses/>
 *
 * @copyright: Tiramizoo GmbH
 * @author: Krzysztof Kowalik <kowalikus@gmail.com>
 * @package: oxTiramizoo
 * @license: http://www.gnu.org/licenses/
 * @version: 1.0.0
 * @link: http://tiramizoo.com
 */

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

        return "oxtiramizoo_category_tab.tpl";
    }

    /**
     * Saves category description text to DB.
     *
     * @return mixed
     */
    public function save()
    {
        $myConfig  = $this->getConfig();


        $soxId      = oxConfig::getParameter( "oxid");
        $aParams    = oxConfig::getParameter( "editval");

 //       print_r($aParams); exit;


        $oCategory = oxNew( "oxcategory" );


        if ( $soxId != "-1" ) {
            $oCategory->load( $soxId );
        } else {
            $aParams['oxcategories__oxid'] = null;
        }

        $oCategory->assign( $aParams );
        $oCategory->save();

        // set oxid if inserted
        if ( $soxId == "-1") {
            oxSession::setVar( "saved_oxid", $oCategory->oxcategories__oxid->value);
        }
    }
}
