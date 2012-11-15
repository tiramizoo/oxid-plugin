<?php

class oxTiramizoo_setup extends Shop_Config
{
    public function __construct() {}

    public function install()
    {
        $this->setupConfigVars();
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
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_country_code', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_contact_name', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_phone_number', '');
        $oxTiramizooConfig->saveShopConfVar( "str", 'oxTiramizoo_shop_email_address', '');
        $oxTiramizooConfig->saveShopConfVar( "bool", 'oxTiramizoo_enable_module', 0);
        $oxTiramizooConfig->saveShopConfVar( "bool", 'oxTiramizoo_is_installed', 0);
        
        // clear cache 
        oxUtils::getInstance()->rebuildCache();
    }
}