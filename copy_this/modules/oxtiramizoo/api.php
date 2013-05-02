<?php
/**
 * This is a Tiramizo Webhook URL
 */

//oxid should execute oxtiramizoo_webhook view
$_POST['cl'] = 'oxtiramizoo_webhook';

// executing regular routines ...

if ( !defined( 'OXID_PHP_UNIT' ) ) {
	require '../../index.php';
}
