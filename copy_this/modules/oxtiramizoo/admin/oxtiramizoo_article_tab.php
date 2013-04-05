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


        $this->_aViewData['inheritedData'] = oxTiramizooArticleHelper::getInstance()->getArticleInheritData($oArticle);
        $this->_aViewData['effectiveData'] = $effectiveData = oxTiramizooArticleHelper::getInstance()->buildArticleEffectiveData($oArticle);

        if ($effectiveData->weight == 0 || 
            $effectiveData->width  == 0 || 
            $effectiveData->height == 0 || 
            $effectiveData->length == 0) {

                $this->_aViewData['warningDimensions'] = 'You have to specify dimensions and weight. You can do this in global settings, category tab or article extended tab.';
        }


        $this->_aViewData['disabledCategory'] = oxTiramizooArticleHelper::getInstance()->getDisabledCategory($oArticle);



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
