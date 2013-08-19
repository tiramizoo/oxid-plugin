[{assign var="oShipSet" value=$oView->getShipSet() }]

[{if $oShipSet->getId() == 'Tiramizoo'}]
<div id="orderShipping">
<form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
    <h3 class="section">
        <strong>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_SHIPPINGCARRIER" }]</strong>
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="payment">
        <input type="hidden" name="fnc" value="">
        <button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_MODIFY2" }]</button>
    </h3>
</form>
[{ $oShipSet->oxdeliveryset__oxtitle->value }]

[{* oxtiramizoo BEGIN *}]
[{if $sFormattedTiramizooTimeWindow}]
    ([{ $sFormattedTiramizooTimeWindow }])
[{/if}]
[{* oxtiramizoo END *}]

</div>

<div id="orderPayment">
    <form action="[{ $oViewConf->getSslSelfLink() }]" method="post">
        <h3 class="section">
            <strong>[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_PAYMENTMETHOD" }]</strong>
            [{ $oViewConf->getHiddenSid() }]
            <input type="hidden" name="cl" value="payment">
            <input type="hidden" name="fnc" value="">
            <button type="submit" class="submitButton largeButton">[{ oxmultilang ident="PAGE_CHECKOUT_ORDER_MODIFY3" }]</button>
        </h3>
    </form>
    [{assign var="payment" value=$oView->getPayment() }]
    [{ $payment->oxpayments__oxdesc->value }]
</div>
[{else}]
  [{$smarty.block.parent}]
[{/if}]


