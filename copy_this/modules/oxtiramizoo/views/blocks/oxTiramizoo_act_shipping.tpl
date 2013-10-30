[{$smarty.block.parent}]

[{* oxtiramizoo BEGIN *}]


[{if $oView->getCurrentShipSet() == 'Tiramizoo' }]
<p>
	[{foreach from=$oView->getAvailableDeliveryTypes() item=oDeliveryType}]

		<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooDeliveryType" value="[{ $oDeliveryType->getType() }]" [{if $oView->getTiramizooDeliveryType() == $oDeliveryType->getType() }]checked="true"[{/if}] /> [{ $oDeliveryType->getName() }]

			[{if $oDeliveryType->getType() == 'immediate'}]

				[{ assign var=oTimeWindow value=$oDeliveryType->getImmediateTimeWindow() }]
 				<span> - [{ $oTimeWindow->getFormattedDeliveryTimeWindow() }]</span>
				<span style="display:none;">
					<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooTimeWindow" value="[{ $oTimeWindow->getHash() }]" [{if ($oView->getSelectedTimeWindow() == $oTimeWindow->getHash()) && ($oView->getTiramizooDeliveryType() == $oDeliveryType->getType()) }]checked="true"[{/if}] />
				</span>


			[{elseif $oDeliveryType->getType() == 'evening'}]

				[{ assign var=oTimeWindow value=$oDeliveryType->getEveningTimeWindow() }]
				<span> - [{ $oTimeWindow->getFormattedDeliveryTimeWindow() }]</span>
				<span style="display:none;">
					<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooTimeWindow" value="[{ $oTimeWindow->getHash() }]" [{if ($oView->getSelectedTimeWindow() == $oTimeWindow->getHash()) && ($oView->getTiramizooDeliveryType() == $oDeliveryType->getType()) }]checked="true"[{/if}] />
				</span>

			[{/if}]

			<br />
	[{/foreach}]
</p>
[{/if}]
[{* oxtiramizoo BEGIN *}]
