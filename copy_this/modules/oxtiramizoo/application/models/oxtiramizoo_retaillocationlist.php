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
 * Retail Location List manager.
 *
 * @extend oxList
 * @package oxTiramizoo
 */
class oxTiramizoo_RetailLocationList extends oxList 
{
    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxTiramizoo_RetailLocation';

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTable = 'oxtiramizooretaillocation';

    /**
     * Loads all retail location records.
     *
     * @return null
     */
    public function loadAll()
    {
        $sTableName = $this->getBaseObject()->getCoreTableName();

        $oDb = oxDb::getDb();
        $sQ = "SELECT * FROM oxtiramizooretaillocation 
                    WHERE OXSHOPID = '" . $this->getConfig()->getShopId() . "';";

        $this->selectString( $sQ );
        $this->_aArray = array_reverse( $this->_aArray, true );
    } 
}
