<?php

class oxTiramizoo_RetailLocationList extends oxList 
{

    protected $_sObjectsInListName = 'oxTiramizoo_RetailLocation';

    protected $_sCoreTable = 'oxtiramizooretaillocation';

    public function loadAll()
    {
        $sTableName = $this->getBaseObject()->getCoreTableName();

        $oDb = oxDb::getDb();
        $sQ = "SELECT * FROM oxtiramizooretaillocation 
                    WHERE OXSHOPID = '" . $this->getConfig()->getShopId() . "';";

        $this->selectString( $sQ );
        $this->_aArray = array_reverse( $this->_aArray, true );
    } 

    public function loadByApiToken($sApiToken)
    {
        $sTableName = $this->getBaseObject()->getCoreTableName();

        $oDb = oxDb::getDb();
        $sQ = "SELECT * FROM oxtiramizooretaillocation 
                    WHERE OXSHOPID = '" . $this->getConfig()->getShopId() . "'
                        AND OXAPITOKEN = '" . $sApiToken . "';";

        $this->selectString( $sQ );
        $this->_aArray = array_reverse( $this->_aArray, true );
    } 

}