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

            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_response = new oxField(base64_encode(serialize($tiramizooResult)), oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_request_data = new oxField(base64_encode(serialize($oTiramizooData)), oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_status = new oxField($tiramizooResult['response']->state, oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_external_id = new oxField($oTiramizooData->external_id, oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_tracking_url = new oxField($tiramizooResult['response']->tracking_url . '?locale=' . oxLang::getInstance()->getLanguageAbbr(), oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__oxorderid = new oxField($oOrder->getId());

            $oTiramizooOrderExtended->save();

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

		$this->oxtiramizooschedulejob__oxcreatedat = new oxField(oxTiramizoo_Date::date());
		
		$oRunAfterDate = new oxTiramizoo_Date();
		$oRunAfterDate->modify('+1 minutes');
		$this->oxtiramizooschedulejob__oxrunafter = new oxField($oRunAfterDate->get());

		$oRunBeforeDate = new oxTiramizoo_Date();
		$oRunBeforeDate->modify('+34 minutes');
		$this->oxtiramizooschedulejob__oxrunbefore = new oxField($oRunBeforeDate->get());

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

		$oRunAfterDate = new oxTiramizoo_Date($sCreatedAt);
		$oRunAfterDate->modify('+' . $iMinutes . ' minutes');

		$this->oxtiramizooschedulejob__oxrunafter = new oxField($oRunAfterDate->get());
		
		$this->oxtiramizooschedulejob__oxstate = new oxField('retry');
		
		$this->save();
	}
}