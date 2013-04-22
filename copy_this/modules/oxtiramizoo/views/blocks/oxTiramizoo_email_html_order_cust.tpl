[{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}]
    <h3 style="font-weight: bold; margin: 20px 0 7px; padding: 0; line-height: 35px; font-size: 12px;font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; border-bottom: 4px solid #ddd;">
        [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGCARRIER" }]
    </h3>
    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; margin: 5px 0 10px;">
        <b>[{ $order->oDelSet->oxdeliveryset__oxtitle->value }]</b>
    </p>

	[{ assign var=oOrderExtended value=$order->getOrderExtended() }]
    [{if $oOrderExtended->getTrackingUrl()}]
    <p>
    	Tracking URL: [{ $oOrderExtended->getTrackingUrl() }]
	</p>
	[{/if}]
[{/if}]
