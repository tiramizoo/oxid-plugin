[{$smarty.block.parent}]

[{* oxtiramizoo BEGIN *}]
[{ assign var=oOrderExtended value=$order->getOrderExtended() }]
[{if $oOrderExtended->getTrackingUrl()}]
<p>
	[{ oxmultilang ident="oxTiramizoo_tracking_url" }]: [{ $oOrderExtended->getTrackingUrl() }]
</p>
[{/if}]
[{* oxtiramizoo END *}]

