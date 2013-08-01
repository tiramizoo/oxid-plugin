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
 * Tiramizoo Order Extended manager.
 * Performs creation assigning, updating, deleting and other order functions.
 *
 * @extends oxBase
 * @package oxTiramizoo
 */
class oxTiramizoo_OrderExtended extends oxBase 
{
    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizooorderextended';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxTiramizoo_OrderExtended';

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
        $this->init( 'oxtiramizooorderextended' );
    }

    /**
     * Returns order extended id by order id
     * 
     * @param string $sOrderId order id
     *
     * @return string
     */
    public function getIdByOrderId($sOrderId) 
    {
        $oDb = oxDb::getDb(  );
        $sQ = "SELECT oxid FROM " . $this->_sCoreTbl . " WHERE OXORDERID = '" . $sOrderId . "';";
        return $oDb->getOne($sQ);
    }

    /**
     * Unserialize and decode request data value
     *
     * @return mixed
     */
    public function getTiramizooData()
    {
        return unserialize(base64_decode($this->oxtiramizooorderextended__tiramizoo_request_data->value));
    }

    /**
     * Serialize and encode request data value. Assign to object field.
     *
     * @return mixed
     */
    public function setTiramizooData($oTiramizooData)
    {
        $this->oxtiramizooorderextended__tiramizoo_request_data = new oxField( base64_encode( serialize( $oTiramizooData ) ) );
    }

    /**
     * Returns tracking url
     *
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->oxtiramizooorderextended__tiramizoo_tracking_url->value;
    }
}
