<div class="error">
  <?php echo $message; ?>
</div>

<?php echo form_open("services/login"); ?>

<p>
  <label for="email">Username:</label>
  <?php echo form_input($username); ?>
</p>

<p>
  <label for="password">Password:</label>
  <?php echo form_input($password); ?>
</p>


<p>
  <?php echo form_submit('submit', 'Login', 'class="submit"'); ?>
</p>


<?php echo form_close(); ?>