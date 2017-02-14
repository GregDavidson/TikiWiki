{* $Id: blog_post_author_info.tpl 58675 2016-05-23 17:50:57Z jonnybradley $ *}
<div class="author_info">
	{if $blog_data.show_avatar eq 'y'}
		{$post_info.avatar}
	{/if}
	{if $blog_data.use_author eq 'y'}
		{icon name="user" iclass="tips" ititle=":{tr}Published By{/tr}"}
		{$post_info.user|userlink}
	{/if}
	{if $blog_data.add_date eq 'y'}
		<span style="font-size: 80%">{icon name="clock-o" iclass="tips" ititle=":{tr}Publish Date{/tr}"}</span> {$post_info.created|tiki_long_date}
	{/if}
</div>
