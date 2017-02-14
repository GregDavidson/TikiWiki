{* $Id: layout_view.tpl 48366 2013-11-08 16:12:24Z lphuberdeau $ *}<!DOCTYPE html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}"{if !empty($page_id)} id="page_{$page_id}"{/if}>
<head>
    {include file='header.tpl'}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body{html_body_attributes class="navbar-padding"}>
{$cookie_consent_html}

{include file="layout_fullscreen_check.tpl"}

{if $prefs.feature_ajax eq 'y'}
    {include file='tiki-ajax_header.tpl'}
{/if}
<div class="middle_outer" id="middle_outer">
{if $smarty.session.fullscreen ne 'y'}
    <div class="fixed-topbar"></div>
{/if}
    <div class="container{if $smarty.session.fullscreen eq 'y'}-fluid{/if} clearfix middle" id="middle">
{if $smarty.session.fullscreen ne 'y'}
        <div class="topbar_wrapper">
            <div class="topbar row" id="topbar">
                {modulelist zone=topbar}
            </div>
        </div>
{/if}

        <div class="row" id="row-middle">
            {if zone_is_empty('left') and zone_is_empty('right')}
                <div class="col-md-12 col1" id="col1">
                    {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                        {modulelist zone=pagetop}
                    {/if}
                    {feedback}
                    {block name=quicknav}{/block}
                    {block name=title}{/block}
                    {block name=navigation}{/block}
                    {block name=content}{/block}
                    {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                        {modulelist zone=pagebottom}
                    {/if}
                </div>
            {elseif zone_is_empty('left') or $prefs.feature_left_column eq 'n'}
                <div class="col-md-12 text-right">
                    {if $prefs.feature_right_column eq 'user'}
                        {$icon_name = (not empty($smarty.cookies.hide_zone_right)) ? 'toggle-left' : 'toggle-right'}
                        {icon name=$icon_name class='toggle_zone right' href='#' title='{tr}Toggle right modules{/tr}'}
                    {/if}
                </div>
                <div class="col-md-9 col1" id="col1">
                    {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                        {modulelist zone=pagetop}
                    {/if}
                    {feedback}
                    {block name=quicknav}{/block}
                    {block name=title}{/block}
                    {block name=navigation}{/block}
                    {block name=content}{/block}
                    {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                        {modulelist zone=pagebottom}
                    {/if}
                </div>
                <div class="col-md-3" id="col3">
                    {modulelist zone=right}
                </div>
            {elseif zone_is_empty('right') or $prefs.feature_right_column eq 'n'}
                <div class="col-md-12 text-left">
                    {if $prefs.feature_left_column eq 'user'}
                        {$icon_name = (not empty($smarty.cookies.hide_zone_left)) ? 'toggle-right' : 'toggle-left'}
                        {icon name=$icon_name class='toggle_zone left' href='#' title='{tr}Toggle left modules{/tr}'}
                    {/if}
                </div>
                <div class="col-md-9 col-md-push-3 col1" id="col1">
                    {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                        {modulelist zone=pagetop}
                    {/if}
                    {feedback}
                    {block name=quicknav}{/block}
                    {block name=title}{/block}
                    {block name=navigation}{/block}
                    {block name=content}{/block}
                    {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                        {modulelist zone=pagebottom}
                    {/if}
                </div>
                <div class="col-md-3 col-md-pull-9" id="col2">
                    {modulelist zone=left}
                </div>
            {else}
                <div class="col-md-6 text-left">
                    {if $prefs.feature_left_column eq 'user'}
                        {$icon_name = (not empty($smarty.cookies.hide_zone_left)) ? 'toggle-right' : 'toggle-left'}
                        {icon name=$icon_name class='toggle_zone left' href='#' title='{tr}Toggle left modules{/tr}'}
                    {/if}
                </div>
                <div class="col-md-6 text-right">
                    {if $prefs.feature_right_column eq 'user'}
                        {$icon_name = (not empty($smarty.cookies.hide_zone_right)) ? 'toggle-left' : 'toggle-right'}
                        {icon name=$icon_name class='toggle_zone right' href='#' title='{tr}Toggle right modules{/tr}'}
                    {/if}
                </div>
                <div class="col-md-8 col-md-push-2 col1" id="col1">
                    {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                        {modulelist zone=pagetop}
                    {/if}
                    {feedback}
                    {block name=quicknav}{/block}
                    {block name=title}{/block}
                    {block name=navigation}{/block}
                    {block name=content}{/block}
                    {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                        {modulelist zone=pagebottom}
                    {/if}
                </div>
                <div class="col-md-2 col-md-pull-8" id="col2">
                    {modulelist zone=left}
                </div>
                <div class="col-md-2" id="col3">
                    {modulelist zone=right}
                </div>
            {/if}
        </div>
    </div>
</div>

{if $smarty.session.fullscreen ne 'y'}
    <footer class="footer" id="footer">
        <div class="footer_liner">
            <div class="container{if $smarty.session.fullscreen eq 'y'}-fluid{/if}">
                {modulelist zone=bottom class='row row-sidemargins-zero'}
            </div>
        </div>
    </footer>
{/if}

{if $smarty.session.fullscreen ne 'y'}
<nav class="navbar {* navbar-inverse *}navbar-default navbar-fixed-top" role="navigation" id="navbar-fixed-top">
    <div class="container{if $smarty.session.fullscreen eq 'y'}-fluid{/if}">
        <div class="navbar-header">
            {if $module_params.navbar_toggle neq 'n'}
                <button type="button" class="navbar-toggle" data-toggle="collapse"
                        data-target="#navbar-collapse-social-modules">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            {/if}
        </div>
        <div class="collapse navbar-collapse fixed-topbar" id="navbar-collapse-social-modules">
            {modulelist zone="top" nobox="y" navbar="y" menuclass="navbar-inverse collapse navbar-collapse noclearfix" class="top_modules modules row context modules noclearfix"}
        </div>
    </div>
</nav>
{/if}

{include file='footer.tpl'}
</body>
<script type="text/javascript">
    $(document).ready(function () {
        $('.tooltips').tooltip({
            'container': 'body'
        });
    });
</script>
</html>
{if !empty($smarty.request.show_smarty_debug)}
    {debug}
{/if}
