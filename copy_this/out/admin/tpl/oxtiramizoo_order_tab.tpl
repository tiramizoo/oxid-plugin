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
    <td class="edittext">Tiramizoo status</td>
    <td class="edittext">[{$edit->oxorder__tiramizoo_status->value}]</td>
</tr>

<tr>
    <td class="edittext">Tiramizoo tracking url</td>
    <td class="edittext">[{$edit->oxorder__tiramizoo_tracking_url->value}]</td>
</tr>

<tr>
    <td class="edittext">Tiramizoo external_id</td>
    <td class="edittext">[{$edit->oxorder__tiramizoo_external_id->value}]</td>
</tr>

<tr>
    <td class="edittext">Tiramizoo response params</td>
    <td class="edittext">
        <textarea style="width:400px; height:180px; border:1px solid #AAA;" readonly="true">
        [{$aTiramizooParams|@print_r}]
        </textarea>
    </td>
</tr>

</table>
</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
