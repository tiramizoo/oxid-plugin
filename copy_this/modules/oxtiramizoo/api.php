<?php
/**
 * This is a Tiramizo Webhook URL
 */

//oxid should execute oxtiramizoo_webhook view
$_POST['cl'] = 'oxtiramizoo_webhook';

// executing regular routines ...
require '../../index.php';