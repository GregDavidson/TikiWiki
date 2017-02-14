{* $Id: include_community.tpl 58946 2016-06-20 15:43:40Z yonixxx $ *}
<form class="form-horizontal" action="tiki-admin.php?page=community" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="t_navbar margin-bottom-md">
		{button href="tiki-admingroups.php" _class="btn-link tips" _type="text" _icon_name="group" _text="{tr}Groups{/tr}" _title=":{tr}Group Administration{/tr}"}
		{button href="tiki-adminusers.php" _class="btn-link tips" _type="text" _icon_name="user" _text="{tr}Users{/tr}" _title=":{tr}User Administration{/tr}"}
		{permission_link addclass="btn btn-link" _type="text" mode=text label="{tr}Permissions{/tr}"}
		<a href="{service controller=managestream action=list}" class="btn btn-link tips">{tr}Activity Rules{/tr}</a>
		<div class="pull-right">
			<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
		</div>
	</div>
	{tabset name="admin_community"}
		{tab name="{tr}Community features{/tr}"}
			<h2>{tr}Community features{/tr}</h2>
			<fieldset>
				<legend>{tr}Community{/tr}{help url="Community"}</legend>
				{preference name=feature_community_gender}
				{preference name=feature_community_mouseover}
				<div class="adminoptionboxchild" id="feature_community_mouseover_childcontainer">
					{preference name=feature_community_mouseover_name}
					{preference name=feature_community_mouseover_gender}
					{preference name=feature_community_mouseover_picture}
					{preference name=feature_community_mouseover_score}
					{preference name=feature_community_mouseover_country}
					{preference name=feature_community_mouseover_email}
					{preference name=feature_community_mouseover_lastlogin}
					{preference name=feature_community_mouseover_distance}
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Additional options{/tr}</legend>
				<div class="adminoptionbox">
					{preference name=feature_invite}
					{preference name=auth_token_tellafriend}
					{preference name=auth_token_share}
					{preference name=feature_group_transition}
					{preference name=user_likes}
					{preference name=mustread_enabled}
					<div class="adminoptionboxchild" id="mustread_enabled_childcontainer">
						{preference name=mustread_tracker}
					</div>
					{preference name=user_multilike_config}
				</div>
			</fieldset>
		{/tab}
		{tab name="{tr}Social Interaction{/tr}"}
			<h2>{tr}Social Interaction{/tr}</h2>
			<fieldset class="table">
				<legend>{tr}Friendship and Followers{/tr}</legend>
				{preference name=feature_friends}
				<div class="adminoptionboxchild" id="feature_friends_childcontainer">
					{preference name=social_network_type}
					<fieldset>
						<legend>{tr}Select which items to display when listing users{/tr}</legend>
						{preference name=user_list_order}
						{preference name=feature_community_list_name}
						{preference name=feature_community_list_score}
						{preference name=feature_community_list_country}
						{preference name=feature_community_list_distance}
					</fieldset>
				</div>
			</fieldset>
			<fieldset class="table">
				<legend>{tr}Activity Stream{/tr}</legend>
				{preference name=activity_basic_events}
				<div class="adminoptionboxchild" id="activity_basic_events_childcontainer">
					{preference name=activity_basic_tracker_create}
					{preference name=activity_basic_tracker_update}
					{preference name=activity_basic_user_follow_add}
					{preference name=activity_basic_user_follow_incoming}
					{preference name=activity_basic_user_friend_add}
				</div>
				{preference name=activity_custom_events}
				{preference name=activity_notifications}
			</fieldset>
			<fieldset>
				<legend>{tr}Goal, Recognition and Rewards{/tr}</legend>
				{preference name=goal_enabled}
				{preference name=goal_badge_tracker}
				{preference name=goal_group_blacklist}
			</fieldset>
			<fieldset>
				<legend>{tr}Score{/tr}</legend>
				{preference name=feature_score}
			</fieldset>
		{/tab}
		{tab name="{tr}Plugins{/tr}"}
			<h2>{tr}Plugins{/tr}</h2>
			{preference name=wikiplugin_author}
			{preference name=wikiplugin_avatar}
			{preference name=wikiplugin_favorite}
			{preference name=wikiplugin_group}
			{preference name=wikiplugin_groupexpiry}
			{preference name=wikiplugin_invite}
			{preference name=wikiplugin_mail}
			{preference name=wikiplugin_map}
			{preference name=wikiplugin_memberlist}
			{preference name=wikiplugin_memberpayment}
			{preference name=wikiplugin_perm}
			{preference name=wikiplugin_proposal}
			{preference name=wikiplugin_realnamelist}
			{preference name=wikiplugin_subscribegroup}
			{preference name=wikiplugin_subscribegroups}
			{preference name=wikiplugin_topfriends}
			{preference name=wikiplugin_usercount}
			{preference name=wikiplugin_userlink}
			{preference name=wikiplugin_userlist}
			{preference name=wikiplugin_userpref}
		{/tab}

		{tab name="{tr}BigBlueButton{/tr}"}
			<h2>{tr}BigBlueButton{/tr}</h2>
			{preference name=bigbluebutton_feature}
			<div class="adminoptionboxchild" id="bigbluebutton_feature_childcontainer">
				{preference name=bigbluebutton_server_location}
				{preference name=bigbluebutton_server_salt}
				{preference name=bigbluebutton_recording_max_duration}
				{preference name=wikiplugin_bigbluebutton}
			</div>
		{/tab}
	{/tabset}
	<div class="t_navbar margin-bottom-md text-center">
		<input type="hidden" name="userfeatures" />
		<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
	</div>
</form>
