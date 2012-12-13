<?php
/**
* Tiramizoo admin translation Deutsch
 */

$sLangName  = "Deutsch";

// -------------------------------
// RESOURCE IDENTITFIER = STRING
// -------------------------------
$aLang = array(

'charset' => 'UTF-8',

// Admin Menu
'oxTiramizoo_admin_menu_label'                          => 'Tiramizoo',
'oxTiramizoo_settings'                                  => 'Einstellungen',
'oxtiramizoo_category_tab_label'                        => 'Tiramizoo',
'oxtiramizoo_article_tab_label'                         => 'Tiramizoo',
'oxtiramizoo_order_tab_label'                           => 'Tiramizoo',

//Admin settings
'oxTiramizoo_settings_save_label'                       => 'Einstellungen speichern',

'oxTiramizoo_settings_title'                            => 'Tiramizoo Einstellungen',

'oxTiramizoo_settings_api_url_label'                    => 'Tiramizoo URL',
'oxTiramizoo_settings_api_url_help'                     => 'Produktivsystem meistens https://www.tiramizoo.com/api/v1, Testsystem https://sandbox.tiramizoo.com/api/v1',

'oxTiramizoo_settings_api_token_label'                  => 'Tiramizoo API token',
'oxTiramizoo_settings_api_token_help'                   => 'Zu finden in Ihrem Nutzerprofil',

'oxTiramizoo_settings_shop_url_label'                   => 'Tiramizoo Shop URL',
'oxTiramizoo_settings_shop_url_help'                    => 'URL Ihres Webshops',

'oxTiramizoo_settings_shop_address_label'               => 'Abholort Straße und Hausnummer',
'oxTiramizoo_settings_shop_address_help'                => 'z.B. Alexanderplatz 1',

'oxTiramizoo_settings_shop_postal_code_label'           => 'Abholort Postleitzahl',
'oxTiramizoo_settings_shop_postal_code_help'            => 'z.B. 10112',

'oxTiramizoo_settings_shop_country_label'               => 'Abholhort Ländercode',
'oxTiramizoo_settings_shop_country_help'                => 'z.B. DE',

'oxTiramizoo_settings_shop_contact_name_label'          => 'Abholort Bezeichnung',
'oxTiramizoo_settings_shop_contact_name_help'           => 'z.B. Testshop GmbH',

'oxTiramizoo_settings_shop_phone_number_label'          => 'Abholort Telefonnummer',
'oxTiramizoo_settings_shop_phone_number_help'           => 'z.B. +49 30 123456',

'oxTiramizoo_settings_shop_email_address_label'         => 'Email',
'oxTiramizoo_settings_shop_email_address_help'          => 'Email-Adresse für Benachrichtigungen',

'oxTiramizoo_settings_shop_city_label'                  => 'Abholort Stadt',
'oxTiramizoo_settings_shop_city_help'                   => 'z.B. Berlin',

'oxTiramizoo_settings_enable_module_label'              => 'Tiramizoo aktivieren',
'oxTiramizoo_settings_enable_module_help'               => 'Bieten Sie tiramizoo als Lieferoption an',

'oxTiramizoo_settings_pickup_hour_1_label'              => '1. Abholzeitpunkt',
'oxTiramizoo_settings_pickup_hour_2_label'              => '2. Abholzeitpunkt',
'oxTiramizoo_settings_pickup_hour_3_label'              => '3. Abholzeitpunkt',
'oxTiramizoo_settings_pickup_hour_4_label'              => '4. Abholzeitpunkt',
'oxTiramizoo_settings_pickup_hour_5_label'              => '5. Abholzeitpunkt',
'oxTiramizoo_settings_pickup_hour_6_label'              => '6. Abholzeitpunkt',
'oxTiramizoo_settings_pickup_hours_help'                => 'Sie können bis zu 3 Abholzeitpunkten wählen',

'oxTiramizoo_pickup_hour_not_specified'                 => 'Nicht angegeben',

'oxTiramizoo_settings_payment_methods_assigned_label'   => 'Zahlungsmethoden, mit denen Tiramizoo verwendet werden kann',

'oxTiramizoo_settings_order_to_pickup_offset_label'     => 'Packzeit',
'oxTiramizoo_settings_order_to_pickup_offset_help'      => 'Wieviel Zeit benötigen Sie zwischen Bestellung und Abholung zum Bereitstellen der Ware?',

'oxTiramizoo_settings_pickup_del_offset_label'          => 'Delivery time window length',
'oxTiramizoo_settings_pickup_del_offset_help'           => 'Delivery time window length 90 minutes is minimal',

'oxTiramizoo_settings_pickup_time_length_label'         => 'Pickup time window length',
'oxTiramizoo_settings_pickup_time_length_help'          => 'Pickup time window length 90 minutes is minimal',


'oxTiramizoo_is_required'                               => 'erforderlich ist',
'oxTiramizoo_pickup_hours_required_error'               => 'Sie müssen mindestens ein Abholzeitpunkt',
'oxTiramizoo_payments_required_error'                   => 'Sie müssen mindestens ein Zahlungsmethode Tiramizoo Versandkosten zuweisen',
'oxTiramizoo_enable_fix_errors_header'                  => 'Fix diese Fehler, um tiramizoo ermöglichen',

'oxTiramizoo_settings_articles_with_stock_gt_0'         => 'Enable only articles with stock > 0',
'oxTiramizoo_settings_articles_with_stock_gt_0_help'    => 'Enable only articles with stock > 0',

'oxTiramizoo_settings_weight_label'                     => 'Gewicht',
'oxTiramizoo_settings_weight_help'                      => 'Dieses Gewicht wird allen Artikeln zugeordnet, bei denen es nicht angegeben ist. Sie können es in den Kategorie- oder Artikeleinstellungen anpassen.',
'oxTiramizoo_settings_dimensions_label'                 => 'Abmessungen',
'oxTiramizoo_settings_dimensions_help'                  => 'L-Länge, B - Breite, H - Höhe. Diese Abmessungen werden allen Artikeln zugeordnet, bei denen sie nicht angegeben sind. Sie können sie in den Kategorie- oder Artikeleinstellungen anpassen.',

'oxTiramizoo_settings_weight_dimensions_warning'        => 'Bitte Gewicht und Abmessungen komplett angeben, ansonsten werden sie nicht angewendet.',

// Article administration -> Tiramizoo Tab

'oxTiramizoo_article_tab_enable_tiramizoo_label'        => 'Tiramizoo aktivieren',
'oxTiramizoo_article_tab_enable_tiramizoo_help'         => 'Falls aktiviert, müssen Sie Gewicht und Abmessungen per Produkt oder Kategorie angeben',
'oxTiramizoo_article_tab_enable_inherit_value'          => 'Aus Kategorie übernehmen',
'oxTiramizoo_article_tab_enable_yes_value'              => 'Ja',
'oxTiramizoo_article_tab_enable_no_value'               => 'Nein',

'oxTiramizoo_article_tab_article_effective_label'       => 'Effektive Werte des Artikels',

'oxTiramizoo_article_tab_article_is_enabled'            => 'Artikel ist aktiviert',
'oxTiramizoo_article_tab_article_is_disabled'           => 'Artikel is deaktiviert',
'oxTiramizoo_article_tab_disabled_by_category'          => 'für den Versand mit tiramizoo.com, da die übergeordnete Kategorie {Jeans} deaktiviert ist. Bitte den Reiter tiramizoo in den Kategorieeinstellungen überprüfen.',

'oxTiramizoo_article_tab_effective_values_warning'      => 'Sie müssen Abmessungen und Gewicht angeben. Sie können dies in den allgemeinen Einstellungen, Kategorie-Reiter oder den erweiterten Einstellungen des Artikels vornehmen.',

'oxTiramizoo_article_tab_width_label'                   => 'Breite',
'oxTiramizoo_article_tab_height_unit'                   => 'Höhe',
'oxTiramizoo_article_tab_length_unit'                   => 'Länge',


'oxTiramizoo_article_tab_weight_label'                  => 'Gewicht',
'oxTiramizoo_article_tab_weight_unit'                   => 'kg',

'oxTiramizoo_article_tab_dimensions_label'              => 'Abmessungen',
'oxTiramizoo_article_tab_dimensions_unit'               => 'cm',

// Category administration -> Tiramizoo Tab

'oxTiramizoo_category_tab_enable_tiramizoo_label'       => 'Tiramizoo aktivieren',
'oxTiramizoo_category_tab_enable_tiramizoo_help'        => 'Falls aktiviert, müssen Sie Gewicht und Abmessungen per Produkt oder Kategorie angeben',
'oxTiramizoo_category_tab_enable_inherit_value'         => 'Aus übergeordneter Kategorie übernehmen',
'oxTiramizoo_category_tab_enable_yes_value'             => 'Ja',
'oxTiramizoo_category_tab_enable_no_value'              => 'Nein',

'oxTiramizoo_category_tab_weight_label'                 => 'Gewicht',
'oxTiramizoo_category_tab_weight_unit'                  => 'kg',
'oxTiramizoo_category_tab_weight_help'                  => 'Dieses Gewicht wird allen Produkten dieser Kategorie zugeornet, bei denen keines angegeben wurde',

'oxTiramizoo_category_tab_dimensions_label'             => 'Abmessungen',
'oxTiramizoo_category_tab_dimensions_unit'              => 'cm',
'oxTiramizoo_category_tab_dimensions_help'              => 'L - Länge, W - Breite, H - Höhe. Diese Abmessungen werden allen Produkten dieser Kategorie zugeordnet, bei denen keine angegeben wurden',

// Order administration -> Tiramizoo Tab
// 
'oxTiramizoo_order_tab_status_label'                    => 'Tiramizoo Auslieferstatus',
'oxTiramizoo_order_tab_status_help'                     => 'Aktueller Lieferstatus der Bestellung',

'oxTiramizoo_order_tab_tracking_url_label'              => 'Tiramizoo tracking url',
'oxTiramizoo_order_tab_tracking_url_help'               => 'Link auf Tiramizoo.com um den Lieferstatus pro Bestellung einzusehen ',

'oxTiramizoo_order_tab_external_id_label'               => 'Tiramizoo external_id',
'oxTiramizoo_order_tab_external_id_help'                => 'Eigenschaft, die für Tiramizoo API Webhooks verwendet wird',

'oxTiramizoo_order_tab_response_label'                  => 'Tiramizoo API response',
'oxTiramizoo_order_tab_response_help'                   => 'Tiramizoo API response data',

'oxTiramizoo_order_tab_webhook_response_label'          => 'Tiramizoo webhook response',
'oxTiramizoo_order_tab_webhook_response_help'           => 'Tiramizoo webhook response data',


);
