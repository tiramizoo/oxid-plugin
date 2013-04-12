<?php

/**
 * Core class for API log entries
 *
 * @author FATCHIP GmbH | Robert Müller
 */
class oxtiramizooorderextended extends oxBase {

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
    protected $_sClassName = 'oxtiramizooorderextended';

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

            $oTiramizooRetailLocation = oxNew('oxtiramizooorderextended');
            $oTiramizooRetailLocation->load( $rs->fields['OXID'] );            

            return $oTiramizooRetailLocation;
        }

        return null;
    }

}