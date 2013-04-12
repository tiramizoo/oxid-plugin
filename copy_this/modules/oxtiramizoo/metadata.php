<?php

/*
 *    This file is part of the module Tiramizoo for OXID eShop Community Edition.
 *
 *
 */

/**
 * Metadata version
 */
$sMetadataVersion = '0.2.23';

/**
 * Module information
 */
$aModule = array(
    'id'           =>   'oxTiramizoo',
    'title'        =>   'OXID Tiramizoo.com',
    'description'  =>   array(
                            'de'=>'OXID Tiramizoo.com',
                            'en'=>'OXID Tiramizoo.com'
                        ),

    'thumbnail'    =>   'oxTiramizoo.png',
    'version'      =>   '0.9',
    'author'       =>   'tiramizoo',
    'url'          =>   'http://github.com/tiramizoo/oxid-plugin/',
    'email'        =>   'kowalikus@gmail.com',

    'extend'       =>   array(
                            'oxorder'       => 'oxtiramizoo/core/oxtiramizoo_oxorder',
                            'order'         => 'oxtiramizoo/views/oxtiramizoo_order',
                            'payment'       => 'oxtiramizoo/views/oxtiramizoo_payment',
                        ),

    'files'        =>   array(
                            'oxTiramizoo_settings'              => 'oxtiramizoo/admin/oxtiramizoo_settings.php',
                            'oxTiramizoo_Article_tab'           => 'oxtiramizoo/admin/oxtiramizoo_article_tab.php',
                            'oxTiramizoo_Category_tab'          => 'oxtiramizoo/admin/oxtiramizoo_category_tab.php',
                            'oxTiramizoo_Order_Tab'             => 'oxtiramizoo/admin/oxtiramizoo_order_tab.php',
                            'oxTiramizoo_setup'                 => 'oxtiramizoo/core/oxtiramizoo_setup.php',
                            'oxTiramizooApi'                    => 'oxtiramizoo/core/TiramizooApi/oxTiramizooApi.php',
                            'TiramizooApi'                      => 'oxtiramizoo/core/TiramizooApi/TiramizooApi.php',
                            'oxTiramizooEvents'                 => 'oxtiramizoo/core/oxtiramizoo_events.php',
                            'oxTiramizooConfig'                 => 'oxtiramizoo/core/oxtiramizoo_config.php',
                            'oxTiramizooArticleHelper'          => 'oxtiramizoo/core/oxtiramizoo_articlehelper.php',
                            'oxTiramizooHelper'                 => 'oxtiramizoo/core/oxtiramizoo_helper.php',

                            /* models */
                            'oxtiramizooretaillocation'         => 'oxtiramizoo/models/oxtiramizoo_retaillocation.php',
                            'oxtiramizooretaillocationconfig'   => 'oxtiramizoo/models/oxtiramizoo_retaillocationconfig.php',

                            'oxtiramizooarticleextended'        => 'oxtiramizoo/models/oxtiramizoo_articleextended.php',
                            'oxtiramizoocategoryextended'       => 'oxtiramizoo/models/oxtiramizoo_categoryextended.php',
                            'oxtiramizooorderextended'          => 'oxtiramizoo/models/oxtiramizoo_orderextended.php',


                            /* exception */
                            'oxTiramizoo_ApiException'          => 'oxtiramizoo/core/exception/oxtiramizoo_apiexception.php',
                            'oxTiramizoo_NotAvailableException' => 'oxtiramizoo/core/exception/oxtiramizoo_notavailableexception.php',
                            'oxTiramizoo_SendOrderException'    => 'oxtiramizoo/core/exception/oxtiramizoo_sendorderexception.php',
                        ),

    'templates'    =>   array(
                            'oxTiramizoo_settings.tpl'      => 'oxtiramizoo/out/admin/tpl/oxtiramizoo_settings.tpl',
                            'oxTiramizoo_article_tab.tpl'   => 'oxtiramizoo/out/admin/tpl/oxtiramizoo_article_tab.tpl',
                            'oxTiramizoo_category_tab.tpl'  => 'oxtiramizoo/out/admin/tpl/oxtiramizoo_category_tab.tpl',
                            'oxTiramizoo_order_tab.tpl'     => 'oxtiramizoo/out/admin/tpl/oxtiramizoo_order_tab.tpl',
                        ),
    // @ToDo: check it
    'events'       =>   array(
                            'onActivate'   => 'oxTiramizooEvents::onActivate',
                            'onDeactivate' => 'oxTiramizooEvents::onDeactivate'
                        ),


    'blocks'       =>   array(
                            array(  'template'  =>  'page/checkout/payment.tpl',    
                                    'block'     =>  'act_shipping',              
                                    'file'      =>  'views/blocks/oxTiramizoo_act_shipping.tpl'
                            ),

                            array(  'template'  =>  'page/checkout/order.tpl',    
                                    'block'     =>  'shippingAndPayment',              
                                    'file'      =>  'views/blocks/oxTiramizoo_shippingAndPayment.tpl'
                            ),
                        ),

);



