<?php

class oxTiramizoo_CategoryExtended extends oxBase {

    /**
     * Object core table name
     *
     * @var string
     */
    protected $_sCoreTbl = 'oxtiramizoocategoryextended';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxTiramizoo_CategoryExtended';

    protected $_aConfigVars = null;

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizoocategoryextended' );
    }


    public static function findOneByFilters($aFilters) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $whereItems = array();

        foreach ($aFilters as $sColumnName => $value) 
        {
            $whereItems[] =  $sColumnName . " = " . $oDb->quote( $value );
        }

        $sQ = "SELECT * FROM oxtiramizoocategoryextended WHERE " . implode(' AND ', $whereItems);
        $rs = $oDb->select( $sQ );
        
        if ( $rs && $rs->RecordCount() ) {

            $oTiramizooRetailLocation = oxNew('oxTiramizoo_CategoryExtended');
            $oTiramizooRetailLocation->load( $rs->fields['OXID'] );            

            return $oTiramizooRetailLocation;
        }

        return null;
    }


    public static function findOneByFiltersOrCreate($aFilters) 
    {
        $oTiramizooCategoryExtended = oxTiramizoo_CategoryExtended::findOneByFilters($aFilters);

        if (!$oTiramizooCategoryExtended) {
            $oTiramizooCategoryExtended = oxNew('oxTiramizoo_CategoryExtended');
        }

        return $oTiramizooCategoryExtended;
    }
}