<?php

abstract class oxTiramizoo_ScheduleJob 
{
	private $_jobType = '';
	private $_repeat = '';


	protected function _beforeRun()
	{

	}

	protected function _afterRun()
	{

	}

	public function run()
	{
		$this->_beforeRun();
		$this->_proccess();
		$this->_afterRun();		
	}

	protected function _proccess()
	{

	}

	protected function start()
	{
		
	}

	public function initialize()
	{

	}

	public function jobExists()
	{
		
	}

}