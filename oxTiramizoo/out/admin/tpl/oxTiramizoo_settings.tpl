[{include file="headitem.tpl" title="PAYENGINE_SETUP_TITLE"|oxmultilangassign}]


[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{cycle assign="_clear_" values=",2" }]
 
<div id=liste>

  [{if $mo_payen__sqlExecutionErrors}]
    <strong style="color: red">[{oxmultilang ident="MO_PAYENGINE__SQL_INSTALL_ERROR_HEADER"}]</strong>
    <pre style="border: 2px solid red;">[{$mo_payen__sqlExecutionErrors}]</pre>
  [{/if}]
  
  <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
  [{$oViewConf->getHiddenSid()}]
  <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
  <input type="hidden" name="fnc" value="save">
  <input type="hidden" name="oxid" value="[{$oxid}]">
  <input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">
  <table cellspacing="0" cellpadding="0" border="0" style="width:100%;height:100%;">

    <tr>
      <td valign="top" class="edittext" style="padding:10px;">
       <table cellspacing="0" cellpadding="5" border="0" class="edittext" style="text-align: left;">

          
          <tr>
            <td valign="top" class="edittext" width="250" nowrap="">[{ oxmultilang ident="PAYENGINE_PSPID" }]</td>
            <td valign="top" class="edittext">
              <input type=text class="editinput" style="width:410px" name=confstrs[payengine_sPSPID] value="[{$confstrs.payengine_sPSPID}]" maxlength="30" />
            	[{ oxinputhelp ident="HELP_PAYENGINE_PSPID" }]
            </td>
          </tr>
          
          <tr>
            <td valign="top" class="edittext" width="250" nowrap="">[{ oxmultilang ident="MO_PAYENGINE__API_USERID" }]</td>
            <td valign="top" class="edittext">
              <input type=text class="editinput" style="width:410px" name=confstrs[mo_payen__api_userid] value="[{$confstrs.mo_payen__api_userid}]" maxlength="30" />
            	[{ oxinputhelp ident="MO_PAYENGINE__API_USERID_HELP" }]
            </td>
          </tr>
          <tr>
            <td valign="top" class="edittext" width="250" nowrap="">[{ oxmultilang ident="MO_PAYENGINE__API_USERPASS" }]</td>
            <td valign="top" class="edittext">
              <input type="password" class="editinput" style="width:410px" name="confstrs[mo_payen__api_userpass]" value="[{$confstrs.mo_payen__api_userpass}]" maxlength="30" />
            	[{ oxinputhelp ident="MO_PAYENGINE__API_USERPASS_HELP" }]
            </td>
          </tr>
          
          <tr>
            <td valign="top" class="edittext" width="250" nowrap="">[{ oxmultilang ident="PAYENGINE_HASHING" }]</td>
            <td valign="top" class="edittext">
							<select name="confstrs[payengine_sHashingAlgorithm]" class="editinput" [{ $readonly }]>
                <option value="SHA-1" [{ if $confstrs.payengine_sHashingAlgorithm == "SHA-1" }]SELECTED[{/if}]>[{ oxmultilang ident="PAYENGINE_HASHING_ALGORITHM_SHA_1" }]</option>
                <option value="SHA-256" [{ if $confstrs.payengine_sHashingAlgorithm == "SHA-256" || $confstrs.payengine_sHashingAlgorithm == "" }]SELECTED[{/if}]>[{ oxmultilang ident="PAYENGINE_HASHING_ALGORITHM_SHA_256" }]</option>
                <option value="SHA-512" [{ if $confstrs.payengine_sHashingAlgorithm == "SHA-512" }]SELECTED[{/if}]>[{ oxmultilang ident="PAYENGINE_HASHING_ALGORITHM_SHA_512" }]</option>
	            </select>
            </td>
          </tr>
          
          <tr>
            <td valign="top" class="edittext" width="250" nowrap="">[{ oxmultilang ident="PAYENGINE_SECURE_KEY_IN" }]</td>
            <td valign="top" class="edittext">
              <input type=password class="editinput" style="width:410px;" name=confstrs[payengine_sSecureKeyIn] value="[{$confstrs.payengine_sSecureKeyIn}]"><br />
            </td>
          </tr>
          <tr>
            <td valign="top" class="edittext" width="250" nowrap="">[{ oxmultilang ident="PAYENGINE_SECURE_KEY_OUT" }]</td>
            <td valign="top" class="edittext">
              <input type=password class="editinput" style="width:410px;" name=confstrs[payengine_sSecureKeyOut] value="[{$confstrs.payengine_sSecureKeyOut}]"><br />
            </td>
          </tr>
          <tr>
            <td valign="top" class="edittext" width="250" nowrap="">[{ oxmultilang ident="MO_PAYENGINE__GATEWAY_URLS" }]</td>
            <td valign="top" class="edittext">
              <fieldset>
                <input type=text 
                       class="editinput" 
                       style="width:410px" 
                       name="confstrs[mo_payen__gateway_url_redirect]" 
                       value="[{ if $confstrs.mo_payen__gateway_url_redirect != "" }][{$confstrs.mo_payen__gateway_url_redirect}][{else}][{$strPaymentServer}][{/if}]"
                       > [{oxmultilang ident="MO_PAYENGINE__GATEWAY_URL_REDIRECT"}]<br />
                <input type=text 
                       class="editinput" 
                       style="width:410px" 
                       name="confstrs[mo_payen__gateway_url_alias]" 
                       value="[{if $confstrs.mo_payen__gateway_url_alias}][{$confstrs.mo_payen__gateway_url_alias}][{else}][{$mo_payen__gateway_url_alias}][{/if}]"
                       > [{oxmultilang ident="MO_PAYENGINE__GATEWAY_URL_ALIAS"}]<br />
                <input type=text 
                       class="editinput" 
                       style="width:410px" 
                       name="confstrs[mo_payen__gateway_url_orderdir]" 
                       value="[{if $confstrs.mo_payen__gateway_url_orderdir}][{$confstrs.mo_payen__gateway_url_orderdir}][{else}][{$mo_payen__gateway_url_orderdir}][{/if}]"
                       > [{oxmultilang ident="MO_PAYENGINE__GATEWAY_URL_ORDERDIRECT"}]<br />
              </fieldset>
            </td>
          </tr>
          <!--
          <tr>
            <td valign="top" class="edittext" width="250" nowrap="">[{ oxmultilang ident="PAYENGINE_ALIAS" }]</td>
            <td valign="top" class="edittext" nowrap="">
              <fieldset id="payenginetemplate">
                <legend>
                  <input type=hidden name=confbools[payengine_blAlias] value=false>
                  <input type="checkbox" name=confbools[payengine_blAlias] value="true"[{ if $confbools.payengine_blAlias == true }] checked=""[{/if}] onchange="Javascript:templateAlias(this);" [{ $readonly }]>
                </legend>
                [{ oxmultilang ident="PAYENGINE_ALIAS_USAGE" }]<br />
                <span id="alias_usage" style="display:inline;">
                [{foreach from=$languages item=lang}]
                [{ $lang->name }] <input type=text class="editinput" style="width:410px;" name=confstrs[payengine_sAliasUsage[{ $lang->id }]] value="[{ $lang->aliasUsage }]"><br />
                [{/foreach}]
                </span>
              </fieldset>
            </td>
          </tr>
          -->
          <tr>
            <td valign="top" class="edittext" width="250" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE" }]</td>
            <td valign="top" class="edittext">
	            <fieldset id="payenginetemplate">
	            	<legend>
									<select name=confstrs[payengine_sTemplate] class="editinput" onchange="Javascript:templateOptions(this);" [{ $readonly }]>
		                <option value="true" [{ if $confstrs.payengine_sTemplate == "true" }]SELECTED[{/if}]>[{ oxmultilang ident="PAYENGINE_TEMPLATE_TRUE" }]</option>
		                <option value="false" [{ if $confstrs.payengine_sTemplate == "false" }]SELECTED[{/if}]>[{ oxmultilang ident="PAYENGINE_TEMPLATE_FALSE" }]</option>
			            </select>
	            	</legend>
								<table id="template_styling" cellspacing="0" cellpadding="5" border="0" class="edittext" style="text-align: left;">
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_PMLISTSTYLE_TITLE" }]</td>
		                <td valign="top" class="edittext" nowrap="">
											<select name="confstrs[payengine_sTplPMListStyle]" class="editinput" [{ $readonly }]>
				                <option value="0" [{ if $confstrs.payengine_sTplPMListStyle == 0 }]SELECTED[{/if}]>[{ oxmultilang ident="PAYENGINE_PMLISTSTYLE_0" }]</option>
				                <option value="1" [{ if $confstrs.payengine_sTplPMListStyle == 1 }]SELECTED[{/if}]>[{ oxmultilang ident="PAYENGINE_PMLISTSTYLE_1" }]</option>
				                <option value="2" [{ if $confstrs.payengine_sTplPMListStyle == 2 }]SELECTED[{/if}]>[{ oxmultilang ident="PAYENGINE_PMLISTSTYLE_2" }]</option>
					            </select>
											[{ oxinputhelp ident="PAYENGINE_PMLISTSTYLE_TITLE_DESCRIPTION" }]
		                </td>
		              </tr>
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_BACK_TITLE" }]</td>
		                <td valign="top" class="edittext" nowrap="">
				              <input type=hidden name=confbools[payengine_blBackButton] value=false>
											<input type="checkbox" name=confbools[payengine_blBackButton] value="true"[{ if $confbools.payengine_blBackButton == 'true' }] checked[{/if}] [{ $readonly }]>
		                </td>
		              </tr>
				      	</table>
				      	
								<table id="template_dynamic" cellspacing="0" cellpadding="5" border="0" class="edittext" style="text-align: left;">
				      	</table>
				      	
								<table id="template_static" cellspacing="0" cellpadding="5" border="0" class="edittext" style="text-align: left;">
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE_TITLE" }]</td>
		                <td valign="top" class="edittext" nowrap="">
											<input type="checkbox" name=confbools[payengine_blTplTitle] value="true"[{ if $confbools.payengine_blTplTitle == 'true' }] checked[{/if}] onchange="Javascript:templateTitle(this);" [{ $readonly }]>
											[{ oxinputhelp ident="PAYENGINE_TEMPLATE_TITLE_DESCRIPTION" }]
											<br />
											<span id="template_title">
				        			[{foreach from=$languages item=lang}]
	              			[{ $lang->name }] <input type=text class="editinput" style="width:410px;" name=confstrs[payengine_sTplTitle[{ $lang->id }]] value="[{ $lang->tplTitle }]"><br />
				        			[{/foreach}]
											</span>
		                </td>
		              </tr>
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE_BGCOLOR" }]</td>
		                <td valign="top" class="edittext" nowrap="">
		                  <input type=text class="editinput" style="width:250px;" name=confstrs[payengine_sTplBGColor] value="[{$confstrs.payengine_sTplBGColor}]">
											[{ oxinputhelp ident="PAYENGINE_TEMPLATE_BGCOLOR_DESCRIPTION" }]
		                </td>
		              </tr>
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE_FONTCOLOR" }]</td>
		                <td valign="top" class="edittext" nowrap="">
		                  <input type=text class="editinput" style="width:250px;" name=confstrs[payengine_sTplFontColor] value="[{$confstrs.payengine_sTplFontColor}]">
											[{ oxinputhelp ident="PAYENGINE_TEMPLATE_FONTCOLOR_DESCRIPTION" }]
		                </td>
		              </tr>
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE_TABLE_BGCOLOR" }]</td>
		                <td valign="top" class="edittext" nowrap="">
		                  <input type=text class="editinput" style="width:250px;" name=confstrs[payengine_sTplTableBGColor] value="[{$confstrs.payengine_sTplTableBGColor}]">
											[{ oxinputhelp ident="PAYENGINE_TEMPLATE_TABLE_BGCOLOR_DESCRIPTION" }]
		                </td>
		              </tr>
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE_TABLE_FONTCOLOR" }]</td>
		                <td valign="top" class="edittext" nowrap="">
		                  <input type=text class="editinput" style="width:250px;" name=confstrs[payengine_sTplTbFontColor] value="[{$confstrs.payengine_sTplTbFontColor}]">
											[{ oxinputhelp ident="PAYENGINE_TEMPLATE_TABLE_FONTCOLOR_DESCRIPTION" }]
		                </td>
		              </tr>
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE_BUTTON_BGCOLOR" }]</td>
		                <td valign="top" class="edittext" nowrap="">
		                  <input type=text class="editinput" style="width:250px;" name=confstrs[payengine_sTplBtnBGColor] value="[{$confstrs.payengine_sTplBtnBGColor}]">
											[{ oxinputhelp ident="PAYENGINE_TEMPLATE_BUTTON_BGCOLOR_DESCRIPTION" }]
		                </td>
		              </tr>
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE_BUTTON_FONTCOLOR" }]</td>
		                <td valign="top" class="edittext" nowrap="">
		                  <input type=text class="editinput" style="width:250px;" name=confstrs[payengine_sTplBtnFontColor] value="[{$confstrs.payengine_sTplBtnFontColor}]">
											[{ oxinputhelp ident="PAYENGINE_TEMPLATE_BUTTON_FONTCOLOR_DESCRIPTION" }]
		                </td>
		              </tr>
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE_FONTFAMILY" }]</td>
		                <td valign="top" class="edittext" nowrap="">
		                  <input type=text class="editinput" style="width:250px;" name=confstrs[payengine_sTplFontFamily] value="[{$confstrs.payengine_sTplFontFamily}]">
											[{ oxinputhelp ident="PAYENGINE_TEMPLATE_FONTFAMILY_DESCRIPTION" }]
		                </td>
		              </tr>
		              <tr>
		                <td valign="top" class="edittext" nowrap="">[{ oxmultilang ident="PAYENGINE_TEMPLATE_LOGO" }]</td>
		                <td valign="top" class="edittext" nowrap="">
		                  <input type=text class="editinput" style="width:250px;" name=confstrs[payengine_sTplLogo] value="[{$confstrs.payengine_sTplLogo}]">
											[{ oxinputhelp ident="PAYENGINE_TEMPLATE_LOGO_DESCRIPTION" }]
		                </td>
		              </tr>
      					</table>
	            </fieldset>
							<br />
							[{ if $start_setup }]
                <input type="submit" name="save" value="[{ oxmultilang ident="START_SETUP" }]" [{ $readonly}]>
							[{else}]
                <input type="submit" name="save" value="[{ oxmultilang ident="UPDATE_SETUP" }]" [{ $readonly}]>
              
                [{if $oView->mo_payen__hasRegisteredTemplateBlocks()}]
                  <input type="button" value="[{ oxmultilang ident="MO_PAYENGINE__UNINSTALL_TPL_BLOCKS" }]" [{ $readonly}] onclick="window.location.href='[{$oViewConf->getSelfLink()}]cl=mo_payen__setup&fnc=mo_payen__fncUninstallTemplateBlocks';" />
                [{else}]
                  <input type="button" value="[{ oxmultilang ident="MO_PAYENGINE__INSTALL_TPL_BLOCKS" }]" [{ $readonly}] onclick="window.location.href='[{$oViewConf->getSelfLink()}]cl=mo_payen__setup&fnc=mo_payen__fncInstallTemplateBlocks';" />
                [{/if}]
              
							[{/if}]
            </td>
          </tr>
      	</table>
      </td>
    </tr>
  </table>
  </form>
  <div style="text-align:right; font-weight:bold;">PayEngine Module Version: [{$mo_payen__moduleVersion}]</div>
  [{include file="pagenavisnippet.tpl"}]
</div>
[{include file="pagetabsnippet.tpl"}]

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
