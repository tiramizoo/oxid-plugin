<?php

class oxTiramizoo_RetailLocationConfigList extends oxList 
{

    protected $_sObjectsInListName = 'oxTiramizoo_RetailLocationConfig';

    protected $_sCoreTable = 'oxtiramizooretaillocationconfig';

    public function loadByRetailLocationId($sRetailLocationId)
    {
        $sTableName = $this->getBaseObject()->getCoreTableName();

        $oDb = oxDb::getDb();
        $sQ = "SELECT * FROM oxtiramizooretaillocationconfig 
                    WHERE OXRETAILLOCATIONID = '" . $sRetailLocationId . "';";

        $this->selectString( $sQ );
        $this->_aArray = array_reverse( $this->_aArray, true );
    } 

}