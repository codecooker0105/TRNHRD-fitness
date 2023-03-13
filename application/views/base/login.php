<!-- Login Section -->
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
                <?php echo form_open("member/login", "class='form-horizontal ng-pristine ng-valid'"); ?>
                <!-- <form class="form-horizontal ng-pristine ng-valid" method="post" accept-charset="utf-8" action="/member/login"> -->

                <fieldset>
                  <div class="form-group">
                    <div class="error">
                      <?php echo $message; ?>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="ui-input-group">
                      <!-- <input type="text" required class="form-control" /> -->
                      <?php echo form_input($username, "", "placeholder='Username' class='form-control' style='padding-inline-start: 35px;'"); ?>
                      <span class="input-bar"></span>
                      <i class="fa fa-user"></i>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="ui-input-group">
                      <!-- <input type="text" required class="form-control" /> -->
                      <?php echo form_input($password, "", "placeholder='Password' class='form-control' style='padding-inline-start: 35px;'"); ?>
                      <span class="input-bar"></span>
                      <i class="fa fa-lock"></i>
                    </div>
                  </div>
                  <div class="spacer"></div>
                  <div class="form-group checkbox-field">
                    <label for="check_box" class="text-small">
                      <!-- <input type="checkbox" id="check_box" /> -->
                      <?php echo form_checkbox('remember', '1', FALSE, 'id="check_box"'); ?>
                      <span class="ion-ios-checkmark-empty22 custom-check"></span>
                      Remember me
                    </label>
                  </div>
                  <div class="spacer"></div>
                  <div class="form-group">
                    <!-- <a class="btn-text log-in" href="#">Log in</a> -->
                    <?php echo form_submit('submit', 'Log in', 'class="btn-text log-in"'); ?>
                  </div>
                  <div class="spacer"></div>
                  <div class="form-group">
                    <a class="btn-text sign-up" href="/member/register">Join Now / Sign up</a>
                  </div>
                </fieldset>
                <!-- </form> -->
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