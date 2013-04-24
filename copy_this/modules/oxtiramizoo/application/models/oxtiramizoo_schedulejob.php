<?php

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

    protected $_aConfigVars = null;

    protected static $_aJobTypes = array('send_order' => 'oxTiramizoo_SendOrderJob',
                                         'synchronize_configuration' => 'oxTiramizoo_SyncConfigJob');


    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizooschedulejob' );
    }


    public static function findOneByFilters($aFilters) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $whereItems = array();

        foreach ($aFilters as $sColumnName => $value) 
        {
            $whereItems[] =  $sColumnName . " = " . $oDb->quote( $value );
        }

        $sQ = "SELECT * FROM oxtiramizooschedulejob WHERE " . implode(' AND ', $whereItems);
        $rs = $oDb->select( $sQ );
        
        if ( $rs && $rs->RecordCount() ) {

            $sClassName = self::$_aJobTypes[$oRs->fields['OXJOBTYPE']];

            $oTiramizooJob = oxNew($sClassName);
            $oTiramizooJob->load( $rs->fields['OXID'] );            

            return $oTiramizooJob;
        }

        return null;
    }

    public static function findAllToRun($iLimit = 100) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $sQ = "SELECT * FROM oxtiramizooschedulejob 
                        WHERE oxstate IN ('new', 'retry') 
                            AND oxrunafter <= NOW()
                            AND oxrunbefore >= NOW()
                        LIMIT " . intval($iLimit) . ";";
        $oRs = $oDb->select( $sQ );
        
        $result = array();

        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while (!$oRs->EOF) {


                $sClassName = self::$_aJobTypes[$oRs->fields['OXJOBTYPE']];

                $oTiramizooJob = oxNew($sClassName);
                $oTiramizooJob->load( $oRs->fields['OXID'] );            

                $result[] = $oTiramizooJob;
                $oRs->moveNext();
            }
        }

        return $result;
    }

    public static function findDailyByType($sType) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $sQ = "SELECT * FROM oxtiramizooschedulejob 
                    WHERE oxjobtype = '" . $sType . "'
                        AND DATE(oxcreatedat)=CURDATE();";

        $oRs = $oDb->select( $sQ );
        
        if ( $oRs && $oRs->RecordCount() ) {

            $sClassName = self::$_aJobTypes[$oRs->fields['OXJOBTYPE']];

            $oTiramizooJob = oxNew($sClassName);
            $oTiramizooJob->load( $rs->fields['OXID'] );            

            return $oTiramizooJob;
        }

        return null;
    }







    const MAX_REPEATS = 2;
    const JOB_TYPE = '';

    protected $_sJobType = '';

    public function setDefaultData()
    {
        $this->oxtiramizooschedulejob__oxrepeatcounter = new oxField(0);
        $this->oxtiramizooschedulejob__oxstate = new oxField('new');
    }

    public function setExternalId($sID)
    {
        $this->oxtiramizooschedulejob__oxexternalid = new oxField($sID);
    }

    public function getExternalId()
    {
        return $this->oxtiramizooschedulejob__oxexternalid->value;
    }

    public function setParams($aParams)
    {
        $this->oxtiramizooschedulejob__oxparams = new oxField( base64_encode( serialize( $aParams ) ) );
    }

    public function getParams()
    {
        return unserialize(base64_decode($this->oxtiramizooschedulejob__oxparams));
    }

    public function getState()
    {
        return $this->oxtiramizooschedulejob__oxstate;
    }

    public function setState($sState)
    {
        $this->oxtiramizooschedulejob__oxstate = new oxField($sState);
    }

    public function getRepeats()
    {
        return $this->oxtiramizooschedulejob__oxrepeatcounter->value;
    }

    public function closeJob()
    {
        $this->oxtiramizooschedulejob__oxstate = new oxField('error');
        
        $this->save();
    }


    public function finishJob()
    {
        $this->oxtiramizooschedulejob__oxfinishedat = new oxField(oxTiramizoo_Date::date());
        $this->oxtiramizooschedulejob__oxstate = new oxField('finished');
        $this->save();
    }

    
    public function run()
    {
    }
}