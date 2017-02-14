{* $Id: mod-mobile.tpl 40642 2012-04-02 13:49:16Z robertplummer $ *}
{tikimodule error=$module_params.error title=$tpl_module_title name=$tpl_module_name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	{if $prefs.mobile_mode neq 'y'}
		{self_link _ajax='n' _onclick="setCookie('mobile_mode','y', '', 'session');return true;"}{$module_params.to_label}{/self_link}
	{elseif empty($mobile_params.switch_perspective) or $mobile_params.switch_perspective eq "y"}
		{if empty($mobile_params.stay_on_same_page)}{assign var=stay_on_same_page value="1"}{else}{assign var=stay_on_same_page value="0"}{/if}
		<a href="tiki-switch_perspective.php?mobile_mode=n&perspective=0&back={$stay_on_same_page}" rel="external" onclick="setCookie('mobile_mode','n', '', 'session');return true;">
			{$module_params.from_label}
		</a>
	{else}
		<a href="tiki-switch_theme.php?mobile_mode=n&theme=" rel="external">
			{$module_params.from_label}
		</a>
	{/if}
{/tikimodule}
