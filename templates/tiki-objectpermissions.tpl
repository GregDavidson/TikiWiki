{* $Id: tiki-objectpermissions.tpl 57907 2016-03-16 13:23:47Z patrick-proulx $ *}

{title help="Permissions"}{if $objectType eq 'global'}{tr}Assign global permissions{/tr}{else}{tr}Assign permissions to {/tr}{$objectType}: {$objectName}{/if}{/title}

<div class="t_navbar margin-bottom-md">
	{if !empty($referer)}{button href="$referer" _class="btn btn-link" _type="link" _icon_name="back" _text="{tr}Back{/tr}"}{/if}
	{if $tiki_p_admin eq 'y'} {* only full admins can manage groups, not tiki_p_admin_users *}
		{button href="tiki-admingroups.php" class="btn btn-link" _type="link" _icon_name="group" _text="{tr}Admin Groups{/tr}"}
	{/if}
	{if $tiki_p_admin_users eq 'y'}
		{button href="tiki-adminusers.php" class="btn btn-link" _type="link" _icon_name="user" _text="{tr}Admin users{/tr}"}
	{/if}
	{if $tiki_p_admin_categories eq 'y'}
		{button href="tiki-admin_categories.php" class="btn btn-link" _type="link" _icon_name="admin_category" _text="{tr}Admin Categories{/tr}"}
	{/if}
	{button href="tiki-list_object_permissions.php" class="btn btn-link" _type="link" _icon_name="permission" _text="{tr}Object Permissions List{/tr}"}
</div>

