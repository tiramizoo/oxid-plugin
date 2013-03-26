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

  ul li {background: transparent;}
  .delete_date {color: #888;}
</style>





<link rel="stylesheet" type="text/css" href="/modules/oxtiramizoo/src/yui/build/calendar/assets/skins/sam/calendar.css">
 
<!-- Dependencies -->
<script src="/modules/oxtiramizoo/src/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script src="/modules/oxtiramizoo/src/yui/build/event-delegate/event-delegate.js"></script>
<!-- Source file -->
<script src="/modules/oxtiramizoo/src/yui/build/calendar/calendar-min.js"></script>

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
  <td colspan="2"><h3>Api connection settings</h3></td>
</tr>  


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
  <td colspan="2"><h3>Enabling shipping method</h3></td>
</tr>  

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
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_enable_select_time_label" }]</td>
              <td valign="top" class="edittext">
                <input type="hidden"  name="confstrs[oxTiramizoo_enable_select_time]" value"0" />
                <input type="checkbox" name="confstrs[oxTiramizoo_enable_select_time]" value"1" [{ if $confstrs.oxTiramizoo_enable_select_time}]checked="checked"[{ /if }]> 
                [{ oxinputhelp ident="oxTiramizoo_settings_enable_select_time_help" }]
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
