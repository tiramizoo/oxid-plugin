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

'oxTiramizoo_settings_api_url_label'                    => 'Tiramizoo API URL',
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
'oxTiramizoo_settings_enable_module_help'               => 'Bieten Sie tiramizoo.com als Lieferoption an',

'oxTiramizoo_settings_enable_select_time_label'			=> 'tiramizoo.com "Festes Abholzeitfenster" aktivieren',


'oxTiramizoo_settings_pickup_hour_1_label'              => 'Beginn 1. Abholzeitfenster',
'oxTiramizoo_settings_pickup_hour_2_label'              => 'Beginn 2. Abholzeitfenster',
'oxTiramizoo_settings_pickup_hour_3_label'              => 'Beginn 3. Abholzeitfenster',
'oxTiramizoo_settings_pickup_hour_4_label'              => 'Beginn 4. Abholzeitfenster',
'oxTiramizoo_settings_pickup_hour_5_label'              => 'Beginn 5. Abholzeitfenster',
'oxTiramizoo_settings_pickup_hour_6_label'              => 'Beginn 6. Abholzeitfenster',
'oxTiramizoo_settings_pickup_hours_help'                => 'Sie können bis zu 3 Abholzeitpunkte wählen',

'oxTiramizoo_pickup_hour_not_specified'                 => 'Nicht angegeben',

'oxTiramizoo_settings_payment_methods_assigned_label'   => 'Zahlungsmethoden, mit denen Tiramizoo verwendet werden kann',

'oxTiramizoo_settings_order_to_pickup_offset_label'     => 'Packzeit',
'oxTiramizoo_settings_order_to_pickup_offset_help'      => 'Wieviel Zeit benötigen Sie zwischen Bestellung und Abholung zum Bereitstellen der Ware?',

'oxTiramizoo_settings_pickup_del_offset_label'          => 'Lieferzeitfenster in Minuten',
'oxTiramizoo_settings_pickup_del_offset_help'           => 'Mindestens 90 Minuten',

'oxTiramizoo_settings_pickup_time_length_label'         => 'Abholzeitfenster in Minuten',
'oxTiramizoo_settings_pickup_time_length_help'          => 'Mindestens 90 Minuten',


'oxTiramizoo_is_required'                               => 'ist erforderlich',
'oxTiramizoo_pickup_hours_required_error'               => 'Sie müssen mindestens ein Abholzeitfenster angeben',
'oxTiramizoo_payments_required_error'                   => 'Sie müssen mindestens eine Zahlungsmethode der Tiramizoo-Lieferoption zuweisen',
'oxTiramizoo_enable_fix_errors_header'                  => 'Folgende Fehler beheben, damit tiramizoo funktioniert',

'oxTiramizoo_settings_articles_with_stock_gt_0'         => 'Nur Artikel mit Lagerbestand > 0 über tiramizoo versendbar machen',
'oxTiramizoo_settings_articles_with_stock_gt_0_help'    => 'Nur Artikel mit Lagerbestand > 0 über tiramizoo versendbar machen',

'oxTiramizoo_delivery_select_type_enable'               => 'Enable Tiramizoo special delivery type',
'oxTiramizoo_delivery_select_type_enable_help'          => 'In forntend customer is able to select time windows today or next possible day. It depends on current hour.',

'oxTiramizoo_settings_weight_label'                     => 'Gewicht',
'oxTiramizoo_settings_weight_help'                      => 'Dieses Gewicht wird allen Artikeln zugeordnet, bei denen es nicht angegeben ist. Sie können es in den Kategorie- oder Artikeleinstellungen anpassen.',
'oxTiramizoo_settings_dimensions_label'                 => 'Abmessungen',
'oxTiramizoo_settings_dimensions_help'                  => 'L-Länge, B - Breite, H - Höhe. Diese Abmessungen werden allen Artikeln zugeordnet, bei denen sie nicht angegeben sind. Sie können sie in den Kategorie- oder Artikeleinstellungen anpassen.',

'oxTiramizoo_settings_weight_dimensions_warning'        => 'Bitte Gewicht und Abmessungen komplett angeben, ansonsten werden sie nicht angewendet.',

'oxTiramizoo_settings_enable_immediate_label'           => 'tiramizoo.com "Sofortlieferung" aktivieren',
'oxTiramizoo_settings_enable_immediate_help'            => '"Sofortlieferung" als Versandmethode aktivieren. Dies wählt automatisch das nächste verfügbare tiramizoo.com Zeitfenster.',

