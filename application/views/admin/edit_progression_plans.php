<div class='mainInfo'>

  <h1>Edit Progression Plan</h1>

  <div id="infoMessage">
    <?php echo $message; ?>
  </div>

  <?php echo form_open("admin/edit_progression_plan/" . $this->uri->segment(3)); ?>
  <p>Title:<br />
    <?php echo form_input($title); ?>
  </p>

  <p>Days a Week:<br />
    <?php echo form_dropdown('days_week', $days_week_options, $days_week, 'class="required"'); ?>
  </p>

  <p>Focus:<br />
    <?php echo form_dropdown('focus_id', $focus_options, $focus_id, 'class="required"'); ?>
  </p>

  <?php for ($x = 1; $x <= 30; $x++) { ?>
    <p>Day
      <?= $x ?>:
      <?php echo form_dropdown('days[' . $x . ']', $progression_options, $days[$x], ''); ?>
    </p>
  <?php } ?>
  </p>

  <?php echo form_input($plan_id); ?>
  <p>
    <?php echo form_submit('submit', 'Submit'); ?>
  </p>


  <?php echo form_close(); ?>

</div>