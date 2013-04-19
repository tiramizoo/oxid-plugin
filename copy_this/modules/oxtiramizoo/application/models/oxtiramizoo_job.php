<?php

/**
 * Core class for API log entries
 *
 * @author FATCHIP GmbH | Robert Müller
 */
class oxtiramizoojob extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizoojob';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxtiramizoojob';

    protected $_aConfigVars = null;

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizoojob' );
    }


    public static function findOneByFilters($aFilters) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $whereItems = array();

        foreach ($aFilters as $sColumnName => $value) 
        {
            $whereItems[] =  $sColumnName . " = " . $oDb->quote( $value );
        }

        $sQ = "SELECT * FROM oxtiramizoojob WHERE " . implode(' AND ', $whereItems);
        $rs = $oDb->select( $sQ );
        
        if ( $rs && $rs->RecordCount() ) {

            $oTiramizooJob = oxNew('oxtiramizoojob');
            $oTiramizooJob->load( $rs->fields['OXID'] );            

            return $oTiramizooJob;
        }

        return null;
    }



    

    

}