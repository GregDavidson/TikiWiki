{* $Id: $ *}
<span class="blogactions">
	{if $prefs.javascript_enabled != 'y'}
		{$js = 'n'}
	{else}
		{$js = 'y'}
	{/if}
	<div class="btn-group">
		{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
		<a class="btn btn-link" data-toggle="dropdown" data-hover="dropdown" href="#">
			{icon name='menu-extra'}
		</a>
		<ul class="dropdown-menu">
			<li class="dropdown-title">
				{tr}Blog Actions{/tr}
			</li>
			<li class="divider"></li>
			{if $tiki_p_blog_post eq "y"}
				{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y" or $public eq "y"}
					<li>
						<a href="tiki-blog_post.php?blogId={$blogId}">
							{icon name='post'} {tr}Post{/tr}
						</a>
					</li>
				{/if}
			{/if}
			{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y"}
				<li>
					<a href="tiki-edit_blog.php?blogId={$blogId}">
						{icon name='edit'} {tr}Edit{/tr}
					</a>
				</li>
				{if $allow_comments eq 'y'}
					<li>
						<a href='tiki-list_comments.php?types_section=blogs&amp;blogId={$blogId}'>
							{icon name='comments'} {tr}Comments{/tr}
						</a>
					</li>
				{/if}
			{/if}
			{if $user and $prefs.feature_user_watches eq 'y'}
				<li>
					{if $user_watching_blog eq 'n'}
						<a href="tiki-view_blog.php?blogId={$blogId}&amp;watch_event=blog_post&amp;watch_object={$blogId}&amp;watch_action=add">
							{icon name='watch'} {tr}Monitor{/tr}
						</a>
					{else}
						<a href="tiki-view_blog.php?blogId={$blogId}&amp;watch_event=blog_post&amp;watch_object={$blogId}&amp;watch_action=remove">
							{icon name='stop_watching'} {tr}Stop monitoring{/tr}
						</a>
					{/if}
				</li>
			{/if}
			{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
				<li>
					<a href="tiki-object_watches.php?objectId={$blogId|escape:"url"}&amp;watch_event=blog_post&amp;objectType=blog&amp;objectName={$title|escape:"url"}&amp;objectHref={'tiki-view_blog.php?blogId='|cat:$blogId|escape:"url"}">
						{icon name="watch-group"} {tr}Group Monitor{/tr}
					</a>
				</li>
			{/if}
			{if $prefs.feed_blog eq "y"}
				<li>
					<a href="tiki-blog_rss.php?blogId={$blogId}">
						{icon name='rss'} {tr}RSS{/tr}
					</a>
				</li>
			{/if}
		</ul>
		{if $js == 'n'}</li></ul>{/if}
		{if $user and $prefs.feature_user_watches eq 'y'}
			{if $category_watched eq 'y'}
				<div>
					{tr}Watched by categories:{/tr}
					{section name=i loop=$watching_categories}
						<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}" class="btn btn-default btn-small">{$watching_categories[i].name|escape}</a>&nbsp;
					{/section}
				</div>
			{/if}
		{/if}
	</div>
</span>