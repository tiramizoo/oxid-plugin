<?php
/**
 * This file is part of the module oxTiramizoo for OXID eShop.
 *
 * The module oxTiramizoo for OXID eShop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free Software Foundation
 * either version 3 of the License, or (at your option) any later version.
 *
 * The module oxTiramizoo for OXID eShop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. 
 *  
 * See the GNU General Public License for more details <http://www.gnu.org/licenses/>
 *
 * @copyright: Tiramizoo GmbH
 * @author: Krzysztof Kowalik <kowalikus@gmail.com>
 * @package: oxTiramizoo
 * @license: http://www.gnu.org/licenses/
 * @version: 1.0.1
 * @link: http://tiramizoo.com
 */

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
    const VERSION = '1.0.1';

    /**
     * Error message
     * @var string
     */
    protected $_messageInfo = '';

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

        } catch(Exception $e) {
            echo $this->_messageInfo;
            print_r($e);
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
                }
            }
        }
    }

    /**
     * Update database to version 1.0.0 
     */
    public function migration_1_0_0()
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

        $this->ExecuteSQL("INSERT IGNORE INTO `oxdel2delset` SET
                            OXID = MD5(CONCAT('Tiramizoo', 'Tiramizoo')),
                            OXDELID = 'Tiramizoo',
                            OXDELSETID = 'Tiramizoo';");

        $this->ExecuteSQL("INSERT IGNORE INTO `oxdelivery` SET
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

        $this->ExecuteSQL("INSERT IGNORE INTO `oxdeliveryset` SET
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
        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_version', '1.0.0');
    }

    /**
     * Update database to version 1.0.1 
     */
    public function migration_1_0_1()
    {
        if ($this->columnExistsInTable('TIRAMIZOO_STATUS', 'oxorder')) {
            $sql = "ALTER TABLE oxorder MODIFY TIRAMIZOO_STATUS VARCHAR(255);";
            $result = $this->ExecuteSQL($sql);
            $sql = "UPDATE oxorder 
                        SET TIRAMIZOO_STATUS = 'processing' 
                        WHERE TIRAMIZOO_STATUS IN (0, 1);";
            $result = $this->ExecuteSQL($sql);
        }

        if ($this->columnExistsInTable('TIRAMIZOO_ENABLE', 'oxcategories')) {
            $sql = "ALTER TABLE oxcategories MODIFY TIRAMIZOO_ENABLE INT(1) NOT NULL DEFAULT 1;";
            $result = $this->ExecuteSQL($sql);
            $sql = "UPDATE oxcategories 
                        SET TIRAMIZOO_ENABLE = 1 
                        WHERE TIRAMIZOO_ENABLE = 0;";
            $result = $this->ExecuteSQL($sql);

        }

        $oxConfig = $this->getConfig();

        $oxConfig->saveShopConfVar( "str", 'oxTiramizoo_version', '1.0.1');
    }

    /**
     * Executesql query
     * 
     * @param string $sql SQL query to execute
     * @return: SQL query result
     */
    protected function ExecuteSQL($sql)
    {
        $result = oxDb::getDb()->Execute($sql);
        if ($result === false) {
            $this->_messageInfo .= $sql . ";\n";
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
            $result = $this->ExecuteSQL($sql);
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
        $result = $this->ExecuteSQL($sql);

        return $result->RecordCount();
    }
}