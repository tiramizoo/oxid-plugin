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
  <td colspan="2"><h3>Defining pickup and delivery times</h3></td>
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
              <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="oxTiramizoo_settings_pickup_hour_4_label" }]</td>
              <td valign="top" class="edittext">
                <select name=oxTiramizoo_shop_pickup_hour[]>
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
                <select name=oxTiramizoo_shop_pickup_hour[]>
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
                <select name=oxTiramizoo_shop_pickup_hour[]>
                  <option value="">[{ oxmultilang ident="oxTiramizoo_pickup_hour_not_specified" }]</option>
                  [{foreach from=$aAvailablePickupHours item=aAvailablePickupHour}]
                    <option value="[{$aAvailablePickupHour}]" [{if ($confstrs.oxTiramizoo_shop_pickup_hour_6 == $aAvailablePickupHour)}]selected="selected"[{/if}]>[{$aAvailablePickupHour}]</option>
                  [{/foreach}]
                </select>
                [{ oxinputhelp ident="oxTiramizoo_settings_pickup_hours_help" }]
              </td>
            </tr>













<tr>
  <td colspan="2"><h3>[{ oxmultilang ident="oxTiramizoo_settings_opening_hours_heading" }]</h3></td>
</tr>  



            <tr>
              <td>[{oxmultilang ident="oxTiramizoo_settings_working_days_label"}]</td>
              <td>
                <ul>


                     <li>
                        <input type="hidden" name="confstrs[oxTiramizoo_works_mon]" value="0" />
                        <input type="checkbox" name="confstrs[oxTiramizoo_works_mon]" value="1" [{if ($confstrs.oxTiramizoo_works_mon)}]checked="checked"[{/if}] />
                        [{oxmultilang ident="oxTiramizoo_settings_monday"}]
                     </li>
                     <li>
                        <input type="hidden" name="confstrs[oxTiramizoo_works_tue]" value="0" />
                        <input type="checkbox" name="confstrs[oxTiramizoo_works_tue]" value="1" [{if ($confstrs.oxTiramizoo_works_tue)}]checked="checked"[{/if}] />
                        [{oxmultilang ident="oxTiramizoo_settings_tuesday"}]
                     </li>
                     <li>
                        <input type="hidden" name="confstrs[oxTiramizoo_works_wed]" value="0" />
                        <input type="checkbox" name="confstrs[oxTiramizoo_works_wed]" value="1" [{if ($confstrs.oxTiramizoo_works_wed)}]checked="checked"[{/if}] />
                        [{oxmultilang ident="oxTiramizoo_settings_wedensday"}]
                     </li>
                     <li>
                        <input type="hidden" name="confstrs[oxTiramizoo_works_thu]" value="0" />
                        <input type="checkbox" name="confstrs[oxTiramizoo_works_thu]" value="1" [{if ($confstrs.oxTiramizoo_works_thu)}]checked="checked"[{/if}] />
                        [{oxmultilang ident="oxTiramizoo_settings_thursday"}]
                     </li>
                     <li>
                        <input type="hidden" name="confstrs[oxTiramizoo_works_fri]" value="0" />
                        <input type="checkbox" name="confstrs[oxTiramizoo_works_fri]" value="1" [{if ($confstrs.oxTiramizoo_works_fri)}]checked="checked"[{/if}] />
                        [{oxmultilang ident="oxTiramizoo_settings_friday"}]
                     </li>
                     <li>
                        <input type="hidden" name="confstrs[oxTiramizoo_works_sat]" value="0" />
                        <input type="checkbox" name="confstrs[oxTiramizoo_works_sat]" value="1" [{if ($confstrs.oxTiramizoo_works_sat)}]checked="checked"[{/if}] />
                        [{oxmultilang ident="oxTiramizoo_settings_saturday"}]
                     </li>
                     <li>
                        <input type="hidden" name="confstrs[oxTiramizoo_works_sun]" value="0" />
                        <input type="checkbox" name="confstrs[oxTiramizoo_works_sun]" value="1" [{if ($confstrs.oxTiramizoo_works_sun)}]checked="checked"[{/if}] />
                        [{oxmultilang ident="oxTiramizoo_settings_sunday"}]
                     </li>

                </ul>
              </td>
            </tr>



            <tr>
              <td>[{oxmultilang ident="oxTiramizoo_settings_exclude_days_label"}]</td>
              <td>
                <ul id="ExcludeDatesList" style="max-height:140px; overflow: auto;">                
                    [{foreach from=$aExcludeDates item=sDate}]
                    <li id="exclude-date-[{$sDate}]">
                      <input type="hidden" value="[{$sDate}]" name="exclude_date[]"/>
                      <span>[{$sDate}]</span>
                      <a class="delete_date" href="#" title="Remove date">[x]</a>
                    </li>
                    [{/foreach}]
                </ul>
              </td>
            </tr>


            <tr>
              <td>[{oxmultilang ident="oxTiramizoo_settings_include_days_label"}]</td>
              <td>
                <ul id="IncludeDatesList" style="max-height:140px; overflow: auto;">
                    [{foreach from=$aIncludeDates item=sDate}]
                    <li id="include-date-[{$sDate}]">
                      <input type="hidden" value="[{$sDate}]" name="include_date[]"/>
                      <span>[{$sDate}]</span>
                      <a class="delete_date" href="#" title="Remove date">[x]</a>
                    </li>
                    [{/foreach}]
                </ul>
              </td>
            </tr>

            <tr>
              <td></td>
              <td>
                <div class="yui-skin-sam">
                  


                  <input type="text" name="cal1Date1" id="cal1Date1" autocomplete="off" size="16" /> 
                  <button id="ExcludeDate">[{oxmultilang ident="oxTiramizoo_settings_exclude_day_caption"}]</button>
                  <button id="IncludeDate">[{oxmultilang ident="oxTiramizoo_settings_include_day_caption"}]</button>

                  <div id="cal1Container" style="display:none;"></div>



                  <script type="text/javascript">
                  (function() {
                      var Dom = YAHOO.util.Dom,
                          Event = YAHOO.util.Event,
                          cal1,
                          over_cal = false,
                          cur_field = '';

                      var init = function() {
                          cal1 = new YAHOO.widget.Calendar("cal1","cal1Container");
                          cal1.selectEvent.subscribe(getDate, cal1, true);
                          cal1.renderEvent.subscribe(setupListeners, cal1, true);
                          Event.addListener(['cal1Date1'], 'focus', showCal);
                          Event.addListener(['cal1Date1'], 'blur', hideCal);
                          Event.addListener(['ExcludeDate'], 'click', excludeDate );
                          Event.addListener(['IncludeDate'], 'click', includeDate );

                          var delete_dates = YAHOO.util.Dom.getElementsByClassName('delete_date');
                          Event.addListener(delete_dates, 'click', deleteDateItem);



                          cal1.render();
                      }

                      var setupListeners = function() {
                          Event.addListener('cal1Container', 'mouseover', function() {
                              over_cal = true;
                          });
                          Event.addListener('cal1Container', 'mouseout', function() {
                              over_cal = false;
                          });
                      }

                      var sortUnorderedList = function(ul) {
                        ul = document.getElementById(ul);

                        if(!ul) return;

                        var list = ul.getElementsByTagName("LI");
                        var values = [];

                        for(var i = 0, l = list.length; i < l; i++)
                          values.push(list[i].innerHTML);

                        values.sort();

                        for(var i = 0; i < list.length; i++)
                          list[i].innerHTML = values[i];
                      }



                      var getDate = function() {
                              var calDate = this.getSelectedDates()[0];
                              calDate = calDate.getFullYear() + '-' + (calDate.getMonth() + 1 <= 9 ? '0' : '' ) + (calDate.getMonth() + 1) + '-' + (calDate.getDate() <= 9 ? '0' : '' ) + calDate.getDate();
                              cur_field.value = calDate;            
                              over_cal = false;
                              hideCal();
                      }


                      var deleteDateItem = function(e) {
                        var link = Dom.get(e.target);
                        var liItem = link.parentNode; 

                        liItem.parentNode.removeChild(liItem);

                        Event.preventDefault(e); 
                      }

                      var excludeDate = function(e) {
                        var date = Dom.get('cal1Date1').value;
                        if (date && (!Dom.get('exclude-date-' + date))) {

                          var html = '<input type="hidden" value="' + date + '" name="exclude_date[]"/>';
                          html += '<span>' + date + '</span> ';
                          html += '<a class="delete_date" href="#" title="Remove date">[x]</a>';
                              
                          var li = document.createElement("li");
                          li.innerHTML = html;
                          Dom.setAttribute(li, 'id', 'exclude-date-' + date);

                          Dom.get('ExcludeDatesList').appendChild(li);
                          sortUnorderedList('ExcludeDatesList');

                          var delete_dates = Dom.get('ExcludeDatesList').getElementsByClassName('delete_date');
                          Event.addListener(delete_dates, 'click', deleteDateItem);
                         }

                        Dom.get('cal1Date1').value = '';
                        Event.preventDefault(e); 
                      }


                      var includeDate = function(e) {
                        var date = Dom.get('cal1Date1').value;
                        if (date && (!Dom.get('include-date-' + date))) {
                         
                          var html = '<input type="hidden" value="' + date + '" name="include_date[]"/>';
                          html += '<span>' + date + '</span> ';
                          html += '<a class="delete_date" href="#" title="Remove date">[x]</a>';
                              
                          var li = document.createElement("li");
                          li.innerHTML = html;
                          Dom.setAttribute(li, 'id', 'include-date-' + date);

                          Dom.get('IncludeDatesList').appendChild(li);
                          sortUnorderedList('IncludeDatesList');

                          var delete_dates = Dom.get('ExcludeDatesList').getElementsByClassName('delete_date');
                          Event.addListener(delete_dates, 'click', deleteDateItem);
                        }

                        Dom.get('cal1Date1').value = '';
                        Event.preventDefault(e); 
                      }


                      var showCal = function(ev) {
                          var tar = Event.getTarget(ev);
                          cur_field = tar;
                      
                          var xy = Dom.getXY(tar),
                              date = Dom.get(tar).value;
                          if (date) {
                              cal1.cfg.setProperty('selected', date);
                              cal1.cfg.setProperty('pagedate', new Date(date), true);
                          } else {
                              cal1.cfg.setProperty('selected', '');
                              cal1.cfg.setProperty('pagedate', new Date(), true);
                          }
                          cal1.render();
                          Dom.setStyle('cal1Container', 'display', 'block');
                          xy[1] = xy[1] + 20;
                          Dom.setXY('cal1Container', xy);
                      }

                      var hideCal = function() {
                          if (!over_cal) {
                              Dom.setStyle('cal1Container', 'display', 'none');
                          }
                      }

                      Event.addListener(window, 'load', init);

                  })();
                  </script>

                </div>

              </td>
            </tr>





<tr>
  <td colspan="2"><h3>Available payment methods</h3></td>
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
