<select name="sShipSet" onChange="JavaScript:document.forms.shipping.submit();">
[{foreach key=sShipID from=$oView->getAllSets() item=oShippingSet name=ShipSetSelect}]
    <option value="[{$sShipID}]" [{if $oShippingSet->blSelected}]SELECTED[{/if}]>[{ $oShippingSet->oxdeliveryset__oxtitle->value }]</option>
[{/foreach}]
</select>
<noscript>
<button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_UPDATESHIPPING" }]</button>
</noscript>


[{if $sCurrentShipSet == 'Tiramizoo' }]
	<br />
	<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooDeliveryType" value="immediate" [{if $sTiramizooDeliveryType == 'immediate' }]checked="true"[{/if}] /> Immediate <br />
	<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooDeliveryType" value="evening" [{if $sTiramizooDeliveryType == 'evening' }]checked="true"[{/if}] /> Evening <br />
	<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooDeliveryType" value="select_time" [{if $sTiramizooDeliveryType == 'select_time' }]checked="true"[{/if}] /> Select Time <br />

  [{if ($isTiramizooSelectTimeShippingMethod && ($sCurrentShipSet == 'Tiramizoo')) }]
  <div style="margin-left:20px;">
      <select id="tiramizooSelectDate" onchange="JavaScript:showTiramizooTimeWindows();">
          [{foreach from=$aTiramizooSelectTimeWindows item=aDeliveryDate}]
              <option value="[{$aDeliveryDate.date}]" [{if ($sTiramizooSelectedDate == $aDeliveryDate.date)}] selected="selected" [{/if}]>[{$aDeliveryDate.label}]</option>
          [{/foreach}]
      </select>

      [{foreach key=key from=$aTiramizooSelectTimeWindows item=sDeliveryDate}]
        <div class="tiramizooSelectDateTimeWrapper" id="tiramizooSelectDateTime-[{$sDeliveryDate.date}]" [{if (!$sDeliveryDate.active) }] style="display:none;" [{/if}]>


          [{foreach from=$sDeliveryDate.timeWindows item=aTimeWindow}]
<br />
				[{$aTimeWindow.pickup.from|date_format:"%Y-%m-%d %H:%M:%S"}]<br />
				[{$aTimeWindow.pickup.to|date_format:"%Y-%m-%d %H:%M:%S"}]<br /><br />
				[{$aTimeWindow.delivery.from|date_format:"%Y-%m-%d %H:%M:%S"}]<br />
				[{$aTimeWindow.delivery.to|date_format:"%Y-%m-%d %H:%M:%S"}]<br />
<br />

				<input type="radio" name="sTiramizooTimeWindow" value="[{$aTimeWindow.timeWindowDate}]" onChange="JavaScript:document.forms.shipping.submit();" [{if (!$aTimeWindow.enable) }] disabled="true" [{/if}] [{if ($sTiramizooTimeWindow == $aTimeWindow.timeWindowDate) }] checked="checked" [{/if}] />
				<span [{if (!$aTimeWindow.enable) }] style="color:#666;" [{/if}]>[{$aTimeWindow.timeWindowLabel}]</span> <br />
          [{/foreach}]
        </div>
      [{/foreach}]
  </div>

  <script type="text/javascript">
    function showTiramizooTimeWindows() {
        var selectDateObject = document.getElementById("tiramizooSelectDate");
        var selectedDate = selectDateObject.options[selectDateObject.selectedIndex].value;
        
        var tiramizooSelectDateTimeWrappers = document.getElementsByClassName('tiramizooSelectDateTimeWrapper');
        for (i = 0; i < tiramizooSelectDateTimeWrappers.length; i++){
            tiramizooSelectDateTimeWrappers[i].style.display = 'none';
        }

        var tiramizooSelectedDateTimeWrapper = document.getElementById('tiramizooSelectDateTime-' + selectedDate);
        tiramizooSelectedDateTimeWrapper.style.display = 'block';
    }
  </script>
  [{/if}]



[{/if}]