<?php

class oxTiramizoo_SyncConfigJob extends oxTiramizoo_ScheduleJob
{
	const JOB_TYPE = 'synchronize_configuration';

    const MAX_REPEATS = 10;

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

	        foreach ($oRetailLocationList as $oRetailLocation) 
	        {
	            oxTiramizooConfig::getInstance()->synchronizeAll( $oRetailLocation->getApiToken() );
	        }

	        $this->finishJob();

		} catch (Exception $oEX) {
	        $this->refreshJob();
		}
	}

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

	public function save()
	{
		if (!$this->getId()) {
			$this->setDefaultData();
		}

		parent::save();
	}

	public function refreshJob()
	{		
		$this->oxtiramizooschedulejob__oxstate = new oxField('retry');
		$this->oxtiramizooschedulejob__oxrepeatcounter->value++;
		$this->save();
	}
}