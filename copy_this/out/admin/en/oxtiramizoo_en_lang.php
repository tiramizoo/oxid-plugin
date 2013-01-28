<?php
/**
 * Tiramizoo admin translation English
 */

$sLangName  = "English";

// -------------------------------
// RESOURCE IDENTITFIER = STRING
// -------------------------------
$aLang = array(

'charset' => 'UTF-8',

// Admin Menu
'oxTiramizoo_admin_menu_label'                          => 'Tiramizoo',
'oxTiramizoo_settings'                                  => 'Settings',
'oxtiramizoo_category_tab_label'                        => 'Tiramizoo',
'oxtiramizoo_article_tab_label'                         => 'Tiramizoo',
'oxtiramizoo_order_tab_label'                           => 'Tiramizoo',

// Tiramizoo settings
'oxTiramizoo_settings_save_label'                       => 'Save settings',

'oxTiramizoo_settings_title'                            => 'Tiramizoo settings',

'oxTiramizoo_settings_api_url_label'                    => 'Tiramizoo URL',
'oxTiramizoo_settings_api_url_help'                     => 'Production version probably https://www.tiramizoo.com/api/v1, testing version https://sandbox.tiramizoo.com/api/v1',

'oxTiramizoo_settings_api_token_label'                  => 'Tiramizoo API token',
'oxTiramizoo_settings_api_token_help'                   => 'Can be obtained via your user profile',

'oxTiramizoo_settings_shop_url_label'                   => 'Shop URL',
'oxTiramizoo_settings_shop_url_help'                    => 'The URL of your web shop',

'oxTiramizoo_settings_shop_address_label'               => 'Pickup street address',
'oxTiramizoo_settings_shop_address_help'                => 'Where the goods should be picked up from, e.g. Alexanderplatz 1',

'oxTiramizoo_settings_shop_postal_code_label'           => 'Pickup Postal Code',
'oxTiramizoo_settings_shop_postal_code_help'            => 'e.g. 10112',

'oxTiramizoo_settings_shop_country_label'               => 'Pickup Country Code',
'oxTiramizoo_settings_shop_country_help'                => 'e.g. DE',

'oxTiramizoo_settings_shop_contact_name_label'          => 'Pickup Location Name',
'oxTiramizoo_settings_shop_contact_name_help'           => 'e.g. Example Shop GmbH',

'oxTiramizoo_settings_shop_phone_number_label'          => 'Pickup Phone Number',
'oxTiramizoo_settings_shop_phone_number_help'           => 'e.g. +49 30 / 123456',

'oxTiramizoo_settings_shop_email_address_label'         => 'Pickup email',
'oxTiramizoo_settings_shop_email_address_help'          => 'Email for notifications',

'oxTiramizoo_settings_shop_city_label'                  => 'Pickup city',
'oxTiramizoo_settings_shop_city_help'                   => 'e.g. Berlin',

'oxTiramizoo_settings_enable_module_label'              => 'Enable Tiramizoo',
'oxTiramizoo_settings_enable_module_help'               => 'If the tiramizoo shipping option should be enabled',

'oxTiramizoo_settings_pickup_hour_1_label'              => '1st pick up hour',
'oxTiramizoo_settings_pickup_hour_2_label'              => '2nd pick up hour',
'oxTiramizoo_settings_pickup_hour_3_label'              => '3rd pick up hour',
'oxTiramizoo_settings_pickup_hour_4_label'              => '4th pick up hour',
'oxTiramizoo_settings_pickup_hour_5_label'              => '5th pick up hour',
'oxTiramizoo_settings_pickup_hour_6_label'              => '6th pick up hour',
'oxTiramizoo_settings_pickup_hours_help'                => 'You can select 1-3 pickup hours',

'oxTiramizoo_pickup_hour_not_specified'                 => 'Not specified',

'oxTiramizoo_settings_payment_methods_assigned_label'   => 'Tiramizoo payment methods assigned',

'oxTiramizoo_settings_order_to_pickup_offset_label'     => 'Order To Pickup Time offset',
'oxTiramizoo_settings_order_to_pickup_offset_help'      => 'Preparation time for goods from ordering to pickup',

'oxTiramizoo_settings_pickup_del_offset_label'          => 'Delivery time window length',
'oxTiramizoo_settings_pickup_del_offset_help'           => 'Delivery time window length 90 minutes is minimal',

'oxTiramizoo_settings_pickup_time_length_label'         => 'Pickup time window length',
'oxTiramizoo_settings_pickup_time_length_help'          => 'Pickup time window length 90 minutes is minimal',

'oxTiramizoo_is_required'                               => 'is required',
'oxTiramizoo_pickup_hours_required_error'               => 'You must specify at least one pick up time window',
'oxTiramizoo_payments_required_error'                   => 'You must assign at least one payment method to Tiramizoo shipping',
'oxTiramizoo_enable_fix_errors_header'                  => 'Fix these errors to enable tiramizoo',

'oxTiramizoo_settings_articles_with_stock_gt_0'         => 'Enable only articles with stock > 0',
'oxTiramizoo_settings_articles_with_stock_gt_0_help'    => 'Enable only articles with stock > 0',


'oxTiramizoo_settings_weight_label'                     => 'Weight',
'oxTiramizoo_settings_weight_help'                      => 'This weight will be assigned for all products without specified weight. You can override it in category settings or article settings.',
'oxTiramizoo_settings_dimensions_label'                 => 'Dimensions',
'oxTiramizoo_settings_dimensions_help'                  => 'L-length, W - width, H - height. These dimensions will be assigned for all products without specified dimensions. You can override them in category settings or article settings.',

'oxTiramizoo_settings_enable_immediate_label'           => 'Enable Tiramizoo "Immediate delivery"',
'oxTiramizoo_settings_enable_immediate_help'            => 'If checked add shipping method "Immediate delivery" to checkout with the first possible Tiramizoo delivery time window.',

'oxTiramizoo_settings_enable_evening_label'             => 'Enable Tiramizoo "Evening delivery"',
'oxTiramizoo_settings_enable_evening_help'              => 'If checked and time window selected add shipping method "Evening delivery" to checkout with the selected Tiramizoo delivery time window.',
'oxTiramizoo_settings_weight_dimensions_warning'        => 'Enter the weight and all dimensions at once. If these values are filled partially then will not propagate to articles.',

'oxTiramizoo_settings_select_evening_label'             => 'Select evening delivery window',

'oxTiramizoo_settings_not_select_evening_error'         => 'If You want to enable evening delivery window You have to select evening delivery window',
'oxTiramizoo_settings_package_size_label'               => 'Standard Package size',
'oxTiramizoo_settings_package_size_help'                => 'Please enter a Standard Package size in centimeters with the following pattern (W - width, L - length, H - height).',

'oxTiramizoo_settings_package_weight_label'             => 'Standard Package weight',
'oxTiramizoo_settings_package_weight_help'              => 'Please enter a Standard Package weight',

'oxTiramizoo_settings_dimensions_unit'                  => 'cm',
'oxTiramizoo_settings_weight_unit'                      => 'kg',

'oxTiramizoo_settings_opening_hours_heading'         	=> 'Opening times',
'oxTiramizoo_settings_working_days_label'         		=> 'Working days',

'oxTiramizoo_settings_monday'         					=> 'Monday',
'oxTiramizoo_settings_tuesday'         					=> 'Tuesday',
'oxTiramizoo_settings_wedensday'         				=> 'Wedensday',
'oxTiramizoo_settings_thursday'         				=> 'Thursday',
'oxTiramizoo_settings_friday'         					=> 'Friday',
'oxTiramizoo_settings_saturday'         				=> 'Saturday',
'oxTiramizoo_settings_sunday'         					=> 'Sunday',

'oxTiramizoo_settings_exclude_days_label'         		=> 'Exclude dates',
'oxTiramizoo_settings_include_days_label'         		=> 'Include dates',

'oxTiramizoo_settings_exclude_day_caption'         		=> 'Exclude date',
'oxTiramizoo_settings_include_day_caption'         		=> 'Include date',

// Article administration -> Tiramizoo Tab

'oxTiramizoo_article_tab_enable_tiramizoo_label'        => 'Enable tiramizoo',
'oxTiramizoo_article_tab_enable_tiramizoo_help'         => 'If <strong>\'Yes\'</strong> You have to specify weight and dimensions per product or category',
'oxTiramizoo_article_tab_enable_inherit_value'          => 'Inherit from parent settings',
'oxTiramizoo_article_tab_enable_yes_value'              => 'Yes',
'oxTiramizoo_article_tab_enable_no_value'               => 'No',

'oxTiramizoo_article_tab_article_effective_label'       => 'Article\'s effective values',

'oxTiramizoo_article_tab_article_is_enabled'            => 'Article is enabled',
'oxTiramizoo_article_tab_article_is_disabled'           => 'Article is disabled',
'oxTiramizoo_article_tab_disabled_by_category_1'        => 'in tiramizoo shipping because parent category', 
'oxTiramizoo_article_tab_disabled_by_category_2'        => 'is disabled. Please check the tiramizoo tab in category settings',

'oxTiramizoo_article_tab_effective_values_warning'      => 'You have to specify dimensions and weight. You can do this in global settings, category tab or article extended tab.',

'oxTiramizoo_article_tab_width_label'                   => 'Width',
'oxTiramizoo_article_tab_height_unit'                   => 'Height',
'oxTiramizoo_article_tab_length_unit'                   => 'Length',


'oxTiramizoo_article_tab_weight_label'                  => 'Weight',
'oxTiramizoo_article_tab_weight_unit'                   => 'kg',

'oxTiramizoo_article_tab_dimensions_label'              => 'Dimensions',
'oxTiramizoo_article_tab_dimensions_unit'               => 'cm',

'oxTiramizoo_article_tab_use_package_label'             => 'Individual package',
'oxTiramizoo_article_tab_use_package_value'             => 'Yes',
'oxTiramizoo_article_tab_use_package_help'              => 'This item will not be packaged into a box, use dimensions directly',



// Category administration -> Tiramizoo Tab

'oxTiramizoo_category_tab_enable_tiramizoo_label'       => 'Enable tiramizoo',
'oxTiramizoo_category_tab_enable_tiramizoo_help'        => 'If <strong>\'Yes\'</strong> You have to specify weight and dimensions per product or category',
'oxTiramizoo_category_tab_enable_inherit_value'         => 'Inherit from parent settings',
'oxTiramizoo_category_tab_enable_yes_value'             => 'Yes',
'oxTiramizoo_category_tab_enable_no_value'              => 'No',

'oxTiramizoo_category_tab_weight_label'                 => 'Weight',
'oxTiramizoo_category_tab_weight_unit'                  => 'kg',
'oxTiramizoo_category_tab_weight_help'                  => 'This weight will be assigned to all products in this category without weight specified',

'oxTiramizoo_category_tab_dimensions_label'             => 'Dimensions',
'oxTiramizoo_category_tab_dimensions_unit'              => 'cm',
'oxTiramizoo_category_tab_dimensions_help'              => 'L-length, W - width, H - height. These dimensions will be assigned to all products in this category without dimensions specified',

'oxTiramizoo_category_tab_use_package_label'            => 'Articles has individual package',
'oxTiramizoo_category_tab_use_package_value'            => 'Yes',
'oxTiramizoo_category_tab_use_package_help'             => 'Items in this category will not be packaged into a box, use dimensions directly',

// Order administration -> Tiramizoo Tab
'oxTiramizoo_order_tab_status_label'                    => 'Tiramizoo status',
'oxTiramizoo_order_tab_status_help'                     => 'Current Tiramizoo status for this order',

'oxTiramizoo_order_tab_tracking_url_label'              => 'Tiramizoo tracking url',
'oxTiramizoo_order_tab_tracking_url_help'               => 'Url to Tiramizoo website where you can check the status for this order',

'oxTiramizoo_order_tab_external_id_label'               => 'Tiramizoo external_id',
'oxTiramizoo_order_tab_external_id_help'                => 'Property used for Tiramizoo API Webhooks',

'oxTiramizoo_order_tab_response_label'                  => 'Tiramizoo API response',
'oxTiramizoo_order_tab_response_help'                   => 'Tiramizoo API response data',

'oxTiramizoo_order_tab_webhook_response_label'          => 'Tiramizoo webhook response',
'oxTiramizoo_order_tab_webhook_response_help'           => 'Tiramizoo webhook response data',




);


