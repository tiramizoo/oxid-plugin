[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function loadLang(obj)
{
    var langvar = document.getElementById("catlang");
    if (langvar != null )
        langvar.value = obj.value;
    document.myedit.submit();
}
//-->
</script>


<form name="transfer" id="transfer" action="[{ $shop->selflink }]" method="post">
    [{ $shop->hiddensid }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="category_text">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>



        <form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post" onSubmit="copyLongDesc( 'oxtiramizoocategoryextended__oxlongdesc' );" style="padding: 0px;margin: 0px;height:0px;">
        [{ $shop->hiddensid }]
        <input type="hidden" name="cl" value="oxtiramizoo_category_tab">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="voxid" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxtiramizoocategoryextended__oxid]" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxtiramizoocategoryextended__oxlongdesc]" value="">
        <table cellspacing="0" cellpadding="0" border="0">

          <tr>
            <td class="edittext">
              [{ oxmultilang ident="oxTiramizoo_category_tab_enable_tiramizoo_label" }]
            </td>
            <td class="edittext">
                <select name="oxTiramizooCategoryExtended[oxtiramizoocategoryextended__tiramizoo_enable]">
                    <option value="1" [{if ($oxTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_enable->value == 1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_category_tab_enable_yes_value" }]</option>
                    <option value="-1" [{if ($oxTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_enable->value == -1)}]selected="selected"[{/if}]>[{ oxmultilang ident="oxTiramizoo_category_tab_enable_no_value" }]</option>
                </select>
                [{ oxinputhelp ident="oxTiramizoo_category_tab_enable_tiramizoo_help" }]
            </td>
          </tr>

          <tr>
            <td class="edittext">
              [{ oxmultilang ident="oxTiramizoo_category_tab_use_package_label" }]
            </td>
            <td class="edittext">
                <input type="hidden" name="oxTiramizooCategoryExtended[oxtiramizoocategoryextended__tiramizoo_use_package]" value="1" />
                <input type="checkbox" name="oxTiramizooCategoryExtended[oxtiramizoocategoryextended__tiramizoo_use_package]" value="-1" [{if ($oxTiramizooCategoryExtended->getId() && $oxTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_use_package->value == -1)}]checked="checked"[{/if}] />
                [{ oxmultilang ident="oxTiramizoo_category_tab_use_package_value" }]
                [{ oxinputhelp ident="oxTiramizoo_category_tab_use_package_help" }]
            </td>
          </tr>

          <tr>
            <td class="edittext">
              [{ oxmultilang ident="oxTiramizoo_category_tab_weight_label" }]
            </td>
            <td class="edittext">
              <input type="text" class="editinput" size="10" maxlength="10" name="oxTiramizooCategoryExtended[oxtiramizoocategoryextended__tiramizoo_weight]" value="[{$oxTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_weight->value}]">[{ oxmultilang ident="oxTiramizoo_category_tab_weight_unit" }]
              [{ oxinputhelp ident="oxTiramizoo_category_tab_weight_help" }]
            </td>
          </tr>
          <tr>
            <td class="edittext">
              [{ oxmultilang ident="oxTiramizoo_category_tab_dimensions_label" }]
            </td>
            <td class="edittext">
              L:&nbsp;<input type="text" class="editinput" size="3" maxlength="10" name="oxTiramizooCategoryExtended[oxtiramizoocategoryextended__tiramizoo_length]" value="[{$oxTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_length->value}]">[{ oxmultilang ident="oxTiramizoo_category_tab_dimensions_unit" }]
              W:&nbsp;<input type="text" class="editinput" size="3" maxlength="" name="oxTiramizooCategoryExtended[oxtiramizoocategoryextended__tiramizoo_width]" value="[{$oxTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_width->value}]">[{ oxmultilang ident="oxTiramizoo_category_tab_dimensions_unit" }]
              H:&nbsp;<input type="text" class="editinput" size="3" maxlength="" name="oxTiramizooCategoryExtended[oxtiramizoocategoryextended__tiramizoo_height]" value="[{$oxTiramizooCategoryExtended->oxtiramizoocategoryextended__tiramizoo_height->value}]">[{ oxmultilang ident="oxTiramizoo_category_tab_dimensions_unit" }]

              [{ oxinputhelp ident="oxTiramizoo_category_tab_dimensions_help" }]

            </td>
          </tr>

        <tr>
          <td>
                <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'">
          </td>
        </tr>
        </form>
      </table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
