{* $Id: mod-change_category.tpl 56041 2015-08-13 16:35:36Z jonnybradley $ *}

{if $showmodule}
	{tikimodule error=$module_params.error title=$tpl_module_title name="change_category" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		{if !empty($module_params.imgUrlNotIn) and !empty($module_params.imgUrlIn)}
			{foreach key=k item=i from=$modcatlist}{* Smarty hack to access $modcatlist's first key. This should only access one element. *}
				{if $modcatlist[$k].incat eq 'n'}
					{capture name='title'}{tr}Assign into category:{/tr} {tr}{$modcatlist[$k].name|escape}{/tr}{/capture}
					{self_link modcatid=$modcatid modcatchange=$k _title=$smarty.capture.title}<img src="{$module_params.imgUrlNotIn}">{/self_link}
				{else}
					{capture name='title'}{tr}Unassign category:{/tr} {tr}{$modcatlist[$k].name|escape}{/tr}{/capture}
					{self_link remove=$k _title=$smarty.capture.title}<img src="{$module_params.imgUrlIn}">{/self_link}
				{/if}
			{/foreach}
		{else}

			{if $detailed eq 'y'}
				<div class="table-responsive">
					<table class="table">
						{foreach key=k item=i from=$modcatlist}
							{if $i.incat eq 'y'}
								<tr>
									<td class="{cycle advance=false}">
										{if isset($module_params.path) and $module_params.path eq 'n'}
											{$i.name|escape}
										{else}
											{$i.relativePathString|escape}
										{/if}
									</td>
									{if !isset($module_params.del) or $module_params.del eq 'y'}
										<td>
											{self_link remove=$i.categId _class='tips' _title=":{tr}Delete{/tr}"}{icon name='delete'}{/self_link}
										</td>
									{/if}
								</tr>
							{/if}
						{/foreach}
					</table>
				</div>
			{/if}

			{if $detailed eq 'n' or ($add eq 'y' and not $isInAllManagedCategories)}
				<div class="text-center">
					<form method="post" target="_self">
						<input type="hidden" name="page" value="{$page|escape}" />
						<input type="hidden" name="modcatid" value="{$modcatid}" />

						<div class="form-group">
							{if $multiple eq 'y'}
								<select name="modcatchange[]" multiple="multiple" class="form-control">
							{else}
								<select name="modcatchange" size="1" onchange="this.form.submit();" class="form-control">
							{/if}
							{if $add eq 'y'}
								{if !isset($module_params.notop)}
									<option value="0" style="font-style: italic;">{tr}None{/tr}</option>
								{/if}
							{/if}
							{foreach key=k item=i from=$modcatlist}
								{if $detailed eq 'n' or $i.incat ne 'y'}
									{if ($add eq 'y' or $i.incat eq 'y')}
										<option value="{$k}"{if $multiple eq 'y' and $i.incat eq 'y'} selected="selected"{/if}>
											{if !empty($module_params.path) and $module_params.path eq 'n'}
												{$i.name|escape}
											{else}
												{$i.relativePathString|escape}
											{/if}
										</option>
									{/if}
								{/if}
							{/foreach}
							</select>
						</div>

						{if $multiple eq 'y' and $add eq 'y'}
							<div class="form-group">
								<input type="submit" class="btn btn-default btn-sm" name="categorize" value="{if isset($module_params.categorize)}{tr}{$module_params.categorize}{/tr}{else}{tr}Categorize{/tr}{/if}" />
							</div>
						{/if}
					</form>
				</div>
			{/if}

		{/if}

	{/tikimodule}
{/if}
