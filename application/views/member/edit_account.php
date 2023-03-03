<div class="form-field edit-account">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <h1>Edit Account</h1>
            <div class="error"><?php echo $message;?></div>
        </div>
    </div>
    <?php echo form_open("member/edit_account",'id="register_form"');?>
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">First Name:</label>
                <?php echo form_input($first_name);?>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">Last Name:</label>
                <?php echo form_input($last_name);?>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">City:</label>
                <?php echo form_input($city);?>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">State:</label>
                <? echo form_dropdown('state',$state_options,$state_value,'class="required smallsize"'); ?>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">Zip:</label>
                <?php echo form_input($zip);?>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">Email:</label>
                <?php echo form_input($email);?>
            </div>
        </div>

        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <p class="p-0 m-0"><input type="submit" class="submit" value="Save"></p>
            </div>
        </div>
    </div>
    <?php echo form_close();?>
</div>