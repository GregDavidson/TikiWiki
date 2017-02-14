<!DOCTYPE html>
<html>
	<head>
		{include file="header.tpl"}
	</head>
	<body class="file_gallery slideshow">
		{jq}
	$("a[data-box*='shadowbox']").colorbox({
		rel: function(){
			return $(this).attr('data-box');
		},
		open: true,
		slideshowStart: "{tr}Start the Slideshow{/tr}",
		slideshowStop: "{tr}Pause the Slideshow{/tr}",
		{{if ($slideshow_noclose)}
			slideshowAuto: false,
			overlayClose: false,
			close: ''
		{/if}}
	});
		{/jq}

{if (!$slideshow_noclose)}
		<div style="position:fixed; top:5px; right:10px;">{button href='#' _onclick='javascript:window.close(); return false' _text="{tr}Close{/tr}"}</div>
{/if}

		<div id="images" class="hidden">
			{if $cant eq 0}
				<p style="font-style:italic; margin-left:10px">
					{tr}There are no images to display{/tr}
				</p>
			{else}
				<ul>
					{foreach from=$file key=i item=f name=files}
						<li>
							<a id="id{$i}" href="tiki-download_file.php?preview&fileId={$f.id}"
								data-box="shadowbox[slideshow];type=img"
								{if ($caption)}
									{if ($caption eq 'd')}
										{assign var="itemcaption" value="{$f.description}"}
									{elseif ($caption eq 'n')}
										{assign var="itemcaption" value="{$f.name}"}
									{elseif ($caption eq 'f')}
										{assign var="itemcaption" value="{$f.filename}"}
									{/if}
									title="{$itemcaption}">{$itemcaption}
								{/if}
							>
							</a>
						</li>
					{/foreach}
				</ul>
			{/if}
		</div>

<!-- Put JS at the end -->
{if $headerlib}
	{$headerlib->output_js_files()}
	{$headerlib->output_js()}
{/if}

	</body>
</html>
