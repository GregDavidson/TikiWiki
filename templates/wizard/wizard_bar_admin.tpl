{* $Id: wizard_bar_admin.tpl 60027 2016-10-21 12:31:15Z xavidp $ *}
<div class="row form-group">
	{if $prefs.feature_bidi eq 'y'}
		<div dir="rtl">
			<div class="col-sm-9">
			{if !isset($showOnLoginDisplayed) or $showOnLoginDisplayed neq 'y'}
				<input type="checkbox" name="showOnLogin" {if isset($showOnLogin) AND $showOnLogin eq true}checked="checked"{/if} /> {tr}Show on admin login{/tr}
				{assign var="showOnLoginDisplayed" value="y" scope="root"}
			{else}
				&nbsp;
			{/if}
			</div>
	{else}
			<div class="col-sm-9">
			</div>
			<div class="col-sm-3">
			{if !isset($showOnLoginDisplayed) or $showOnLoginDisplayed neq 'y'}
					<input type="checkbox" name="showOnLogin" {if isset($showOnLogin) AND $showOnLogin eq true}checked="checked"{/if} /> {tr}Show on admin login{/tr}
					{assign var="showOnLoginDisplayed" value="y" scope="root"}
				{else}
					&nbsp;
			{/if}
			</div>
	{/if}
	{if $prefs.connect_feature eq "y"}
		{if !isset($provideFeedback) or $provideFeedback neq 'y'}
			{capture name=likeicon}{icon name="thumbs-up"}{/capture}
			<label>
				<input type="checkbox" id="connect_feedback_cbx" {if !empty($connect_feedback_showing)}checked="checked"{/if}>
				{tr}Provide Feedback{/tr}
				<a href="http://doc.tiki.org/Connect" target="tikihelp" class="tikihelp" title="{tr}Provide Feedback:{/tr}
					{tr}Once selected, some icon/s will be shown next to all features so that you can provide some on-site feedback about them{/tr}.
					<br/><br/>
					<ul>
						<li>{tr}Icon for 'Like'{/tr} {$smarty.capture.likeicon|escape}</li>
<!--					<li>{tr}Icon for 'Fix me'{/tr} <img src=img/icons/connect_fix.png></li> -->
<!--					<li>{tr}Icon for 'What is this for?'{/tr} <img src=img/icons/connect_wtf.png></li> -->
					</ul>
					<br/>
					{tr}Your votes will be sent when you connect with mother.tiki.org (currently only by clicking the 'Connect > <strong>Send Info</strong>' button){/tr}
					<br/><br/>
					{tr}Click to read more{/tr}
				">
					{icon name='help'}
				</a>
			</label>
			{$headerlib->add_jsfile("lib/jquery_tiki/tiki-connect.js")}

			{assign var="provideFeedback" value="y" scope="root"}
		{else}
			&nbsp;
		{/if}
	{/if}
	{if $prefs.feature_bidi eq 'y'}
		</div>
	{/if}
</div>

<div class="row form-group">
{if $prefs.feature_bidi eq 'y'}
<div dir="rtl">
	<div class="col-sm-3">
{else}
	<div class="col-sm-9">
	</div>
	<div class="col-sm-3">
{/if}
		<input type="hidden" name="url" value="{$homepageUrl}">
		<input type="hidden" name="wizard_step" value="{$wizard_step}">
		{if isset($useDefaultPrefs)}
			<input type="hidden" name="use-default-prefs" value="{$useDefaultPrefs}">
		{/if}
		{if isset($useUpgradeWizard)}
			<input type="hidden" name="use-upgrade-wizard" value="{$useUpgradeWizard}">
		{/if}
		{if $prefs.feature_bidi neq 'y'}
			{if !isset($firstWizardPage)}<input type="submit" class="btn btn-default btn-sm" name="back" value="{tr}Back{/tr}" />{/if}
		{/if}&nbsp;
		<input type="submit" class="btn btn-primary btn-sm" name="{if isset($firstWizardPage)}use-default-prefs{else}continue{/if}" value="{if isset($lastWizardPage)}{tr}Finish{/tr}{elseif isset($firstWizardPage)}{tr}Start{/tr}{else}{if $isEditable eq true}{tr}Save and Continue{/tr}{else}{tr}Next{/tr}{/if}{/if}" />
		<input type="submit" class="btn btn-warning btn-sm" name="close" value="{tr}Close{/tr}" />
		{if $prefs.feature_bidi eq 'y'}
			{if !isset($firstWizardPage)}<input type="submit" class="btn btn-default btn-sm" name="back" value="{tr}Back{/tr}" />{/if}
		{/if}&nbsp;
	</div>
	<div class="col-sm-9 text-center">
		{if !isset($showWizardPageTitle) or $showWizardPageTitle neq 'y'}
			<h1 class="adminWizardPageTitle">{$pageTitle}</h1>
			{assign var="showWizardPageTitle" value="y" scope="root"}
		{/if}
	</div>
{if $prefs.feature_bidi eq 'y'}
</div>
{/if}
</div>
