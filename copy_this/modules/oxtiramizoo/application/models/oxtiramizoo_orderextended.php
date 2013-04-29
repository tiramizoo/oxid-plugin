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


    public static function findOneByFilters($aFilters) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $whereItems = array();

        foreach ($aFilters as $sColumnName => $value) 
        {
            $whereItems[] =  $sColumnName . " = " . $oDb->quote( $value );
        }

        $sQ = "SELECT * FROM oxtiramizooorderextended WHERE " . implode(' AND ', $whereItems);
        $rs = $oDb->select( $sQ );
        
        if ( $rs && $rs->RecordCount() ) {

            $oxTiramizooOrderExtended = oxNew('oxTiramizoo_OrderExtended');
            $oxTiramizooOrderExtended->load( $rs->fields['OXID'] );            

            return $oxTiramizooOrderExtended;
        }

        return null;
    }


    public static function findOneByFiltersOrCreate($aFilters) 
    {
        $oTiramizooOrderExtended = oxTiramizoo_OrderExtended::findOneByFilters($aFilters);

        if (!$oTiramizooOrderExtended) {
            $oTiramizooOrderExtended = oxNew('oxTiramizoo_OrderExtended');
        }

        return $oTiramizooOrderExtended;
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