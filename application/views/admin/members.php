<div class='mainInfo'>

	<h1>Members</h1>
	<p><a href="<?php echo site_url('admin/create_member'); ?>">Create a new member</a></p>
	<div id="infoMessage">
		<?php echo $message; ?>
	</div>

	<table cellpadding="0" cellspacing="0" class="listing tablesorter" id="users" width="100%">
		<thead>
			<tr>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Email</th>
				<th>Group</th>
				<th>Status</th>
				<th>Options</th>
			</tr>
		</thead>
		<?php foreach ($users as $user): ?>
			<tr>
				<td>
					<?php echo $user['first_name'] ?>
				</td>
				<td>
					<?php echo $user['last_name'] ?>
				</td>
				<td>
					<?php echo $user['email']; ?>
				</td>
				<td>
					<?php echo $user['group_description']; ?>
				</td>
				<td>
					<?php echo ($user['active']) ? anchor("admin/deactivate/" . $user['id'], 'Active') : anchor("admin/activate/" . $user['id'], 'Inactive'); ?>
				</td>
				<td>
					<?php echo anchor("admin/edit_user/" . $user['id'], 'Edit'); ?> |
					<?php echo anchor("admin/edit_password/" . $user['id'], 'Password'); ?> | <a href="#" class="delete_member"
						id="member<?= $user['id'] ?>">Delete</a>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>



</div>

<div id="delete_member_dialog" title="Confirm Delete Member" style="display:none;">
	<p>Are you sure you want to delete this member? All workouts and information will be permenantly deleted!!</p>

</div>