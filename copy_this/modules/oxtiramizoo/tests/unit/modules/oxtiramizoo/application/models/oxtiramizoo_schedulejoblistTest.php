<?php


class Unit_Modules_oxTiramizoo_Application_Models_oxTiramizoo_ScheduleJobListTest extends OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();
        oxUtilsObject::resetClassInstances();        
    }

    public function testLoadToRun()
    {
        oxTiramizoo_Date::changeCurrentTime('2013-04-01 09:00:00');

        $oScheduleJob1 = oxNew('oxTiramizoo_ScheduleJob');
        $oScheduleJob1->oxtiramizooschedulejob__oxjobtype = new oxField('send_order');
        $oScheduleJob1->oxtiramizooschedulejob__oxcreatedat = new oxField(oxTiramizoo_Date::date('Y-m-d'));
        $oScheduleJob1->oxtiramizooschedulejob__oxrunafter = new oxField('2013-04-01 08:00:00');
        $oScheduleJob1->oxtiramizooschedulejob__oxrunbefore = new oxField('2013-04-01 10:00:00');
        $oScheduleJob1->save();

        $oScheduleJob2 = oxNew('oxTiramizoo_ScheduleJob');
        $oScheduleJob2->oxtiramizooschedulejob__oxjobtype = new oxField('send_order');
        $oScheduleJob2->oxtiramizooschedulejob__oxcreatedat = new oxField(oxTiramizoo_Date::date('Y-m-d'));
        $oScheduleJob2->oxtiramizooschedulejob__oxrunafter = new oxField('2013-04-01 04:00:00');
        $oScheduleJob2->oxtiramizooschedulejob__oxrunbefore = new oxField('2013-04-01 06:00:00');
        $oScheduleJob2->save();

        $oScheduleJob3 = oxNew('oxTiramizoo_ScheduleJob');
        $oScheduleJob3->oxtiramizooschedulejob__oxjobtype = new oxField('send_order');
        $oScheduleJob3->oxtiramizooschedulejob__oxcreatedat = new oxField(oxTiramizoo_Date::date('Y-m-d'));
        $oScheduleJob3->oxtiramizooschedulejob__oxrunafter = new oxField('2013-04-01 18:00:00');
        $oScheduleJob3->oxtiramizooschedulejob__oxrunbefore = new oxField('2013-04-01 20:00:00');
        $oScheduleJob3->save();

        $oScheduleJobList = oxNew('oxTiramizoo_ScheduleJobList');
        $oScheduleJobList->loadToRun();

        $this->assertContains($oScheduleJob1->getId(), array_keys($oScheduleJobList->getArray()));
        $this->assertNotContains($oScheduleJob2->getId(), array_keys($oScheduleJobList->getArray()));
        $this->assertNotContains($oScheduleJob3->getId(), array_keys($oScheduleJobList->getArray()));

        $oScheduleJob1->delete();
        $oScheduleJob2->delete();
        $oScheduleJob3->delete();
    }
}