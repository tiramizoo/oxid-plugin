Test
<select name="sShipSet" onChange="JavaScript:document.forms.shipping.submit();">
[{foreach key=sShipID from=$oView->getAllSets() item=oShippingSet name=ShipSetSelect}]
<option value="[{$sShipID}]" [{if $oShippingSet->blSelected}]SELECTED[{/if}]>[{ $oShippingSet->oxdeliveryset__oxtitle->value }]</option>
[{/foreach}]
</select>
<noscript>
<button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_UPDATESHIPPING" }]</button>
</noscript>
Test