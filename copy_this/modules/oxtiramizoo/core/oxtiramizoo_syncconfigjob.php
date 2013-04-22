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

	        $aApiKeys = oxtiramizooretaillocation::getAll(); 

	        foreach ($aApiKeys as $oTiramizooRetailLocation) 
	        {
	            oxTiramizooConfig::getInstance()->synchronizeAll( $oTiramizooRetailLocation->getApiToken() );
	        }

	        $this->finishJob();

		} catch (Exception $oEX) {
	        $this->refreshJob();
			echo $oEX; exit;
		}
	}

	public function setDefaultData() 
	{
		parent::setDefaultData();

		$this->oxtiramizooschedulejob__oxcreatedat = new oxField(date('Y-m-d H:i:s'));
		$this->oxtiramizooschedulejob__oxrunafter = new oxField(date('Y-m-d'));
		$this->oxtiramizooschedulejob__oxrunbefore = new oxField(date('Y-m-d', strtotime('+1 day')));
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