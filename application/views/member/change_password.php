<div class="form-field edit-account change-password">
      <div class="row">
            <div class="col-md-12 col-sm-12">
                  <h1>Change Password</h1>
                  <br />
                  <div id="infoMessage">
                        <?php echo $message; ?>
                  </div>
            </div>
      </div>

      <?php echo form_open("member/change_password"); ?>
      <div class="row">
            <div class="col-md-12 col-sm-12">
                  <label class="form-label">Old Password:</label>
                  <?php echo form_input($old_password); ?>
            </div>
            <div class="col-md-12 col-sm-12">
                  <label class="form-label">New Password:</label>
                  <?php echo form_input($new_password); ?>
            </div>
            <div class="col-md-12 col-sm-12">
                  <label class="form-label">Confirm New Password:</label>
                  <?php echo form_input($new_password_confirm); ?>
            </div>
            <div class="col-md-12 col-sm-12">
                  <?php echo form_input($user_id); ?>
                  <p class="submit p-0 m-0">
                        <?php echo form_submit('submit', 'Change'); ?>
                  </p>
            </div>
      </div>
      <?php echo form_close(); ?>
</div>