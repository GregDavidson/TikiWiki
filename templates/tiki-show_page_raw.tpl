{if $prefs.feature_page_title eq 'y' && !$is_slideshow eq 'y'}<h1><a href="tiki-backlinks.php?page={$page}" title="{tr}backlinks to{/tr} {$page}">{$page}</a></h1>{/if}
<div class="wikitext">{$parsed}</div>
{if !isset($smarty.request.clean)}
	{if isset($prefs.wiki_authors_style) && $prefs.wiki_authors_style eq 'business'}
		<footer class="editdate">
			{tr}Last edited by{/tr} {$lastUser}
			{section name=author loop=$contributors}
				{if $smarty.section.author.first}, {tr}based on work by{/tr}
				{else}
					{if !$smarty.section.author.last},
					{else} {tr}and{/tr}
					{/if}
				{/if}
				{$contributors[author]}
			{/section}.<br>
			{tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
		</footer>
	{elseif isset($prefs.wiki_authors_style) && $prefs.wiki_authors_style eq 'collaborative'}
		<footer class="editdate">
			{tr}Contributors to this page:{/tr} {$lastUser}
			{section name=author loop=$contributors}
			{if !$smarty.section.author.last},
			{else} {tr}and{/tr}
			{/if}
			{$contributors[author]}
			{/section}.<br>
			{tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
		</footer>
	{elseif isset($prefs.wiki_authors_style) && $prefs.wiki_authors_style eq 'none'}
	{else}
		<footer class="editdate">
			{tr}Created by:{/tr} {$creator}
			{tr}Last Modification:{/tr} {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}
		</footer>
	{/if}

	{if (!$prefs.page_bar_position or $prefs.page_bar_position eq 'bottom' or $prefs.page_bar_position eq 'both') and $machine_translate_to_lang == ''}
		{include file='tiki-page_bar.tpl'}
	{/if}

	{if $is_slideshow eq 'y'}
		<div id="tiki_slideshow_buttons" style="display: none;">
			<a href="#" onclick="$.s5.first(); return false;" title="{tr}First Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/resultset_first.png" alt="{tr}First Slide{/tr}">
			</a>
			<a href="#" onclick="$.s5.prev(); return false;" title="{tr}Previous Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/resultset_previous.png" alt="{tr}Previous Slide{/tr}">
			</a>
			<a href="#" onclick="$.s5.goLI('prev'); return false;" title="{tr}Previous Point{/tr}">
				<img src="img/icons/resultset_up.png" alt="{tr}Previous Point{/tr}">
			</a>
			<a href="#" onclick="$.s5.goLI('next'); return false;" title="{tr}Next Point{/tr}">
				<img src="img/icons/resultset_down.png" alt="{tr}Next Point{/tr}">
			</a>
			<a href="#" onclick="$.s5.next(); return false;" title="{tr}Next Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/resultset_next.png" alt="{tr}Next Slide{/tr}">
			</a>
			<a href="#" onclick="$.s5.last(); return false;" title="{tr}Last Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/resultset_last.png" alt="{tr}Last Slide{/tr}">
			</a>
			<a href="#" onclick="$.s5.listSlideTitles(this); return false;" title="{tr}Jump To Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/layers.png" alt="{tr}Jump To Slide{/tr}">
			</a>
			<a href="#" onclick="$.s5.play(); return false;" title="{tr}Play{/tr}">
				<img src="vendor/jquery/jquery-s5/images/control_play_blue.png" alt="{tr}Play{/tr}">
			</a>
			<a href="#" onclick="$.s5.pause(); return false;" title="{tr}Pause{/tr}">
				<img src="vendor/jquery/jquery-s5/images/control_pause_blue.png" alt="{tr}Pause{/tr}">
			</a>
			<a href="#" onclick="$.s5.stop(); return false;" title="{tr}Stop{/tr}">
				<img src="vendor/jquery/jquery-s5/images/control_stop_blue.png" alt="{tr}Stop{/tr}">
			</a>
			<a href="#" onclick="$.s5.getNote(); return false;" title="{tr}Notes{/tr}">
				<img src="vendor/jquery/jquery-s5/images/note.png" alt="{tr}Notes{/tr}">
			</a>
			<a href="#" onclick="$.s5.toggleLoop(); return false;" title="{tr}Toggle Loop{/tr}">
				<img src="vendor/jquery/jquery-s5/images/arrow_rotate_clockwise.png" alt="{tr}Toggle Loop{/tr}">
			</a>
			{if $prefs.feature_slideshow_pdfexport eq 'y'}
				<a href="#" onclick="window.exportPdf(this);" title="{tr}Export to PDF{/tr}">
					<img alt="{tr}Export to PDF{/tr}" src="vendor/jquery/jquery-s5/images/page_go.png">
				</a>
			{/if}
			<a href="{$page|sefurl}" title="{tr}Exit{/tr}">
				<img src="img/icons/cross.png" alt="{tr}Exit{/tr}">
			</a>
			<select class="tiki-slideshow-theme">
				<option value="">{tr}Change Theme{/tr}</option>
				<option value="none">{tr}none{/tr} ({tr}styled by current theme{/tr})</option>
				<option value="default">{tr}Site default{/tr} ({tr}by jQuery-UI choice{/tr})</option>
				<option value="ui-lightness">ui-lightness</option>
				<option value="ui-darkness">ui-darkness</option>
				<option value="smoothness">smoothness</option>
				<option value="start">start</option>
				<option value="redmond">redmond</option>
				<option value="sunny">sunny</option>
				<option value="overcast">overcast</option>
				<option value="le-frog">le-frog</option>
				<option value="flick">flick</option>
				<option value="pepper-grinder">pepper-grinder</option>
				<option value="eggplant">eggplant</option>
				<option value="dark-hive">dark-hive</option>
				<option value="cupertino">cupertino</option>
				<option value="south-street">south-street</option>
				<option value="blitzer">blitzer</option>
				<option value="humanity">humanity</option>
				<option value="hot-sneaks">hot-sneaks</option>
				<option value="excite-bike">excite-bike</option>
				<option value="vader">vader</option>
				<option value="dot-luv">dot-luv</option>
				<option value="mint-choc">mint-choc</option>
				<option value="black-tie">black-tie</option>
				<option value="trontastic">trontastic</option>
				<option value="swanky-purse">swanky-purse</option>
			</select>
		</div>
		<div id="tiki_slideshowNote_buttons" style="display: none;">
			<a href="#" onclick="window.opener.$.s5.first(); return false;" title="{tr}First Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/resultset_first.png" alt="{tr}First Slide{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.prev(); return false;" title="{tr}Previous Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/resultset_previous.png" alt="{tr}Previous Slide{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.goLI('prev'); return false;" title="{tr}Previous Point{/tr}">
				<img src="img/icons/resultset_up.png" alt="{tr}Previous Point{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.goLI('next'); return false;" title="{tr}Next Point{/tr}">
				<img src="img/icons/resultset_down.png" alt="{tr}Next Point{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.next(); return false;" title="{tr}Next Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/resultset_next.png" alt="{tr}Next Slide{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.last(); return false;" title="{tr}Last Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/resultset_last.png" alt="{tr}Last Slide{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.listSlideTitles(this); return false;" title="{tr}Jump To Slide{/tr}">
				<img src="vendor/jquery/jquery-s5/images/layers.png" alt="{tr}Jump To Slide{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.play(); return false;" title="{tr}Play{/tr}">
				<img src="vendor/jquery/jquery-s5/images/control_play_blue.png" alt="{tr}Play{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.pause(); return false;" title="{tr}Pause{/tr}">
				<img src="vendor/jquery/jquery-s5/images/control_pause_blue.png" alt="{tr}Pause{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.stop(); return false;" title="{tr}Stop{/tr}">
				<img src="vendor/jquery/jquery-s5/images/control_stop_blue.png" alt="{tr}Stop{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.getNote(); return false;" title="{tr}Notes{/tr}">
				<img src="vendor/jquery/jquery-s5/images/note.png" alt="{tr}Notes{/tr}">
			</a>
			<a href="#" onclick="window.opener.$.s5.toggleLoop(); return false;" title="{tr}Toggle Loop{/tr}">
				<img src="vendor/jquery/jquery-s5/images/arrow_rotate_clockwise.png" alt="{tr}Toggle Loop{/tr}">
			</a>
			{if $prefs.feature_slideshow_pdfexport eq 'y'}
				<a href="#" onclick="window.exportPdf(this);" title="{tr}Export to PDF{/tr}">
					<img alt="{tr}Export to PDF{/tr}" src="vendor/jquery/jquery-s5/images/page_go.png">
				</a>
			{/if}
			<a href="{$page|sefurl}" title="{tr}Exit{/tr}">
				<img src="img/icons/cross.png" alt="{tr}Exit{/tr}">
			</a>
			<select class="tiki-slideshow-theme">
				<option value="">{tr}Change Theme{/tr}</option>
				<option value="none">{tr}none{/tr} ({tr}styled by current theme{/tr})</option>
				<option value="default">{tr}Site default{/tr} ({tr}by jQuery-UI choice{/tr})</option>
				<option value="ui-lightness">ui-lightness</option>
				<option value="ui-darkness">ui-darkness</option>
				<option value="smoothness">smoothness</option>
				<option value="start">start</option>
				<option value="redmond">redmond</option>
				<option value="sunny">sunny</option>
				<option value="overcast">overcast</option>
				<option value="le-frog">le-frog</option>
				<option value="flick">flick</option>
				<option value="pepper-grinder">pepper-grinder</option>
				<option value="eggplant">eggplant</option>
				<option value="dark-hive">dark-hive</option>
				<option value="cupertino">cupertino</option>
				<option value="south-street">south-street</option>
				<option value="blitzer">blitzer</option>
				<option value="humanity">humanity</option>
				<option value="hot-sneaks">hot-sneaks</option>
				<option value="excite-bike">excite-bike</option>
				<option value="vader">vader</option>
				<option value="dot-luv">dot-luv</option>
				<option value="mint-choc">mint-choc</option>
				<option value="black-tie">black-tie</option>
				<option value="trontastic">trontastic</option>
				<option value="swanky-purse">swanky-purse</option>
			</select>
		</div>
		{if $prefs.feature_slideshow_pdfexport eq 'y'}
			{jq}

				window.exportPdf = function() {
					var inputs = $('<div />');
					var buttons = {};

					inputs.append(
						tr('Layout: ') +
						'<select id="layout">' +
							'<option value="landscape">' + tr('Landscape') + '</option>' +
							'<option value="portrait">' + tr('Portrait') + '</option>' +
						'</select>'
					);

					inputs.append('<br>');

					inputs.append(
						tr('Font Size: ') +
						'<select id="fontsize">' +
							'<option value="small">' + tr('Small') + '</option>' +
							'<option value="medium">' + tr('Medium') + '</option>' +
							'<option value="large">' + tr('Large') + '</option>' +
							'<option value="x-large">' + tr('X-Large') + '</option>' +
						'</select>'
					);

					buttons[tr("Ok")] = function() {
						var s5;

						if (window.opener) {
							s5 = window.opener.$.s5;
						} else {
							s5 = $.s5;
						}

						$body.prepend(
							'<style id="tempStyle">' +
							'body *{' +
							'font-size:' + escape($('#fontsize').val()) + ' ! important;' +
							'}' +
							'</style>'
						);

						s5.exportPDF('tiki-slideshow.php?page={{$page|escape:'url'}}&pdf&' + $('#layout').val() + '&fontsize=' + $('#fontsize').val(), tr("PDF Loading... This can take a minute or two."));
						inputs.dialog('close');
						$body.find('#tempStyle').remove();
					};


					inputs.dialog({
						title: tr("Configure"),
						buttons: buttons,
						modal: true
					});
				};
			{/jq}
		{/if}
	{/if}
{/if}
