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
 * Extends oxbasket class. Overrides method to calculate price
 *
 * @extends oxTiramizoo_ScheduleJob
 * @package oxTiramizoo
 */
class oxTiramizoo_SyncConfigJob extends oxTiramizoo_ScheduleJob
{
    /**
     * Maximum number of repeating running job
     */
    const MAX_REPEATS = 10;
    
    /**
     * Job type
     */	
	const JOB_TYPE = 'synchronize_configuration';

    /**
     * Check if job could be fired. Run job and save state as finished.
     * 
     * @return null
     */
	public function run()
	{
		try 
		{
			if ($this->getRepeats() >= self::MAX_REPEATS) {
				$this->closeJob();
				return true;
			}

			$oRetailLocationList = oxnew('oxTiramizoo_RetailLocationList');
			$oRetailLocationList->loadAll();

			$oTiramizooConfig = oxRegistry::get('oxTiramizoo_Config');

	        foreach ($oRetailLocationList as $oRetailLocation) 
	        {
	            $oTiramizooConfig->synchronizeAll( $oRetailLocation->getApiToken() );
	        }

	        $this->finishJob();

		} catch (oxException $oEX) {
	        $this->refreshJob();
		}
	}


    /**
     * Setting object with default data. Execute parent::setDefaultData().
     * Add date range when running is available
     * 
     * @extend oxTiramizoo_ScheduleJob::setDefaultData()
     *
     * @return null
     */
	public function setDefaultData() 
	{
		parent::setDefaultData();

		$this->oxtiramizooschedulejob__oxcreatedat = new oxField(oxTiramizoo_Date::date());
		
		$oRunAfterDate = new oxTiramizoo_Date();
		$this->oxtiramizooschedulejob__oxrunafter = new oxField($oRunAfterDate->get('Y-m-d'));

		$oRunBeforeDate = new oxTiramizoo_Date();
		$oRunBeforeDate->modify('+1 day');
		$this->oxtiramizooschedulejob__oxrunbefore = new oxField($oRunBeforeDate->get('Y-m-d'));

        $this->oxtiramizooschedulejob__oxjobtype = new oxField(self::JOB_TYPE);
	}

    /**
     * Saves (updates) user object data information in DB. Set default data.
     * executes parent::save()
     * 
     * @extend oxBase::save()
     *
     * @return null
     */
	public function save()
	{
		if (!$this->getId()) {
			$this->setDefaultData();
		}

		parent::save();
	}

    /**
     * Modify job state and increment repeat counter. Update record in DB.
     *
     * @return null
     */
	public function refreshJob()
	{		
		$this->oxtiramizooschedulejob__oxstate = new oxField('retry');
		$this->oxtiramizooschedulejob__oxrepeatcounter->value++;
		$this->save();
	}
}
