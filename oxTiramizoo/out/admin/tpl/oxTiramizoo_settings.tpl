[{include file="headitem.tpl" title="PAYENGINE_SETUP_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{cycle assign="_clear_" values=",2" }]
 
<h2>[{ oxmultilang ident="oxTiramizoo_settings_title" }]</h2>

<div id=liste>
  
    <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <input type="hidden" name="fnc" value="save">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">

    <table cellspacing="0" cellpadding="0" border="0" style="width:100%;height:100%;">
      <tr>
        <td valign="top" class="edittext" style="padding:10px;">
         <table cellspacing="0" cellpadding="5" border="0" class="edittext" style="text-align: left;">
            
            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_api_url_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_api_url] value="[{$confstrs.oxTiramizoo_api_url}]" maxlength="30" />
                [{ oxinputhelp ident="oxTiramizoo_settings_api_url_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_api_key_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_api_key] value="[{$confstrs.oxTiramizoo_api_key}]" maxlength="30" />
                [{ oxinputhelp ident="oxTiramizoo_settings_api_key_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_url_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_url] value="[{$confstrs.oxTiramizoo_shop_url}]" maxlength="30" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_url_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_address_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_address] value="[{$confstrs.oxTiramizoo_shop_address}]" maxlength="30" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_address_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_city_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_city] value="[{$confstrs.oxTiramizoo_shop_city}]" maxlength="30" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_city_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_postal_code_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_postal_code] value="[{$confstrs.oxTiramizoo_shop_postal_code}]" maxlength="30" />
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
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_contact_name] value="[{$confstrs.oxTiramizoo_shop_contact_name}]" maxlength="30" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_contact_name_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_phone_number_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_phone_number] value="[{$confstrs.oxTiramizoo_shop_phone_number}]" maxlength="30" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_phone_number_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_shop_email_address_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_shop_email_address] value="[{$confstrs.oxTiramizoo_shop_email_address}]" maxlength="30" />
                [{ oxinputhelp ident="oxTiramizoo_settings_shop_email_address_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_order_to_pickup_offset_label" }]</td>
              <td valign="top" class="edittext">
                <input type=text class="editinput" name=confstrs[oxTiramizoo_order_pickup_offset] value="[{$confstrs.oxTiramizoo_order_pickup_offset}]" maxlength="30" />
                [{ oxinputhelp ident="oxTiramizoo_settings_order_to_pickup_offset_help" }]
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_hour_1_label" }]</td>
              <td valign="top" class="edittext">
                <select name=oxTiramizoo_shop_pickup_hour[]>
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
                <select name=oxTiramizoo_shop_pickup_hour[]>
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
                <select name=oxTiramizoo_shop_pickup_hour[]>
                  <option value="">[{ oxmultilang ident="oxTiramizoo_pickup_hour_not_specified" }]</option>
                  [{foreach from=$aAvailablePickupHours item=aAvailablePickupHour}]
                    <option value="[{$aAvailablePickupHour}]" [{if ($confstrs.oxTiramizoo_shop_pickup_hour_3 == $aAvailablePickupHour)}]selected="selected"[{/if}]>[{$aAvailablePickupHour}]</option>
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
                     <li>
                        <input type="hidden" name="payment[[{$sPaymentId}]]" value="0" />
                        <input type="checkbox" name="payment[[{$sPaymentId}]]" value="1" [{if ($aPayment.checked)}]checked="checked"[{/if}] />
                        [{$aPayment.desc}]
                     </li>
                  [{/foreach}]                
                </ul>
              </td>
            </tr>

            <tr>
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_enable_module_label" }]</td>
              <td valign="top" class="edittext">
                <input type="hidden"  name="confstrs[oxTiramizoo_enable_module]" value"0" />
                <input type="checkbox" name="confstrs[oxTiramizoo_enable_module]" value"1" [{ if $confstrs.oxTiramizoo_enable_module}]checked="checked"[{ /if }]> 
                [{ oxinputhelp ident="oxTiramizoo_settings_enable_module_help" }]
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
