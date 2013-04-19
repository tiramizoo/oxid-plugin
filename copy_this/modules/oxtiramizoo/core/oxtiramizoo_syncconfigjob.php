<?php

class oxTiramizoo_SyncConfigJob extends oxTiramizoo_ScheduleJob
{
	private $_jobType = 'synchronize_configuration';


	protected function _proccess()
	{

	}

	public function runIfNeed()
	{
		return count( oxtiramizooretaillocation::getAll() );
	}
}