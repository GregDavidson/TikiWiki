{* $Id: tiki-received_pages.tpl 58787 2016-06-05 13:59:28Z lindonb $ *}

{title help="Communication Center"}{tr}Received Pages{/tr}{/title}

{if $receivedPageId > 0 or $view eq 'y'}
	<h2>{tr}Preview{/tr}</h2>
	<div class="wikitext">{$parsed}</div>
{/if}

{if $receivedPageId > 0}
	<h2>{tr}Edit Received Page{/tr}</h2>
	<form action="tiki-received_pages.php" method="post" class="form-horizontal">
		<input type="hidden" name="receivedPageId" value="{$receivedPageId|escape}">
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Name:{/tr}</label>
			<div class="col-sm-7">
				<input type="text" name="pageName" value="{$pageName|escape}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Data:{/tr}</label>
			<div class="col-sm-7">
				<textarea name="data" rows="10" cols="60" class="form-control">{$data|escape}</textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3">{tr}Comment:{/tr}</label>
			<div class="col-sm-7">
				<input type="text" name="comment" value="{$comment|escape}" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3"></label>
			<div class="col-sm-7">
				<input type="submit" class="btn btn-default btn-sm" name="preview" value="{tr}Preview{/tr}">
				&nbsp;
				<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			</div>
		</div>
	</form>
{/if}

<h2>{tr}Received Pages{/tr}</h2>
<div align="center">
	{include file='find.tpl'}
	{if $channels|@count > 0}
		<p>
			<span class="highlight">{tr}The highlight pages already exist.{/tr}</span> {tr}Please, change the name if you want the page to be uploaded.{/tr}
		</p>
	{/if}
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tr>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedPageId_desc'}receivedPageId_asc{else}receivedPageId_desc{/if}">{tr}ID{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Name{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedDate_desc'}receivedDate_asc{else}receivedDate_desc{/if}">{tr}Date{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromSite_desc'}receivedFromSite_asc{else}receivedFromSite_desc{/if}">{tr}Site{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'receivedFromUser_desc'}receivedFromUser_asc{else}receivedFromUser_desc{/if}">{tr}User{/tr}</a>
				</th>
				<th></th>
			</tr>

			{section name=user loop=$channels}
				<tr>
					<td class="id">{$channels[user].receivedPageId}</td>
					{if $channels[user].pageExists ne ''}
						<td class="text">
							<span class="highlight">{$channels[user].pageName}</span>
						</td>
					{else}
						<td class="text">{$channels[user].pageName}</td>
					{/if}
					<td class="date">{$channels[user].receivedDate|tiki_short_date}</td>
					<td class="text">{$channels[user].receivedFromSite}</td>
					<td class="text">{$channels[user].receivedFromUser}</td>
					<td class="action">
						<a class="tips" title=":{tr}Edit{/tr}" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedPageId={$channels[user].receivedPageId}">
							{icon name='edit'}
						</a>
						<a class="tips" title=":{tr}View{/tr}" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$channels[user].receivedPageId}">
							{icon name='view'}
						</a>
						<a class="tips" title=":{tr}Accept{/tr}" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$channels[user].receivedPageId}">
							{icon name='ok'}
						</a> &nbsp;
						<a class="tips" title=":{tr}Remove{/tr}" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].receivedPageId}">
							{icon name='remove'}
						</a>
					</td>
				</tr>
			{sectionelse}
				{norecords _colspan=6}
			{/section}
		</table>
	</div>
	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
</div>

<h2>{tr}Received Structures{/tr}</h2>
{if $structures|@count > 0}
	<p>
		<span class="highlight">{tr}The highlight pages already exist.{/tr}</span> {tr}Please, change the name if you want the page to be uploaded.{/tr}
	</p>
{/if}
<form action="tiki-received_pages.php" method="post">
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tr>
				<th>&nbsp;</th>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'receivedPageId_desc'}receivedPageId_asc{else}receivedPageId_desc{/if}">{tr}ID{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'structureName_desc'}structureName_asc{else}structureName_desc{/if}">{tr}Structure{/tr}</a>
				</th>
				<th>{tr}Page{/tr}</th>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'receivedDate_desc'}receivedDate_asc{else}receivedDate_desc{/if}">{tr}Date{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'receivedFromSite_desc'}receivedFromSite_asc{else}receivedFromSite_desc{/if}">{tr}Site{/tr}</a>
				</th>
				<th>
					<a href="tiki-received_pages.php?offset={$offset}&amp;sort_modes={if $sort_modes eq 'receivedFromUser_desc'}receivedFromUser_asc{else}receivedFromUser_desc{/if}">{tr}User{/tr}</a>
				</th>
				<th></th>
			</tr>

			{section name=user loop=$structures}
				{if $structures[user].structureName eq $structures[user].pageName}
					<tr>
						<td class="text">&nbsp;</td>
						<td class="id">{$structures[user].receivedPageId}</td>
						<td class="text">{$structures[user].pageName}</td>
						<td class="text">&nbsp;</td>
						<td class="date">{$structures[user].receivedDate|tiki_short_date}</td>
						<td class="text">{$structures[user].receivedFromSite}</td>
						<td class="text">{$structures[user].receivedFromUser}</td>
						<td class="action">
							<a class="tips" title=":{tr}Accept{/tr}" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;accept={$structures[user].receivedPageId}">
								{icon name='ok'}
							</a>
							&nbsp;
							<a class="tips" title=":{tr}Remove{/tr}" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$structures[user].receivedPageId}">
								{icon name='remove'}
							</a>
						</td>
					</tr>
					{section name=ix loop=$structures}
						{if $structures[ix].structureName eq $structures[user].structureName}
							<tr>
								<td class="checkbox-cell">
									<input type="checkbox" name="checked[]" value="{$structures[ix].pageName|escape}" >
								</td>
								<td class="id">{$structures[ix].receivedPageId}</td>
								<td class="text">&nbsp;</td>
								{if $structures[ix].pageExists ne ''}
									<td class="text">
										<span class="highlight">{$structures[ix].pageName}</span>
									</td>
								{else}
									<td class="text">{$structures[ix].pageName}</td>
								{/if}
								<td class="date">{$structures[ix].receivedDate|tiki_short_date}</td>
								<td class="text">{$structures[ix].receivedFromSite}</td>
								<td class="text">{$structures[ix].receivedFromUser}</td>
								<td class="action">
									<a class="tips" title=":{tr}Edit{/tr}" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;receivedPageId={$structures[ix].receivedPageId}">
										{icon name='edit'}
									</a>
									<a class="tips" title=":{tr}View{/tr}" href="tiki-received_pages.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;view={$structures[ix].receivedPageId}">
										{icon name='view'}
									</a>
								</td>
							</tr>
						{/if}
					{/section}
				{/if}
			{sectionelse}
				{norecords _colspan=8}
			{/section}
			{select_all checkbox_names='checked[]' label="{tr}Select All{/tr}"}
		</table>
	</div>
	<div class="form-inline">
	{tr}Prefix the checked: {/tr}<input type="text" name="prefix" class="form-control">
	{tr} Postfix the checked: {/tr}<input type="text" name="postfix" class="form-control">&nbsp;<input type="submit" class="btn btn-default " value="{tr}OK{/tr}">
	</div>
</form>
