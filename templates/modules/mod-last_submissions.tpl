{* $Id: mod-last_submissions.tpl 33949 2011-04-14 05:13:23Z chealer $ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="last_submissions" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{modules_list list=$modLastSubmissions nonums=$nonums}
	{section name=ix loop=$modLastSubmissions}
		<li>
		{if $tiki_p_edit_submission eq 'y'}
			<a class="linkmodule" href="tiki-edit_submission.php?subId={$modLastSubmissions[ix].subId}">
				{$modLastSubmissions[ix].title|escape}
			</a>
		{else}
			<span class="module">{$modLastSubmissions[ix].title|escape}</span>
		{/if}
		</li>
	{/section}
{/modules_list}
{/tikimodule}
