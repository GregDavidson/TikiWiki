{* $Id: blog_post_related_content.tpl 53176 2014-11-21 23:12:16Z jyhem $ *}
{if isset($post_info.related_posts) && !empty($post_info.related_posts)}
	<div class="related_posts">
		<h4>{tr}Related content:{/tr}</h4>
		<ul>
			{foreach from=$post_info.related_posts item=related}
				<li>{self_link postId=$related.postId}{$related.name}{/self_link}</li>
			{/foreach}
		</ul>
	</div>
{/if}
