<?php

/*
 *    This file is part of the module Tiramizoo for OXID eShop Community Edition.
 *
 *
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

/**
 * Metadata version
 */
$sMetadataVersion = '0.1';
 
/**
 * Module information
 */
$aModule = array(
    'id'           => 'oxTiramizoo',
    'title'        => 'OXID Tiramizoo.com',
    'description'  => array(
                            'de'=>'OXID Tiramizoo.com',
                            'en'=>'OXID Tiramizoo.com'
                        ),
    'thumbnail'    => 'oxTiramizoo.png',
    'version'      => '0.1',
    'author'       => 'Krzysztof Kowalik',
    'url'          => 'http://github.com/',
    'email'        => 'kowalikus@gmail.com',
    'extend'       => array(
                            'oxorder'       => 'oxTiramizoo/core/oxTiramizoo_oxorder',
                            'oxdelivery'    => 'oxTiramizoo/core/oxTiramizoo_oxdelivery',
                            'order'         => 'oxTiramizoo/views/oxTiramizoo_order',
                            'payment'       => 'oxTiramizoo/views/oxTiramizoo_payment',
                        ),
    'files'        =>   array(
                        'oxTiramizoo_settings' => 'oxTiramizoo/admin/oxTiramizoo_settings.php',
                        'oxTiramizoo_setup' => 'oxTiramizoo/core/oxTiramizoo_setup.php',
                      ),
    'templates'    =>   array(
                            'oxTiramizoo_settings.tpl' => 'oxTiramizoo/out/admin/tpl/oxTiramizoo_settings.tpl',
                        ),

    'blocks'        =>  array(
                            array(  'template'  =>  'page/checkout/payment.tpl',    
                                    'block'     =>  'act_shipping',              
                                    'file'      =>   'oxTiramizoo_logo.tpl'
                            ),
    ),

);



