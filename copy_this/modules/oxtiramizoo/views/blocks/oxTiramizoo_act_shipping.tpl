[{$smarty.block.parent}]

[{* oxtiramizoo BEGIN *}]


[{if $oView->getCurrentShipSet() == 'Tiramizoo' }]
<p>
	[{foreach from=$oView->getAvailableDeliveryTypes() item=oDeliveryType}]


			[{if $oDeliveryType->getType() == 'immediate'}]
				<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooDeliveryType" value="[{ $oDeliveryType->getType() }]" [{if $oView->getTiramizooDeliveryType() == $oDeliveryType->getType() }]checked="true"[{/if}] /> [{ $oDeliveryType->getName() }]

				[{ assign var=oTimeWindow value=$oDeliveryType->getImmediateTimeWindow() }]
 				<span> - [{ $oTimeWindow->getFormattedDeliveryTimeWindow() }]</span>
				<span style="display:none;">
					<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooTimeWindow" value="[{ $oTimeWindow->getHash() }]" [{if ($oView->getSelectedTimeWindow() == $oTimeWindow->getHash()) && ($oView->getTiramizooDeliveryType() == $oDeliveryType->getType()) }]checked="true"[{/if}] />
				</span>


			[{elseif $oDeliveryType->getType() == 'evening'}]
				<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooDeliveryType" value="[{ $oDeliveryType->getType() }]" [{if $oView->getTiramizooDeliveryType() == $oDeliveryType->getType() }]checked="true"[{/if}] /> [{ $oDeliveryType->getName() }]

				[{ assign var=oTimeWindow value=$oDeliveryType->getEveningTimeWindow() }]
				<span> - [{ $oTimeWindow->getFormattedDeliveryTimeWindow() }]</span>
				<span style="display:none;">
					<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooTimeWindow" value="[{ $oTimeWindow->getHash() }]" [{if ($oView->getSelectedTimeWindow() == $oTimeWindow->getHash()) && ($oView->getTiramizooDeliveryType() == $oDeliveryType->getType()) }]checked="true"[{/if}] />
				</span>

			[{elseif $oDeliveryType->getType() == 'special'}]
				<span style="display:block;" >
					<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooDeliveryType" value="[{ $oDeliveryType->getType() }]" [{if $oView->getTiramizooDeliveryType() == $oDeliveryType->getType() }]checked="true"[{/if}] /> [{ $oDeliveryType->getName() }] <br />
				</span>

				[{if $oDeliveryType->getType() == $oView->getTiramizooDeliveryType()}]
					<p>
						[{ oxmultilang ident="oxTiramizoo_select_time_window_label" }]
					</p>

					[{ assign var=oTimeWindow value=$oDeliveryType->getSpecialTimeWindow() }]


					[{foreach from=$oDeliveryType->getAvailableTimeWindows() item=oTimeWindow}]

						<span>
							<input type="radio" onchange="JavaScript:document.forms.shipping.submit();" name="sTiramizooTimeWindow" value="[{ $oTimeWindow->getHash() }]" [{if ($oView->getSelectedTimeWindow() == $oTimeWindow->getHash()) && ($oView->getTiramizooDeliveryType() == $oDeliveryType->getType()) }]checked="true"[{/if}] />
							<span>[{ $oTimeWindow->getFormattedDeliveryTimeWindow() }]</span> <br />
						</span>
					[{/foreach}]

				[{/if}]

			[{/if}]

			<br />
	[{/foreach}]
</p>
[{/if}]
[{* oxtiramizoo BEGIN *}]
