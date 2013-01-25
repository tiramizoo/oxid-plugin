<?php

/**
 * This class is used to install or update oxTiramizoo module
 *
 * @package: oxTiramizoo
 */
class oxTiramizoo_setup extends Shop_Config
{
    /**
     * Current version of oxTiramizoo module
     */
    const VERSION = '0.8.8';

    /**
     * Error message
     * @var string
     */
    protected $_migrationErrors = array();

    /**
     * Install or update module if needed
     */
    public function install()
    {
        $oxConfig = $this->getConfig();

        $currentInstalledVersion = $oxConfig->getConfigParam('oxTiramizoo_version');
        $tiramizooIsInstalled = $oxConfig->getConfigParam('oxTiramizoo_is_installed');

        try 
        { 
            if (!$tiramizooIsInstalled || !$currentInstalledVersion) {

                $this->runMigrations();
                $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_is_installed', 1);
                oxUtils::getInstance()->rebuildCache();

            } else if ($tiramizooIsInstalled && (version_compare(oxTiramizoo_setup::VERSION, $currentInstalledVersion) !== 0)) {
                
                $this->runMigrations(oxTiramizoo_setup::VERSION);
                oxUtils::getInstance()->rebuildCache();
            }

        } catch(oxException $e) {
            $errorMessage = $e->getMessage . "<ul><li>" . implode("</li><li>", $this->_migrationErrors) . "</li></ul>";
            echo $errorMessage;
            exit;
        }
    }

    /**
     * This method executes all migration methods newer than already installed version and older than new version
     * 
     * @param  string $version Version of this package
     */
    public function runMigrations($version = null)
    {
        $currentInstalledVersion = $this->getConfig()->getConfigParam('oxTiramizoo_version');

        $methodsName = get_class_methods(__CLASS__);

        $migrationsMethods = array();
        
        foreach ($methodsName as $methodName) 
        {
            if (strpos($methodName, 'migration_') === 0) {
                $methodVersion = str_replace('migration_', '', $methodName);
                $methodVersion = str_replace('_', '.', $methodVersion);
                $migrationsMethods[$methodVersion] = $methodName;
            }
        }        

        uksort($migrationsMethods, 'version_compare');

        foreach($migrationsMethods as $methodVersion => $migrationMethod)
        {
            if (version_compare($methodVersion, $currentInstalledVersion) > 0) {
                if (version_compare($methodVersion, oxTiramizoo_setup::VERSION) <= 0) {
                    call_user_func_array(array($this, $migrationMethod), array());

                    if ($this->stopMigrationsIfErrors($methodVersion)) {
                        throw new oxException('You need to manually run this sql statements to update database to version: ' . $methodVersion);
                    }

                    $this->getConfig()->saveShopConfVar( "str", 'oxTiramizoo_version', $methodVersion);                    
                }
            }
        }
    }

    public function stopMigrationsIfErrors($migrationVersion)
    {
        $oxConfig = $this->getConfig();
        if (count($this->_migrationErrors)) {
            //disable tiramizoo if db errors
            $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_enable_module', 0);
            return true;
        } else {
            $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_update_errors', '');
            return false;
        }
    }

