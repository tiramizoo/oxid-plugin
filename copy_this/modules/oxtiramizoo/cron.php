<?php
/**
 * This is a Tiramizo CRON URL
 */

//oxid should execute oxtiramizoo_webhook view
$_POST['cl'] = 'oxtiramizoo_cron';

// executing regular routines ...
require '../../index.php';