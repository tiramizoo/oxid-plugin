<?php

/**
 * Tiramizoo product tab
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_Article_Tab extends oxAdminDetails
{
    /**
     * Collects available article extended parameters, passes them to
     * Smarty engine and returns tamplate file name "article_extend.tpl".
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

        $this->_aViewData['edit'] = oxNew( 'oxarticle' );

        $soxId = $this->getConfig()->getRequestParameter( 'oxid' );

        $oTiramizooArticleExtended = oxNew('oxTiramizoo_ArticleExtended');
        $oTiramizooArticleExtended->load($oTiramizooArticleExtended->getIdByArticleId($soxId));

        $this->_aViewData['oxTiramizooArticleExtended'] = $oTiramizooArticleExtended;

        $this->_aViewData['inheritedData'] = $oTiramizooArticleExtended->getArticleInheritData();
        $this->_aViewData['effectiveData'] = $effectiveData = $oTiramizooArticleExtended->buildArticleEffectiveData();

        if ($effectiveData->weight == 0 || 
            $effectiveData->width  == 0 || 
            $effectiveData->height == 0 || 
            $effectiveData->length == 0) {

            $this->_aViewData['warningDimensions'] = 'You have to specify dimensions and weight. You can do this in global settings, category tab or article extended tab.';
        }

        $this->_aViewData['disabledCategory'] = $oTiramizooArticleExtended->getDisabledCategory();

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
            $oTiramizooArticleExtended = oxNew('oxTiramizoo_ArticleExtended');
            $oTiramizooArticleExtended->load($oTiramizooArticleExtended->getIdByArticleId($soxId));
            $aParams['oxarticleid'] = $soxId;

            $oTiramizooArticleExtended->assign( $aParams );
            $oTiramizooArticleExtended->save();
        }
    }
}
