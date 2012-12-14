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
                    <option value="1" [{if ($edit->oxarticles__tiramizoo_enable->value == 1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_yes_value" }]</option>
                    <option value="-1" [{if ($edit->oxarticles__tiramizoo_enable->value == -1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_no_value" }]</option>
                </select>
                
                [{ oxinputhelp ident="oxTiramizoo_article_tab_enable_tiramizoo_help" }]

                [{if (($inheritedData.tiramizoo_enable) && ($edit->oxarticles__tiramizoo_enable->value != -1 )) }]
                  <span style="color:green;">[{ oxmultilang ident="oxTiramizoo_article_tab_article_is_enabled" }]</span>
                  [{else}]
                  <div>
                  <span style="color:red;">[{ oxmultilang ident="oxTiramizoo_article_tab_article_is_disabled" }]</span>

                    [{if ($disabledCategory) }]
                      <span style="color:red;">[{ oxmultilang ident="oxTiramizoo_article_tab_disabled_by_category_1"  }] [{$disabledCategory->oxcategories__oxtitle->value }] [{ oxmultilang  ident="oxTiramizoo_article_tab_disabled_by_category_2"  }] </span>
                    [{/if}]
                  </div>
                [{/if}]

                


            </td>
          </tr>

          <tr>          
            <td class="edittext"></td>
            <td class="edittext">
              <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'" ><br>
            </td>
          </tr>





          <tr>          
            <td class="edittext">[{ oxmultilang ident="oxTiramizoo_article_tab_article_effective_label" }]</td>
            <td class="edittext">
              <p>

              [{oxmultilang ident="oxTiramizoo_article_tab_weight_label"}]: [{$effectiveData->weight}] [{oxmultilang ident="oxTiramizoo_article_tab_weight_unit"}].<br />
              [{oxmultilang ident="oxTiramizoo_article_tab_width_label"}]: [{$effectiveData->width}] [{oxmultilang ident="oxTiramizoo_article_tab_dimensions_unit"}].<br />
              [{oxmultilang ident="oxTiramizoo_article_tab_height_unit"}]: [{$effectiveData->height}] [{oxmultilang ident="oxTiramizoo_article_tab_dimensions_unit"}].<br />
              [{oxmultilang ident="oxTiramizoo_article_tab_length_unit"}]: [{$effectiveData->length}] [{oxmultilang ident="oxTiramizoo_article_tab_dimensions_unit"}].<br />
              </p>

                [{if ($warningDimensions)}]
                  <span style="color:red;">[{oxmultilang ident="oxTiramizoo_article_tab_effective_values_warning"}]</span>
                [{/if}]


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
