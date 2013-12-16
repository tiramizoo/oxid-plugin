<?php
/**
 * This file is part of the oxTiramizoo OXID eShop plugin.
 *
 * LICENSE: This source file is subject to the MIT license that is available
 * through the world-wide-web at the following URI:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  module
 * @package   oxTiramizoo
 * @author    Tiramizoo GmbH <support@tiramizoo.com>
 * @copyright Tiramizoo GmbH
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * This class is used to install or update oxTiramizoo module
 *
 * @package oxTiramizoo
 */
class oxTiramizoo_Setup
{
    /**
     * Current version of oxTiramizoo module
     */
    const VERSION = '1.0.4';

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
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        $currentInstalledVersion = $oTiramizooConfig->getShopConfVar('oxTiramizoo_version');

        $tiramizooIsInstalled = $oTiramizooConfig->getShopConfVar('oxTiramizoo_is_installed');

        try
        {
            if (!$tiramizooIsInstalled || !$currentInstalledVersion) {
                $this->runMigrations();
                $oTiramizooConfig->saveShopConfVar( "bool", 'oxTiramizoo_is_installed', 1);
            } elseif ($tiramizooIsInstalled && (version_compare($this->getVersion(), $currentInstalledVersion) > 0)) {
                $this->runMigrations();
            }

        } catch(oxException $e) {
            $sErros = implode("</li><li>", $this->_migrationErrors);
            $errorMessage = $e->getMessage() . "<ul><li>" . $sErros . "</li></ul>";

            $this->getModule()->deactivate();

            throw new oxException($errorMessage);
        }
    }

    public function getModule()
    {
        $oModule = oxnew('oxModule');
        $oModule->load('oxTiramizoo');

        return $oModule;
    }

    public function getVersion()
    {
        return oxTiramizoo_setup::VERSION;
    }

    /**
     * This method executes all migration methods newer than already installed version and older than new version
     */
    public function runMigrations()
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        $currentInstalledVersion = $oTiramizooConfig->getShopConfVar('oxTiramizoo_version')
                                        ? $oTiramizooConfig->getShopConfVar('oxTiramizoo_version')
                                        : '0.0.0';

        $migrationsMethods = $this->getMigrationMethods();

        foreach($migrationsMethods as $methodVersion => $migrationMethod)
        {
            if (version_compare($methodVersion, $currentInstalledVersion) > 0) {
                if (version_compare($methodVersion, $this->getVersion()) <= 0) {
                    call_user_func_array(array($this, $migrationMethod), array());

                    if ($this->stopMigrationsIfErrors()) {
                        throw new oxException('<p>Cannot execute the following sql queries:</p>');
                    }
                    $oTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_version', $methodVersion);
                }
            }
        }
    }

    public function getMigrationMethods($class = __CLASS__)
    {
        $methodsNames = get_class_methods($class);

        $migrationsMethods = array();

        foreach ($methodsNames as $methodName)
        {
            if (strpos($methodName, 'migration_') === 0) {
                $methodVersion = str_replace('migration_', '', $methodName);
                $methodVersion = str_replace('_', '.', $methodVersion);
                $migrationsMethods[$methodVersion] = $methodName;
            }
        }

        uksort($migrationsMethods, 'version_compare');

        return $migrationsMethods;
    }

    public function stopMigrationsIfErrors()
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        if (count($this->_migrationErrors)) {
            //disable tiramizoo if db errors
            $oTiramizooConfig->saveShopConfVar( "bool", 'oxTiramizoo_enable_module', 0);
            $blReturn = true;
        } else {
            $oTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_update_errors', '');
            $blReturn = false;
        }

        return $blReturn;
    }

    /**
     * Update database to version 0.9.0
     */
    public function migration_0_9_0()
    {
        $oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

        $this->executeSQL("INSERT IGNORE INTO oxdeliveryset SET
                                OXID = 'Tiramizoo',
                                OXSHOPID = '" . $oTiramizooConfig->getShopId() . "',
                                OXACTIVE = 0,
                                OXACTIVEFROM = '0000-00-00 00:00:00',
                                OXACTIVETO = '0000-00-00 00:00:00',
                                OXTITLE = 'Tiramizoo',
                                OXTITLE_1 = 'Tiramizoo',
                                OXTITLE_2 = 'Tiramizoo',
                                OXTITLE_3 = 'Tiramizoo',
                                OXPOS = 1;");

        if ($this->columnExistsInTable('OXSHOPINCL', 'oxdeliveryset')) {
            $this->executeSQL("UPDATE oxdeliveryset SET
                                    OXSHOPINCL = 1
                                WHERE
                                    OXID = 'Tiramizoo'
                                AND
                                    OXSHOPID = '" . $oTiramizooConfig->getShopId() . "'");
        }

         $this->executeSQL("INSERT IGNORE INTO oxdelivery SET
                                OXID = 'TiramizooStandardDelivery',
                                OXSHOPID = '" . $oTiramizooConfig->getShopId() . "',
                                OXACTIVE = 1,
                                OXACTIVEFROM = '0000-00-00 00:00:00',
                                OXACTIVETO = '0000-00-00 00:00:00',
                                OXTITLE = 'Tiramizoo Standard Delivery',
                                OXTITLE_1 = 'Tiramizoo Standard Delivery',
                                OXTITLE_2 = 'Tiramizoo Standard Delivery',
                                OXTITLE_3 = 'Tiramizoo Standard Delivery',
                                OXADDSUMTYPE = 'abs',
                                OXADDSUM = 8,
                                OXDELTYPE = 'p',
                                OXPARAM = 0,
                                OXPARAMEND = 999999,
                                OXFIXED = 0,
                                OXSORT = 1,
                                OXFINALIZE = 1;");

        if ($this->columnExistsInTable('OXSHOPINCL', 'oxdelivery')) {
            $this->executeSQL("UPDATE oxdelivery SET
                                    OXSHOPINCL = 1
                                WHERE
                                    OXID = 'TiramizooStandardDelivery'
                                AND
                                    OXSHOPID = '" . $oTiramizooConfig->getShopId() . "'");
        }

        $this->executeSQL("INSERT IGNORE INTO oxdel2delset SET
                                OXID = MD5(CONCAT('TiramizooStandardDelivery', 'Tiramizoo')),
                                OXDELID = 'TiramizooStandardDelivery',
                                OXDELSETID = 'Tiramizoo';");

        $oTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_api_url', 'https://sandbox.tiramizoo.com/api/v1');
        $oTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_url', '');
        $oTiramizooConfig->saveShopConfVar( "bool", 'oxTiramizoo_articles_stock_gt_0', 1);
        $oTiramizooConfig->saveShopConfVar( "int", 'oxTiramizoo_package_strategy', 0);
    }

    /**
     * Update database to version 1.0.4
     */
    public function migration_1_0_4()
    {
        $this->executeSQL("ALTER TABLE oxtiramizooretaillocationconfig
                                MODIFY oxvarvalue MEDIUMTEXT;");
        $oTiramizooConfig->saveShopConfVar( "bool", 'oxTiramizoo_delivery_special', 1);
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

        return $result->RecordCount() > 0;
    }
}