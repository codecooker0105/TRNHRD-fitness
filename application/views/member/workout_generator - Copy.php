<form action="/member/process_workout_generator" id="workout_generator_form" method="post">
	<div id="generator_left">

		<h2 class="header_392">Workout Generator</h2>
		<p>You can create a custom workout for your clients</p>
		<p>Workout Title (Optional) <input type="text" name="workout_title" value="" id="workout_title" /></p>
		<h3 class="header_221">Workout Dates</h3>
		<p>You can select either a specific date or a range of dates.<br />
			<input type="text" value="" id="date" class="date" />
		</p>
		<div id="week_days">
			<p><strong>Days of Week:<br /></strong>
				<?php foreach ($weekdays as $day => $checkbox) {
					?>
					<?php echo form_checkbox($checkbox); ?>
					<?= $weekday_title[$day] ?> &nbsp&nbsp&nbsp&nbsp;
					<?
				} ?>
			</p>
		</div>

		<hr />
		<p><strong>Great! Now select your available equipment.</strong><br />
			<?php foreach ($equipment as $id => $title) {
				?>
				<?php echo form_checkbox($available_equipment[$id]); ?>
				<?= $title ?>&nbsp&nbsp&nbsp&nbsp;
				<?
			} ?>
		</p>

		<hr />
		<p>Select a progression type and workout to get started and a workout will be generated for you. You can always
			modify the workout afterwards.<br />
			<label for="progression">Select progression:</label>
			<?php echo form_dropdown('progression_id', $progressions, $progression_id, 'class="progression"'); ?>
			<br /><label for="skeleton_workout_id">Select workout type:</label>
			<?php echo form_dropdown('skeleton_workout_id', $skeleton_workouts, $skeleton_workout_id, 'class="skeleton"'); ?><br />
			<input type="button" name="generate" id="generate" value="Generate Workout" />
		</p>

		<ul id="workout_list">
		</ul>

		<p>&nbsp;</p>
		<input type="submit" class="submit" value="Save" />

	</div>

	<div id="generator_right">
		<div class="right_block">
			<div class="header">
				<h3 class="header_221">Clients</h3><a href="/member/clients" class="small_edit">Edit</a>
			</div>
			<div class="inner_block">
				<select name="client" id="client">
					<option value="">Select a Client</option>
					<?php if ($clients) {
						foreach ($clients as $client) {
							?>
							<option value="<?= $client->user_id ?>"><?= $client->first_name . ' ' . $client->last_name ?></option>
							<?
						}
					} ?>
				</select>
			</div>
		</div>
		<div id="user_photo_area">
			<div id="photo">
			</div>
		</div>

		<div id="library_wrapper">
			<h2 class="library_header" style="clear:both;">YOUR EXERCISE LIBRARY</h2>
			<p class="smaller">Select an area of the body to view associated exercises</p>
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
														<ul class="exercises">
															<?php foreach ($level['exercises'] as $exercise) {
																?>
																<li class="exercise"><a href="/member/popup_video/<?= $exercise['id'] ?>"
																		class="play-exercise"><?= $exercise['title'] ?></a></li>
																<?
															} ?>
														</ul>
													<?php } ?>
												</li>
												<?
											}
										} ?>
									</ul>
								<?php } ?>
							</li>
							<?
						}
					}
				} ?>
			</ul>
		</div>
	</div>
	<input type="hidden" id="current_exercise_edit" value="" />
</form>


<div id="dialog" title="Create new section">
	<form>
		<label for="name">Section Type</label>
		<select name="section" id="section_dropdown" class="ui-widget-content ui-corner-all">
			<?php $result = mysql_query("SELECT * FROM skeleton_section_types ORDER BY title");
			while ($row = mysql_fetch_assoc($result)) {
				?>
				<option value="<?= $row['title'] ?>"><?= $row['title'] ?></option>
				<?
			} ?>
		</select>
	</form>
</div>

