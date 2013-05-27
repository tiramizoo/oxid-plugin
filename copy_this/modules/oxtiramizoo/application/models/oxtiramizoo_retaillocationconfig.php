<?php

class oxTiramizoo_RetailLocationConfig extends oxBase {

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
    protected $_sClassName = 'oxTiramizoo_RetailLocationConfig';

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() 
    {
        parent::__construct();
        $this->init( 'oxtiramizooretaillocationconfig' );
    }

    public function getValue()
    {
        return unserialize( base64_decode( $this->oxtiramizooretaillocationconfig__oxvarvalue->value ) );
    }

    public function getIdByRetailLocationIdAndVarName($sRetailLocationId, $sVarName) 
    {
        $oDb = oxDb::getDb(  );
        $sQ = "SELECT oxid FROM " . $this->_sCoreTbl . " WHERE OXRETAILLOCATIONID = '" . $sRetailLocationId . "' AND OXVARNAME = '" . $sVarName . "';";
        return $oDb->getOne($sQ);
    }
}