'oxTiramizoo_settings_enable_evening_label'             => 'tiramizoo.com "Abendzustellung" aktivieren',
'oxTiramizoo_settings_enable_evening_help'              => '"Abendzustellung" als Versandmethode aktivieren. Verwendet wird hierfür das ausgewählte Zeitfenster.',

'oxTiramizoo_settings_select_evening_label'             => 'Zeitfenster für Abendzustellung auswählen',

'oxTiramizoo_settings_not_select_evening_error'         => 'Sie müssen ein Zeitfenster für die Abendzustellung auswählen, um diese zu aktivieren',
'oxTiramizoo_settings_package_size_label'               => 'Standard-Paketgröße',
'oxTiramizoo_settings_package_size_help'                => 'Bitte geben Sie die Standard-Paketgröße in Zentimetern im folgenden Format an: (B - Breite, L - Länge, H - Höhe)',

'oxTiramizoo_settings_package_weight_label'             => 'Standard-Paketgewicht',
'oxTiramizoo_settings_package_weight_help'              => 'Bitte geben Sie das Standard-Paketgewicht in kg an',

'oxTiramizoo_settings_dimensions_unit'                  => 'cm',
'oxTiramizoo_settings_weight_unit'                      => 'kg',

'oxTiramizoo_settings_package_sizes_heading'    		=> 'Paketgrößen',

'oxTiramizoo_settings_package_sizes_strategy_1_label'  	=> 'Alle Produkte haben eigene Abmessungen',
'oxTiramizoo_settings_package_sizes_strategy_2_label'   => 'Paketgrößen angeben',
'oxTiramizoo_settings_package_sizes_strategy_3_label'  	=> 'Standardpaketgröße für alle Artikel',

'oxTiramizoo_settings_package_std_size_weight_label'  	=> 'Standard-Paketgröße und -gewicht',
'oxTiramizoo_settings_package_std_size_weight_help'  	=> 'Jede Lieferung wird in ein Paket mit den folgenden Abmessungen verpackt. L - Länge, B - Breite, H - Höhe, G - Gewicht.',

'oxTiramizoo_settings_package_size_and_weight_label'  	=> 'Paketgröße und Gewicht',
'oxTiramizoo_settings_package_size_and_weight_help'  	=> 'Paketgröße angeben. L - Länge, B - Breite, H - Höhe, G - Gewicht.',

'oxTiramizoo_settings_dimensions_short_width_label'  	=> 'B',
'oxTiramizoo_settings_dimensions_short_length_label'  	=> 'L',
'oxTiramizoo_settings_dimensions_short_height_label'  	=> 'H',
'oxTiramizoo_settings_dimensions_short_weight_label'  	=> 'G',

'oxTiramizoo_settings_opening_hours_heading'         	=> 'Öffnungszeiten',
'oxTiramizoo_settings_working_days_label'         		=> 'Arbeitstage',

'oxTiramizoo_settings_monday'         					=> 'Montag',
'oxTiramizoo_settings_tuesday'         					=> 'Dienstag',
'oxTiramizoo_settings_wedensday'         				=> 'Mittwoch',
'oxTiramizoo_settings_thursday'         				=> 'Donnerstag',
'oxTiramizoo_settings_friday'         					=> 'Freitag',
'oxTiramizoo_settings_saturday'         				=> 'Samstag',
'oxTiramizoo_settings_sunday'         					=> 'Sonntag',

'oxTiramizoo_settings_exclude_days_label'         		=> 'Ausschließen termine',
'oxTiramizoo_settings_include_days_label'         		=> 'Enthalten termine',

'oxTiramizoo_settings_exclude_day_caption'         		=> 'Ausschließen datum',
'oxTiramizoo_settings_include_day_caption'         		=> 'Beinhalten datum',

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
'oxTiramizoo_article_tab_use_package_label'             => 'Eigene Verpackung',
'oxTiramizoo_article_tab_use_package_value'             => 'Ja',
'oxTiramizoo_article_tab_use_package_help'              => 'Dieser Artikel wird nicht in ein Paket verpackt, sondern die Artikel-Abmessungen direkt für die Lieferung verwendet (z.B: Fahrräder)',

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

'oxTiramizoo_category_tab_use_package_label'            => 'Articles has individual package',
'oxTiramizoo_category_tab_use_package_value'            => 'Yes',
'oxTiramizoo_category_tab_use_package_help'             => 'Items in this category will not be packaged into a box, use dimensions directly',


// Order administration -> Tiramizoo Tab
//
'oxTiramizoo_order_tab_status_label'                    => 'Tiramizoo Auslieferstatus',
'oxTiramizoo_order_tab_status_help'                     => 'Aktueller Lieferstatus der Bestellung',

