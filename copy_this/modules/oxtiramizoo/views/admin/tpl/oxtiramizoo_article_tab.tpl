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

    [{assign var="aEffectiveData" value=$oView->getEffectiveData() }]
    [{assign var="oTiramizooArticleExtended" value=$oView->getTiramizooArticleExtended() }]

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
                            <select name="oxTiramizooArticleExtended[oxtiramizooarticleextended__tiramizoo_enable]">
                                <option value="0" [{if ($oTiramizooArticleExtended->oxtiramizooarticleextended__tiramizoo_enable->value == 0)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_inherit_value" }]</option>
                                <option value="1" [{if ($oTiramizooArticleExtended->oxtiramizooarticleextended__tiramizoo_enable->value == 1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_yes_value" }]</option>
                                <option value="-1" [{if ($oTiramizooArticleExtended->oxtiramizooarticleextended__tiramizoo_enable->value == -1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_no_value" }]</option>
                            </select>

                            [{if ( !$oTiramizooArticleExtended->oxtiramizooarticleextended__tiramizoo_enable->value) }]
                            [{if ($aEffectiveData.tiramizoo_enable) }]
                                <span style="color:green;">[{ oxmultilang ident="oxTiramizoo_article_tab_article_is_enabled" }]
                            [{else}]
                                <span style="color:red;">[{ oxmultilang ident="oxTiramizoo_article_tab_article_is_disabled"  }]
                            [{/if}]

                            [{if ($aEffectiveData.tiramizoo_enable_inherited_from == 'category') }]
                                 ([{ oxmultilang ident="oxTiramizoo_category_label"  }]: [{ $aEffectiveData.tiramizoo_enable_inherited_from_category_title }])
                            [{elseif ($aEffectiveData.tiramizoo_enable_inherited_from == 'global') }]
                                ([{ oxmultilang ident="oxTiramizoo_tiramizoo_settings_path" }])
                            [{/if}]
                                </span>
                            [{/if}]

                            [{ oxinputhelp ident="oxTiramizoo_article_tab_enable_tiramizoo_help" }]
                        </td>
                    </tr>

                    <tr>
                        <td class="edittext">
                            [{ oxmultilang ident="oxTiramizoo_article_tab_use_package_label" }]
                        </td>
                        <td class="edittext">
                            <select name="oxTiramizooArticleExtended[oxtiramizooarticleextended__tiramizoo_use_package]">
                                <option value="0" [{if ($oTiramizooArticleExtended->oxtiramizooarticleextended__tiramizoo_use_package->value == 0)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_inherit_value" }]</option>
                                <option value="1" [{if ($oTiramizooArticleExtended->oxtiramizooarticleextended__tiramizoo_use_package->value == 1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_yes_value" }]</option>
                                <option value="-1" [{if ($oTiramizooArticleExtended->oxtiramizooarticleextended__tiramizoo_use_package->value == -1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_no_value" }]</option>
                            </select>

                            [{if ( !$oTiramizooArticleExtended->oxtiramizooarticleextended__tiramizoo_use_package->value) }]
                            [{if ($aEffectiveData.tiramizoo_use_package) }]
                                <span>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_no_value" }]
                            [{else}]
                                <span>[{ oxmultilang ident="oxTiramizoo_article_tab_enable_yes_value"  }]
                            [{/if}]

                            [{if ($aEffectiveData.tiramizoo_use_package_inherited_from == 'category') }]
                                 ([{ oxmultilang ident="oxTiramizoo_category_label"  }]: [{ $aEffectiveData.tiramizoo_use_package_inherited_from_category_title }])
                            [{elseif ($aEffectiveData.tiramizoo_use_package_inherited_from == 'global') }]
                                ([{ oxmultilang ident="oxTiramizoo_tiramizoo_settings_path" }])
                            [{/if}]
                                </span>
                            [{/if}]

                            [{ oxinputhelp ident="oxTiramizoo_article_tab_use_package_help" }]
                        </td>
                    </tr>

                    <tr>
                        <td class="edittext"></td>
                        <td class="edittext">
                            <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'" ><br>
                        </td>
                    </tr>

                    <tr>
                        <td class="edittext">[{ oxmultilang ident="oxTiramizoo_article_tab_article_effective_label" }]
                            [{if ($aEffectiveData.tiramizoo_dimensions_inherited_from == 'category') }]
                            <br />([{ oxmultilang ident="oxTiramizoo_category_label"  }]: [{ $aEffectiveData.tiramizoo_enable_inherited_from_category_title }])
                            [{elseif ($aEffectiveData.tiramizoo_dimensions_inherited_from == 'global') }]
                            <br />([{ oxmultilang ident="oxTiramizoo_tiramizoo_settings_path" }])
                            [{/if}]
                        </td>
                        <td class="edittext">
                            <p>
                                [{oxmultilang ident="oxTiramizoo_article_tab_weight_label"}]: [{$aEffectiveData.weight}] [{oxmultilang ident="oxTiramizoo_article_tab_weight_unit"}].<br />
                                [{oxmultilang ident="oxTiramizoo_article_tab_width_label"}]: [{$aEffectiveData.width}] [{oxmultilang ident="oxTiramizoo_article_tab_dimensions_unit"}].<br />
                                [{oxmultilang ident="oxTiramizoo_article_tab_height_unit"}]: [{$aEffectiveData.height}] [{oxmultilang ident="oxTiramizoo_article_tab_dimensions_unit"}].<br />
                                [{oxmultilang ident="oxTiramizoo_article_tab_length_unit"}]: [{$aEffectiveData.length}] [{oxmultilang ident="oxTiramizoo_article_tab_dimensions_unit"}].<br />
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
