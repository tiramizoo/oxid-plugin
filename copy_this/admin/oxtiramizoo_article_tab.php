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
 * @version   SVN: $Id: article_extend.php 26176 2010-03-02 13:19:57Z arvydas $
 */

/**
 * Admin article extended parameters manager.
 * Collects and updates (on user submit) extended article properties ( such as
 * weight, dimensions, purchase Price and etc.). There is ability to assign article
 * to any chosen article group.
 * Admin Menu: Manage Products -> Articles -> Extended.
 * @package admin
 */
class oxTiramizoo_Article_Tab extends oxAdminDetails
{
    /**
     * Collects available article axtended parameters, passes them to
     * Smarty engine and returns tamplate file name "article_extend.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oArticle = oxNew( 'oxarticle' );

        $soxId = oxConfig::getParameter( 'oxid' );


        if ( $soxId != "-1" && isset( $soxId ) ) {
            // load object
            $oArticle->load( $soxId );
        }


        return "oxtiramizoo_article_tab.tpl";
    }

    /**
     * Saves modified extended article parameters.
     *
     * @return mixed
     */
    public function save()
    {
        $soxId      = oxConfig::getParameter( "oxid");
        $aParams    = oxConfig::getParameter( "editval");

        $oArticle = oxNew( "oxarticle" );
        $oArticle->loadInLang( $this->_iEditLang, $soxId);
        $oArticle->assign( $aParams);
        $oArticle->save();

    }

}
