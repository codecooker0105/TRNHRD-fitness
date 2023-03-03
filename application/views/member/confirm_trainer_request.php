<h1>Confirm Trainer Request</h1>

<p>The following trainer as requested to train you on Hybrid Fitness. Please review their information and confirm or deny the request.</p>
<p><strong>Trainer's Name:</strong> <?=$trainer->first_name?> <?=$trainer->last_name?><br />
<strong>Trainer's Email:</strong> <?=$trainer->email?></p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("member/confirm_trainer_request/" . $this->uri->segment(3));?>

      <p>Decision:<br />
      <?php echo form_dropdown('decision', $decision_options, $decision);?>
      </p>
      
      <?php echo form_input($request_id);?>
      <p><?php echo form_submit('submit', 'Submit Decision');?></p>
      
<?php echo form_close();?>