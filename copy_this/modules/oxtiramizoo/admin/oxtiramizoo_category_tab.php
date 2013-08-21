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
 * Admin category extended tiramizoo parameters manager.
 * Collects and updates (on user submit) extended category properties ( such as
 * weight, dimensions, enable tiramizoo delivery).
 * Admin Menu: Administer Products -> Category -> Tiramizoo.
 *
 * @extend oxAdminDetails
 * @package oxTiramizoo
 */
class oxTiramizoo_Category_Tab extends oxAdminDetails
{
    /**
     * @var oxTiramizoo_CategoryExtended
     */
    protected $_oTiramizooCategoryExtended = null;

    /**
     * Getter method, returns oxTiramizoo_CategoryExtended object
     *
     * @return oxTiramizoo_CategoryExtended
     */
    public function getTiramizooCategoryExtended()
    {
        return $this->_oTiramizooCategoryExtended;
    }

    /**
     * Loads category extended object data, passes it to Smarty engine and returns
     * name of template file "oxTiramizoo_category_tab.tpl".
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

        $this->_aViewData['edit'] = oxNew( 'oxcategory' );

        $soxId = $this->getConfig()->getRequestParameter( "oxid");

        $oCategory = oxNew('oxCategory');
        $oCategory->load($soxId);

        $oTiramizooCategoryExtended = oxNew('oxTiramizoo_CategoryExtended');
        $oTiramizooCategoryExtended->load($oTiramizooCategoryExtended->getIdByCategoryId($soxId));
        $this->_aViewData['oxTiramizooCategoryExtended'] = $oTiramizooCategoryExtended;

        $oArticleInheritedData = oxNew('oxTiramizoo_ArticleInheritedData');
        $this->_aViewData['effectiveData'] = $oArticleInheritedData->getCategoryEffectiveData($oCategory, true);

        return "oxTiramizoo_category_tab.tpl";
    }

    /**
     * Saves category extended tiramizoo parameters.
     *
     * @return void
     */
    public function save()
    {
        $soxId   = $this->getConfig()->getRequestParameter( "oxid");
        $aParams = $this->getConfig()->getRequestParameter( "oxTiramizooCategoryExtended");

        if ( $soxId != "-1" && isset( $soxId ) ) {
            $this->_oTiramizooCategoryExtended = oxNew('oxTiramizoo_CategoryExtended');
            $this->_oTiramizooCategoryExtended->load($this->_oTiramizooCategoryExtended->getIdByCategoryId($soxId));
            $aParams['oxcategoryid'] = $soxId;

            $this->_oTiramizooCategoryExtended->assign( $aParams );
            $this->_oTiramizooCategoryExtended->save();
        }
    }
}
