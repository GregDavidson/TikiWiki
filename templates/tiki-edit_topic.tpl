{* $Id: tiki-edit_topic.tpl 58749 2016-06-01 01:39:05Z lindonb $ *}

{title help="Articles"}{tr}Admin Article Topics{/tr}{/title}
<h2>{tr}Edit article topic{/tr}</h2>

<form enctype="multipart/form-data" action="tiki-edit_topic.php" method="post" role="form">
	<table class="table">
		<tr>
			<td>
				<strong>{tr}Name{/tr}</strong>
			</td>
			<td>
				<input type="hidden" name="topicid" value="{$topic_info.topicId}">
				<input type="text" class="form-control" name="name" value="{$topic_info.name|escape}">
			</td>
		</tr>
		<tr>
			<td>
				<strong>{tr}Image{/tr}</strong>
			</td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
				<input name="userfile1" class="form-control" type="file">
			</td>
		</tr>
		<tr>
			<td>
				<strong>{tr}Notification Email{/tr}&nbsp;<a href="tiki-admin_notifications.php" title="{tr}Admin notifications{/tr}"></strong>{icon name='wrench' alt="{tr}Admin notifications{/tr}"}</a>
			</td>
			<td>
				<input type="text" name="email" class="form-control" value="{$email|escape}" placeholder="{tr}Enter email addresses {/tr}">
			</td>
		</tr>
	</table>
	<input type="submit" class="btn btn-primary btn-sm center-block" name="edittopic" value="{tr}Save{/tr}">
</form>
