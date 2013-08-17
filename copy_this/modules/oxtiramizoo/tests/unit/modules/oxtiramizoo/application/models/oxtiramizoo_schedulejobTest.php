<?php


class Unit_Modules_oxTiramizoo_Application_Models_oxTiramizoo_ScheduleJobTest extends OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testGetIdTodayByType()
    {
        $oScheduleJob = oxNew('oxTiramizoo_ScheduleJob');
        $oScheduleJob->oxtiramizooschedulejob__oxjobtype = new oxField('job type');
        $oScheduleJob->oxtiramizooschedulejob__oxcreatedat = new oxField(oxTiramizoo_Date::date('Y-m-d'));
        $oScheduleJob->save();

        $this->assertEquals($oScheduleJob->getId(), $oScheduleJob->getIdTodayByType('job type'));

        $oScheduleJob->delete();
    }

    public function testSetDefaultData()
    {
        $oScheduleJob = oxNew('oxTiramizoo_ScheduleJob');
        $oScheduleJob->setDefaultData();

        $this->assertEquals(0, $oScheduleJob->oxtiramizooschedulejob__oxrepeatcounter->value);
        $this->assertEquals('new', $oScheduleJob->oxtiramizooschedulejob__oxstate->value);
    }

    public function testSetGetExternalId()
    {
        $oScheduleJob = oxNew('oxTiramizoo_ScheduleJob');
        $oScheduleJob->setExternalId('some external id');

        $this->assertEquals('some external id', $oScheduleJob->getExternalId());
    }

    public function testSetGetParams()
    {
        $oScheduleJob = oxNew('oxTiramizoo_ScheduleJob');
        $oScheduleJob->setParams(array('some param' => 'some value'));

        $this->assertEquals(array('some param' => 'some value'), $oScheduleJob->getParams());
    }

    public function testSetGetState()
    {
        $oScheduleJob = oxNew('oxTiramizoo_ScheduleJob');
        $oScheduleJob->setState('finished');

        $this->assertEquals('finished', $oScheduleJob->getState());
    }

    public function testRun()
    {
        $oScheduleJob = oxNew('oxTiramizoo_ScheduleJob');
        $oScheduleJob->setDefaultData();
        $oScheduleJob->save();

        $this->assertEquals(0, $oScheduleJob->getRepeats());
        
        $oScheduleJob->run();
        $oScheduleJob->run();
        $oScheduleJob->run();
        $this->assertEquals(3, $oScheduleJob->getRepeats());

        $oScheduleJob->closeJob();
        $this->assertEquals('error', $oScheduleJob->getState());

        $oScheduleJob->finishJob();
        $this->assertEquals('finished', $oScheduleJob->getState());

        $oScheduleJob->delete();
    }


}