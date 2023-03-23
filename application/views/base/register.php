<div id="login" class="padding ptb-xs-40 page-signin">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="main-body">
          <div class="body-inner">
            <div class="card bg-white">
              <div class="card-content">
                <section class="logo text-center">
                  <h2 class="logo-h2">TRNHRD</h2>
                </section>
                <?php echo form_open("member/register", 'id="register_form"', 'class="form-horizontal ng-pristine ng-valid"'); ?>
                <?php echo form_input($hpot); ?>
                <fieldset>
                  <div class="form-group">
                    <div class="ui-input-group">
                      <div class="error">
                        <?php echo $message; ?>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="ui-input-group member-radios">
                      <div>
                        <input type="radio" class="member-radio" name="member_type" value="members" <?php echo set_radio('member_type', 'members', true) ?> />
                        <label for="members" onClick="select_member('members')">Client </label>
                      </div>
                      <div>
                        <input type="radio" class="member-radio" name="member_type" value="trainers" <?php echo set_radio('member_type', 'trainers') ?> />
                        <label for="trainers" onClick="select_member('trainers')">Trainer </label>
                      </div>
                    </div>
                  </div>
                  <div class="register-form">
                    <div class="form-group">
                      <div class="ui-input-group" style="display: inline-block; width: 50%;">
                        <!-- <input type="text" required class="form-control" /> -->
                        <?php echo form_input($first_name); ?>
                        <span class="input-bar"></span>
                      </div>
                      <div class="ui-input-group" style="float: right; width: 50%;">
                        <!-- <input type="text" required class="form-control" /> -->
                        <?php echo form_input($last_name); ?>
                        <span class="input-bar"></span>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="ui-input-group">
                        <?php echo form_input($city); ?>
                        <span class="input-bar"></span>
                        <i class="fa fa-info-circle"></i>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="ui-input-group">
                        <?php echo form_dropdown('state', $state_options, $state_value, 'class="form-control" style="border: none;text-indent: 35px;" placeholder="State"'); ?>
                        <span class="input-bar"></span>
                        <i class="fa fa-info-circle"></i>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="ui-input-group">
                        <?php echo form_input($zip); ?>
                        <span class="input-bar"></span>
                        <i class="fa fa-info-circle"></i>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="ui-input-group">
                        <!-- <input type="text" required class="form-control" /> -->
                        <?php echo form_input($email); ?>
                        <span class="input-bar"></span>
                        <i class="fa fa-inbox"></i>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="ui-input-group">
                        <!-- <input type="text" required class="form-control" /> -->
                        <?php echo form_input($username); ?>
                        <span class="input-bar"></span>
                        <i class="fa fa-user"></i>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="ui-input-group">
                        <!-- <input type="text" required class="form-control" /> -->
                        <?php echo form_input($password); ?>
                        <span class="input-bar"></span>
                        <i class="fa fa-lock"></i>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="ui-input-group">
                        <!-- <input type="text" required class="form-control" /> -->
                        <?php echo form_input($password_confirm); ?>
                        <span class="input-bar"></span>
                        <i class="fa fa-lock"></i>
                      </div>
                    </div>
                    <div class="spacer"></div>
                    <div class="form-group checkbox-field">
                      <label for="terms_accept" class="text-small">
                        <?php echo form_checkbox($terms_accept,'1', FALSE, 'id="terms_accept"'); ?>
                        <span class="ion-ios-checkmark-empty22 custom-check"></span>
                        I have read and agree to TrnHrd <a href="javascript:;"><i>terms & conditions</i></a> and 
                        <a href="javascript:;"><i>privacy policy</i></a>
                      </label>
                    </div>
                    <div class="spacer"></div>
                    <div class="form-group">
                      <input type="submit" class="btn-text log-in" value="Register">
                    </div>
                    <div class="form-group">
                      <a class="btn-text already-account" href="/member/login">Already have an account? Login</a>
                    </div>
                  </div>
                </fieldset>
                <?php echo form_close(); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- End Login Section -->