    /**
     * Update database to version 0.8.0 
     */
    public function migration_0_8_0()
    {
        $this->addColumnToTable('oxorder', 'TIRAMIZOO_TRACKING_URL', 'VARCHAR(255) NOT NULL');
        $this->addColumnToTable('oxorder', 'TIRAMIZOO_STATUS', 'TINYINT NOT NULL');
        $this->addColumnToTable('oxorder', 'TIRAMIZOO_PARAMS', 'TEXT NOT NULL');
        $this->addColumnToTable('oxorder', 'TIRAMIZOO_WEBHOOK_RESPONSE', 'TEXT NOT NULL');
        $this->addColumnToTable('oxorder', 'TIRAMIZOO_EXTERNAL_ID', 'VARCHAR(40) NOT NULL');
        $this->addColumnToTable('oxarticles', 'TIRAMIZOO_ENABLE', 'INT(1) NOT NULL DEFAULT 0');
        $this->addColumnToTable('oxcategories', 'TIRAMIZOO_ENABLE', 'INT(1) NOT NULL DEFAULT 0');
        $this->addColumnToTable('oxcategories', 'TIRAMIZOO_WIDTH', 'FLOAT NOT NULL DEFAULT 0');
        $this->addColumnToTable('oxcategories', 'TIRAMIZOO_HEIGHT', 'FLOAT NOT NULL DEFAULT 0');
        $this->addColumnToTable('oxcategories', 'TIRAMIZOO_LENGTH', 'FLOAT NOT NULL DEFAULT 0');
        $this->addColumnToTable('oxcategories', 'TIRAMIZOO_WEIGHT', 'FLOAT NOT NULL DEFAULT 0');

        $this->executeSQL("INSERT IGNORE INTO `oxdel2delset` SET
                            OXID = MD5(CONCAT('Tiramizoo', 'Tiramizoo')),
                            OXDELID = 'Tiramizoo',
                            OXDELSETID = 'Tiramizoo';");

        $this->executeSQL("INSERT IGNORE INTO `oxdelivery` SET
                            OXID = 'Tiramizoo',
                            OXSHOPID = 'oxbaseshop',
                            OXACTIVE = 0,
                            OXACTIVEFROM = '0000-00-00 00:00:00',
                            OXACTIVETO = '0000-00-00 00:00:00',
                            OXTITLE = 'Tiramizoo',
                            OXTITLE_1 = 'Tiramizoo',
                            OXTITLE_2 = 'Tiramizoo',
                            OXTITLE_3 = 'Tiramizoo',
                            OXADDSUMTYPE = 'abs',
                            OXADDSUM = 7.90,
                            OXDELTYPE = 'p',
                            OXPARAM = 0,
                            OXPARAMEND = 999999,
                            OXFIXED = 0,
                            OXSORT = 1,
                            OXFINALIZE = 1;");

        $this->executeSQL("INSERT IGNORE INTO `oxdeliveryset` SET
                            OXID = 'Tiramizoo',
                            OXSHOPID = 'oxbaseshop',
                            OXACTIVE = 0,
                            OXACTIVEFROM = '0000-00-00 00:00:00',
                            OXACTIVETO = '0000-00-00 00:00:00',
                            OXTITLE = 'Tiramizoo',
                            OXTITLE_1 = 'Tiramizoo',
                            OXTITLE_2 = 'Tiramizoo',
                            OXTITLE_3 = 'Tiramizoo',
                            OXPOS = 1;");


        $oxConfig = $this->getConfig();

        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_api_url', 'https://sandbox.tiramizoo.com/api/v1'); 
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_api_token', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_url', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_address', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_postal_code', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_city', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_country_code', 'de');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_contact_name', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_phone_number', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_email_address', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_order_pickup_offset', 30);
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_pickup_del_offset', 90);
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_pickup_hour_1', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_pickup_hour_2', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_pickup_hour_3', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_pickup_hour_4', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_pickup_hour_5', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_pickup_hour_6', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_price', "7.90");
        $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_enable_module', 0);
        $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_is_installed', 0);
    }

    /**
     * Update database to version 0.8.1
     */
    public function migration_0_8_1()
    {
        if ($this->columnExistsInTable('TIRAMIZOO_STATUS', 'oxorder')) {
            $sql = "ALTER TABLE oxorder MODIFY TIRAMIZOO_STATUS VARCHAR(255);";
            $result = $this->executeSQL($sql);
            $sql = "UPDATE oxorder 
                        SET TIRAMIZOO_STATUS = 'processing' 
                        WHERE TIRAMIZOO_STATUS IN (0, 1);";
            $result = $this->executeSQL($sql);
        }

        if ($this->columnExistsInTable('TIRAMIZOO_ENABLE', 'oxcategories')) {
            $sql = "ALTER TABLE oxcategories MODIFY TIRAMIZOO_ENABLE INT(1) NOT NULL DEFAULT 1;";
            $result = $this->executeSQL($sql);
            $sql = "UPDATE oxcategories 
                        SET TIRAMIZOO_ENABLE = 1 
                        WHERE TIRAMIZOO_ENABLE = 0;";
            $result = $this->executeSQL($sql);
        }
    }

    /**
     * Update database to version 0.8.8
     */
    public function migration_0_8_8()
    {
        $oxConfig = $this->getConfig();
        
        $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_works_mon', 1);
        $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_works_tue', 1);
        $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_works_wed', 1);
        $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_works_thu', 1);
        $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_works_fri', 1);
        $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_works_sat', 0);
        $oxConfig->saveShopConfVar( "bool", 'oxTiramizoo_works_sun', 0);

        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_exclude_days', '');
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_include_days', '');
    }
    /*
     * Update database to version 0.8.3
     */
    public function migration_0_8_3()
    {

        $this->executeSQL("INSERT IGNORE INTO `oxdelivery` SET
                            OXID = 'TiramizooEvening',
                            OXSHOPID = 'oxbaseshop',
                            OXACTIVE = 0,
                            OXACTIVEFROM = '0000-00-00 00:00:00',
                            OXACTIVETO = '0000-00-00 00:00:00',
                            OXTITLE = 'Tiramizoo Evening',
                            OXTITLE_1 = 'Tiramizoo Evening',
                            OXTITLE_2 = 'Tiramizoo Evening',
                            OXTITLE_3 = 'Tiramizoo Evening',
                            OXADDSUMTYPE = 'abs',
                            OXADDSUM = 7.90,
                            OXDELTYPE = 'p',
                            OXPARAM = 0,
                            OXPARAMEND = 999999,
                            OXFIXED = 0,
                            OXSORT = 2,
                            OXFINALIZE = 1;");

        $this->executeSQL("INSERT IGNORE INTO `oxdeliveryset` SET
                            OXID = 'TiramizooEvening',
                            OXSHOPID = 'oxbaseshop',
                            OXACTIVE = 0,
                            OXACTIVEFROM = '0000-00-00 00:00:00',
                            OXACTIVETO = '0000-00-00 00:00:00',
                            OXTITLE = 'Tiramizoo Evening',
                            OXTITLE_1 = 'Tiramizoo Evening',
                            OXTITLE_2 = 'Tiramizoo Evening',
                            OXTITLE_3 = 'Tiramizoo Evening',
                            OXPOS = 2;");

        $this->executeSQL("INSERT IGNORE INTO `oxdel2delset` SET
                            OXID = MD5(CONCAT('TiramizooEvening', 'TiramizooEvening')),
                            OXDELID = 'TiramizooEvening',
                            OXDELSETID = 'TiramizooEvening';");


        $this->executeSQL("UPDATE `oxdelivery` SET
                            OXTITLE = 'Tiramizoo Immediate'
                            WHERE OXID = 'Tiramizoo';");


        $this->executeSQL("UPDATE `oxdeliveryset` SET
                            OXTITLE = 'Tiramizoo Immediate'
                            WHERE OXID = 'Tiramizoo';");
    }

    /**
     * Update database to version 0.8.5
     */
    public function migration_0_8_5()
    {
        $this->addColumnToTable('oxarticles', 'TIRAMIZOO_USE_PACKAGE', 'INT(1) NOT NULL DEFAULT 1');
        $this->addColumnToTable('oxcategories', 'TIRAMIZOO_USE_PACKAGE', 'INT(1) NOT NULL DEFAULT 1');
    }

    /**
     * Execute sql query
     * 
     * @param string $sql SQL query to execute
     * @return: SQL query result
     */
    protected function executeSQL($sql)
    {
        $result = oxDb::getDb()->Execute($sql);
        if ($result === false) {
            $this->_migrationErrors[] = $sql;
        }
        return $result;
    }

    /**
     * Create sql query add column to table
     * 
     * @param string $tableName  Table name
     * @param string $columnName Column name
     * @param string $columnData Column datatype
     */
    protected function addColumnToTable($tableName, $columnName, $columnData)
    {
        if (!$this->columnExistsInTable($columnName, $tableName)) {
            $sql = "ALTER TABLE " . $tableName . " ADD " . $columnName . " " . $columnData . ";";
            $result = $this->executeSQL($sql);
        }
    }

    /**
     * Check if column exists in table
     * 
     * @param string $tableName  Table name
     * @param string $columnName Column name
     * @return boolean
     */
    protected function columnExistsInTable($columnName, $tableName)
    {
        $sql = "SHOW COLUMNS FROM " . $tableName . " LIKE '" . $columnName . "'";
        $result = oxDb::getDb()->Execute($sql);

        return $result->RecordCount();
    }
}