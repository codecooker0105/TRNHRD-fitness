<h1>Create New Member</h1>
<div id="infoMessage">
    <?php echo $message; ?>
</div>

<?php echo form_open("admin/create_member"); ?>
<table width="100%">
    <tr>
        <td valign="top" width="50%">
            <table width="392" cellpadding="0" cellspacing="4">
                <tr>
                    <td>Member Type:</td>
                    <td>
                        <?php echo form_dropdown('group_name', $group_options, 'members'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Trained By:</td>
                    <td>
                        <?php echo form_dropdown('trainer', $trainer_options, $this->form_validation->set_value('trainer')); ?>
                    </td>
                </tr>
                <tr>
                    <td>First Name:</td>
                    <td>
                        <?php echo form_input($first_name); ?>
                    </td>
                </tr>
                <tr>
                    <td>Last Name:</td>
                    <td>
                        <?php echo form_input($last_name); ?>
                    </td>
                </tr>
                <tr>
                    <td>City:</td>
                    <td>
                        <?php echo form_input($city); ?>
                    </td>
                </tr>
                <tr>
                    <td>State:</td>
                    <td>
                        <?php echo form_dropdown('state', $state_options, $state_value, 'class="required smallsize"'); ?>
                    </td>

                </tr>
                <tr>
                    <td>Zip:</td>
                    <td>
                        <?php echo form_input($zip); ?>
                    </td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>
                        <?php echo form_input($email); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p>&nbsp;</p>
                    </td>
                </tr>
                <tr>
                    <td>Username:</td>
                    <td><input type="text" name="username" size="40" class="required email midsize" /></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="small_print">Must be 6-15 alphanumeric characters and can use the
                            underscore (_)</span></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password" size="25" class="required midsize" /></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="small_print">Must be 6-15 alphanumeric characters and can use following
                            (_ # @ *)</span></td>
                </tr>
                <tr>
                    <td>Confirm Password:</td>
                    <td><input type="password" name="password_confirm" size="25" class="required midsize" /></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p align="right"><input type="submit" class="submit" value="Complete >"></p>

<?php echo form_close(); ?>