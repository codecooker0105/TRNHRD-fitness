<div id="secpage_header" class="workout_page_heading">
	<h1>Workout Generator</h1>
	<p class="p-0">By observing, Adam, our model you can learn proper technique, form and muscle intervention. You can
		utilize the training calculator to create goal specific workouts that either you have made for yourself or we have
		provided for you. We have professionally designed workouts for all levels and are available for purchase through our
		store.</p>
</div>

<form action="/member/process_workout_generator" id="workout_generator_form" method="post">
	<input type="hidden" name="workout_id" id="workout_id"
		value="<?php if (isset($workout_id)) { ?><?= $workout_id ?><?php } ?>" />
	<input type="hidden" name="trainer_workout_id" id="trainer_workout_id"
		value="<?php if (isset($trainer_workout_id)) { ?><?= $trainer_workout_id ?><?php } ?>" />
	<input type="hidden" name="group_workout_id" id="group_workout_id"
		value="<?php if (isset($group_workout_id)) { ?><?= $group_workout_id ?><?php } ?>" />
	<input type="hidden" name="trainer_group_workout_id" id="trainer_group_workout_id"
		value="<?php if (isset($trainer_group_workout_id)) { ?><?= $trainer_group_workout_id ?><?php } ?>" />

	<div id="generator_left">

		<h2 class="library_header">Workout Generator</h2>
		<p>Select a date and plan your exercises. Title custom workouts if you wish. Visit your CALENDAR to see past,
			present and future workouts. View your LOG BOOK to see your progress!</p>
		<p>Workout Title (Optional) <input type="text" name="workout_title" value="" id="workout_title" /></p>
		<h2 class="library_header">Workout Dates</h2>
		<p>You can select either a specific date or a range of dates.<br />
			<input type="text" value="<?php if (isset($workout_date)) { ?><?= $workout_date ?><?php } ?>" id="date"
				class="date" />
		</p>
		<div id="week_days">
			<p><strong>Days of Week:<br /></strong>
				<?php foreach ($weekdays as $day => $checkbox) {
					?>
					<?php echo form_checkbox($checkbox); ?>
					<?= $weekday_title[$day] ?> &nbsp;
				<?php
				} ?>
			</p>
		</div>

		<hr class="divider" />
		<h2 class="library_header">Workout Autogenerator</h2>
		<p>If you would like a workout generated for this client based on their fitness level and available equipment.
			Select one of the predefined focus and workout type below. You can always modify the workout afterwards.<br />
		<table width="100%" class="exercise-checkbox">
			<tr class="d-flex">
				<td valign="top" class="w-100">
					<p><strong>Available Equipment.</strong><br />
						<?php foreach ($equipment as $id => $title) {
							?>
							<?php echo form_checkbox($available_equipment[$id]); ?>
							<?= $title ?><br />
						<?php
						} ?>
					</p>
				</td>
				<td valign="top" class="w-100">
					<strong>Select focus:</strong><br />
					<?php echo form_dropdown('progression_id', $progressions, $progression_id, 'class="progression"'); ?>
					<br />
					<strong>Select workout type:</strong><br />
					<?php echo form_dropdown('skeleton_workout_id', $skeleton_workouts, $skeleton_workout_id, 'class="skeleton"'); ?><br />
				</td>
			</tr>
		</table>
		<input type="button" name="generate" id="generate" value="Generate Workout" /></p>
	</div>

	<div id="generator_right">
		<div class="right_block">
			<div class="header">
				<h2 class="library_header ">Clients/Groups</h2><a href="/member/clients"
					class="small_edit edit-text-btn">Edit</a>
			</div>
			<div class="inner_block w-100">
				<select name="client" id="client">
					<option value="">Select a Client/Group</option>
					<?php if ($clients) {
						?>
						<option value=""><strong>Single Clients</strong></option>
						<?php
						foreach ($clients as $client) {
							?>
							<option value="<?= $client->user_id ?>" <?php if (isset($client_id) && $client_id == $client->user_id) { ?>
									selected="selected" <?php } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $client->first_name . ' ' . $client->last_name ?></option>
						<?php
						}
					} ?>
					<?php if ($trainer_groups) {
						?>
						<option value=""><strong>Client Groups</strong></option>
						<?php
						foreach ($trainer_groups as $group) {
							?>
							<option value="group-<?= $group['id'] ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $group['title'] ?></option>
						<?php
						}
					} ?>
				</select>
			</div>
		</div>
		<div id="user_photo_area">
			<div id="photo">
			</div>
		</div>
	</div>

	<hr style="clear:both" class="divider-hr" />

	<div id="workout_left">


		<h2 class="library_header">Current Workout</h2>
		<ul id="workout_list">
		</ul>
		<div class="add_section ui-widget ui-helper-clearfix ui-state-default ui-corner-all pointer"><span
				class="ui-icon ui-icon-plus pointer"></span>Add Section</div>

		<p>&nbsp;</p>
		<input type="submit" class="large_submit"
			value="<?php if (isset($workout_id)) { ?>Update Workout<?php } else { ?>Save Workout<?php } ?>" />

	</div>

	<div id="workout_right">

		<h2 class="library_header" style="clear:both;">YOUR EXERCISE LIBRARY</h2>
		<!--<p class="smaller">Select an area of the body to view associated exercises</p>-->
		<ul id="exercise_library">
			<?php if ($exercise_library) {
				foreach ($exercise_library['muscles'] as $muscle) {
					if ($muscle['no_exercises'] == 0) {
						?>
						<li><a href="#" class="muscle_title off">
								<?= $muscle['title'] ?>
							</a>
							<?php if (isset($muscle['levels'])) { ?>
								<ul class="levels">
									<?php foreach ($muscle['levels'] as $level) {
										if ($level['no_exercises'] == 0) {
											?>
											<li><a href="#" class="level_title off"><span>
														<?= $level['title'] ?>
													</span></a>
												<?php if (isset($level['exercises'])) { ?>
													<ul class="exercises" id="exercise-video">
														<?php foreach ($level['exercises'] as $exercise) {
															?>
															<li class="exercise">
																<div class="ui-widget ui-helper-clearfix ui-state-default ui-corner-all move"><span
																		class="ui-icon ui-icon-arrow-4 move "></span><span class="ex_title">
																		<?= $exercise['title'] ?>
																	</span></div><a id="<?= $exercise['id'] ?>" href="/member/popup_video/<?= $exercise['id'] ?>"
																	class="play-exercise">
																	<div class="ui-widget ui-helper-clearfix ui-state-default ui-corner-all play"><span
																			class="ui-icon ui-icon-play"></span></div>
																</a>
															</li>
														<?php
														} ?>
													</ul>
												<?php } ?>
											</li>
										<?php
										}
									} ?>
								</ul>
							<?php } ?>
						</li>
					<?php
					}
				}
			} ?>
		</ul>
	</div>
	<input type="hidden" id="current_exercise_edit" value="" />
