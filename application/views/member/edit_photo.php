<h1>Pofile Photo</h1>

<p>Current Photo<br />
  <?php if ($user->photo != '') { ?>

    <img src="/images/member_photos/<?= $user->photo ?>" />
  <?php } else { ?>
    No photo
  </p>
<?php } ?>

<div id="infoMessage">
  <?php echo $error; ?>
</div>

<?php echo form_open_multipart('member/edit_photo'); ?>
<input type="hidden" name="upload" value="true">
<input type="file" name="photo" size="20" />

<br /><br />

<p>
  <?php echo form_submit('submit', 'Upload', 'class="submit"'); ?>
</p>

<?php echo form_close(); ?>