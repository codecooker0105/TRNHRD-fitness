<!-- CONTENT -->
<!-- Intro Section -->
<section class="inner-intro bg-img-home light-color overlay-before parallax-background">
  <div class="container">
    <div class="row title">
      <div class="title_row">
      </div>
    </div>
  </div>
</section>
<!-- Intro Section -->

<section class="dashboard">
  <div class="row" style="margin: 0;">
    <div class="col-lg-2 col-md-2" style="background: white;padding: 0;">
      <div class="account-profile">
        <div>
          <?php if ($user->photo != "") { ?>
            <img src="/images/member_photos/<?php echo $user->photo; ?>" class="avatar-img" alt="avatar">
          <?php } else { ?>
            <img src="/images/template/no_photo.jpg" class="avatar-img" alt="avatar" />
          <?php } ?>
        </div>
        <h4>Weight Loss: <span>3.5 KG</span></h4>
        <div class="bottom-line"></div>
        <div class="acount-profile-content">
          <div class="widget_shop">
            <div id="MainMenu2">
              <div class="list-group panel">
                <div id="demo3">
                  <a href="/member/log_book" class="list-parent list-group-item">Logbook</a>
                  <!-- <a href="/member/calendar" class="list-group-item">Calendar</a> -->
                  <!-- <a href="/member/workout_generator" class="list-group-item">WorkOut Generator</a> -->
                  <div class="list-parent list-group-item">Account Functions</div>
                  <div class="list-group-submenu">
                    <a href="/member/edit_account" class="list-group-item">Edit Account Profile</a>
                    <!-- <a href="/member/edit_photo" class="list-group-item">Edit Avatar</a> -->
                    <a href="/member/change_password" class="list-group-item">Change password</a>
                  </div>
                </div>
              </div>
            </div>
            <!-- category-list -->
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8 col-md-8 today_workout">
      <?php if ($member_group == 'member' && $trainer) { ?>
        <h3>Todays work out loaded</h3>
      <?php } ?>
      <?php if ($member_group == 'trainer') { ?>
        <h3>Workouts</h3>
      <?php } ?>
      <div class="row">
        <div class="col-lg-8 col-md-8">
          <div class=" recent-workout">
            <h4>Recent workout</h4>
            <hr style="background-color: lightgray;" />
            <div class="carousel-slider-recent-work nf-carousel-theme arrow_theme">
              <?php if ($get_past_workouts_testimonial) {
                foreach ($get_past_workouts_testimonial as $testimonial_item) {
                  ?>
                  <div class="textimonial_show">
                    <div class="testimonial-block cyan-background">
                      <div class="title">
                        <h4>
                          <?php echo $testimonial_item->first_name . ' ' . $testimonial_item->last_name; ?>
                        </h4>
                      </div>
                      <div class="content">
                        <div>
                          <img class="img-circle img-border" src="/assets/images/testimonial/2.jpg" alt="" />
                        </div>
                        <div class="content-body">
                          <h3>
                            <?php echo $testimonial_item->title ? $testimonial_item->title : 'Workout'; ?>
                          </h3>
                          <p class="multi-line-text-wrap">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                            Quam quos aperiam ipsam modi dolor suscipit asperiores
                            perspiciatis.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php }
              } else { ?>
                <div>
                  <h2>No Recent Workout</h2>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-4">
          <?php if ($member_group == 'member' && $trainer) { ?>
            <div class="past-workout client-part">
              <h4>Past workout</h4>
              <hr style="background-color: lightgray;" />
              <?php if ($past_5_workouts) {
                foreach ($past_5_workouts as $workout_item) {
                  ?>
                  <div class="item-workout">
                    <div class="item-workout-avtar">
                      <img src="/assets/images/about-1.jpg" alt="">
                    </div>
                    <div class="item-workout-content text-center">
                      <h5 class="single-line-text-wrap">
                        <?php echo $workout_item->title ? $workout_item->title : 'Workout name'; ?>
                      </h5>
                      <span>20 mins</span>
                    </div>
                  </div>
                <?php }
              } else { ?>
                <div>
                  <h4>No Past Workout</h4>
                </div>
              <?php } ?>
            </div>
          <?php } ?>

          <?php if ($member_group == 'trainer') { ?>
            <div class="past-workout trainer-part">
              <h4>Exercise Library</h4>
              <hr style="background-color: lightgray;" />
              <div class="content">
                <button class="add-exercise">add exercise</button>
                <?php if ($workouts) {
                  foreach ($workouts as $workout_item) {
                    ?>
                    <div class="item-workout">
                      <input type="hidden" class="exercise_id" name="exercise_id" value="<?= $workout_item->exercise_id ?>" />
                      <div class="item-workout-avtar">
                        <img src="/assets/images/about-1.jpg" alt="">
                      </div>
                      <div class="item-workout-content text-center single-line-text-wrap">
                        <h5 class="single-line-text-wrap">
                          <?php echo $workout_item->exercise_title ? $workout_item->exercise_title : 'Workout name'; ?>
                        </h5>
                      </div>
                      <div class="item-workout-play">
                        <button class="show_video">
                          <img src="/images/template/play.png" alt="">
                        </button>
                      </div>
                    </div>
                  <?php }
                } else { ?>
                  <div>
                    <h4>No Past Workout</h4>
                  </div>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php if ($member_group == 'member' && $trainer) { ?>
      <div class="col-lg-2 col-md-2 client-part" style="background: white;">
        <div class="account-profile">
          <h3>My Trainer</h3>
          <div>
            <?php if ($trainer->photo != "") { ?>
              <img src="/images/member_photos/<?php echo $trainer->photo; ?>" class="avatar-img" alt="avatar" />
            <?php } else { ?>
              <img src="/images/template/no_photo.jpg" class="avatar-img" alt="avatar" />
            <?php } ?>

          </div>
          <div class="widget_shop">
            <h4>
              <?= $trainer->first_name ?>
              <?= $trainer->last_name ?>
            </h4>
            <p>
              <?= $trainer->email ?>
            </p>
            <!-- category-list -->
          </div>
        </div>
      </div>
    <?php } ?>

    <?php if ($member_group == 'trainer') { ?>
      <div class="col-lg-2 col-md-2 trainer-part" style="background: white;">
        <div class="account-profile">
          <h3>My Clients</h3>
          <div class="past-workout">
            <?php if ($clients) {
              foreach ($clients as $client) {
                ?>
                <div class="item-workout dashboard_client" data-id="<?= $client->id ?>">
                  <div class="item-workout-avtar">
                    <?php if ($client->photo != "") { ?>
                      <img src="/images/member_photos/<?php echo $client->photo; ?>" class="avatar-img" alt="avatar">
                    <?php } else { ?>
                      <img src="/images/template/no_photo.jpg" class="avatar-img" alt="avatar" />
                    <?php } ?>
                  </div>
                  <div class="item-workout-content text-center">
                    <h5>
                      <?= $client->first_name . ' ' . $client->last_name ?>
                    </h5>
                  </div>
                </div>
                <br />
                <?php
              }
            } else { ?>
              <div class="">
                <h4>No Client</h4>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php } ?>
    <div id="popup_video" class="modal" style="display:none;">
      <div class="modal-content">
        <span class="close_button video_close">&times;</span>
        <video width="100%" height="auto" controls="controls">
          <source id="video_source" src="/video/mobile_exercises/Chest_1_Pushup.mp4" type="video/mp4">
        </video>
      </div>
    </div>
    <div id="popup_add_exercise" class="modal" style="display:none;">
      <div class="modal-content">
        <span class="close_button close_add_exercise">&times;</span>
        <h3>add exercise</h3>
        <div class="form-group">
          <label>exercise name</label>
          <input class="" name="exercise_id" placeholder="Name" />
        </div>
        <div class="form-group">
          <label>Group</label>
          <span>Neque porro quisquam est qui dolorem</span>
          <select name="type" id="exercise_group_dropdown" class="ui-widget-content ui-corner-all">
            <option value="">Select Group</option>
            <?php if ($exercise_group) {
              foreach ($exercise_group as $group_item) {
              ?>
                <option value="<?= $group_item->id ?>"><?= $group_item->title ?></option>
              <?php
              }
            } ?>
          </select>
        </div>
        <div class="form-group">
          <h2>Add video</h2>
          <label for="video-upload" class="custom-file-upload">
            Add video now
          </label>
          <input id="video-upload" type="file"/>
        </div>
        <button id="add_exercise_submit" class="submit">add exercise</button>
      </div>
    </div>
  </div>
</section>