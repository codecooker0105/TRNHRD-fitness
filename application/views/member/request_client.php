<div class="client-requests-form">

<h1>Client Request</h1>
<p class="desc-para">Enter the clients name, email and an optional message to include in the email we send them.</p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("member/request_client");?>
<p>Name:<br />
<?php echo form_input($name);?>
</p>

<p>Email:<br />
<?php echo form_input($email);?>
</p>

<p>Message:<br />
<?php echo form_textarea($email_message);?>
</p>

<p><?php echo form_submit('submit', 'Send Request');?></p>


<?php echo form_close();?>
</div>