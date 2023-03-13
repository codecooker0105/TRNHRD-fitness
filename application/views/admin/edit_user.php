<div class='mainInfo'>

	<h1>Edit Member</h1>
	
	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open("admin/edit_user/".$this->uri->segment(3));?>
    <?php echo form_input($user_id);?>
    <table width="100%">
    <tr>
        <td>Member Type:</td>
        <td><?php echo form_dropdown('group_id',$group_options,$group_value,'class="mediumsize"'); ?></td>

    </tr>
    <tr>
        <td>Trainer:</td>
        <td><?php echo form_dropdown('trainer_id',$trainer_options,$trainer_value,'class="mediumsize"'); ?></td>

    </tr>
    <tr>
        <td>Username:</td>
        <td><?php echo form_input($username);?></td>
    </tr>
    <tr>
        <td>First Name:</td>
        <td><?php echo form_input($first_name);?></td>
    </tr>
    <tr>
        <td>Last Name:</td>
        <td><?php echo form_input($last_name);?></td>
    </tr>
    <tr>
        <td>City:</td>
        <td><?php echo form_input($city);?></td>
    </tr>
    <tr>
        <td>State:</td>
        <td><?php echo form_dropdown('state',$state_options,$state_value,'class="smallsize"'); ?></td>

    </tr>
    <tr>
        <td>Zip:</td>
        <td><?php echo form_input($zip);?></td>
    </tr>
    <tr>
        <td>Email:</td>
        <td><?php echo form_input($email);?></td>
    </tr>
</table>
<p><?php echo form_submit('submit', 'Submit');?></p>
      
    <?php echo form_close();?>

</div>