'oxTiramizoo_order_tab_tracking_url_label'              => 'Tiramizoo tracking url',
'oxTiramizoo_order_tab_tracking_url_help'               => 'Link auf Tiramizoo.com um den Lieferstatus pro Bestellung einzusehen ',

'oxTiramizoo_order_tab_external_id_label'               => 'Tiramizoo external id',
'oxTiramizoo_order_tab_external_id_help'                => 'Eigenschaft, die für Tiramizoo API Webhooks verwendet wird',

'oxTiramizoo_order_tab_response_label'                  => 'Tiramizoo API response',
'oxTiramizoo_order_tab_response_help'                   => 'Tiramizoo API response data',

'oxTiramizoo_order_tab_request_label'                   => 'Tiramizoo API request',
'oxTiramizoo_order_tab_request_help'                    => 'Tiramizoo API request data',


'oxTiramizoo_order_tab_webhook_response_label'          => 'Tiramizoo webhook response',
'oxTiramizoo_order_tab_webhook_response_help'           => 'Tiramizoo webhook response data',


'oxTiramizoo_settings_saved_success'                    => 'Die Konfiguration wurde gespeichert.',
'oxTiramizoo_add_location_success'                      => 'Die Filialadresse wurde hinzugefügt.',
'oxTiramizoo_add_location_error'                        => 'Die Filialadresse wurde nicht hinzugefügt. Bitte überprüfen Sie das API-Token und die Tiramizoo API URL.',
'oxTiramizoo_remove_location_success'                   => 'Die Filialadresse wurde entfernt.',
'oxTiramizoo_synchronize_error'                         => 'Bei der Synchronisation sind Fehler aufgetreten. Bitte versuchen Sie es zu einem späteren Zeitpunkt nochmal oder kontaktieren Sie Tiramizoo.',


'oxTiramizoo_synchronize_success'                       => 'Filialadressenkonfiguration wurde synchronisiert.',


'oxTiramizoo_api_settings_section_label'                => 'Settings der API-Verbindung',
'oxTiramizoo_packing_settings_section_label'            => 'Verpackungsstrategie',
'oxTiramizoo_default_dimensions_settings_section_label' => 'Grundeinstellungen Maße und Gewichte',
'oxTiramizoo_payments_settings_section_label'           => 'Verfügbare Zahlungsmethoden',
'oxTiramizoo_stock_enable_settings_section_label'       => 'Freischalten Liefermethode',
'oxTiramizoo_retail_locations_settings_section_label'   => 'Filialadressen',
'oxTiramizoo_sync_locations_settings_section_label'     => 'Synchronisation aller Konfigurationen',
'oxTiramizoo_new_token_settings_section_label'          => 'Neues API Token',
'oxTiramizoo_add_new_token_settings_button_label'       => 'Hinzufügen neuer Filialadresse',
'oxTiramizoo_new_token_label'                           => 'API token',
'oxTiramizoo_dashboard_link'                            => 'Gehen Sie zu Dashboard',
'oxTiramizoo_remove_retail_location_button'             => 'Entfernen diese Filiale',
'oxTiramizoo_sync_button'                               => 'Synchronisiere',
'oxTiramizoo_retail_locations_list_label'               => 'Filialen',
'oxTiramizoo_retail_locations_empty_label'              => 'Keine verbundene API Tokens vorhanden',

'oxTiramizoo_sync_locations_settings_section_help'      => 'Manuelles Synchronisieren der Konfigurationen der Filialadressen. Dieser Prozess ist automatisiert und läuft einmal täglich.',

'oxTiramizoo_settings_payments_help'                    => 'Bezahlmethoden, die für den tiramizoo-Service verfügbar sind. Bezahlung per Nachnahme ist z.Zt. über tiramizoo nicht möglich.',
'oxTiramizoo_packing_settings_help'                     => 'Wählen Sie die Verpackungsmethode für das Produkt',
'oxTiramizoo_packing_settings_label'                    => 'Definieren Sie die Paktegrösse',
'oxTiramizoo_required_info'                             => 'Die mit * gekennzeichneten Felder sind Pflichtfelder',


'oxTiramizoo_invalid_delivery_type_error'               => 'Unzulässige Liefermethode',

'oxTiramizoo_invalid_time_window_error'                 => 'Ihr ausgewähltes Zeitfenster kann leider nicht mehr bedient werden. Bitte wählen Sie ein neues Zeitfenster.',


'oxTiramizoo_category_label'                            => 'Kategorie',

'oxTiramizoo_tiramizoo_settings_path'                   => 'Tiramizoo -> Einstellungen'






);
