{* $Id: tiki-stats.tpl 57565 2016-02-22 10:56:40Z jonnybradley $ *}

{title help="Stats"}{tr}Stats{/tr}{/title}

{tabset}
	{tab name="{tr}Tiki Statistics{/tr}"}
		<div class="t_navbar margin-bottom-md">
			{button href="site_stats" _type="link" class="btn btn-link" _icon_name="home" _text="{tr}Site{/tr}"}
			{if $wiki_stats}
				{button href="#wiki_stats" _type="link" class="btn btn-link" _icon_name="file-text" _text="{tr}Wiki{/tr}"}
			{/if}
			{if $igal_stats}
				{button href="#igal_stats" _type="link" class="btn btn-link" _icon_name="file-image-o" _text="{tr}Image galleries{/tr}"}
			{/if}
			{if $fgal_stats}
				{button href="#fgal_stats" _type="link" class="btn btn-link" _icon_name="folder-open-o" _text="{tr}File Galleries{/tr}"}
			{/if}
			{if $cms_stats}
				{button href="#cms_stats" _type="link" class="btn btn-link" _icon_name="newspaper-o" _text="{tr}Articles{/tr}"}
			{/if}
			{if $forum_stats}
				{button href="#forum_stats" _type="link" class="btn btn-link" _icon_name="comments-o" _text="{tr}Forums{/tr}"}
			{/if}
			{if $blog_stats}
				{button href="#blog_stats" _type="link" class="btn btn-link" _icon_name="bold" _text="{tr}Blogs{/tr}"}
			{/if}
			{if $poll_stats}
				{button href="#poll_stats" _type="link" class="btn btn-link" _icon_name="task" _text="{tr}Polls{/tr}"}
			{/if}
			{if $faq_stats}
				{button href="#faq_stats" _type="link" class="btn btn-link" _icon_name="question" _text="{tr}FAQs{/tr}"}
			{/if}
			{if $user_stats}
				{button href="#user_stats" _type="link" class="btn btn-link" _icon_name="users" _text="{tr}User{/tr}"}
			{/if}
			{if $quiz_stats}
				{button href="#quiz_stats" _type="link" class="btn btn-link" _icon_name="list-ol" _text="{tr}Quizzes{/tr}"}
			{/if}
			{if $prefs.feature_referer_stats eq 'y' and $tiki_p_view_referer_stats eq 'y'}
				{button href="tiki-referer_stats.php" _type="link" class="btn btn-link" _icon_name="link" _text="{tr}Referer stats{/tr}"}
			{/if}
			{if $best_objects_stats}
				{button href="#best_objects_stats" _type="link" class="btn btn-link" _icon_name="sort-numeric-asc" _text="{tr}Most viewed objects{/tr}"}
			{/if}
			{if $best_objects_stats}
				{button href="#best_objects_stats_lastweek" _type="link" class="btn btn-link" _icon_name="sort-numeric-asc" _text="{tr}Most viewed objects in the last 7 days{/tr}"}
			{/if}
		</div>

		<h2 id="site_stats">{tr}Site Stats{/tr}</h2>
		{cycle values="odd,even" print=false advance=false}
		<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<td>{tr}Date of first pageview{/tr}</td>
					<td style="text-align:right;">{if $site_stats.started == 'No pageviews yet'}{$site_stats.started}{else}{$site_stats.started|tiki_long_date}{/if}</td>
				</tr>
				<tr>
					<td>{tr}Days since first pageview{/tr}</td>
					<td style="text-align:right;">{$site_stats.days}</td>
				</tr>
				<tr>
					<td>{tr}Total pageviews{/tr}</td>
					<td style="text-align:right;">{$site_stats.pageviews}</td>
				</tr>
				<tr>
					<td>{tr}Average pageviews per day{/tr} ({tr}pvs{/tr})</td>
					<td style="text-align:right;">{$site_stats.ppd}</td>
				</tr>
				{if $site_stats.bestdesc}
					<tr>
						<td>{$site_stats.bestdesc}</td>
						<td style="text-align:right;">{$site_stats.bestday}</td>
					</tr>
				{/if}
				{if $site_stats.worstdesc}
					<tr>
						<td>{$site_stats.worstdesc}</td><td style="text-align:right;">{$site_stats.worstday}</td>
					</tr>
				{/if}
			</table>
		</div>

		{if $wiki_stats}
			<h2 id="wiki_stats">{tr}Wiki Stats{/tr}</h2>
			{cycle values="odd,even" print=false advance=false}
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}Wiki Pages{/tr}</td>
						<td style="text-align:right;">{$wiki_stats.pages}</td>
					</tr>
					<tr>
						<td>{tr}Size of Wiki Pages{/tr}</td>
						<td style="text-align:right;">{$wiki_stats.size} {tr}Mb{/tr}</td>
					</tr>
					<tr>
						<td>{tr}Average page length{/tr}</td>
						<td style="text-align:right;">{$wiki_stats.bpp|string_format:"%.2f"} {tr}bytes{/tr}</td>
					</tr>
					<tr>
						<td>{tr}Versions{/tr}</td>
						<td style="text-align:right;">{$wiki_stats.versions}</td>
					</tr>
					<tr>
						<td>{tr}Average versions per page{/tr}</td>
						<td style="text-align:right;">{$wiki_stats.vpp|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Visits to wiki pages{/tr}</td>
						<td style="text-align:right;">{$wiki_stats.visits}</td>
					</tr>
					<tr>
						<td>{tr}Orphan pages{/tr}</td>
						<td style="text-align:right;">{$wiki_stats.orphan}</td>
					</tr>
					<tr>
						<td>{tr}Average links per page{/tr}</td>
						<td style="text-align:right;">{$wiki_stats.lpp|string_format:"%.2f"}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $igal_stats}
			<h2 id="igal_stats">{tr}Image galleries Stats{/tr}</h2>
			{cycle values="odd,even" print=false advance=false}
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}Galleries{/tr}</td>
						<td style="text-align:right;">{$igal_stats.galleries}</td>
					</tr>
					<tr>
						<td>{tr}Images{/tr}</td>
						<td style="text-align:right;">{$igal_stats.images}</td>
					</tr>
					<tr>
						<td>{tr}Average images per gallery{/tr}</td>
						<td style="text-align:right;">{$igal_stats.ipg|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Total size of images{/tr}</td>
						<td style="text-align:right;">{$igal_stats.size} {tr}Mb{/tr}</td>
					</tr>
					<tr>
						<td>{tr}Average image size{/tr}</td>
						<td style="text-align:right;">{$igal_stats.bpi|string_format:"%.2f"} {tr}bytes{/tr}</td>
					</tr>
					<tr>
						<td>{tr}Visits to image galleries{/tr}</td>
						<td style="text-align:right;">{$igal_stats.visits|@default:'0'}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $fgal_stats}
			<h2 id="fgal_stats">{tr}File galleries Stats{/tr}</h2>
			{cycle values="odd,even" print=false advance=false}
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}Galleries{/tr}</td>
						<td style="text-align:right;">{$fgal_stats.galleries}</td>
					</tr>
					<tr>
						<td>{tr}Files{/tr}</td>
						<td style="text-align:right;">{$fgal_stats.files}</td>
					</tr>
					<tr>
						<td>{tr}Average files per gallery{/tr}</td>
						<td style="text-align:right;">{$fgal_stats.fpg|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Total size of files{/tr}</td>
						<td style="text-align:right;">{$fgal_stats.size} {tr}Mb{/tr}</td>
					</tr>
					<tr>
						<td>{tr}Average file size{/tr}</td>
						<td style="text-align:right;">{$fgal_stats.bpf|string_format:"%.2f"} {tr}Mb{/tr}</td>
					</tr>
					<tr>
						<td>{tr}Visits to file galleries{/tr}</td>
						<td style="text-align:right;">{$fgal_stats.visits|@default:'0'}</td>
					</tr>
					<tr>
						<td>{tr}Downloads{/tr}</td>
						<td style="text-align:right;">{$fgal_stats.hits|@default:'0'}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $cms_stats}
			<h2 id="cms_stats">{tr}Articles Stats{/tr}</h2>
			{cycle values="odd,even" print=false advance=false}
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}Articles{/tr}</td>
						<td style="text-align:right;">{$cms_stats.articles}</td>
					</tr>
					<tr>
						<td>{tr}Total reads{/tr}</td>
						<td style="text-align:right;">{$cms_stats.reads|@default:'0'}</td>
					</tr>
					<tr>
						<td>{tr}Average reads per article{/tr}</td>
						<td style="text-align:right;">{$cms_stats.rpa|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Total articles size{/tr}</td>
						<td style="text-align:right;">{$cms_stats.size} {tr}bytes{/tr}</td>
					</tr>
					<tr>
						<td>{tr}Average article size{/tr}</td>
						<td style="text-align:right;">{$cms_stats.bpa|string_format:"%.2f"} {tr}bytes{/tr}</td>
					</tr>
					<tr>
						<td>{tr}Topics{/tr}</td>
						<td style="text-align:right;">{$cms_stats.topics}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $forum_stats}
			{cycle values="odd,even" print=false advance=false}
			<h2 id="forum_stats">{tr}Forum Stats{/tr}</h2>
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}Forums{/tr}</td>
						<td style="text-align:right;">{$forum_stats.forums}</td>
					</tr>
					<tr>
						<td>{tr}Total topics{/tr}</td>
						<td style="text-align:right;">{$forum_stats.topics}</td>
					</tr>
					<tr>
						<td>{tr}Average topics per forums{/tr}</td>
						<td style="text-align:right;">{$forum_stats.tpf|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Total replies{/tr}</td>
						<td style="text-align:right;">{$forum_stats.threads}</td>
					</tr>
					<tr>
						<td>{tr}Average number of replies per topic{/tr}</td>
						<td style="text-align:right;">{$forum_stats.tpt|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Visits to forums{/tr}</td>
						<td style="text-align:right;">{$forum_stats.visits|@default:'0'}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $blog_stats}
			<h2 id="blog_stats">{tr}Blog Stats{/tr}</h2>
			{cycle values="odd,even" print=false advance=false}
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}Weblogs{/tr}</td>
						<td style="text-align:right;">{$blog_stats.blogs}</td>
					</tr>
					<tr>
						<td>{tr}Total posts{/tr}</td>
						<td style="text-align:right;">{$blog_stats.posts}</td>
					</tr>
					<tr>
						<td>{tr}Average posts per weblog{/tr}</td>
						<td style="text-align:right;">{$blog_stats.ppb|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Total size of blog posts{/tr}</td>
						<td style="text-align:right;">{$blog_stats.size|@default:'0'}</td>
					</tr>
					<tr>
						<td>{tr}Average posts size{/tr}</td>
						<td style="text-align:right;">{$blog_stats.bpp|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Visits to weblogs{/tr}</td>
						<td style="text-align:right;">{$blog_stats.visits|@default:'0'}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $poll_stats}
			<h2 id="poll_stats">{tr}Poll Stats{/tr}</h2>
			{cycle values="odd,even" print=false advance=false}
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}Polls{/tr}</td>
						<td style="text-align:right;">{$poll_stats.polls}</td>
					</tr>
					<tr>
						<td>{tr}Total votes{/tr}</td>
						<td style="text-align:right;">{$poll_stats.votes|@default:'0'}</td>
					</tr>
					<tr>
						<td>{tr}Average votes per poll{/tr}</td>
						<td style="text-align:right;">{$poll_stats.vpp|string_format:"%.2f"}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $faq_stats}
			<h2 id="faq_stats">{tr}FAQ Stats{/tr}</h2>
			{cycle values="odd,even" print=false advance=false}
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}FAQs{/tr}</td>
						<td style="text-align:right;">{$faq_stats.faqs}</td>
					</tr>
					<tr>
						<td>{tr}Total questions{/tr}</td>
						<td style="text-align:right;">{$faq_stats.questions}</td>
					</tr>
					<tr>
						<td>{tr}Average questions per FAQ{/tr}</td>
						<td style="text-align:right;">{$faq_stats.qpf|string_format:"%.2f"}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $user_stats}
			<h2 id="user_stats">{tr}User Stats{/tr}</h2>
			{cycle values="odd,even" print=false advance=false}
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}Users{/tr}</td>
						<td style="text-align:right;">{$user_stats.users}</td>
					</tr>
					<tr>
						<td>{tr}My Bookmarks{/tr}</td>
						<td style="text-align:right;">{$user_stats.bookmarks}</td>
					</tr>
					<tr>
						<td>{tr}Average bookmarks per user{/tr}</td>
						<td style="text-align:right;">{$user_stats.bpu|string_format:"%.2f"}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $quiz_stats}
			<h2 id="quiz_stats">{tr}Quiz Stats{/tr}</h2>
			{cycle values="odd,even" print=false advance=false}
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<td>{tr}Quizzes{/tr}</td>
						<td style="text-align:right;">{$quiz_stats.quizzes}</td>
					</tr>
					<tr>
						<td>{tr}Questions{/tr}</td>
						<td style="text-align:right;">{$quiz_stats.questions}</td>
					</tr>
					<tr>
						<td>{tr}Average questions per quiz{/tr}</td>
						<td style="text-align:right;">{$quiz_stats.qpq|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Quizzes taken{/tr}</td>
						<td style="text-align:right;">{$quiz_stats.visits|@default:'0'}</td>
					</tr>
					<tr>
						<td>{tr}Average quiz score{/tr}</td>
						<td style="text-align:right;">{$quiz_stats.avg|string_format:"%.2f"}</td>
					</tr>
					<tr>
						<td>{tr}Average time per quiz{/tr}</td>
						<td style="text-align:right;">{$quiz_stats.avgtime|string_format:"%.2f"} {tr}secs{/tr}</td>
					</tr>
				</table>
			</div>
		{/if}

		{if $best_objects_stats_between}
			<h2 id="best_objects_stats_between">{tr}Most viewed objects in period{/tr}</h2>
			<form method="post" action="tiki-stats.php">
				{html_select_date time=$startDate prefix="startDate_" start_year=$start_year end_year=$end_year day_value_format="%02d" field_order=$prefs.display_field_order}
				&rarr; {html_select_date time=$endDate prefix="endDate_" start_year=$start_year end_year=$end_year day_value_format="%02d" field_order=$prefs.display_field_order}
				<input type="submit" class="btn btn-default btn-sm" name="modify" value="{tr}Filter{/tr}">
			</form><br>
			<div class="table-responsive">
				<table class="table table-striped normal">
					<tr>
						<th>{tr}Object{/tr}</th>
						<th>{tr}Section{/tr}</th>
						<th>{tr}Hits{/tr}</th>
					</tr>
					{cycle values="odd,even" print=false advance=false}
					{section name=i loop=$best_objects_stats_between}
						<tr>
							<td class="text">{$best_objects_stats_between[i]->object|escape}</td>
							<td class="text">{tr}{$best_objects_stats_between[i]->type}{/tr}</td>
							<td class="integer">{$best_objects_stats_between[i]->hits}</td>
						</tr>
					{/section}
				</table>
			</div>
		{/if}

		{if $best_objects_stats_lastweek}
			<h2 id="best_objects_stats_lastweek">{tr}Most viewed objects in the last 7 days{/tr}</h2>

			<div class="table-responsive">
				<table class="table table-striped normal">
					<tr>
						<th>{tr}Object{/tr}</th>
						<th>{tr}Section{/tr}</th>
						<th>{tr}Hits{/tr}</th>
					</tr>
					{cycle values="odd,even" print=false advance=false}
					{section name=i loop=$best_objects_stats_lastweek}
						<tr>
							<td class="text">{$best_objects_stats_lastweek[i]->object|escape}</td>
							<td class="text">{tr}{$best_objects_stats_lastweek[i]->type}{/tr}</td>
							<td class="integer">{$best_objects_stats_lastweek[i]->hits}</td>
						</tr>
					{/section}
				</table>
			</div>
		{/if}

		<a id="charts" href="tiki-stats.php?chart=usage#charts" class="link">{tr}Usage chart{/tr}</a>

		{if $usage_chart eq 'y'}
			<div align="center">
				<img src="tiki-usage_chart.php" alt="{tr}Usage chart image{/tr}">
			</div>
			<br>
			<div align="center">
				<img src="tiki-usage_chart.php?type=daily" alt="{tr}Daily Usage{/tr}">
			</div>
		{/if}
	{/tab}

	{if $prefs.site_piwik_analytics_server_url || $prefs.site_piwik_site_id}
		{tab name="{tr}Piwik Analytics{/tr}"}
			<h2 id="site_stats">{tr}Piwik Analytics Dashboard{/tr}</h2>
			{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Defaul dashboard is set to Piwik default dashboard. You can customize the modules to be displayed using the Dashboard setting; Create new dashboard.{/tr}{/remarksbox}
			<div id="dashboard">
				{wikiplugin _name=piwik moduleToWidgetize="Dashboard,index" period="month" _height="880" _scrolling="yes"}{/wikiplugin}
			</div>
		{/tab}
	{/if}

	{if $prefs.site_google_credentials}
		{tab name="{tr}Google Analytics{/tr}"}
			<h2 id="site_stats">{tr}Google Analytics{/tr}</h2>
			{remarksbox}Google Analytics dashboard to do...{/remarksbox}
			{*{wikiplugin _name=googleanalytics account=$prefs.site_google_analytics_account}{/wikiplugin}*}
		{/tab}
	{/if}

{/tabset}
