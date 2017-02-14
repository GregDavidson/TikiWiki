<!DOCTYPE html>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="StyleSheet" href="styles/{$prefs.style}" type="text/css">
		<title>{tr}Live support:User window{/tr}</title>
		{literal}
			<script type="text/javascript" src="lib/live_support/live-support.js">
			</script>
		{/literal}
		{$headerlib->output_headers()}
	</head>
	<body onUnload="client_close();" style="background-color: white">
		<div id='request_chat' align="center">
			<input type="hidden" id="reqId">
			<input type="hidden" id="tiki_user" value="{$user|escape}">

			<h2>{tr}Request live support{/tr}</h2>

		    <div class="col-sm-6 col-sm-offset-3 col-xs-12">
				<div class="table-responsive">
					<table class="table">
						{if $user}
							<input type="hidden" id="username" value="{$user|escape}">
							<input type="hidden" id="emailaddress" value="{$user_email|escape}">
							<tr>
								<td><strong>{tr}User{/tr}</strong></td>
								<td>
									{$user}
								</td>
							</tr>
							<tr>
								<td><strong>{tr}Email{/tr}</strong></td>
								<td>
									{$user_email}
								</td>
							</tr>
						{else}
							<tr>
								<td><strong>{tr}User{/tr}</strong></td>
								<td>
									<input type="text" id="username">
								</td>
							</tr>
							<tr>
								<td><strong>{tr}Email{/tr}</strong></td>
								<td>
									<input type="text" id="emailaddress">
								</td>
							</tr>
						{/if}
						<tr>
							<td><strong>{tr}Reason{/tr}</strong></td>
							<td>
								<textarea id='reason' rows='3' class="form-control"></textarea>
							</td>
						</tr>
					</table>
				</div>
			<input onClick="request_chat(document.getElementById('username').value,document.getElementById('tiki_user').value,document.getElementById('emailaddress').value,document.getElementById('reason').value);" type="button" value="{tr}Request support{/tr}">
			</div>
		</div>

		<div id='requesting_chat' style='display:none;'>
			<b>{tr}Your request is being processed{/tr}....</b>
			<br><br>
			<a href="javascript:client_close();window.close();" class="link">{tr}cancel request and exit{/tr}</a><br>
			<!--<a href="tiki-live_support_message.php" class="link">{tr}cancel request and leave a message{/tr}</a><br>-->
		</div>

	</body>
</html>