<div id="sets_reps_dialog" title="Edit Sets & Reps">
	<form>
		<label for="name">Sets</label>
		<select name="sets" id="sets_dropdown" class="ui-widget-content ui-corner-all">
			<?php for ($x = 1; $x <= 10; $x++) { ?>
				<option value="<?= $x ?>"><?= $x ?></option>
			<?php } ?>
		</select><br />

		<label for="name">Reps</label>
		<select name="sets" id="reps_dropdown" class="ui-widget-content ui-corner-all">
			<?php for ($x = 1; $x <= 30; $x++) { ?>
				<option value="<?= $x ?>"><?= $x ?></option>
			<?php } ?>
		</select>
	</form>
</div>

<div id="exercise_dialog" title="Create new exercise">
	<form>
		<fieldset>
			<label for="name">Exercise Type</label>
			<input type="hidden" id="exercise_type_title" value="" />
			<select name="type" id="exercise_type_dropdown" class="ui-widget-content ui-corner-all">
				<option value="">Select Exercise Type</option>
				<?php $result = mysql_query("SELECT * FROM exercise_types ORDER BY title");
				while ($row = mysql_fetch_assoc($result)) {
					?>
					<option value="<?= $row['id'] ?>"><?= $row['title'] ?></option>
					<?
				} ?>
			</select>
		</fieldset>
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
					<?
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
						$('.remove').click(function () {
							$(this).parent().fadeOut(function () {
								$(this).remove();
							});
						});
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
					//allFields.removeClass('ui-state-error');

					//bValid = bValid && checkLength(name,"username",3,16);
					//bValid = bValid && checkLength(email,"email",6,80);
					//bValid = bValid && checkLength(password,"password",5,16);

					if (bValid) {
						exercise_count++;
						$('#' + section_item).parent().children('ul').append('<li><span class="move ui-icon ui-icon-arrowthick-2-n-s"></span><span class="category_title">' + $('#exercise_type_title').val() + '</span> <input type="hidden" name="exercise_id[]" value="" /><input id="exercise_' + exercise_count + '" type="text" name="exercise_title[]" value="No Exercise Selected" class="exercise_title select_exercise' + $('#exercise_type_dropdown').val() + '"><span class="play-exercise play_exercise' + $('#exercise_type_dropdown').val() + ' ui-icon ui-icon-circle-triangle-e"></span></li>');
						$('.remove').click(function () {
							$(this).parent().fadeOut(function () {
								$(this).remove();
							});
						});
						<?php $result = mysql_query("SELECT * FROM exercise_types");
						while ($row = mysql_fetch_assoc($result)) { ?>
							$('.select_exercise<?= $row['id'] ?>').click(function () {
								select_item = $(this).attr('id');
								return select_item;
							});

							$('.select_exercise<?= $row['id'] ?>').click(function () {
								$('#dialog_<?= $row['id'] ?>').dialog('open');
							});
						<?php } ?>
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

		var select_item;
		var section_item;
		var exercise_count = 100;
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
						//allFields.removeClass('ui-state-error');

						//bValid = bValid && checkLength(name,"username",3,16);
						//bValid = bValid && checkLength(email,"email",6,80);
						//bValid = bValid && checkLength(password,"password",5,16);

						if (bValid) {
							id = $('#dialog_<?= $row['id'] ?> input[name=exercise_id]:checked').val();
							select_item = $("input#current_exercise_edit").val();
							$('#exercise_' + select_item).parent('li').find('input.exercise_id').val(id);
							$('#exercise_' + select_item).parent('li').find('.exercise_title').html('<a href="/member/popup_video/' + id + '" class="play-exercise">' + exerciseid_array[id] + '</a>');
							$('.play-exercise').colorbox();
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
			?>exercise_array['<?= $row['title'] ?>'] = '<?= $row['id'] ?>'; exerciseid_array['<?= $row['id'] ?>'] = '<?= $row['title'] ?>';<?
		} ?>
	});
</script>