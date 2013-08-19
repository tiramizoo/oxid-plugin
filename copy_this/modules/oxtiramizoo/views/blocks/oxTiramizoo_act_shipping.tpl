[{$smarty.block.parent}]

[{* oxtiramizoo BEGIN *}]
[{if $sCurrentShipSet == 'Tiramizoo' }]
<p>
	[{foreach from=$aAvailableDeliveryTypes item=oDeliveryType}]

		<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooDeliveryType" value="[{ $oDeliveryType->getType() }]" [{if $sTiramizooDeliveryType == $oDeliveryType->getType() }]checked="true"[{/if}] /> [{ $oDeliveryType->getName() }]

			[{if $oDeliveryType->getType() == 'immediate'}]

				[{ assign var=oTimeWindow value=$oDeliveryType->getImmediateTimeWindow() }]

 				<span> - [{ $oTimeWindow->getFormattedDeliveryTimeWindow() }]</span>
				<span style="display:none;">
					<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooTimeWindow" value="[{ $oTimeWindow->getHash() }]" [{if ($sSelectedTimeWindow == $oDeliveryType->getImmediateTimeWindow()) && ($sTiramizooDeliveryType == $oDeliveryType->getType()) }]checked="true"[{/if}] />
				</span>


			[{elseif $oDeliveryType->getType() == 'evening'}]

				[{ assign var=oTimeWindow value=$oDeliveryType->getEveningTimeWindow() }]
				<span> - [{ $oTimeWindow->getFormattedDeliveryTimeWindow() }]</span>
				<span style="display:none;">
					<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooTimeWindow" value="[{ $oTimeWindow->getHash() }]" [{if ($sSelectedTimeWindow == $oDeliveryType->getEveningTimeWindow()) && ($sTiramizooDeliveryType == $oDeliveryType->getType()) }]checked="true"[{/if}] />
				</span>

			[{/if}]

			<br />
	[{/foreach}]
</p>
[{/if}]
[{* oxtiramizoo BEGIN *}]
