{* $Id: error_tracker.tpl 49461 2014-01-20 11:21:34Z chibaguy $ *}
{if $prefs.feature_trackers ne 'y'}
	<span class="alert-warning">{tr}This feature is disabled{/tr}</span>
{else}
	<span class="alert-warning">{tr}Missing or incorrect trackerId parameter for the plugin.{/tr}</span>
	{if $tiki_p_view_trackers eq 'y'}{button href="tiki-list_trackers.php" _text="{tr}List Trackers{/tr}"}{/if}
{/if}
