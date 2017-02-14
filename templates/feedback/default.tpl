{* $Id: default.tpl 59786 2016-09-23 22:27:26Z lindonb $ *}

<div id="tikifeedback">
	{if !empty($tikifeedback)}
		{foreach $tikifeedback as $tpl => $fb}
			{if $tpl === 'default'}
				{foreach $fb as $item}
					{remarksbox type=$item.type title=$item.title icon=$item.icon}
						{if $item.mes|count == 1}
							<ul class="list-unstyled">
						{else}
							<ul>
						{/if}
							{foreach $item.mes as $mes}
								<li>
									{$mes}
								</li>
							{/foreach}
						</ul>
					{/remarksbox}
				{/foreach}
			{elseif $tpl}
				{include file='feedback/'|cat:$tpl|cat:'.tpl'}
			{/if}
		{/foreach}
	{/if}
</div>
