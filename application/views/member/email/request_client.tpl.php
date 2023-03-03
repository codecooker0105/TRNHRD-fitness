<html>
<body>
	<p>Dear <?=$name?>,</p>
    <p><?=$trainer_name?> has requested you join HybridFitness.com in order to track your progress and provide detailed workout programs.</p>
    <p>In order to join Hybrid Fitness, simply go to <?php echo anchor('member/register', site_url('member/register'));?> and register for an account. Make sure to use this email address (<?=$email?>) when signing up. You can always change it later. If you are already a member of Hybrid Fitness then login and you will be promtped to confirm <?=$trainer_name?> as your trainer.</p>
    <? if($email_message != ''){ ?>
    	<p><?=$trainer_name?> personal message "<?=$email_message?>"</p>
    <? } ?>
    <p>Thank you for using Hybrid Fitness and we look forward to serving your fitness needs.
</body>
</html>