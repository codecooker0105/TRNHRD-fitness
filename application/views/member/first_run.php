<div class="first-run">
  <h1>Select your starting levels</h1>
  <p>In order to get started using Hybrid Fitness you must let us know some information about you. Namely your
    experience
    level, how quickly you want to progress, what days of week you plan to workout and what you would like to focus on.
  </p>

  <div class="error">
    <?php echo $message; ?>
  </div>

  <?php echo form_open("member/first_run"); ?>
  <div class="section section_experience_level">
    <label for="exp_level">Experience Level:</label>
    <?php echo form_dropdown('exp_level_id', $experience_options, $experience_value, 'class="required smallsize"'); ?>
  </div>

  <div class="section section_availble_equipment">
    <label for="equipment">Available Equipment:</label>
    <?php foreach ($equipment as $id => $title) {
      ?><br />
      <?php echo form_checkbox($available_equipment[$id]); ?>
      <?= $title ?>
      <?php
    } ?>
  </div>

  <?php if ($trainer) { ?>
    <div class="section section_trainer_desc">
      <strong>You are currently being trained by
        <?= $trainer->first_name ?>
        <?= $trainer->last_name ?> and are not required to
        select a progression, focus or days of week you plan to work out. Your trainer can take care of creating your
        workouts. If you select a progression and focus it will add additional workouts to your schedule on top of what
        your
        trainer will create for you.
      </strong>
    </div>
    <div>
      <label for="progression">Progression and Focus:</label>
      <?php echo form_dropdown('progression_plan_id', $progression_plan_options, $progression_plan_value, 'class="smallsize"'); ?>
    </div>

    <div>
      <label for="days">Days of Week:</label>
      <?php foreach ($weekdays as $day => $checkbox) {
        ?><br />
        <?php echo form_checkbox($checkbox); ?>
        <?= $weekday_title[$day] ?>
        <?php
      } ?>
    </div>
  <?php } else { ?>

    <div class="section section_progress_focus">
      <label for="progression">Progression and Focus:</label>
      <?php echo form_dropdown('progression_plan_id', $progression_plan_options, $progression_plan_value, 'class="required smallsize"'); ?>
    </div>

    <div class="section section_days_week">
      <label for="days">Days of Week:</label>
      <?php foreach ($weekdays as $day => $checkbox) {
        ?><br />
        <?php echo form_checkbox($checkbox); ?>
        <?= $weekday_title[$day] ?>
        <?php
      } ?>
    </div>
  <?php } ?>

  <input type="submit" name="submit" class="submit" value="Get Started" />

  <?php echo form_close(); ?>
</div>