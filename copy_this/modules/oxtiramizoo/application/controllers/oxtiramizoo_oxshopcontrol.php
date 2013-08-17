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
 * Add to every shop action CRON like scheduler mechanism
 * to synchronize config and try to resend order to Tiramizoo API
 * 
 * @extend oxShopControl
 * @package oxTiramizoo
 */
class oxTiramizoo_oxShopControl extends oxTiramizoo_oxShopControl_parent
{
    /**
     * Check if scheduler manager is enabled
     * Executes jobs if necessary. 
     * Executes parent::_process()
     *
     * @extend oxShopControl::_process()
     *
     * @param string $sClass      Name of class
     * @param string $sFunction   Name of function
     * @param array  $aParams     Parameters array
     * @param array  $aViewsChain Array of views names that should be initialized also
     *
     * @return null
     */    
    public function _process( $sClass = null, $sFunction = null, $aParams = null, $aViewsChain = null )
    {
    	try
    	{
			$oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');
			$oScheduleJobManager = oxRegistry::get('oxTiramizoo_ScheduleJobManager');

			if (!$oTiramizooConfig->getShopConfVar('cron_is_enabled') && !$oScheduleJobManager->isFinished()) {
				$oScheduleJobManager->addTasks();
				$oScheduleJobManager->runJobs();
			}    		
    	} catch (oxSystemComponentException $e) {
    		
    	}

        // @codeCoverageIgnoreStart
        if (!defined('OXID_PHP_UNIT')) {
			parent::_process( $sClass, $sFunction, $aParams, $aViewsChain );
        }
        // @codeCoverageIgnoreEnd
	}
}
