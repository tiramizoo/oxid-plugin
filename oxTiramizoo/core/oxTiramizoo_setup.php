<?php

class oxTiramizoo_setup extends Shop_Config
{
    // public function __construct() {}

    protected $messageInfo = null;

    public function install()
    {
        try 
        {
            $this->setupDefaultConfigVars();
            $this->runDatabase();
            
            // clear cache 
            oxUtils::getInstance()->rebuildCache();            
        } catch(Exception $e) {
            //do sth
            print_r($e);
        }
    }

    public function setupDefaultConfigVars()
    {
        $oxTiramizooConfig = $this->getConfig();

        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_api_url', 'https://api-sandbox.tiramizoo.com/v1');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_api_key', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_url', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_address', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_postal_code', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_city', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_country_code', 'de');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_contact_name', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_phone_number', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_email_address', '');
        $oxTiramizooConfig->saveShopConfVar( "bool", 'oxTiramizoo_enable_module', 0);
        $oxTiramizooConfig->saveShopConfVar( "bool", 'oxTiramizoo_is_installed', 0);
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_version', '0.1.0');


    }

    public function runDatabase()
    {
        $methodsName = get_class_methods(__CLASS__);
        $migrationsMethods = array();
        
        foreach ($methodsName as $methodName) 
        {
            if (strpos($methodName, 'databaseMigration') === 0) {
                $migrationsMethods[] = $methodName;
            }
        }        

        natsort($migrationsMethods);

        foreach($migrationsMethods as $migrationMethod)
        {
            call_user_func_array(array($this, $migrationMethod), array());
        }
    }

    public function databaseMigration_0_1_0()
    {
        $this->addColumnToTable('oxorder', 'TIRAMIZOO_TRACKING_URL', 'VARCHAR(255) NOT NULL');
        $this->addColumnToTable('oxorder', 'TIRAMIZOO_STATUS', 'TINYINT NOT NULL');
        $this->addColumnToTable('oxorder', 'TIRAMIZOO_PARAMS', 'TEXT NOT NULL');

        $this->addColumnToTable('oxarticles', 'OXTIRAMIZOOENABLE', 'INT(1) NOT NULL DEFAULT 0');
        $this->addColumnToTable('oxcategories', 'OXTIRAMIZOOENABLE', 'INT(1) NOT NULL DEFAULT 0');

        $this->ExecuteSQL("INSERT IGNORE INTO `oxdel2delset` SET
                            OXID = MD5(CONCAT('tiramizoo', 'tiramizoo')),
                            OXDELID = 'tiramizoo',
                            OXDELSETID = 'tiramizoo';");

        //@TODO: what is shop id?
        $this->ExecuteSQL("INSERT INTO `oxdelivery` SET
                            OXID = 'tiramizoo',
                            OXSHOPID = 1,
                            OXACTIVE = 1,
                            OXACTIVEFROM = '0000-00-00 00:00:00',
                            OXACTIVETO = '0000-00-00 00:00:00',
                            OXTITLE = 'Tiramizoo',
                            OXTITLE_1 = 'Tiramizoo',
                            OXTITLE_2 = 'Tiramizoo',
                            OXTITLE_3 = 'Tiramizoo',
                            OXADDSUMTYPE = 'abs',
                            OXADDSUM = 0.1,
                            OXDELTYPE = 'p',
                            OXPARAM = 0,
                            OXPARAMEND = 999999,
                            OXFIXED = 1,
                            OXSORT = 1,
                            OXFINALIZE = 0;");

        //@TODO: what is shop id?
        $this->ExecuteSQL("INSERT INTO `oxdeliveryset` SET
                            OXID = 'Tiramizoo',
                            OXSHOPID = 1,
                            OXACTIVE = 1,
                            OXACTIVEFROM = '0000-00-00 00:00:00',
                            OXACTIVETO = '0000-00-00 00:00:00',
                            OXTITLE = 'Tiramizoo',
                            OXTITLE_1 = 'Tiramizoo',
                            OXTITLE_2 = 'Tiramizoo',
                            OXTITLE_3 = 'Tiramizoo',
                            OXPOS = 10;");
    }

    protected function ExecuteSQL($sql)
    {
        $result = oxDb::getDb()->Execute($sql);
        if ($result === false) {
            $this->messageInfo .= $sql . ";\n";
        }
        return $result;
    }

    protected function addColumnToTable($tableName, $columnName, $columnData)
    {
        if (!$this->columnExistsInTable($columnName, $tableName)) {
            $sql = "ALTER TABLE " . $tableName . " ADD " . $columnName . " " . $columnData . ";";
            $result = $this->ExecuteSQL($sql);
        }
    }

    protected function columnExistsInTable($columnName, $tableName)
    {
        $sql = "SHOW COLUMNS FROM " . $tableName . " LIKE '" . $columnName . "'";
        $result = $this->ExecuteSQL($sql);

        return $result->RecordCount();
    }
}