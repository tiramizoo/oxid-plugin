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
     * minimum delivery time
     * @var string
     */
    protected $minimumDeliveryHour = '8:00';

    /**
     * maximum delivery time default is 20:00 but in the Munich we can set up to 22:00
     * @var string
     */
    protected $maximumDeliveryHour = '20:00';

    /**
     * minimum delivery window length
     * 
     * @var string
     */
    protected $minimumDeliveryWindowLength = '01:30';

    /**
     * pickup hour step
     * @var string
     */
    protected $selectedDeliveryPickupHourStep = '00:30';

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
        // load tiramizoo config
        include getShopBasePath() . '/modules/oxtiramizoo/config.inc.php';
    }

    /**
     * Returns config parameter value if such parameter exists
     *
     * @param string $sName config parameter name
     *
     * @return mixed
     */
    public function getConfigParam( $sName )
    {
        if ( isset( $this->$sName ) ) {
            return $this->$sName;
        }

        return false;
    }

}