<h1>Administration Login</h1>

<p>Please login with your username and password below.</p>

<div class="error">
  <?php echo $message; ?>
</div>

<?php echo form_open("admin/login"); ?>
<div class="form-group mb-2">
  <label for="staticEmail2" class="form-label">Username</label>
  <?php echo form_input($username, 'class="titleInput"'); ?>
</div>
<div class="form-group mb-2">
  <label for="staticEmail2" class="form-label">Password</label>
  <?php echo form_input($password); ?>
</div>

<p class="m-0">
  <label for="remember">Remember Me:</label>
  <?php echo form_checkbox('remember', '1', FALSE); ?>
</p>


<p class="login-btn">
  <?php echo form_submit('submit', 'Login', 'class="submit"'); ?>
</p>


<?php echo form_close(); ?>