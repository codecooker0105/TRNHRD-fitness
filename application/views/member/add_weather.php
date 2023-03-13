<h1>Add Weather Location</h1>
<p>Enter the zip code and wether you would like this to be your default location.</p>

<div id="infoMessage">
  <?php echo $message; ?>
</div>

<?php echo form_open("member/add_weather"); ?>
<p>Zip Code:<br />
  <?php echo form_input($zip); ?>
</p>

<p>
  <?php echo form_submit('submit', 'Add Location'); ?>
</p>


<?php echo form_close(); ?>