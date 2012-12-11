<?php
/**
 * Config file to specify tiramizoo settings
 * Be careful when change it, it should be setted with tiramizoo rules
 */

//minimum delivery time
$this->minimumDeliveryHour = '8:00';

//maximum delivery time default is 20:00 but in the Munich we can set up to 22:00
$this->maximumDeliveryHour = '22:00';

//minimum delivery window length
$this->minimumDeliveryWindowLength = '01:30';

//pickup hour step
$this->selectedDeliveryPickupHourStep = '00:30';