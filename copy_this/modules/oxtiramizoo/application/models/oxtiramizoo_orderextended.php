<?php

class oxTiramizoo_OrderExtended extends oxBase {

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

    protected $_aConfigVars = null;

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizooorderextended' );
    }


    public function getIdByOrderId($sOrderId) 
    {
        $oDb = oxDb::getDb(  );
        $sQ = "SELECT oxid FROM " . $this->_sCoreTbl . " WHERE OXORDERID = '" . $sOrderId . "';";
        return $oDb->getOne($sQ);
    }


    public function getTiramizooData()
    {
        return unserialize(base64_decode($this->oxtiramizooorderextended__tiramizoo_request_data->value));
    }

    public function setTiramizooData($oTiramizooData)
    {
        $this->oxtiramizooorderextended__tiramizoo_request_data = new oxField( base64_encode( serialize( $oTiramizooData ) ) );
    }

    public function getTrackingUrl()
    {
        return $this->oxtiramizooorderextended__tiramizoo_tracking_url->value;
    }
}