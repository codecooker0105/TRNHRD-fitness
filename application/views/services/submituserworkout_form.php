<?php echo form_open("services/submituserworkout"); ?>

<p>
  <label for="email">WorkoutId:</label>
  <?php echo form_input($workout_id); ?>
</p>

<p>
  <label for="password">UserId:</label>
  <?php echo form_input($user_id); ?>
</p>

<p>
  <label for="password">ExerciseId:</label>
  <?php echo form_input($exercise_id); ?>
</p>

<p>
  <label for="password">UserWorkoutExerciseId:</label>
  <?php echo form_input($uwe_id); ?>
</p>

<p>
  <label for="password">Sets:</label>
  <?php echo form_input($sets); ?>
</p>

<p>
  <label for="password">Reps:</label>
  <?php echo form_input($reps); ?>
</p>

<p>
  <label for="password">Time:</label>
  <?php echo form_input($time); ?>
</p>

<p>
  <label for="password">Weight:</label>
  <?php echo form_input($weight); ?>
</p>

<p>
  <label for="password">Difficulty:</label>
  <?php echo form_input($difficulty); ?>
</p>


<p>
  <?php echo form_submit('submit', 'Submit', 'class="submit"'); ?>
</p>


<?php echo form_close(); ?>