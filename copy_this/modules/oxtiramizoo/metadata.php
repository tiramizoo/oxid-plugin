<?php

/*
 *    This file is part of the module Tiramizoo for OXID eShop .
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

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
    'version'      =>   '1.0.3',
    'author'       =>   'tiramizoo',
    'url'          =>   'http://github.com/tiramizoo/oxid-plugin/',
    'email'        =>   'support@tiramizoo.com',

    'extend'       =>   array(
                            'oxorder'       => 'oxtiramizoo/core/oxtiramizoo_oxorder',
                            'oxbasket'      => 'oxtiramizoo/core/oxtiramizoo_oxbasket',
                            'order'         => 'oxtiramizoo/application/controllers/oxtiramizoo_order',
                            'payment'       => 'oxtiramizoo/application/controllers/oxtiramizoo_payment',
                            'oxshopcontrol' => 'oxtiramizoo/application/controllers/oxtiramizoo_oxshopcontrol',
                        ),


    'files'        =>   array(
                            'oxTiramizoo_settings'              => 'oxtiramizoo/application/controllers/admin/oxtiramizoo_settings.php',
                            'oxTiramizoo_Article_tab'           => 'oxtiramizoo/application/controllers/admin/oxtiramizoo_article_tab.php',
                            'oxTiramizoo_Category_tab'          => 'oxtiramizoo/application/controllers/admin/oxtiramizoo_category_tab.php',
                            'oxTiramizoo_Order_Tab'             => 'oxtiramizoo/application/controllers/admin/oxtiramizoo_order_tab.php',

                            'oxTiramizoo_Setup'                 => 'oxtiramizoo/core/oxtiramizoo_setup.php',
                            'TiramizooApi'                      => 'oxtiramizoo/core/TiramizooApi/TiramizooApi.php',
                            'oxTiramizoo_Api'                   => 'oxtiramizoo/core/TiramizooApi/oxtiramizoo_api.php',
                            'oxTiramizoo_CreateOrderData'       => 'oxtiramizoo/core/TiramizooApi/oxtiramizoo_createorderdata.php',
                            'oxTiramizoo_Events'                => 'oxtiramizoo/core/oxtiramizoo_events.php',
                            'oxTiramizoo_Config'                 => 'oxtiramizoo/core/oxtiramizoo_config.php',

                            'oxTiramizoo_DeliverySet'           => 'oxtiramizoo/core/oxtiramizoo_deliveryset.php',
                            'oxTiramizoo_DeliveryPrice'         => 'oxtiramizoo/core/oxtiramizoo_deliveryprice.php',
                            'oxTiramizoo_DeliveryType'          => 'oxtiramizoo/core/oxtiramizoo_deliverytype.php',
                            'oxTiramizoo_DeliveryTypeImmediate' => 'oxtiramizoo/core/oxtiramizoo_deliverytypeimmediate.php',
                            'oxTiramizoo_DeliveryTypeEvening'   => 'oxtiramizoo/core/oxtiramizoo_deliverytypeevening.php',
                            'oxTiramizoo_TimeWindow'            => 'oxtiramizoo/core/oxtiramizoo_timewindow.php',
                            'oxTiramizoo_Date'                  => 'oxtiramizoo/core/oxtiramizoo_date.php',

                            'oxTiramizoo_ArticleInheritedData'  => 'oxtiramizoo/core/oxtiramizoo_articleinheriteddata.php',

                            'oxTiramizoo_Webhook'               => 'oxtiramizoo/application/controllers/oxtiramizoo_webhook.php',

                            'oxTiramizoo_ScheduleJobManager'    => 'oxtiramizoo/core/oxtiramizoo_schedulejobmanager.php',


                            'oxTiramizoo_SendOrderJob'          => 'oxtiramizoo/core/oxtiramizoo_sendorderjob.php',
                            'oxTiramizoo_SyncConfigJob'         => 'oxtiramizoo/core/oxtiramizoo_syncconfigjob.php',

                            /* models */
                            'oxTiramizoo_RetailLocation'        => 'oxtiramizoo/application/models/oxtiramizoo_retaillocation.php',
                            'oxTiramizoo_RetailLocationList'    => 'oxtiramizoo/application/models/oxtiramizoo_retaillocationlist.php',

                            'oxTiramizoo_RetailLocationConfig'      => 'oxtiramizoo/application/models/oxtiramizoo_retaillocationconfig.php',
                            'oxTiramizoo_RetailLocationConfigList'  => 'oxtiramizoo/application/models/oxtiramizoo_retaillocationconfiglist.php',

                            'oxTiramizoo_ArticleExtended'       => 'oxtiramizoo/application/models/oxtiramizoo_articleextended.php',
                            'oxTiramizoo_CategoryExtended'      => 'oxtiramizoo/application/models/oxtiramizoo_categoryextended.php',

                            'oxTiramizoo_OrderExtended'         => 'oxtiramizoo/application/models/oxtiramizoo_orderextended.php',

                            'oxTiramizoo_ScheduleJob'           => 'oxtiramizoo/application/models/oxtiramizoo_schedulejob.php',
                            'oxTiramizoo_ScheduleJobList'       => 'oxtiramizoo/application/models/oxtiramizoo_schedulejoblist.php',

                            /* exception */
                            'oxTiramizoo_ApiException'                  => 'oxtiramizoo/core/exception/oxtiramizoo_apiexception.php',
                            'oxTiramizoo_NotAvailableException'         => 'oxtiramizoo/core/exception/oxtiramizoo_notavailableexception.php',
                            'oxTiramizoo_SendOrderException'            => 'oxtiramizoo/core/exception/oxtiramizoo_sendorderexception.php',
                            'oxTiramizoo_InvalidTimeWindowException'    => 'oxtiramizoo/core/exception/oxtiramizoo_invalidtimewindowexception.php',
                            'oxTiramizoo_InvalidDeliveryTypeException'  => 'oxtiramizoo/core/exception/oxtiramizoo_invaliddeliverytypeexception.php',

                        ),

    'templates'    =>   array(
                            'oxTiramizoo_settings.tpl'      => 'oxtiramizoo/views/admin/tpl/oxtiramizoo_settings.tpl',
                            'oxTiramizoo_article_tab.tpl'   => 'oxtiramizoo/views/admin/tpl/oxtiramizoo_article_tab.tpl',
                            'oxTiramizoo_category_tab.tpl'  => 'oxtiramizoo/views/admin/tpl/oxtiramizoo_category_tab.tpl',
                            'oxTiramizoo_order_tab.tpl'     => 'oxtiramizoo/views/admin/tpl/oxtiramizoo_order_tab.tpl',
                        ),

    'events'       =>   array(
                            'onActivate'   => 'oxTiramizoo_Events::onActivate'
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

                            array(  'template'  =>  'email/html/order_cust.tpl',
                                    'block'     =>  'email_html_order_cust_deliveryinfo',
                                    'file'      =>  'views/blocks/oxTiramizoo_email_html_order_cust.tpl'
                            ),

                            array(  'template'  =>  'email/plain/order_cust.tpl',
                                    'block'     =>  'email_plain_order_cust_deliveryinfo',
                                    'file'      =>  'views/blocks/oxTiramizoo_email_html_order_cust.tpl'
                            ),
                        ),

);
