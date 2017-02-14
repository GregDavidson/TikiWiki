<!-- StartTikiTestRemoveMe -->
<div id="tiki-test-topbar" style="background-color:red;width:100%" class="clearfix">
	<table class="table">
		<tr>
			<td>{icon name='notification'}</td>
			<td><b>TikiTest</b>&nbsp;{$tikitest_filename}</td>
			<td width="30%" align="right">
				<span id="tikitest_pause" style="vertical-align:middle;">{tr}Recording to pause press the pause button{/tr}<span style="vertical-align:middle;" onclick="javascript:tikitest_state('2');">&nbsp;{icon name='pause' alt="{tr}Pause the recording{/tr}"}</span></span>
				<span id="tikitest_play" style="vertical-align:middle;">{tr}Paused to resume press the play button{/tr}<span style="vertical-align:middle;" onclick="javascript:tikitest_state('1');">&nbsp;{icon name='play' alt="{tr}Resume the recording{/tr}"}</span></span>
				<span id="tikitest_stop" style="vertical-align:middle;" onclick="javascript:tikitest_state('3');">{icon name='stop' alt="{tr}Stop the recording{/tr}"}</span>
			</td>
			<td>{icon name='notification'}</td>
		</tr>
	</table>
</div>
{jq}
	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		//document.cookie = name+"="+value+" ";
		document.cookie = name+"="+value+" "+expires+"; path=/";
	//	alert("COOKIE="+ document.cookie);
	}
	function tikitest_state(state) {
		createCookie("tikitest_record","",-1);
		createCookie("tikitest_record",""+state);
		if (state == '2') {
			hide("tikitest_pause");
			toggleSpan("tikitest_play");
		} else if (state == '1') {
			toggleSpan("tikitest_pause");
			hide("tikitest_play");
		} else if (state == '3') {
			location.href="tiki_tests/tiki-tests_edit.php?filename={{$tikitest_filename}}.xml";
		}
	}
	{{if $tikitest_state eq 1}
		hide("tikitest_play");
	{elseif $tikitest_state eq 2}
		hide("tikitest_pause");
	{/if}}
{/jq}
<!-- EndTikiTestRemoveMe -->
