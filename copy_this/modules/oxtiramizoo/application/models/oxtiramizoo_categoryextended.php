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
 * Tiramizoo Category Extended manager.
 * Performs creation assigning, updating, deleting and other order functions.
 *
 * @extends oxBase
 * @package oxTiramizoo
 */
class oxTiramizoo_CategoryExtended extends oxBase
{
    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizoocategoryextended';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxTiramizoo_CategoryExtended';

    /**
     * Model constructor, init fields
     *
     * @extend oxBase::__construct()
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxtiramizoocategoryextended' );
    }

    /**
     * Returns category extended id by category id
     *
     * @param string $sCategoryId category id
     *
     * @return string
     */
    public function getIdByCategoryId($sCategoryId)
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sQ = "SELECT oxid FROM " . $this->_sCoreTbl . " WHERE oxcategoryid = " . $oDb->quote( $sCategoryId );

        return $oDb->getOne($sQ);
    }
}
