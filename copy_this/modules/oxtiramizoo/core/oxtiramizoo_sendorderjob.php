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
class oxTiramizoo_SendOrderJob extends oxTiramizoo_ScheduleJob
{
    /**
     * Maximum number of repeating running job
     */
    const MAX_REPEATS = 5;

    /**
     * Job type
     */
    const JOB_TYPE = 'send_order';

    /**
     * Check if job could be fired. Run job and save state as finished.
     * Send order to Tiramizoo API
     *
     * @throws oxTiramizoo_SendOrderException if response status is not equal 201
     *
     * @return null
     */
	public function run()
	{
		try
		{
			if ($soxId = $this->getExternalId()) {
		        $oOrder = oxNew( "oxorder" );
		        $oOrder->load( $soxId );
			}

			if ($this->getRepeats() >= self::MAX_REPEATS) {
				$this->closeJob();
				return true;
			}

	        $oTiramizooOrderExtended = oxNew('oxTiramizoo_OrderExtended');
	        $sOxId = $oTiramizooOrderExtended->getIdByOrderId($oOrder->getId());
	        $oTiramizooOrderExtended->load($sOxId);

			$oTiramizooData = $oTiramizooOrderExtended->getTiramizooData();

	        $oTiramizooApi = oxTiramizoo_Api::getApiInstance($this->getApiToken());
			$tiramizooResult = $oTiramizooApi->sendOrder($oTiramizooData);

            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_response = new oxField(base64_encode(serialize($tiramizooResult)), oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_request_data = new oxField(base64_encode(serialize($oTiramizooData)), oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_status = new oxField($tiramizooResult['response']->state, oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_external_id = new oxField($oTiramizooData->external_id, oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__tiramizoo_tracking_url = new oxField($tiramizooResult['response']->tracking_url . '?locale=' . oxLang::getInstance()->getLanguageAbbr(), oxField::T_RAW);
            $oTiramizooOrderExtended->oxtiramizooorderextended__oxorderid = new oxField($oOrder->getId());

            $oTiramizooOrderExtended->save();

	        if (!in_array($tiramizooResult['http_status'], array(201))) {
                $errorMessage = oxLang::getInstance()->translateString('oxTiramizoo_post_order_error', oxLang::getInstance()->getTplLanguage(), true);
                throw new oxTiramizoo_SendOrderException( $errorMessage );
	        }

	        $this->finishJob();

		} catch (Exception $oEX) {
	        $this->refreshJob();
		}
	}

    /**
     * Returns API token
     *
     * @return string
     */
	public function getApiToken()
	{
		$aParams = $this->getParams();

		if (isset($aParams['api_token'])) {
			return $aParams['api_token'];
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
		$oRunAfterDate->modify('+1 minutes');
		$this->oxtiramizooschedulejob__oxrunafter = new oxField($oRunAfterDate->get());

		$oRunBeforeDate = new oxTiramizoo_Date();
		$oRunBeforeDate->modify('+34 minutes');
		$this->oxtiramizooschedulejob__oxrunbefore = new oxField($oRunBeforeDate->get());

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
     * Modify job state and increment repeat counter.
     * Change after date exponentially based on repeat counter.
     * Update record in DB.
     *
     * @return null
     */
	public function refreshJob()
	{
		$sCreatedAt = $this->oxtiramizooschedulejob__oxcreatedat->value;
		$iRepeats = ++$this->oxtiramizooschedulejob__oxrepeatcounter->value;

		$iMinutes = pow(2, $iRepeats);

		$oRunAfterDate = oxNew('oxTiramizoo_Date', $sCreatedAt);
		$oRunAfterDate->modify('+' . $iMinutes . ' minutes');

		$this->oxtiramizooschedulejob__oxrunafter = new oxField($oRunAfterDate->get());

		$this->oxtiramizooschedulejob__oxstate = new oxField('retry');

		$this->save();
	}

    /**
     * Change state to finished. Set email with tracking url to Tiramizoo
     * executes parent::finishJob()
     *
     * @extend oxBase::finishJob()
     *
     * @return null
     */
    public function finishJob()
    {
    	parent::finishJob();

		if ($soxId = $this->getExternalId()) {
	        $oOrder = oxNew( "oxorder" );
	        $oOrder->load( $soxId );

	        $oOrderExtended = oxNew('oxTiramizoo_OrderExtended');
	        $sOxId = $oOrderExtended->getIdByOrderId($oOrder->getId());
	        $oOrderExtended->load($sOxId);

	        $oEmail = oxNew( 'oxEmail' );

	        $oShop = $oEmail->getConfig()->getActiveShop();

	        $oEmail->setFrom( $oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue() );
	        $oEmail->setSmtp( $oShop );
	        $oEmail->setBody('Tracking URL:' . $oOrderExtended->getTrackingUrl());
	        $oEmail->setSubject( 'Tiramizoo tracking URL');

	        $oUser = $oOrder->getOrderUser();
	        $sFullName = $oUser->oxuser__oxfname->value . " " . $oUser->oxuser__oxlname->value;

	        $oEmail->setRecipient( $oUser->oxuser__oxusername->value, $sFullName );
	        $oEmail->setReplyTo( $oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue() );

	        // @codeCoverageIgnoreStart
	        if (!defined('OXID_PHP_UNIT')) {
	            $oEmail->send();
	        }
	        // @codeCoverageIgnoreEnd
		}
    }
}
