<div class='mainInfo'>

	<h1>Skeleton Workouts</h1>
	<p><a href="<?php echo site_url('admin/create_skeleton_workout'); ?>">Create a new skeleton workout</a></p>
	<div id="infoMessage">
		<?php echo $message; ?>
	</div>

	<table cellpadding="0" cellspacing="0" class="listing tablesorter" id="exercises" width="100%">
		<thead>
			<tr>
				<th>Title</th>
				<th>Options</th>
			</tr>
		</thead>
		<?php foreach ($workouts as $workout): ?>
			<tr>
				<td>
					<?php echo $workout['title'] ?>
				</td>
				<td>
					<?php echo anchor("admin/edit_skeleton_workout/" . $workout['id'], 'Edit'); ?> |
					<?php echo anchor("admin/delete/skeleton_workout/" . $workout['id'], 'Delete'); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>