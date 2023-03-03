<div class='mainInfo'>

	<h1>Progression Plans</h1>
	<!--<p><a href="<?php echo site_url('admin/create_exercise');?>">Create a new exercise</a></p>-->
	<div id="infoMessage"><?php echo $message;?></div>
	
	<table cellpadding="0" cellspacing="0" class="listing tablesorter" id="exercises" width="100%">
    <thead>
		<tr>
			<th>Title</th>
			<th>Days a Week</th>
			<th>Options</th>
		</tr>
    </thead>
	<?php foreach ($plans as $plan):?>
        <tr>
            <td><?php echo $plan['title']?></td>
            <td><?php echo $plan['days_week']?></td>
            <td><?php echo anchor("admin/edit_progression_plan/". $plan['id'], 'Edit');?></td>
        </tr>
    <?php endforeach;?>
	</table>
	
	
	
</div>