{tabset name='tabs_objectpermissions'}

	{tab name="{tr}Assign Permissions{/tr}"}

		{if $prefs.feature_tabs neq 'y'}
			<h2>{tr}Edit Permissions{/tr}</h2>
		{/if}
		<form method="post" action="{$smarty.server.PHP_SELF}?{query}">
			{capture name="notices"}
				{if empty($filegals_manager)}
					{if $objectType eq 'global'}
						{remarksbox type="note" title="{tr}Note{/tr}"}
							{tr}Currently editing Global permissions.{/tr}
						{/remarksbox}
					{elseif $permissions_displayed eq 'direct'}
						{remarksbox type="warning" title="{tr}Warning{/tr}"}
							{if $objectType neq 'category'}
								{tr}This object's direct permissions override any global permissions or category permissions affecting this object.{/tr}
							{else}
								{tr}This category's direct permissions override any global permissions affecting objects in it.{/tr}
							{/if}
							<br>
							{tr}To edit global permissions{/tr} {self_link objectType='global' objectId='' objectName='' permType=$permType}{tr}click here{/tr}{/self_link}.
						{/remarksbox}
					{elseif $permissions_displayed eq 'category'}
						{remarksbox type="warning" title="{tr}Warning{/tr}"}
							{tr}No permissions yet applied to this object but category permissions affect this object and are displayed below.{/tr}<br>
							{if $tiki_p_admin eq 'y'}{tr}To edit category permissions{/tr} {self_link _script='tiki-admin_categories.php'}{tr}click here{/tr}{/self_link}.{/if}
						{/remarksbox}
					{elseif $permissions_displayed eq 'parent'}
						{remarksbox type="warning" title="{tr}Warning{/tr}" highlight="y"}
							{tr}No direct permissions yet applied to this object. Global permissions apply.{/tr}<br>
							{if $tiki_p_admin eq 'y'}{tr}To edit global permissions{/tr} {self_link objectType='global' permType=$permType}{tr}click here{/tr}{/self_link}.{/if}
							<br><br>
							{tr}Currently inherited permissions displayed.{/tr}
						{/remarksbox}
					{/if}
				{/if}
			{/capture}
			{$smarty.capture.notices}
			<hr>
			<h2>{if $objectType eq 'global'}{tr}Assign global permissions{/tr}{elseif $objectType eq 'category'}{tr}Assign permissions to this category{/tr}{else}{tr}Assign permissions to this object{/tr}{/if} {icon name="cog" id="perms_busy" style="vertical-align:top; display:none;"}</h2>

			<div>
				<input type="hidden" name="referer" value="{$referer|escape}">
				<input type="hidden" name="objectName" value="{$objectName|escape}">
				<input type="hidden" name="objectType" value="{$objectType|escape}">
				<input type="hidden" name="objectId" value="{$objectId|escape}">
				<input type="hidden" name="permType" value="{$permType|escape}">
				<input type="hidden" name="show_disabled_features" value="{$show_disabled_features}">

				<div class="input_submit_container" style="text-align: center">
					<input type="submit" class="btn btn-primary btn-sm" name="assign" value="{tr}Assign{/tr}">
					{if $permissions_displayed eq 'direct' and $objectType neq 'global'}
						<input type="submit" class="btn btn-warning btn-sm" name="remove" value="{if $objectType eq 'category'}{tr}Delete category permissions{/tr}{else}{tr}Delete object permissions{/tr}{/if}" class="tips" title="{tr}Reset Perms{/tr}|{if $objectType neq 'category'}{tr}This will remove all the settings here and permissions will be reset to inherit any category permissions that are set, or the global sitewide permissions.{/tr}{else}{tr}This will remove all the settings here and permissions will be reset to inherit the global sitewide permissions.{/tr}{/if}">
					{/if}
					<input type="submit" class="btn btn-default btn-sm" name="copy" value="{tr}Copy{/tr}" class="tips" title="{tr}Permissions Clipboard{/tr}|{tr}Copy the permissions set here{/tr}">
					{if !empty($perms_clipboard_source)}<input type="submit" class="btn btn-default btn-sm" name="paste" value="{tr}Paste{/tr}" class="tips" title="{tr}Permissions Clipboard{/tr}|{tr}Paste copied permissions from {/tr}<em>{$perms_clipboard_source}</em>" />{/if}
				</div>

				{if $objectType eq 'category'}
					<p>
						<input type="checkbox" id="propagate_category" name="propagate_category" value="1">
						<label for="propagate_category">{tr}Assign or remove permissions on <em>all</em> child categories{/tr}</label>
					</p>
					{jq}
						$("input[name='assign'],input[name='remove']").click(function(){
							if ($("#propagate_category").prop("checked")) {
								return confirm("{tr}Are you sure you want to affect all child categories?\nThere is no undo.{/tr}");
							}
						});
					{/jq}
				{/if}

				{if ($objectType eq 'wiki' or $objectType eq 'wiki page') and !empty($inStructure)}
					<input name="assignstructure" id="assignstructure" type="checkbox">
					<label for="assignstructure">{tr}Assign or remove permissions on all pages of the sub-structure{/tr}</label>
					{jq}
						$("input[name='assign'],input[name='remove']").click(function(){
							if ($("#assignstructure").prop("checked")) {
								return confirm("{tr}Are you sure you want to affect all pages in this sub-structure?\nThere is no undo.{/tr}");
							}
						});
					{/jq}
				{/if}

			</div>
			<h3>{tr}Permissions{/tr}</h3>

			<div class="table-responsive">
				<div class="permission_table">
					{treetable _data=$perms _checkbox=$permGroups _checkboxTitles=$groupNames
						_checkboxColumnIndex=$permGroupCols _valueColumnIndex="permName"
						_columns="\"label\"=\"{tr}Permission{/tr}\"" _groupColumn='type' _openall='y'
						_showSelected='y' _columnsContainHtml='y' class='table table-striped objectperms'}
				</div>
			</div>

			{if ($perms|@count) eq '0'}{remarksbox type="warning" title="{tr}Warning{/tr}"}{tr}You must select at least one feature{/tr}.{/remarksbox}{/if}

			<div class="input_submit_container" style="text-align: center">
				<input type="submit" class="btn btn-primary btn-sm" name="assign" value="{tr}Assign{/tr}">
				{if $permissions_displayed eq 'direct' and $objectType neq 'global'}
					<input type="submit" class="btn btn-default btn-sm" name="remove" value="{if $objectType eq 'category'}{tr}Delete category permissions{/tr}{else}{tr}Delete object permissions{/tr}{/if}" class="tips" title="{tr}Reset Perms{/tr}|{tr}This will remove all the settings here and permissions will be reset to inherit the global sitewide permissions.{/tr}">
				{/if}
			</div>
		</form>

	{/tab}

	{if !empty($permissions_added) or !empty($permissions_removed)}
		{tab name="{tr}View Differences{/tr}"}
			{if !empty($permissions_added)}
				<h3>{tr}Permissions added:{/tr}</h3>
				<blockquote>{$permissions_added}</blockquote>
			{/if}
			{if !empty($permissions_removed)}
				<h3>{tr}Permissions removed:{/tr}</h3>
				<blockquote>{$permissions_removed}</blockquote>
			{/if}
		{/tab}
	{/if}

	{tab name="{tr}Select groups{/tr}"}
		<form method="post" action="{$smarty.server.PHP_SELF}?{query}">
		<div>
			<input type="hidden" name="referer" value="{$referer|escape}">
			{if isset($groupsFiltered)}
				{remarksbox type="warning" title="{tr}Note{/tr}"}
					{tr}Some of your groups have been automatically hidden.<br> Select the groups below to assign permissions for.{/tr}
					{tr}These groups are not the groups that have permissions on the object. It is only the groups you can see in the columns of the first tab.{/tr}
				{/remarksbox}
			{else}
				{remarksbox type="note" title="{tr}Note{/tr}"}
					{tr}These groups are not the groups that have permissions on the object. It is only the groups you can see in the columns of the first tab.{/tr}
				{/remarksbox}
			{/if}
			{if $objectId}
			<div class="t_navbar">
				<input type="submit" class="btn btn-default btn-sm" name="used_groups" value="{tr}Select only groups that have a perm with the object{/tr}">
			</div>
			{/if}

			<h2>{tr}Groups{/tr}</h2>

			<div>
			{treetable _data=$groups _checkbox="group_filter" _checkboxTitles="{tr}Select all{/tr}"
					_checkboxColumnIndex="in_group_filter" _valueColumnIndex="id"
					_columns='"groupName"="{tr}Group name{/tr}","groupDesc"="{tr}Description{/tr}"'
					_sortColumn='parents' _collapseMaxSections=20 _sortColumnDelimiter=',' _selectAllHiddenToo='y'}
			</div>

			<div class="input_submit_container" style="text-align: center">
				<input type="submit" class="btn btn-default btn-sm" name="group_select" value="{tr}Select{/tr}">
			</div>
		</div>
		</form>
	{/tab}

	{tab name="{tr}Select features{/tr}"}
		<form method="post" action="{$smarty.server.PHP_SELF}?{query}">
		<div>
			<input type="hidden" name="referer" value="{$referer|escape}">
			{if isset($featuresFiltered)}
				{remarksbox type="warning" title="{tr}Warning{/tr}"}
					{tr}Some of your features have been automatically hidden.<br> Select the features below to assign permissions for.{/tr}
				{/remarksbox}
			{/if}

			<label for="show_disabled_features2">{tr}Show permissions for disabled features{/tr}</label>
			<input type="checkbox" name="show_disabled_features" id="show_disabled_features2" {if isset($show_disabled_features) and $show_disabled_features eq 'y'}checked="checked"{/if} onchange="this.form.submit();">

			<h2>{tr}Features{/tr}</h2>

			<div>
			{treetable _data=$features _checkbox="feature_filter" _checkboxTitles="{tr}Select all{/tr}" _checkboxColumnIndex="in_feature_filter" _valueColumnIndex="featureName" _columns='"featureName"="{tr}Feature name{/tr}"' _sortColumn="featureName" _sortColumnDelimiter='*' _collapseMaxSections=20 _listFilter='n'}
			</div>

			<div class="input_submit_container" style="text-align: center">
				<input type="submit" class="btn btn-default btn-sm" name="feature_select" value="{tr}Select{/tr}">
			</div>
		</div>
		</form>
	{/tab}

	{* Quickperms *}

	{if $prefs.feature_quick_object_perms eq 'y'}
		{tab name="{tr}Quick Permissions{/tr}"}
		<form name="allperms" method="post" action="{$smarty.server.PHP_SELF}?{query}">
		<div>
		<input type="hidden" name="quick_perms" value="true">


		{if $prefs.feature_tabs neq 'y'}
			<h2>{tr}Quick Permissions{/tr}</h2>
		{/if}

		{$smarty.capture.notices}

		<h2>{tr}Assign Quick-Permissions to this object{/tr}</h2>

		<table class="table">
			<tr>
				<th>{tr}Groups{/tr}</th>
			{foreach item=permgroup from=$quickperms}
				<th>{$permgroup.name}</th>
			{/foreach}
				<th class="tips" title="{tr}A couple of userdefined permissions are currently assigned (See tab Assign Permissions){/tr}">Advanced</th>
			</tr>

			{section name=grp loop=$groups}
				{if $groups[grp].groupName neq 'Admins'}
					<tr>
						<td>
						{$groups[grp].groupName|escape}
						</td>
						{foreach item=permgroup from=$quickperms}
						<td>
							<input type="radio" name="perm_{$groups[grp].groupName|escape:url}" value="{$permgroup.name}" {if $groups[grp].groupSumm eq $permgroup.name}checked{/if}>
						</td>
						{/foreach}
						<td>
							<input type="radio" name="perm_{$groups[grp].groupName|escape:url}" value="userdefined" {if $groups[grp].groupSumm eq 'userdefined'}checked{/if} disabled>
						</td>
					</tr>
				{/if}
			{/section}
		</table>

		<input type="hidden" name="referer" value="{$referer|escape}">
		<input type="hidden" name="objectName" value="{$objectName|escape}">
		<input type="hidden" name="objectType" value="{$objectType|escape}">
		<input type="hidden" name="objectId" value="{$objectId|escape}">
		<input type="hidden" name="permType" value="{$permType|escape}">
		<input type="hidden" name="show_disabled_features" value="{$show_disabled_features}">
		<div class="input_submit_container" style="text-align: center">
			<input type="submit" class="btn btn-primary btn-sm" name="assign" value="{tr}Assign{/tr}">
		</div>

		{if empty($filegals_manager)}
			{remarksbox type="note" icon="bricks" title="{tr}Experimental{/tr}"}
				{tr}<em>Quick permissions</em> should be considered as an experimental feature.{/tr}<br/>
				{tr}Although permissions will be set as expected using this form, it doesn't necessarily show the current permissions reliably.{/tr}<br><br>
				{tr}There is also no undo - <strong>Use with care!</strong>{/tr}
			{/remarksbox}
		{/if}
		</div>
	</form>
	{/tab}
	{/if}

	{* Quickperms END *}

{/tabset}
