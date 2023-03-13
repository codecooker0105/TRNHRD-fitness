<h1>Administration Login</h1>

<p>Please login with your username and password below.</p>

<div class="error">
  <?php echo $message; ?>
</div>

<?php echo form_open("admin/login"); ?>

<p>
  <label for="email">Username:</label>
  <?php echo form_input($username); ?>
</p>

<p>
  <label for="password">Password:</label>
  <?php echo form_input($password); ?>
</p>

<p>
  <label for="remember">Remember Me:</label>
  <?php echo form_checkbox('remember', '1', FALSE); ?>
</p>


<p>
  <?php echo form_submit('submit', 'Login', 'class="submit"'); ?>
</p>


<?php echo form_close(); ?>