<select name="sShipSet" onChange="JavaScript:document.forms.shipping.submit();">
[{foreach key=sShipID from=$oView->getAllSets() item=oShippingSet name=ShipSetSelect}]
<option value="[{$sShipID}]" [{if $oShippingSet->blSelected}]SELECTED[{/if}]>[{ $oShippingSet->oxdeliveryset__oxtitle->value }]</option>
[{/foreach}]
</select>
<noscript>
<button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_PAYMENT_UPDATESHIPPING" }]</button>
</noscript>

[{if $oView->isTiramizooCurrentShiippingMethod()}]
    <dl style="margin-top:16px;">
    [{foreach key=sDeliveryTime from=$oView->getAvailableDeliveryHours() item=sDeliveryWindow}]
        <dt>
            <input id="payment_oxiddebitnote" type="radio" name="paymentid" value="oxiddebitnote" >
            <label for="payment_oxiddebitnote"><b>[{$sDeliveryWindow}]</b></label>
        </dt>
    [{/foreach}]
[{/if}]

</dl>                                                    
