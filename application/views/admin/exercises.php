<div class='mainInfo'>

	<h1>Exercises</h1>
	<p><a href="<?php echo site_url('admin/create_exercise'); ?>">Create a new exercise</a></p>
	<div id="infoMessage">
		<?php echo $message; ?>
	</div>

	<table cellpadding="0" cellspacing="0" class="listing tablesorter" id="exercises" width="100%">
		<thead>
			<tr>
				<th>Title</th>
				<th>Video</th>
				<th>Options</th>
			</tr>
		</thead>
		<?php foreach ($exercises as $exercise): ?>
			<tr>
				<td>
					<?php echo $exercise['title'] ?>
				</td>
				<td><a href="/member/popup_video/<?php echo $exercise['id'] ?>" class="play-exercise">View</a></td>
				<td>
					<?php echo anchor("admin/edit_exercise/" . $exercise['id'], 'Edit'); ?> |
					<?php echo anchor("admin/delete/exercise/" . $exercise['id'], 'Delete'); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>



</div>