</form>


<div id="section_dialog" title="Create new section">
	<form>
		<label for="name">Section Type</label>
		<select name="section" id="section_dropdown" class="ui-widget-content ui-corner-all">
			<?php $result = mysql_query("SELECT * FROM skeleton_section_types ORDER BY title");
			while ($row = mysql_fetch_assoc($result)) {
				?>
				<option value="<?= $row['id'] ?>"><?= $row['title'] ?></option>
			<?php
			} ?>
		</select>
	</form>
</div>

<div id="exercise_dialog" title="Create new exercise">
	<form>
		<label for="name">Exercise Type</label>
		<input type="hidden" id="exercise_type_title" value="" />
		<select name="type" id="exercise_type_dropdown" class="ui-widget-content ui-corner-all">
			<option value="">Select Exercise Type</option>
			<?php $result = mysql_query("SELECT * FROM exercise_types ORDER BY title");
			while ($row = mysql_fetch_assoc($result)) {
				?>
				<option value="<?= $row['id'] ?>"><?= $row['title'] ?></option>
			<?php
			} ?>
		</select>
	</form>
</div>

<?php $result = mysql_query("SELECT * FROM exercise_types");
while ($row = mysql_fetch_assoc($result)) { ?>
	<div id="dialog_<?= $row['id'] ?>" title="Select Exercise">
		<form>
			<h2>Exercise</h2>
			<ul style="list-style:none;">
				<?php $result2 = mysql_query("SELECT * FROM exercises WHERE id IN(SELECT exercise_id FROM exercise_link_types WHERE type_id = '" . $row['id'] . "') ORDER BY title");
				while ($row2 = mysql_fetch_assoc($result2)) {
					?>
					<li><input type="radio" name="exercise_id" value="<?= $row2['id'] ?>"><?= $row2['title'] ?> - <a
							href="/member/popup_video/<?= $row2['id'] ?>" class="play-exercise">View Video</a></li>
				<?php
				} ?>
			</ul>
		</form>
	</div>
<?php } ?>

