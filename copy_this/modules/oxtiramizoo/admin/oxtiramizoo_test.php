<?php


/**
* Tiramizoo test class to add some data and test...
*
* @package: oxTiramizoo
*/
class oxTiramizoo_test extends Shop_Config
{

    public function init()
    {
    }

    /**
    * Executes parent method parent::render() and returns name of template
    *
    * @return string
    */
    public function render()
    {
        // $aConfigData = oxTiramizooApi::getInstance()->getRemoteConfiguration();
        // print_r($aConfigData);

        // $aConfigData = oxTiramizooConfig::getInstance()->getShopConfVar('discounts_enabled');
        // print_r($aConfigData);

        die('OK');
    }


}