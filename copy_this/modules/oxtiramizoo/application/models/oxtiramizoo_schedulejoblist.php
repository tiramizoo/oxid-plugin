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
 * Schedule Job List manager.
 *
 * @extend oxList
 * @package oxTiramizoo
 */
class oxTiramizoo_ScheduleJobList extends oxList {

    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxTiramizoo_ScheduleJob';

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTable = 'oxtiramizooschedulejob';

    /**
     * Job types with class names
     *
     * @var array
     */
    protected $_aJobTypes = array('send_order'                  => 'oxTiramizoo_SendOrderJob',
                                  'synchronize_configuration'   => 'oxTiramizoo_SyncConfigJob');

    /**
     * Loads limited number of jobs to run 
     *
     * @param int $iLimit jobs limit
     *
     * @return null;
     */
    public function loadToRun($iLimit = 10)
    {
        $sTableName = $this->getBaseObject()->getCoreTableName();

        $oDate = oxnew('oxTiramizoo_Date');

        $oDb = oxDb::getDb();
        $sQ = "SELECT * FROM {$sTableName} 
                WHERE oxshopid='".$this->getConfig()->getShopId()."'
                    AND oxstate IN ('new', 'retry') 
                    AND oxrunafter <= '" . $oDate->get() . "'
                    AND oxrunbefore >= '" . $oDate->get() . "'
                LIMIT " . intval($iLimit) . ";";

        $this->selectString( $sQ );
        $this->_aArray = array_reverse( $this->_aArray, true );
    } 

    /**
     * Selects and SQL, creates objects with specified type
     * and assign them
     *
     * @param string $sSql SQL select statement
     *
     * @return null;
     */
    public function selectString( $sSql )
    {
        $this->clear();

        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        if ( $this->_aSqlLimit[0] || $this->_aSqlLimit[1]) {
            $rs = $oDb->selectLimit( $sSql, $this->_aSqlLimit[1], $this->_aSqlLimit[0] );
        } else {
            $rs = $oDb->select( $sSql );
        }

        if ($rs != false && $rs->recordCount() > 0) {

            $sClassName = $this->_aJobTypes[$rs->fields['OXJOBTYPE']];

            $oSaved = oxNew($sClassName);
            $oSaved->setInList();
            $oSaved->init( $this->_sCoreTable );

            while (!$rs->EOF) {

                $oListObject = clone $oSaved;

                $this->_assignElement($oListObject, $rs->fields);

                if ($oListObject->getId()) {
                    $this->_aArray[$oListObject->getId()] = $oListObject;
                } else {
                    $this->_aArray[] = $oListObject;
                }

                $rs->moveNext();
            }
        }
    }
}
