<?php
/**
 * This file is part of the oxTiramizoo OXID eShop plugin.
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  module
 * @package   oxTiramizoo
 * @author    Tiramizoo GmbH <support@tiramizoo.com>
 * @copyright Tiramizoo GmbH
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Admin category extended article parameters manager.
 * Collects and updates (on user submit) extended article properties ( individual packaging, enable tiramizoo delivery).
 * Admin Menu: Administer Products -> Products -> Tiramizoo.
 *
 * @extend oxAdminDetails
 * @package oxTiramizoo
 */
class oxTiramizoo_Article_Tab extends oxAdminDetails
{
    /**
     * @var oxTiramizoo_ArticleExtended
     */
    protected $_oTiramizooArticleExtended = null;

    /**
     * @var array
     */
    protected $_aEffectiveData = null;

    /**
     * @var oxArticle
     */
    protected $_oArticle = null;

    /**
     * Getter method, returns oxTiramizoo_ArticleExtended object
     *
     * @return oxTiramizoo_ArticleExtended
     */
    public function getTiramizooArticleExtended()
    {
        return $this->_oTiramizooArticleExtended;
    }

    /**
     * Getter method, returns array with article effective data
     *
     * @return array
     */
    public function getEffectiveData()
    {
        return $this->_aEffectiveData;
    }

    /**
     * Getter method, returns oxArticle object
     *
     * @return oxArticle
     */
    public function getArticle()
    {
        return $this->_oArticle;
    }

    /**
     * Collects available article extended parameters, passes them to
     * Smarty engine and returns tamplate file name "article_extend.tpl".
     *
     * @extend oxAdminDetails::render
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

        $this->_oArticle = oxNew( 'oxarticle' );

        $soxId = $this->getConfig()->getRequestParameter( 'oxid' );

        $oArticle = oxNew('oxArticle');
        $oArticle->load($soxId);

        $oTiramizooArticleExtended = oxNew('oxTiramizoo_ArticleExtended');
        $oTiramizooArticleExtended->loadByArticle($oArticle);

        $this->_oTiramizooArticleExtended = $oTiramizooArticleExtended;
        $this->_aEffectiveData = $oTiramizooArticleExtended->getEffectiveData();

        return "oxTiramizoo_article_tab.tpl";
    }

    /**
     * Saves modified extended article parameters.
     *
     * @return mixed
     */
    public function save()
    {
        $soxId   = $this->getConfig()->getRequestParameter( "oxid");
        $aParams = $this->getConfig()->getRequestParameter( "oxTiramizooArticleExtended");

        if ( $soxId != "-1" && isset( $soxId ) ) {
            $oArticle = oxNew('oxArticle');
            $oArticle->load($soxId);

            $this->_oTiramizooArticleExtended = oxNew('oxTiramizoo_ArticleExtended');
            $this->_oTiramizooArticleExtended->loadByArticle($oArticle);
            $aParams['oxarticleid'] = $soxId;

            $this->_oTiramizooArticleExtended->assign( $aParams );
            $this->_oTiramizooArticleExtended->save();
        }
    }
}
