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



        <form name="myedit" id="myedit" action="[{ $shop->selflink }]" method="post" onSubmit="copyLongDesc( 'oxcategories__oxlongdesc' );" style="padding: 0px;margin: 0px;height:0px;">
        [{ $shop->hiddensid }]
        <input type="hidden" name="cl" value="oxtiramizoo_category_tab">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="voxid" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxcategories__oxid]" value="[{ $oxid }]">
        <input type="hidden" name="catlang" value="[{$catlang}]">
        <input type="hidden" name="editval[oxcategories__oxlongdesc]" value="">
        <table cellspacing="0" cellpadding="0" border="0">
          
          <tr>
            <td class="edittext">
              Enable Tiramizoo
            </td>
            <td class="edittext">
                <select name="editval[oxcategories__tiramizoo_enable]">
                    <option value="0" [{if ($edit->oxcategories__tiramizoo_enable->value == 0)}]selected="selected"[{/if}]>Inherit from parent settings</option>
                    <option value="1" [{if ($edit->oxcategories__tiramizoo_enable->value == 1)}]selected="selected"[{/if}]>Yes</option>
                    <option value="-1" [{if ($edit->oxcategories__tiramizoo_enable->value == -1)}]selected="selected"[{/if}]>No</option>
                </select>
            </td>
          </tr>

          <tr>
            <td class="edittext">
              Weight
            </td>
            <td class="edittext">
              <input type="text" class="editinput" size="10" maxlength="10" name="editval[oxcategories__tiramizoo_weight]" value="[{$edit->oxcategories__tiramizoo_weight->value}]">kg
            </td>
          </tr>
          <tr>
            <td class="edittext">
              Dimensions
            </td>
            <td class="edittext">
              L:&nbsp;<input type="text" class="editinput" size="3" maxlength="10" name="editval[oxcategories__tiramizoo_length]" value="[{$edit->oxcategories__tiramizoo_length->value}]">cm
              W:&nbsp;<input type="text" class="editinput" size="3" maxlength="" name="editval[oxcategories__tiramizoo_width]" value="[{$edit->oxcategories__tiramizoo_width->value}]">cm
              H:&nbsp;<input type="text" class="editinput" size="3" maxlength="" name="editval[oxcategories__tiramizoo_height]" value="[{$edit->oxcategories__tiramizoo_height->value}]">cm
              
            </td>
          </tr>

        <tr>
          <td>
                <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="CATEGORY_TEXT_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'">
          </td>
        </tr>
        </form>
      </table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
