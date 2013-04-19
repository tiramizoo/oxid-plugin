<?php

/**
 * Core class for API log entries
 *
 * @author FATCHIP GmbH | Robert Müller
 */
class oxtiramizooarticleextended extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizooarticleextended';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxtiramizooarticleextended';

    protected $_aConfigVars = null;

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizooarticleextended' );
    }


    public static function findOneByFilters($aFilters) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $whereItems = array();

        foreach ($aFilters as $sColumnName => $value) 
        {
            $whereItems[] =  $sColumnName . " = " . $oDb->quote( $value );
        }

        $sQ = "SELECT * FROM oxtiramizooarticleextended WHERE " . implode(' AND ', $whereItems);
        $rs = $oDb->select( $sQ );
        
        if ( $rs && $rs->RecordCount() ) {

            $oTiramizooRetailLocation = oxNew('oxtiramizooarticleextended');
            $oTiramizooRetailLocation->load( $rs->fields['OXID'] );            

            return $oTiramizooRetailLocation;
        }

        return null;
    }
    
    public static function findOneByFiltersOrCreate($aFilters) 
    {
        $oTiramizooCategoryExtended = oxtiramizooarticleextended::findOneByFilters($aFilters);

        if (!$oTiramizooCategoryExtended) {
            $oTiramizooCategoryExtended = oxNew('oxTiramizooArticleExtended');
        }

        return $oTiramizooCategoryExtended;
    }
}