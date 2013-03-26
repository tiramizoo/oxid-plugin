<?php

/**
 * This class contains tiramizoo config
 *
 * @package: oxTiramizoo
 */
class oxTiramizooConfig extends oxSuperCfg
{
    /**
     * Singleton instance
     * 
     * @var oxTiramizooApi
     */
    protected static $_instance = null;

    /**
     * Get the instance of class
     * 
     * @return oxTiramizooHelper
     */
    public static function getInstance()
    {
        if ( !self::$_instance instanceof oxTiramizooConfig ) {
                self::$_instance = new oxTiramizooConfig();
        }

        return self::$_instance;
    }

    public function __construct()
    {

    }

    /**
     * Returns config parameter value if such parameter exists
     *
     * @param string $sName config parameter name
     *
     * @return mixed
     */
    public function getConfigParam( $sName, $bDefaultValue = false )
    {
        if ( isset( $this->$sName ) ) {
            return $this->$sName;
        }

        return $defaultValue;
    }
}