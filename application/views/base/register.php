    <div id="login" class="padding ptb-xs-40 page-signin">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="main-body">
              <div class="body-inner">
                <div class="card bg-white">
                  <div class="card-content">
                    <section class="logo text-center">
                      <div class="header-box">
                        <h1 class="main-title">Get Started with TRNHRD!</h1>
                      </div>
                    </section>
                    <?php echo form_open("member/register",'id="register_form"', 'class="form-horizontal ng-pristine ng-valid"');?>
                      <?php echo form_input($hpot);?> 
                      <fieldset>
                        <div class="form-group">
                          <div class="ui-input-group">
                            <div class="error"><?php echo $message;?></div>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="ui-input-group">
                          <?php echo form_dropdown('member_type',array('members' => 'Client','trainers' => 'Trainer'),'', 'class="form-control" style="border: none;border-bottom: 1px solid #CBD5DD;"');?>
                            <span class="input-bar"></span>
                            <label style="top: -20px;left: 0;font-size: 12px;">
                              <i class="fa fa-user"></i> &nbsp;
                              Member Type
                            </label>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="ui-input-group" style="display: inline-block; width: 50%;">
                            <!-- <input type="text" required class="form-control" /> -->
                            <?php echo form_input($first_name);?>
                            <span class="input-bar"></span>
                            <label>
                              <i class="fa fa-info"></i> &nbsp;
                              First
                            </label>
                          </div>
                          <div class="ui-input-group" style="float: right; width: 50%;">
                            <!-- <input type="text" required class="form-control" /> -->
                            <?php echo form_input($last_name);?>
                            <span class="input-bar"></span>
                            <label>
                              <i class="fa fa-info"></i> &nbsp;
                              Last
                            </label>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="ui-input-group">
                          <?php echo form_input($city);?>
                            <span class="input-bar"></span>
                            <label>
                              <i class="fa fa-info-circle"></i> &nbsp;
                              City
                            </label>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="ui-input-group">
                          <? echo form_dropdown('state',$state_options,$state_value,'class="form-control" style="border: none;border-bottom: 1px solid #CBD5DD;"'); ?>
                            <span class="input-bar"></span>
                            <label style="top: -20px;left: 0;font-size: 12px;">
                              <i class="fa fa-info-circle"></i> &nbsp;
                              State
                            </label>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="ui-input-group">
                          <?php echo form_input($zip);?>
                            <span class="input-bar"></span>
                            <label>
                              <i class="fa fa-info-circle"></i> &nbsp;
                              Zip
                            </label>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="ui-input-group">
                            <!-- <input type="text" required class="form-control" /> -->
                            <?php echo form_input($email);?>
                            <span class="input-bar"></span>
                            <label>
                              <i class="fa fa-inbox"></i> &nbsp;
                              Email
                            </label>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="ui-input-group">
                            <!-- <input type="text" required class="form-control" /> -->
                            <?php echo form_input($username);?>
                            <span class="input-bar"></span>
                            <label>
                              <i class="fa fa-user"></i> &nbsp;
                              Username
                            </label>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="ui-input-group">
                            <!-- <input type="text" required class="form-control" /> -->
                            <?php echo form_input($password);?>
                            <span class="input-bar"></span>
                            <label>
                              <i class="fa fa-lock"></i> &nbsp;
                              Password
                            </label>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="ui-input-group">
                            <!-- <input type="text" required class="form-control" /> -->
                            <?php echo form_input($password_confirm);?>
                            <span class="input-bar"></span>
                            <label>
                              <i class="fa fa-lock"></i> &nbsp;
                              Confirm
                            </label>
                          </div>
                        </div>
                        <div class="spacer"></div>
                        <div class="form-group checkbox-field">
                          <label for="terms_accept" class="text-small">
                            <?php echo form_checkbox($terms_accept);?>
                            <span
                              class="ion-ios-checkmark-empty22 custom-check"
                            ></span>
                            I have read and agree to TrnHrd <a href="javascript:;"><i>terms & conditions</i></a> and <a href="javascript:;"><i>privacy policy</i></a></label>
                          </label>
                        </div>
                        <div class="spacer"></div>
                        <div class="form-group">
                          <input type="submit" class="btn-text log-in" value="Register">
                        </div>
                      </fieldset>
                    <?php echo form_close();?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Login Section -->