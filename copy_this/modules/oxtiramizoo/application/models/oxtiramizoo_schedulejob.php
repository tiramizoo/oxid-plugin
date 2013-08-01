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
 * Performs creation assigning, updating, deleting and other order functions.
 *
 * @extend oxBase
 * @package oxTiramizoo
 */
class oxTiramizoo_ScheduleJob extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizooschedulejob';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxTiramizoo_ScheduleJob';

    /**
     * Maximum number of repeating running job
     */
    const MAX_REPEATS = 2;

    /**
     * Job type
     */
    const JOB_TYPE = '';

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizooschedulejob' );
    }

    /**
     * Returns schedule job id by type. If there was no job
     * with current date returns null.
     * 
     * @param string $sType job type
     *
     * @return mixed
     */
    public function getIdTodayByType($sType) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $oDate = oxnew('oxTiramizoo_Date');

        $sQ = "SELECT oxid FROM oxtiramizooschedulejob 
                    WHERE oxshopid='".$this->getConfig()->getShopId()."'
                        AND oxjobtype = '" . $sType . "'
                        AND DATE(oxcreatedat)='" . $oDate->get('Y-m-d') . "';";

        return $oDb->getOne($sQ);
    }

    /**
     * Setting object with default data.
     * 
     * @return null
     */
    public function setDefaultData()
    {
        $this->oxtiramizooschedulejob__oxrepeatcounter = new oxField(0);
        $this->oxtiramizooschedulejob__oxstate = new oxField('new');
    }

    /**
     * Setting external id.
     * 
     * @param string $sID external id value
     * 
     * @return null
     */
    public function setExternalId($sID)
    {
        $this->oxtiramizooschedulejob__oxexternalid = new oxField($sID);
    }

    /**
     * Returns external id.
     * 
     * @return string
     */
    public function getExternalId()
    {
        return $this->oxtiramizooschedulejob__oxexternalid->value;
    }

    /**
     * Setting encoded and serialized params.
     * 
     * @param array $aParams array of parameters
     * 
     * @return null
     */
    public function setParams($aParams)
    {
        $this->oxtiramizooschedulejob__oxparams = new oxField( base64_encode( serialize( $aParams ) ) );
    }

    /**
     * Returns decoded and unserialized params.
     * 
     * @return array
     */
    public function getParams()
    {
        return unserialize(base64_decode($this->oxtiramizooschedulejob__oxparams));
    }

    /**
     * Returns state.
     * 
     * @return string
     */
    public function getState()
    {
        return $this->oxtiramizooschedulejob__oxstate->value;
    }

    /**
     * Setting state.
     * 
     * @return null
     */
    public function setState($sState)
    {
        $this->oxtiramizooschedulejob__oxstate = new oxField($sState);
    }

    /**
     * Returns current repeat counter.
     * 
     * @return null
     */
    public function getRepeats()
    {
        return $this->oxtiramizooschedulejob__oxrepeatcounter->value;
    }

    /**
     * Change state to error and save.
     * 
     * @return null
     */
    public function closeJob()
    {
        $this->oxtiramizooschedulejob__oxstate = new oxField('error');
        $this->save();
    }

    /**
     * Change state to finished, assign finished_at field and save.
     * 
     * @return null
     */
    public function finishJob()
    {
        $this->oxtiramizooschedulejob__oxfinishedat = new oxField(oxTiramizoo_Date::date());
        $this->oxtiramizooschedulejob__oxstate = new oxField('finished');
        $this->save();
    }

    /**
     * Increment current repeat counter and save.
     * 
     * @return null
     */
    public function run()
    {
        $this->oxtiramizooschedulejob__oxrepeatcounter->value++;
        $this->save();
    }
}
