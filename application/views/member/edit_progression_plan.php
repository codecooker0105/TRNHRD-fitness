<h1>Progression Plan</h1>
<p>You can modify the current progression plan you are using. Please note that this will erase all future progression
	plan workouts and create new ones based on your choices below. Any additional workouts you have added to your workout
	plan will remain untouched.</p>

<div class="error">
	<?php echo $message; ?>
</div>

<?php echo form_open("member/edit_progression_plan"); ?>
<p><label for="progression">Progression and Focus:</label>
	<?php echo form_dropdown('progression_plan_id', $progression_plan_options, $progression_plan_value, 'class="required smallsize selectprogression" '); ?>
</p>

<p class="check-box-list"><label for="equipment">Days of Week:</label>
	<?php foreach ($weekdays as $day => $checkbox) {
		?><br />
		<?php echo form_checkbox($checkbox); ?>
		<?= $weekday_title[$day] ?>
		<?php
	} ?>
</p>

<p><input type="submit" name="submit" class="submit progression-submit" value="Change Plan" />

	<?php echo form_close(); ?>