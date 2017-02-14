{* $Id: tiki-wizard_admin.tpl 60439 2016-11-25 11:27:14Z jonnybradley $ *}
{extends "layout_plain.tpl"}

{block name="title"}
	{* {title}{tr}Configuration Wizard{/tr}{/title} *}
{/block}

{block name="content"}
	<form action="tiki-wizard_admin.php" method="post">
	<div class="col-sm-12">
		{include file="wizard/wizard_bar_admin.tpl"}
	</div>
	<div id="wizardBody">
	<div class="row">
		{if !empty($wizard_toc)}
			<div class="col-sm-4">
				<h3 class="adminWizardTOCTitle">{if $useDefaultPrefs}{tr}Profiles Wizard{/tr}{elseif $useUpgradeWizard}{tr}Upgrade Wizard{/tr}{else}{tr}Configuration Wizard{/tr}{/if} - {tr}steps{/tr}:</h3>
				<ol>
					{$wizard_toc}
				</ol>
			</div>
		{/if}
		<div class="{if !empty($wizard_toc)}col-sm-8{else}col-sm-12{/if}">
			{$wizardBody}
		</div>
	</div>
	</div>
	{include file="wizard/wizard_bar_admin.tpl"}
	</form>
{/block}
