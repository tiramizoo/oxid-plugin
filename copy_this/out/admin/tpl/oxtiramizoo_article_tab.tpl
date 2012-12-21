[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]


<script type="text/javascript">
<!--
window.onload = function ()
{
    [{ if $updatelist == 1}]
        top.oxid.admin.updateList('[{ $oxid }]');
    [{ /if}]
    top.reloadEditFrame();
}
function editThis( sID )
{
    var oTransfer = top.basefrm.edit.document.getElementById( "transfer" );
    oTransfer.oxid.value = sID;
    oTransfer.cl.value = top.basefrm.list.sDefClass;

    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();

    var oSearch = top.basefrm.list.document.getElementById( "search" );
    oSearch.oxid.value = sID;
    oSearch.actedit.value = 0;
    oSearch.submit();
}
//-->
</script>

<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="article_extend">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<form name="myedit" id="myedit" action="[{ $shop->selflink }]"  method="post">

[{ $shop->hiddensid }]
<input type="hidden" name="cl" value="oxtiramizoo_article_tab">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{ $oxid }]">
<input type="hidden" name="voxid" value="[{ $oxid }]">
<input type="hidden" name="oxparentid" value="[{ $oxparentid }]">
<input type="hidden" name="editval[article__oxid]" value="[{ $oxid }]">



  <table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
    <tr height="10">
      <td></td><td></td>
    </tr>
    <tr>
      <td width="15"></td>
      <td valign="top" class="edittext">
        <table cellspacing="0" cellpadding="0" border="0">

          <tr>
            <td class="edittext">
              [{ oxmultilang ident="oxTiramizoo_article_tab_enable_tiramizoo_label" }]
            </td>
            <td class="edittext">
                <select name="editval[oxarticles__tiramizoo_enable]">
                    <option value="0" [{if ($edit->oxarticles__tiramizoo_enable->value == 0)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_inherit_value" }]</option>
                    <option value="1" [{if ($edit->oxarticles__tiramizoo_enable->value == 1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_yes_value" }]</option>
                    <option value="-1" [{if ($edit->oxarticles__tiramizoo_enable->value == -1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_no_value" }]</option>
                </select>

                [{ oxinputhelp ident="oxTiramizoo_article_tab_enable_tiramizoo_help" }]
            </td>
          </tr>


          <tr>
            <td class="edittext">
              [{ oxmultilang ident="oxTiramizoo_article_tab_use_package_label" }]
            </td>
            <td class="edittext">
                <input type="hidden" name="editval[oxarticles__tiramizoo_use_package]" value="1" />
                <input type="checkbox" name="editval[oxarticles__tiramizoo_use_package]" value="0" [{if ($edit->oxarticles__tiramizoo_use_package->value == 0)}]checked="checked"[{/if}] />
                [{ oxmultilang ident="oxTiramizoo_article_tab_use_package_value" }]
                [{ oxinputhelp ident="oxTiramizoo_article_tab_use_package_help" }]
            </td>
          </tr>


          <tr>          
            <td class="edittext"></td>
            <td class="edittext">
              <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'" ><br>
            </td>
          </tr>
          </table>
      </td>
      <!-- Ende rechte Seite -->
    </tr>
  </table>


</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
