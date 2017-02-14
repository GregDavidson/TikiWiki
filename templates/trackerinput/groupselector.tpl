{if $field.options lt 1 or $tiki_p_admin_trackers eq 'y'}
	<select name="{$field.ins_id}" class="form-control">
		{if $field.isMandatory ne 'y'}
			<option value="">{tr}None{/tr}</option>
		{/if}
		{section name=ux loop=$field.list}
			{if !isset($field.itemChoices) or $field.itemChoices|@count eq 0 or in_array($field.list[ux], $field.itemChoices)}
				<option value="{$field.list[ux]|escape}" {if $field.value eq $field.list[ux]}selected="selected"{/if}>{$field.list[ux]}</option>
			{/if}
		{/section}
	</select>
{elseif $field.options}
	{$field.defvalue}
	<input type="hidden" name="{$field.ins_id}" value="{$field.defvalue}">
{/if}
