{* $Id: tiki-searchresults.tpl 58777 2016-06-03 14:35:47Z amnabilal $ *}

{if !( $searchStyle eq "menu" )}
	{title admpage="search" help="Search"}{tr}Search{/tr}{/title}
{/if}

<div class="margin-bottom-md nohighlight">
	{if $searchStyle neq "menu" && $prefs.feature_search_show_object_filter eq 'y'}
		<div class="t_navbar margin-bottom-sm">
			{tr}Search in:{/tr}
			{foreach item=name key=k from=$where_list}
				{button _auto_args='where,highlight' href="tiki-searchresults.php?where=$k" _selected="{if $where == $k}y{else}n{/if}" _selected_class="highlight" class="btn btn-default" _text="$name"}
			{/foreach}
		</div>
	{/if}

	{if $prefs.feature_search_show_search_box eq 'y' or $searchStyle eq "menu"}
		<form action="tiki-searchresults.php" method="get" id="search-form" class="form-inline" role="form">
			<div class="form-group">
				<label class="sr-only">{tr}Search{/tr}</label>
				<input id="highlight{$iSearch}" name="highlight" class="form-control" type="text" accesskey="s" placeholder="{tr}Search{/tr}" value="{$words|escape}">
				<!--/div-->
				{if $prefs.search_autocomplete eq 'y'}
					{autocomplete element="#highlight$iSearch" type='pagename'}
				{/if}
			</div>
			{if !( $searchStyle eq "menu" )}
				<div class="form-group">
					<label class="searchboolean control-label" for="boolean">
						{tr}Advanced search:{/tr} <input type="checkbox" name="boolean" id="boolean" {if $boolean eq 'y'} checked="checked"{/if}>
					</label>
					<a href="{bootstrap_modal controller=search action=help}">{tr}Search Help{/tr} {icon name='help'}</a>
				</div>

				{if $prefs.feature_search_show_last_modification eq 'y'}
					<div class="form-group">
						<label class="searchdate control-label" for="date">{tr}Date Search:{/tr}</label>
						<select id="date" class="form-control" name="date" onchange="javascript:submit()">
							{section name=date start=0 loop=12 step=1}
								<option value="{$smarty.section.date.index|escape}" {if $smarty.section.date.index eq $date}selected="selected"{/if}>
									{if $smarty.section.date.index eq 0}
										{tr}All dates{/tr}
									{else}
										{$smarty.section.date.index|escape} {tr}Month{/tr}
									{/if}
								</option>
							{/section}
						</select>
					</div>
				{/if}

				{if $prefs.feature_multilingual eq 'y' and ($where eq 'wikis' || $where eq 'articles')}
					<label class="searchLang" for="searchLang">
						<select id="searchLang" name="searchLang">
							<option value="" >{tr}any language{/tr}</option>
							{section name=ix loop=$languages}
								<option value="{$languages[ix].value|escape}" {if $searchLang eq $languages[ix].value}selected="selected"{/if}>
									{tr}{$languages[ix].name}{/tr}
								</option>
							{/section}
						</select>
					</label>
				{/if}

				{if $prefs.feature_categories eq 'y' and !empty($categories) and $tiki_p_view_category eq 'y' and $prefs.search_show_category_filter eq 'y'}
					<div id="category_singleselect_find" style="display: {if $findSelectedCategoriesNumber > 1}none{else}block{/if};">
						<label class="findcateg">
							<select name="categId">
								<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
								{foreach $categories as $catix}
									<option value="{$catix.categId|escape}" {if $find_categId eq $catix.categId}selected="selected"{/if}>
										{capture}{tr}{$catix.categpath}{/tr}{/capture}{$smarty.capture.default|escape}
									</option>
								{/foreach}
							</select>
						</label>
						{if $prefs.javascript_enabled eq 'y'}<a href="#" onclick="show('category_multiselect_find');hide('category_singleselect_find');">{tr}Multiple select{/tr}</a>{/if}
					</div>

					<div id="category_multiselect_find" style="display: {if $findSelectedCategoriesNumber > 1}block{else}none{/if};">
						<div class="multiselect">
							{if count($categories) gt 0}
								{$cat_tree}
								<div class="clearfix">
									{if $tiki_p_admin_categories eq 'y'}
										<div class="pull-right"><a href="tiki-admin_categories.php" class="link">{tr}Admin Categories{/tr} {icon name='wrench'}</a></div>
									{/if}
									{select_all checkbox_names='cat_categories[]' label="{tr}Select/deselect all categories{/tr}"}
								</div>
							{else}
								<div class="clearfix">
									{if $tiki_p_admin_categories eq 'y'}
										<div class="pull-right"><a href="tiki-admin_categories.php" class="link">{tr}Admin Categories{/tr} {icon name='wrench'}</a></div>
									{/if}
									{tr}No categories defined{/tr}
								</div>
							{/if}
						</div> {* end #multiselect *}
					</div> {* end #category_multiselect_find *}
				{/if}
			{/if}

			{if $prefs.feature_search_show_object_filter eq 'y'}
				{if $searchStyle eq "menu"}
					<span class='searchMenu'>
						{tr}in{/tr}
						<select name="where">
							{if empty($where_list)} {* Required when file included outside tiki-searchindex.php. eg. error.rpl *}
								<option value="pages">{tr}Entire Site{/tr}</option>
								{if $prefs.feature_wiki eq 'y'}
									<option value="wikis">{tr}Wiki Pages{/tr}</option>
								{/if}
								{if $prefs.feature_calendar eq 'y'}
									<option value="calendars">{tr}Calendar Items{/tr}</option>
								{/if}
								{if $prefs.feature_galleries eq 'y'}
									<option value="galleries">{tr}Galleries{/tr}</option>
									<option value="images">{tr}Images{/tr}</option>
								{/if}
								{if $prefs.feature_file_galleries eq 'y'}
									<option value="files">{tr}Files{/tr}</option>
								{/if}
								{if $prefs.feature_forums eq 'y'}
									<option value="forums">{tr}Forums{/tr}</option>
								{/if}
								{if $prefs.feature_faqs eq 'y'}
									<option value="faqs">{tr}FAQs{/tr}</option>
								{/if}
								{if $prefs.feature_blogs eq 'y'}
									<option value="blogs">{tr}Blogs{/tr}</option>
									<option value="posts">{tr}Blog Posts{/tr}</option>
								{/if}
								{if $prefs.feature_directory eq 'y'}
									<option value="directory">{tr}Directory{/tr}</option>
								{/if}
								{if $prefs.feature_articles eq 'y'}
									<option value="articles">{tr}Articles{/tr}</option>
								{/if}
								{if $prefs.feature_trackers eq 'y'}
									<option value="trackers">{tr}Trackers{/tr}</option>
								{/if}
							{else}
								{foreach item=name key=k from=$where_list}
									<option value="{$k|escape}">{$name|escape}</option>
								{/foreach}
							{/if}
						</select>
					</span>
				{else}
					<input type="hidden" name="where" value="{$where|escape}">
					{if $forumId}<input type="hidden" name="forumId" value="{$forumId|escape}">{/if}
				{/if}
			{elseif !empty($where)}
				<input type="hidden" name="where" value="{$where|escape}">
				{if $forumId}<input type="hidden" name="forumId" value="{$forumId|escape}">{/if}
			{/if}
			<label class="findsubmit">
				<input type="submit" class="btn btn-default" name="search" value="{tr}Go{/tr}">
			</label>
			{if !$searchNoResults}
				{button _auto_args='highlight' href="tiki-searchresults.php?highlight=" _text="{tr}Clear Filter{/tr}"}
			{/if}
		</form>
	{/if}
</div><!--nohighlight-->
{* do not change the comment above, since smarty 'highlight' outputfilter is hardcoded to find exactly this... instead you may experience white pages as results *}

{if $searchStyle ne 'menu' and ! $searchNoResults}
	<div class="nohighlight simplebox" style="width:300px">
		{tr}Found{/tr} "{$words|escape}" {tr}in{/tr}
		{if $where_forum}
			"{tr}{$where|escape}:{/tr}" {$where_forum|escape}
		{else}
			{$cant} "{tr}{$where_label|escape}"{/tr}
		{/if}
	</div><!--nohighlight-->
{/if}

{if ! $searchNoResults}
	<ul class="searchresults">
		{section name=search loop=$results}
			<li>
				{if $prefs.feature_search_show_object_type eq 'y' && $results[search].type > ''}
					<span class="objecttype">{tr}{$results[search].type|escape}{/tr}</span>
				{/if}
				{if !empty($results[search].parentName)}
						<a href="{$results[search].parentHref}" class="parentname">{$results[search].parentName|escape}</a>
				{/if}
				{page_in_structure pagechecked=$results[search].pageName} {* check if page in structure *}
				{if $page_in_structure} {page_alias pagechecked=$results[search].pageName} {/if}
				<a href="{$results[search].href}" class="objectname">{if $page_in_structure and $page_alias ne ''}{$page_alias}{else}{$results[search].pageName|escape}{/if}</a>
				{if $prefs.feature_search_show_visit_count eq 'y'}
					<span class="itemhits">({tr}Hits:{/tr} {$results[search].hits|escape})</span>
				{/if}

				{if $prefs.feature_search_show_pertinence eq 'y' && $prefs.feature_search_fulltext eq 'y'}
					<span class="itemrelevance">
						{if $results[search].relevance <= 0}
							({tr}Simple search{/tr})
						{else}
							({tr}Relevance:{/tr} {$results[search].relevance})
						{/if}
					</span>
				{/if}

				<div class="searchdesc">{if $prefs.search_parsed_snippet == 'y'}{$results[search].data}{else}{$results[search].data|strip_tags|escape}{/if}</div>
				{if $prefs.feature_search_show_last_modification eq 'y'}
					<div class="searchdate">{tr}Last modification:{/tr} {$results[search].lastModif|tiki_long_datetime}</div>
				{/if}
			</li>
		{sectionelse}
<li>{tr}No pages matched the search criteria{/tr} </li>		{/section}
	</ul>
	{pagination_links cant=$cant step=$maxRecords offset=$offset _keepall=true}{/pagination_links}
{/if}
