<h1>Change Password</h1>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("admin/edit_password/".$this->uri->segment(3));?>

      <p>New Password:<br />
      <?php echo form_input($password);?>
      </p>
      
      <p>Confirm New Password:<br />
      <?php echo form_input($password_confirm);?>
      </p>
      
      <p><?php echo form_submit('submit', 'Change');?></p>
      
<?php echo form_close();?>