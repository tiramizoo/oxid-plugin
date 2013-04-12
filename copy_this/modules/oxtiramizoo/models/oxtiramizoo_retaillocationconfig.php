<?php

/**
 * Core class for API log entries
 *
 * @author FATCHIP GmbH | Robert Müller
 */
class oxtiramizooretaillocationconfig extends oxBase {

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
    protected $_sClassName = 'oxtiramizooretaillocationconfig';

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->init( 'oxtiramizooretaillocationconfig' );
    }


    public function getValue()
    {
        $sVarVal = $this->oxtiramizooretaillocationconfig__oxvarvalue->value;

        //@ToDo: better

        switch ( $this->oxtiramizooretaillocationconfig__oxvartype->value ) {
            case 'arr':
            case 'aarr':
                $sValue =  unserialize( base64_decode( $sVarVal ) );
                break;
            case 'bool':
                $sValue =  unserialize( base64_decode( $sVarVal ) );
                break;
            default:
                $sValue = unserialize( base64_decode( $sVarVal ) );
                break;
        }

        return $sValue;        
    }



    public static function findOneByFilters($aFilters) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $whereItems = array();

        foreach ($aFilters as $sColumnName => $value) 
        {
            $whereItems[] =  $sColumnName . " = " . $oDb->quote( $value );
        }

        $sQ = "SELECT * FROM oxtiramizooretaillocationconfig WHERE " . implode(' AND ', $whereItems);
        $rs = $oDb->select( $sQ );
        
        if ( $rs && $rs->RecordCount() ) {

            $oTiramizooRetailLocationoConfig = oxNew('oxtiramizooretaillocationconfig');
            $oTiramizooRetailLocationoConfig->load( $rs->fields['OXID'] );            

            return $oTiramizooRetailLocationoConfig;
        }

        return null;
    }

    public static function findByFilters($aFilters) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $whereItems = array();

        foreach ($aFilters as $sColumnName => $value) 
        {
            $whereItems[] =  $sColumnName . " = " . $oDb->quote( $value );
        }

        $sQ = "SELECT * FROM oxtiramizooretaillocationconfig WHERE " . implode(' AND ', $whereItems);
        $oRs = $oDb->select( $sQ );
        
        $result = array();

        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while (!$oRs->EOF) {
                $oRetailLocationConfig = oxNew('oxtiramizooretaillocationconfig');
                $oRetailLocationConfig->load( $oRs->fields['OXID'] );            

                $result[] = $oRetailLocationConfig;
                $oRs->moveNext();
            }
        }

        return $result;
    }

    public static function findOneByFiltersOrCreate($aFilters) 
    {
        $oTiramizooRetailLocationoConfig = oxtiramizooretaillocationconfig::findOneByFilters($aFilters);

        if (!$oTiramizooRetailLocationoConfig) {
            $oTiramizooRetailLocationoConfig = oxNew('oxtiramizooretaillocationconfig');
        }

        return $oTiramizooRetailLocationoConfig;
    }


}