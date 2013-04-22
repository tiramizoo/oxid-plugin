<?php

class oxTiramizoo_SendOrderJob extends oxTiramizoo_ScheduleJob
{
    const JOB_TYPE = 'send_order';

    const MAX_REPEATS = 5;

	public function run()
	{
		try 
		{
			if ($soxId = $this->getExternalId()) {
		        $oOrder = oxNew( "oxorder" );
		        $oOrder->load( $soxId);
			}

			if ($this->getRepeats() >= self::MAX_REPEATS) {
				$this->closeJob();
				return true;
			}

	        $oTiramizooOrderExtended = oxTiramizoo_OrderExtended::findOneByFilters(array('oxorderid' => $oOrder->getId()));
			$oTiramizooData = $oTiramizooOrderExtended->getTiramizooData();

	        $oTiramizooApi = oxTiramizooApi::getApiInstance($this->getApiToken());
			$tiramizooResult = $oTiramizooApi->sendOrder($oTiramizooData);

	        if (!in_array($tiramizooResult['http_status'], array(201))) {
                $errorMessage = oxLang::getInstance()->translateString('oxTiramizoo_post_order_error', oxLang::getInstance()->getBaseLanguage(), false);
                throw new oxTiramizoo_SendOrderException( $errorMessage );
	        }

	        $this->finishJob();

		} catch (Exception $oEX) {
	        $this->refreshJob();
		}
	}

	public function getApiToken()
	{
		$aParams = $this->getParams();

		if (isset($aParams['api_token'])) {
			return $aParams['api_token'];
		}
	}

	public function setDefaultData() 
	{
		parent::setDefaultData();

		$this->oxtiramizooschedulejob__oxcreatedat = new oxField(date('Y-m-d H:i:s'));
		$this->oxtiramizooschedulejob__oxrunafter = new oxField(date('Y-m-d H:i:s', strtotime('+1 minutes')));
		$this->oxtiramizooschedulejob__oxrunbefore = new oxField(date('Y-m-d H:i:s', strtotime('+34 minutes')));
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
		$sCreatedAt = $this->oxtiramizooschedulejob__oxcreatedat->value;
		$iRepeats = ++$this->oxtiramizooschedulejob__oxrepeatcounter->value;
		
		$iMinutes = pow(2, $iRepeats);
		$this->oxtiramizooschedulejob__oxrunafter = new oxField(date('Y-m-d H:i:s', strtotime('+' . $iMinutes . ' minutes', strtotime($sCreatedAt))));
		
		$this->oxtiramizooschedulejob__oxstate = new oxField('retry');
		
		$this->save();
	}
}