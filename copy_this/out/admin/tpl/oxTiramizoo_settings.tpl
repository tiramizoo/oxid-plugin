[{include file="headitem.tpl" title="Tiramizoo"}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{cycle assign="_clear_" values=",2" }]
 
<h2>[{ oxmultilang ident="oxTiramizoo_settings_title" }] <span style="font-size: 9px;">version [{$version}]</span></h2>

<style type="text/css">
  .editinput {width:240px;}
</style>

<div id=liste>
  
    <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <input type="hidden" name="fnc" value="save">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">

    [{if $aErrors|@count gt 0}]
    <div class="errorbox">
      [{ oxmultilang ident="oxTiramizoo_enable_fix_errors_header" }]:
      <ol>
      [{foreach from=$aErrors item=sError}]
        <li>[{$sError}]</li>
      [{/foreach}]
      </ol>
    </div>
    [{/if}]


    <table cellspacing="0" cellpadding="0" border="0" style="width:100%;height:100%;">
      <tr>
        <td valign="top" class="edittext" style="padding:10px;">
         <table cellspacing="0" cellpadding="5" border="0" class="edittext" style="text-align: left;">
            



            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_api_url_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_api_url] value="[{$confstrs.oxTiramizoo_api_url}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_api_url_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_api_token_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_api_token] value="[{$confstrs.oxTiramizoo_api_token}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_api_token_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_url_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_url] value="[{$confstrs.oxTiramizoo_shop_url}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_url_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_address_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_address] value="[{$confstrs.oxTiramizoo_shop_address}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_address_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_city_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_city] value="[{$confstrs.oxTiramizoo_shop_city}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_city_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_postal_code_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_postal_code] value="[{$confstrs.oxTiramizoo_shop_postal_code}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_postal_code_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_country_label" }]</td>
              <td valign="top" class="edittext">
                <select name=confstrs[oxTiramizoo_shop_country_label]>
                  <option value="de">Germany</option>
                </select>
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_country_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_contact_name_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_contact_name] value="[{$confstrs.oxTiramizoo_shop_contact_name}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_contact_name_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_phone_number_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_phone_number] value="[{$confstrs.oxTiramizoo_shop_phone_number}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_phone_number_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_email_address_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_email_address] value="[{$confstrs.oxTiramizoo_shop_email_address}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_email_address_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_order_to_pickup_offset_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_order_pickup_offset] value="[{$confstrs.oxTiramizoo_order_pickup_offset}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_order_to_pickup_offset_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_del_offset_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_pickup_del_offset] value="[{$confstrs.oxTiramizoo_pickup_del_offset}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_pickup_del_offset_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_time_length_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_pickup_time_length] value="[{$confstrs.oxTiramizoo_pickup_time_length}]" maxlength="100" />
                [{ oxinputhelp ident="oxTiramizoo_settings_pickup_time_length_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_hour_1_label" }]</td>
              <td valign="top" class="edittext">
                <select name=oxTiramizoo_shop_pickup_hour[] class="oxTiramizoo-shop-pickup-hour">
                  <option value="">[{ oxmultilang ident="oxTiramizoo_pickup_hour_not_specified" }]</option>
                  [{foreach from=$aAvailablePickupHours item=aAvailablePickupHour}]
                    <option value="[{$aAvailablePickupHour}]" [{if ($confstrs.oxTiramizoo_shop_pickup_hour_1 == $aAvailablePickupHour)}]selected="selected"[{/if}]>[{$aAvailablePickupHour}]</option>
                  [{/foreach}]
                </select>
                [{ oxinputhelp ident="oxTiramizoo_settings_pickup_hours_help" }]              
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_hour_2_label" }]</td>
              <td valign="top" class="edittext">
                <select name=oxTiramizoo_shop_pickup_hour[] class="oxTiramizoo-shop-pickup-hour">
                  <option value="">[{ oxmultilang ident="oxTiramizoo_pickup_hour_not_specified" }]</option>
                  [{foreach from=$aAvailablePickupHours item=aAvailablePickupHour}]
                    <option value="[{$aAvailablePickupHour}]" [{if ($confstrs.oxTiramizoo_shop_pickup_hour_2 == $aAvailablePickupHour)}]selected="selected"[{/if}]>[{$aAvailablePickupHour}]</option>
                  [{/foreach}]
                </select>
                [{ oxinputhelp ident="oxTiramizoo_settings_pickup_hours_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_hour_3_label" }]</td>
              <td valign="top" class="edittext">
                <select name=oxTiramizoo_shop_pickup_hour[] class="oxTiramizoo-shop-pickup-hour">
                  <option value="">[{ oxmultilang ident="oxTiramizoo_pickup_hour_not_specified" }]</option>
                  [{foreach from=$aAvailablePickupHours item=aAvailablePickupHour}]
                    <option value="[{$aAvailablePickupHour}]" [{if ($confstrs.oxTiramizoo_shop_pickup_hour_3 == $aAvailablePickupHour)}]selected="selected"[{/if}]>[{$aAvailablePickupHour}]</option>
                  [{/foreach}]
                </select>
                [{ oxinputhelp ident="oxTiramizoo_settings_pickup_hours_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_hour_4_label" }]</td>
              <td valign="top" class="edittext">
                <select name=oxTiramizoo_shop_pickup_hour[] class="oxTiramizoo-shop-pickup-hour">
                  <option value="">[{ oxmultilang ident="oxTiramizoo_pickup_hour_not_specified" }]</option>
                  [{foreach from=$aAvailablePickupHours item=aAvailablePickupHour}]
                    <option value="[{$aAvailablePickupHour}]" [{if ($confstrs.oxTiramizoo_shop_pickup_hour_4 == $aAvailablePickupHour)}]selected="selected"[{/if}]>[{$aAvailablePickupHour}]</option>
                  [{/foreach}]
                </select>
                [{ oxinputhelp ident="oxTiramizoo_settings_pickup_hours_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_hour_5_label" }]</td>
              <td valign="top" class="edittext">
                <select name=oxTiramizoo_shop_pickup_hour[] class="oxTiramizoo-shop-pickup-hour">
                  <option value="">[{ oxmultilang ident="oxTiramizoo_pickup_hour_not_specified" }]</option>
                  [{foreach from=$aAvailablePickupHours item=aAvailablePickupHour}]
                    <option value="[{$aAvailablePickupHour}]" [{if ($confstrs.oxTiramizoo_shop_pickup_hour_5 == $aAvailablePickupHour)}]selected="selected"[{/if}]>[{$aAvailablePickupHour}]</option>
                  [{/foreach}]
                </select>
                [{ oxinputhelp ident="oxTiramizoo_settings_pickup_hours_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_hour_6_label" }]</td>
              <td valign="top" class="edittext">
                <select name=oxTiramizoo_shop_pickup_hour[] class="oxTiramizoo-shop-pickup-hour">
                  <option value="">[{ oxmultilang ident="oxTiramizoo_pickup_hour_not_specified" }]</option>
                  [{foreach from=$aAvailablePickupHours item=aAvailablePickupHour}]
                    <option value="[{$aAvailablePickupHour}]" [{if ($confstrs.oxTiramizoo_shop_pickup_hour_6 == $aAvailablePickupHour)}]selected="selected"[{/if}]>[{$aAvailablePickupHour}]</option>
                  [{/foreach}]
                </select>
                [{ oxinputhelp ident="oxTiramizoo_settings_pickup_hours_help" }]
              </td>
            </tr>

            <tr>
              <td>[{ oxmultilang ident="oxTiramizoo_settings_payment_methods_assigned_label" }]</td>
              <td>
                <ul>
                  [{foreach from=$oPaymentsList key=sPaymentId item=aPayment}]
                     <li style="background:transparent;">
                        <input type="hidden" name="payment[[{$sPaymentId}]]" value="0" />
                        <input type="checkbox" name="payment[[{$sPaymentId}]]" value="1" [{if ($aPayment.checked)}]checked="checked"[{/if}] />
                        [{$aPayment.desc}]
                     </li>
                  [{/foreach}]                
                </ul>
              </td>
            </tr>
            <script>
                var helloWorld = function(e) {
                  var elements = YAHOO.util.Dom.getElementsByClassName('oxTiramizoo-shop-pickup-hour');
                  
                  for (var i in elements) {
                    if (i == 0) continue;

                    YAHOO.util.Dom.get('oxTiramizoo-evening-window').getElementsByTagName('option')[i].text = elements[i - 1].value;
                    YAHOO.util.Dom.get('oxTiramizoo-evening-window').getElementsByTagName('option')[i].value = elements[i - 1].value;
                  };
                }

                var elements = YAHOO.util.Dom.getElementsByClassName('oxTiramizoo-shop-pickup-hour');

                YAHOO.util.Event.addListener(elements, "click", helloWorld);

            </script>

          <tr>
            <td class="edittext">
              [{ oxmultilang ident="oxTiramizoo_settings_weight_label" }]
            </td>
            <td class="edittext">
              <input type="text" class="editinput" size="10" maxlength="10" style="width:40px;" name="confstrs[oxTiramizoo_global_weight]" value="[{$confstrs.oxTiramizoo_global_weight}]">[{ oxmultilang ident="oxTiramizoo_category_tab_weight_unit" }] 
              [{ oxinputhelp ident="oxTiramizoo_settings_weight_help" }]
            </td>
          </tr>

          <tr>
            <td class="edittext">
              [{ oxmultilang ident="oxTiramizoo_settings_dimensions_label" }]
            </td>
            <td class="edittext">
              L:&nbsp;<input type="text" class="editinput" size="3" maxlength="10" style="width:40px;" name="confstrs[oxTiramizoo_global_length]" value="[{$confstrs.oxTiramizoo_global_length}]">[{ oxmultilang ident="oxTiramizoo_category_tab_dimensions_unit" }]
              W:&nbsp;<input type="text" class="editinput" size="3" maxlength="" style="width:40px;" name="confstrs[oxTiramizoo_global_width]" value="[{$confstrs.oxTiramizoo_global_width}]">[{ oxmultilang ident="oxTiramizoo_category_tab_dimensions_unit" }]
              H:&nbsp;<input type="text" class="editinput" size="3" maxlength="" style="width:40px;" name="confstrs[oxTiramizoo_global_height]" value="[{$confstrs.oxTiramizoo_global_height}]">[{ oxmultilang ident="oxTiramizoo_category_tab_dimensions_unit" }]

              [{ oxinputhelp ident="oxTiramizoo_settings_dimensions_help" }]
            </td>
          </tr>

            <script>
                var helloWorld = function(e) {
                  var elements = YAHOO.util.Dom.getElementsByClassName('oxTiramizoo-shop-pickup-hour');
                  
                  for (var i in elements) {
                    if (i == 0) continue;

                    YAHOO.util.Dom.get('oxTiramizoo-evening-window').getElementsByTagName('option')[i].text = elements[i - 1].value;
                    YAHOO.util.Dom.get('oxTiramizoo-evening-window').getElementsByTagName('option')[i].value = elements[i - 1].value;
                  };
                }

                var elements = YAHOO.util.Dom.getElementsByClassName('oxTiramizoo-shop-pickup-hour');

                YAHOO.util.Event.addListener(elements, "click", helloWorld);

            </script>


         

            <tr>
              <td valign="top" colspan="2" class="edittext">
                <h3>[{ oxmultilang ident="oxTiramizoo_settings_package_sizes_heading" }]</h3>
              </td>
            </tr>


            <tr>
              <td valign="top" class="edittext" nowrap="">Define Package size</td>
              <td valign="top" class="edittext" id="selectPackageStrategyRadiosCell">
                <input class="selectPackageStrategyRadios" type="radio" name=confstrs[oxTiramizoo_package_strategy] [{if ($confstrs.oxTiramizoo_package_strategy == 0)}]checked="checked"[{/if}] value="0" /> [{ oxmultilang ident="oxTiramizoo_settings_package_sizes_strategy_1_label" }]<br />
                <input class="selectPackageStrategyRadios" type="radio" name=confstrs[oxTiramizoo_package_strategy] [{if ($confstrs.oxTiramizoo_package_strategy == 1)}]checked="checked"[{/if}] value="1" /> [{ oxmultilang ident="oxTiramizoo_settings_package_sizes_strategy_2_label" }]<br />
                <input class="selectPackageStrategyRadios" type="radio" name=confstrs[oxTiramizoo_package_strategy] [{if ($confstrs.oxTiramizoo_package_strategy == 2)}]checked="checked"[{/if}] value="2" /> [{ oxmultilang ident="oxTiramizoo_settings_package_sizes_strategy_3_label" }]<br />
              </td>
            </tr>

            <script type="text/javascript">
            (function() {
                var Dom = YAHOO.util.Dom,
                    Event = YAHOO.util.Event;

                var initPackage = function() {

                    var selectPackageStrategyRadios = YAHOO.util.Dom.getElementsByClassName('selectPackageStrategyRadios');
                    Event.addListener(selectPackageStrategyRadios, 'click', selectPackageStrategy);
                }

                var selectPackageStrategy = function() {
                  var input = YAHOO.util.Dom.getElementsBy(function (el) {
                                  return (el.name === 'confstrs[oxTiramizoo_package_strategy]' && el.checked);
                              }, 'input', 'selectPackageStrategyRadiosCell', null, null, null, true);

                  var packageStrategyId = input.value;

                  var oxTiramizoo_package_strategy_rows = YAHOO.util.Dom.getElementsByClassName('oxTiramizoo_package_strategy_row');
                  YAHOO.util.Dom.setStyle(oxTiramizoo_package_strategy_rows, 'display', 'none');

                  var oxTiramizoo_package_strategy_rows_to_show = YAHOO.util.Dom.getElementsByClassName('oxTiramizoo_package_strategy_' + packageStrategyId);
                  YAHOO.util.Dom.setStyle(oxTiramizoo_package_strategy_rows_to_show, 'display', 'table-row');

                }

                Event.addListener(window, 'load', initPackage);

            })();
            </script>

            <tr class="oxTiramizoo_package_strategy_row oxTiramizoo_package_strategy_2" [{if ($confstrs.oxTiramizoo_package_strategy != 2)}]style="display:none;"[{/if}]>
              <td valign="top" class="edittext" nowrap="">[{oxmultilang ident="oxTiramizoo_settings_package_std_size_weight_label"}]</td>
              <td valign="top" class="edittext">

                [{oxmultilang ident="oxTiramizoo_settings_dimensions_short_width_label"}]: <input type=text class="editinput" name="confstrs[oxTiramizoo_std_package_width]" value="[{$confstrs.oxTiramizoo_std_package_width}]" maxlength="10" style="width:40px;" /> [{ oxmultilang ident="oxTiramizoo_settings_dimensions_unit" }]
                
                [{oxmultilang ident="oxTiramizoo_settings_dimensions_short_length_label"}]: <input type=text class="editinput" name="confstrs[oxTiramizoo_std_package_length]" value="[{$confstrs.oxTiramizoo_std_package_length}]" maxlength="10" style="width:40px;" /> [{ oxmultilang ident="oxTiramizoo_settings_dimensions_unit" }]

                [{oxmultilang ident="oxTiramizoo_settings_dimensions_short_height_label"}]: <input type=text class="editinput" name="confstrs[oxTiramizoo_std_package_height]" value="[{$confstrs.oxTiramizoo_std_package_height}]" maxlength="10" style="width:40px;" /> [{ oxmultilang ident="oxTiramizoo_settings_dimensions_unit" }]

                [{oxmultilang ident="oxTiramizoo_settings_dimensions_short_weight_label"}]: <input type=text class="editinput" name="confstrs[oxTiramizoo_std_package_weight]" value="[{$confstrs.oxTiramizoo_std_package_weight}]" maxlength="10" style="width:40px;" /> [{ oxmultilang ident="oxTiramizoo_settings_weight_unit" }]

                [{ oxinputhelp ident="oxTiramizoo_settings_package_std_size_weight_help" }]

              </td>
            </tr>

 

            [{foreach from=$aPackageSizes key=key item=aPackage}]
            <tr class="oxTiramizoo_package_strategy_row oxTiramizoo_package_strategy_1" [{if ($confstrs.oxTiramizoo_package_strategy != 1)}]style="display:none;"[{/if}]>
              <td valign="top" class="edittext" nowrap="">[{$key}] [{oxmultilang ident="oxTiramizoo_settings_package_size_and_weight_label"}]</td>
              <td valign="top" class="edittext">

                [{oxmultilang ident="oxTiramizoo_settings_dimensions_short_width_label"}]: <input type=text class="editinput" name="packageSizes[width][[{$key}]]" value="[{$aPackage.width}]" maxlength="10" style="width:40px;" /> [{ oxmultilang ident="oxTiramizoo_settings_dimensions_unit" }],
                
                [{oxmultilang ident="oxTiramizoo_settings_dimensions_short_length_label"}]: <input type=text class="editinput" name="packageSizes[length][[{$key}]]" value="[{$aPackage.length}]" maxlength="10" style="width:40px;" /> [{ oxmultilang ident="oxTiramizoo_settings_dimensions_unit" }],

                [{oxmultilang ident="oxTiramizoo_settings_dimensions_short_height_label"}]: <input type=text class="editinput" name="packageSizes[height][[{$key}]]" value="[{$aPackage.height}]" maxlength="10" style="width:40px;" /> [{ oxmultilang ident="oxTiramizoo_settings_dimensions_unit" }],

                [{oxmultilang ident="oxTiramizoo_settings_dimensions_short_weight_label"}]: <input type=text class="editinput" name="packageSizes[weight][[{$key}]]" value="[{$aPackage.weight}]" maxlength="10" style="width:40px;" /> [{ oxmultilang ident="oxTiramizoo_settings_weight_unit" }]

                [{ oxinputhelp ident="oxTiramizoo_settings_package_size_and_weight_help" }]

              </td>
            </tr>
            [{/foreach}]


            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_articles_with_stock_gt_0" }]</td>
              <td valign="top" class="edittext">
                <input type="hidden"  name="confstrs[oxTiramizoo_articles_stock_gt_0]" value"0" />
                <input type="checkbox" name="confstrs[oxTiramizoo_articles_stock_gt_0]" value"1" [{ if $confstrs.oxTiramizoo_articles_stock_gt_0}]checked="checked"[{ /if }]> 
                [{ oxinputhelp ident="oxTiramizoo_settings_articles_with_stock_gt_0_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_enable_immediate_label" }]</td>
              <td valign="top" class="edittext">
                <input type="hidden"  name="confstrs[oxTiramizoo_enable_immediate]" value"0" />
                <input type="checkbox" name="confstrs[oxTiramizoo_enable_immediate]" value"1" [{ if $confstrs.oxTiramizoo_enable_immediate}]checked="checked"[{ /if }]> 
                [{ oxinputhelp ident="oxTiramizoo_settings_enable_immediate_help" }]
              </td>
            </tr>


            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_enable_evening_label" }]</td>
              <td valign="top" class="edittext">
                <input type="hidden"  name="confstrs[oxTiramizoo_enable_evening]" value"0" />
                <input type="checkbox" name="confstrs[oxTiramizoo_enable_evening]" value"1" [{ if $confstrs.oxTiramizoo_enable_evening}]checked="checked"[{ /if }]> 

                [{ oxmultilang ident="oxTiramizoo_settings_select_evening_label" }]
                <input type="hidden"  name="confstrs[oxTiramizoo_evening_window]" value"0" />
                <select name="confstrs[oxTiramizoo_evening_window]" id="oxTiramizoo-evening-window">
                  <option value="">Not specified</option>
                  [{foreach from=$aPickupHours item=sPickupHour}]
                     <option value="[{$sPickupHour}]" [{if ($sPickupHour == $confstrs.oxTiramizoo_evening_window)}]selected="selected"[{/if}]>[{$sPickupHour}]</option>
                  [{/foreach}]    
                </select>
                
                [{ oxinputhelp ident="oxTiramizoo_settings_enable_evening_help" }]
              </td>
            </tr>


            <tr>
              <td valign="top" class="edittext" width="250" nowrap="">          
                <input type="submit" name="save" value="[{ oxmultilang ident="oxTiramizoo_settings_save_label" }]" [{ $readonly}]>
              </td>
            </tr>

          </table>

        </td>
      </tr>
    </table>
    </form>
  [{include file="pagenavisnippet.tpl"}]
</div>
[{include file="pagetabsnippet.tpl"}]

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
