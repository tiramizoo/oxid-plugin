[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function ThisDate( sID)
{
    document.myedit['editval[oxorder__oxpaid]'].value=sID;
}
//-->
</script>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{assign var="oCurr" value=$edit->getOrderCurrency() }]

<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="cur" value="[{ $oCurr->id }]">
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="order_main">
</form>



<form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post">
[{ $shop->hiddensid }]
<input type="hidden" name="cur" value="[{ $oCurr->id }]">
<input type="hidden" name="cl" value="oxtiramizoo_order_tab">
<input type="hidden" name="fnc" value="save">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="editval[oxorder__oxid]" value="[{ $oxid }]">

<table cellspacing="0" cellpadding="0" border="0">
<tr>
    <td class="edittext">[{ oxmultilang ident="oxTiramizoo_order_tab_status_label" }]</td>
    <td class="edittext">[{$oxTiramizooOrderExtended->oxorder__tiramizoo_status->value}] [{ oxinputhelp ident="oxTiramizoo_order_tab_status_help" }]</td>
</tr>

<tr>
    <td class="edittext">[{ oxmultilang ident="oxTiramizoo_order_tab_tracking_url_label" }]</td>
    <td class="edittext">[{$oxTiramizooOrderExtended->getTrackingUrl() }] [{ oxinputhelp ident="oxTiramizoo_order_tab_tracking_url_help" }]</td>
</tr>

<tr>
    <td class="edittext">[{ oxmultilang ident="oxTiramizoo_order_tab_external_id_label" }]</td>
    <td class="edittext">[{$oxTiramizooOrderExtended->oxorder__tiramizoo_external_id->value}] [{ oxinputhelp ident="oxTiramizoo_order_tab_external_id_help" }]</td>
</tr>

<tr>
    <td class="edittext">[{ oxmultilang ident="oxTiramizoo_order_tab_request_label" }]</td>
    <td class="edittext">
        <textarea style="width:600px; height:180px; border:1px solid #AAA;" readonly="true">
        [{$aTiramizooRequest|@print_r}]
        </textarea>
        [{ oxinputhelp ident="oxTiramizoo_order_tab_request_help" }]
    </td>
</tr>

<tr>
    <td class="edittext">[{ oxmultilang ident="oxTiramizoo_order_tab_response_label" }]</td>
    <td class="edittext">
        <textarea style="width:600px; height:180px; border:1px solid #AAA;" readonly="true">
        [{$aTiramizooResponse|@print_r}]
        </textarea>
        [{ oxinputhelp ident="oxTiramizoo_order_tab_response_help" }]
    </td>
</tr>


<tr>
    <td class="edittext">[{ oxmultilang ident="oxTiramizoo_order_tab_webhook_response_label" }]</td>
    <td class="edittext">
        <textarea style="width:600px; height:180px; border:1px solid #AAA;" readonly="true">
        [{$aTiramizooWebhookResponse|@print_r}]
        </textarea>
        [{ oxinputhelp ident="oxTiramizoo_order_tab_webhook_response_help" }]
    </td>
</tr>



</table>
</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
