[{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGCARRIER" }] 
	[{ $order->oDelSet->oxdeliveryset__oxtitle->getRawValue() }]
	
	[{* oxtiramizoo BEGIN *}]
	[{ assign var=oOrderExtended value=$order->getOrderExtended() }]
    [{if $oOrderExtended->getTrackingUrl()}]
    	Tracking URL: [{ $oOrderExtended->getTrackingUrl() }]
	[{/if}]
	[{* oxtiramizoo END *}]

[{/if}]
