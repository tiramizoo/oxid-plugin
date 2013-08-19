[{$smarty.block.parent}]

[{* oxtiramizoo BEGIN *}]
[{ assign var=oOrderExtended value=$order->getOrderExtended() }]
[{if $oOrderExtended->getTrackingUrl()}]
<p>
	Tracking URL: [{ $oOrderExtended->getTrackingUrl() }]
</p>
[{/if}]
[{* oxtiramizoo END *}]

