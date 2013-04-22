<?php

/**
 * Tiramizoo product tab
 *
 * @package: oxTiramizoo
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

        $oTiramizooArticleExtended = oxTiramizoo_ArticleExtended::findOneByFiltersOrCreate(array('oxarticleid' => $oArticle->getId()));

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

        $soxId   = oxConfig::getParameter( "oxid");
        $aParams = oxConfig::getParameter( "oxTiramizooArticleExtended");

        $oArticle = oxNew( "oxarticle" );

        if ( $soxId != "-1" ) {
            $oArticle = oxNew( "oxarticle" );
            $oxTiramizooArticleExtended = oxTiramizoo_ArticleExtended::findOneByFiltersOrCreate(array('oxarticleid' => $soxId));
            $aParams['oxarticleid'] = $soxId;

            $oxTiramizooArticleExtended->assign( $aParams );
            $oxTiramizooArticleExtended->save();
        }

    }

}
