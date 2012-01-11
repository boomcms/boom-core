<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<form method="post" action="/_cms_user_manager/save_user" id="sledge-tagmanager-create-person-form">
	<table width="100%">
		<tbody>
			<tr>
				<td><label for="create-firstname">First name</label></td>
				<td><input type="text" id="create-firstname" name="firstname" class="sledge-input" /></td>
			</tr>
			<tr>
				<td><label for="create-surname">Surname</label></td>
				<td><input type="text" id="create-surname" name="surname" class="sledge-input" /></td>
			</tr>
			<tr>
				<td><label for="create-email">Email</label></td>
				<td><input type="text" id="create-email" name="email" class="sledge-input" /></td>
			</tr>
			<tr>
				<td><label for="create-password">Password</label></td>
				<td><input type="password" id="create-password" name="password" class="sledge-input" /></td>
			</tr>
			<tr>
				<td><label for="create-group">Group</label></td>
				<td>
					<select id="create-group" name="group_rid">
						<?
						$groups_tag = Tag::find_tag(1, 'Groups');
						foreach(Tag::gettags($groups_tag->rid) as $tag) {
							if (!Tag::is_smart('tag', $tag->rid)) {
								echo Cms_tag_manager_Controller::recurse_combo($tag, NULL, 0);
							}
						}?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</form>
