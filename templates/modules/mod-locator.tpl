{* $Id: mod-locator.tpl 57405 2016-02-02 11:31:50Z jonnybradley $ *}
{tikimodule error=$module_params.error title=$tpl_module_title name="locator" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div class="minimap map-container" data-marker-filter=".geolocated"{$center}></div>
{/tikimodule}
