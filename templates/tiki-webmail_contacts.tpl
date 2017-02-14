<!DOCTYPE html>
{* Index we display a wiki page here *}

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="StyleSheet" href="styles/{$prefs.style}" type="text/css">
		<title>{tr}Address book{/tr}</title>
	</head>
	<body>
		<div id="tiki-clean">

			<h2>{tr}Contacts{/tr}</h2>
			<div align="center">
				{include file='find.tpl'}
				{initials_filter_links}
				<div class="table-responsive">
					<table class="table">
						<tr>
							<th>
								<a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'firstName_desc'}firstName_asc{else}firstName_desc{/if}">
									{tr}First Name{/tr}
								</a>
							</th>
							<th>
								<a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastName_desc'}lastName_asc{else}lastName_desc{/if}">
									{tr}Last Name{/tr}
								</a>
							</th>
							<th>
								<a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">
									{tr}Email{/tr}
								</a>
							</th>
							<th>
								<a href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'nickname_desc'}nickname_asc{else}nickname_desc{/if}">
									{tr}Nickname{/tr}
								</a>
							</th>
						</tr>

						{section name=user loop=$channels}
							<tr>
								<td class="text">{$channels[user].firstName}</td>
								<td class="text">{$channels[user].lastName}</td>
								<td class="email">
									<a class="link" href="#" onClick="javascript:window.opener.document.getElementById('{$element}').value=window.opener.document.getElementById('{$element}').value + '{$channels[user].email}' + ' ';">
										{$channels[user].email}
									</a>
									[&nbsp;&nbsp;
									<a class="link tips" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}" title=":{tr}Delete{/tr}">
										{icon name='delete'}
									</a>
									&nbsp;&nbsp;]
								</td>
								<td class="text">{$channels[user].nickname}</td>
							</tr>
						{/section}
					</table>
				</div>
				<div class="center-block">
					{if $prev_offset >= 0}
						[<a class="prevnext" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">
							{tr}Prev{/tr}
						</a>
						]&nbsp;
					{/if}
					{tr}Page:{/tr} {$actual_page}/{$cant_pages}
					{if $next_offset >= 0}
						&nbsp;[
						<a class="prevnext" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">
							{tr}Next{/tr}
						</a>]
					{/if}
					{if $prefs.direct_pagination eq 'y'}
						<br>
						{section loop=$cant_pages name=foo}
							{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
							<a class="prevnext" href="tiki-webmail_contacts.php?element={$element}&amp;section=contacts&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
								{$smarty.section.foo.index_next}
							</a>&nbsp;
						{/section}
					{/if}
				</div>
			</div>
		</div>
	</body>
</html>
