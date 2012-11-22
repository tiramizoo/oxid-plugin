<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   admin
 * @copyright (C) OXID eSales AG 2003-2010
 * @version OXID eShop CE
 * @version   SVN: $Id: category_text.php 25466 2010-02-01 14:12:07Z alfonsas $
 */

/**
 * Admin article categories text manager.
 * Category text/description manager, enables editing of text.
 * Admin Menu: Manage Products -> Categories -> Text.
 * @package admin
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
