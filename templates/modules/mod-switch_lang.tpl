{* $Id: mod-switch_lang.tpl 60452 2016-11-29 09:30:37Z drsassafras $ *}
{strip}
	{tikimodule error=$module_params.error title=$tpl_module_title name="switch_lang" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{if $mode eq 'flags'}
			{section name=ix loop=$languages}
				{assign var='val' value=$languages[ix].value|escape}
				{assign var='langname' value=$languages[ix].name|escape}
				{assign var='flag' value=$languages[ix].flag|escape}
				{assign var='class' value=$languages[ix].class|escape}
				{if $flag neq ''}
					{icon href="tiki-switch_lang.php?language=$val" alt="$langname" title="$langname" _id="img/flags/$flag.png" height=11 class="icon $class"}
				{else}
					{button _text="$langname" href="tiki-switch_lang.php?language=$val" _title="$langname" _class="$class"}
				{/if}
			{/section}
		{elseif $mode eq 'words' || $mode eq 'abrv'}
			<ul>
				{section name=ix loop=$languages}
					<li>
						{if $mode eq 'words'}
							<a title="{$languages[ix].name|escape}" class="linkmodule {$languages[ix].class}" href="tiki-switch_lang.php?language={$languages[ix].value|escape}">
								{$languages[ix].name|escape}
							</a>
						{else}
							<a title="{$languages[ix].name|escape}" class="linkmodule {$languages[ix].class}" href="tiki-switch_lang.php?language={$languages[ix].value|escape}">
								{$languages[ix].value|escape}
							</a>
						{/if}
					</li>
				{/section}
			</ul>
		{else}{* do menu as before is not flags or words *}
			<form method="get" action="tiki-switch_lang.php" target="_self">
				<select name="language" size="1" onchange="this.form.submit();" class="form-control">
					{section name=ix loop=$languages}
						<option value="{$languages[ix].value|escape}"
							{if $prefs.language eq $languages[ix].value} selected="selected"{/if}>
							{$languages[ix].name}
						</option>
					{/section}
				</select>
			</form>
		{/if}
	{/tikimodule}
{/strip}
