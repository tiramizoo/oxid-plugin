[{$smarty.block.parent}]

[{* oxtiramizoo BEGIN *}]
[{ assign var=oOrderExtended value=$order->getOrderExtended() }]
[{if $oOrderExtended->getTrackingUrl()}]
	Tracking URL: [{ $oOrderExtended->getTrackingUrl() }]
[{/if}]
[{* oxtiramizoo END *}]
