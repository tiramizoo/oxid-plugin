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
 * Tiramizoo Retail Location Config manager.
 * Performs creation assigning, updating, deleting and other order functions.
 *
 * @extends oxBase
 * @package oxTiramizoo
 */
class oxTiramizoo_RetailLocationConfig extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizooretaillocationconfig';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxTiramizoo_RetailLocationConfig';

    /**
     * Class constructor, initialize fields
     *
     * @extend oxBase::__construct()
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxtiramizooretaillocationconfig' );
    }

    /**
     * Returns API token
     *
     * @return string
     */
    public function getValue()
    {
        return unserialize( base64_decode( $this->oxtiramizooretaillocationconfig__oxvarvalue->value ) );
    }

    /**
     * Returns retail location config id
     * by retail location id and variable name
     *
     * @param string $sRetailLocationId retail location config
     * @param string $sRetailLocationId retail location config variable name
     *
     * @return string
     */
    public function getIdByRetailLocationIdAndVarName($sRetailLocationId, $sVarName)
    {
        $oDb = oxDb::getDb(  );
        $sQ = "SELECT oxid
                    FROM " . $this->_sCoreTbl . "
                    WHERE OXRETAILLOCATIONID = '" . $sRetailLocationId . "'
                        AND OXVARNAME = '" . $sVarName . "';";
        return $oDb->getOne($sQ);
    }
}