<div id="complete_dialog" title="Workout Successfully Added">

</div>

<div id="error_dialog" title="Error Occurred">

</div>



<div id="equipment_dialog" title="Workout Reset Warning">
	<p>Changing the available equipment will reset the current workout and lose any unsaved data. If you wish to continue
		click on Reset Workout. Otherwise just close this warning or click 'Leave Equipment As Is'.</p>

</div>

<script type="text/javascript">
	var select_item;
	var clicked_item;
	var section_item;
	var exercise_count = 100;
	(function ($) {
		$(document).ready(function () {
			$("#dialog").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 300,
				modal: true,
				buttons: {
					'Add New Section': function () {
						var bValid = true;
						//allFields.removeClass('ui-state-error');

						//bValid = bValid && checkLength(name,"username",3,16);
						//bValid = bValid && checkLength(email,"email",6,80);
						//bValid = bValid && checkLength(password,"password",5,16);

						if (bValid) {
							$('#workout_list').append('<li class="section"><span class="move ui-icon ui-icon-arrowthick-2-n-s"></span><span class="title">' + $('#section_dropdown').val() + '</span><span class="remove ui-icon ui-icon-circle-close"></span><ul class="categories"></ul></li>');
							updateWorkoutListControls();
							$(this).dialog('close');
						}
					},
					Cancel: function () {
						$(this).dialog('close');
					}
				},
				close: function () {
					//allFields.val('').removeClass('ui-state-error');
				}
			});

			$("#exercise_dialog").dialog({
				bgiframe: true,
				autoOpen: false,
				height: 300,
				modal: true,
				buttons: {
					'Add New Exercise': function () {
						var bValid = true;

						if (bValid) {
							exercise_count++;
							$.post('/member/get_exercise_type', { id: $('#exercise_type_dropdown').val() }, function (data) {
								clicked_item.parent().children('ul').append(data);
								updateWorkoutListControls();
							}, 'html');



							$(this).dialog('close');
						}
					},
					Cancel: function () {
						$(this).dialog('close');
					}
				},
				close: function () {
					//allFields.val('').removeClass('ui-state-error');
				}
			});


			<?php $result = mysql_query("SELECT * FROM exercise_types");
			while ($row = mysql_fetch_assoc($result)) { ?>

				$("#dialog_<?= $row['id'] ?>").dialog({
					bgiframe: true,
					autoOpen: false,
					height: 300,
					modal: true,
					buttons: {
						'Select Exercise': function () {
							var bValid = true;

							if (bValid) {
								id = $('#dialog_<?= $row['id'] ?> input[name=exercise_id]:checked').val();
								clicked_item.parents('li').first().find('a.play-exercise').attr("href", '/member/popup_video/' + id);
								clicked_item.parents('li').first().find('a.play-exercise').html(exerciseid_array[id]);
								clicked_item.parents('li').first().find('.exercise_id').val(id);
								$('.play-exercise').colorbox();
								updateWorkoutListControls();
								$(this).dialog('close');
							}
						},
						Cancel: function () {
							$(this).dialog('close');
						}
					},
					close: function () {
						//allFields.val('').removeClass('ui-state-error');
					}
				});
			<?php } ?>

			var exercise_array = new Array();
			var exerciseid_array = new Array();
			<?php $result = mysql_query("SELECT * FROM exercises");
			while ($row = mysql_fetch_assoc($result)) {
				?>exercise_array['<?= $row['title'] ?>'] = '<?= $row['id'] ?>'; exerciseid_array['<?= $row['id'] ?>'] = '<?= $row['title'] ?>';<?php
			} ?>
		});
	})(jQuery);
</script>