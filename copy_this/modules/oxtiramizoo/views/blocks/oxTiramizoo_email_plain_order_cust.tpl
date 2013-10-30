[{$smarty.block.parent}]

[{* oxtiramizoo BEGIN *}]
[{ assign var=oOrderExtended value=$order->getOrderExtended() }]
[{if $oOrderExtended->getTrackingUrl()}]
	[{ oxmultilang ident="oxTiramizoo_tracking_url" }]: [{ $oOrderExtended->getTrackingUrl() }]
[{/if}]
[{* oxtiramizoo END *}]
