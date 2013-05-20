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

    public function getIdByCategoryId($sCategoryId) 
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sQ = "SELECT oxid FROM " . $this->_sCoreTbl . " WHERE oxcategoryid = " . $oDb->quote( $sCategoryId );
        
        return $oDb->getOne($sQ);
    }
}