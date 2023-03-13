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
    <div class="col-lg-2" style="background: white;">
      <div class="account-profile">
        <div>
          <?php if ($user->photo != "") { ?>
            <img src="/images/member_photos/<?php echo $user->photo; ?>" class="avatar-img" alt="avatar">
          <?php } else { ?>
            <img src="/images/template/no_photo.jpg" class="avatar-img" alt="avatar" />
          <?php } ?>
        </div>
        <h5>Weight Loss: <span>3.5 KG</span></h5>
        <div class="bottom-line"></div>
        <div class="acount-profile-content">
          <div class="widget_shop">
            <div id="MainMenu2">
              <div class="list-group panel">
                <div id="demo3">
                  <a href="/member/log_book" class="list-group-item">Logbook</i></a>
                  <a href="/member/calendar" class="list-group-item">Calendar</i></a>
                  <a href="/member/workout_generator" class="list-group-item">WorkOut Generator</i></a>
                  <a href="#category2" class="list-group-item" data-toggle="collapse" data-parent="#2">Account
                    Functions<i class="fa fa-caret-down"></i></a>
                  <div class="collapse list-group-submenu" id="category2">
                    <a href="/member/edit_account" class="list-group-item">Edit Account Profile</a>
                    <a href="/member/edit_photo" class="list-group-item">Edit Avatar</a>
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
    <div class="col-lg-8 today_workout">
      <?php if ($member_group == 'member' && $trainer) { ?>
        <h3>Todays work out loaded</h3>
      <?php } ?>
      <?php if ($member_group == 'trainer') { ?>
        <h3>Workouts</h3>
      <?php } ?>
      <div class="row">
        <div class="col-lg-8">
          <div class=" recent-workout">
            <h4>Recent workout</h4>
            <hr style="background-color: lightgray;" />
            <div class="carousel-slider-recent-work nf-carousel-theme arrow_theme">

              <div class="textimonial_show">
                <div class="testimonial-block cyan-background">
                  <div>
                    <img class="img-circle img-border" src="/assets/images/testimonial/2.jpg" alt="" />
                  </div>
                  <div>
                    <h3>Recent workout</h3>
                    <p>
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                      Quam quos aperiam ipsam modi dolor suscipit asperiores
                      perspiciatis.
                    </p>
                  </div>
                </div>
              </div>

              <div class="textimonial_show">
                <div class="testimonial-block cyan-background">
                  <div>
                    <img class="img-circle img-border" src="/assets/images/testimonial/2.jpg" alt="" />
                  </div>
                  <div>
                    <h3>Recent workout</h3>
                    <p>
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                      Quam quos aperiam ipsam modi dolor suscipit asperiores
                      perspiciatis.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">

          <?php if ($member_group == 'member' && $trainer) { ?>
            <div class="past-workout">
              <h4>Past workout</h4>
              <hr style="background-color: lightgray;" />
              <div class="item-workout">
                <div class="item-workout-avtar">
                  <img src="/assets/images/about-1.jpg" alt="">
                </div>
                <div class="item-workout-content text-center">
                  <h5>Workout name</h5>
                  <span>20 mins</span>
                </div>
              </div>

              <div class="item-workout">
                <div class="item-workout-avtar">
                  <img src="/assets/images/about-1.jpg" alt="">
                </div>
                <div class="item-workout-content text-center">
                  <h5>Workout name</h5>
                  <span>20 mins</span>
                </div>
              </div>

              <div class="item-workout">
                <div class="item-workout-avtar">
                  <img src="/assets/images/about-1.jpg" alt="">
                </div>
                <div class="item-workout-content text-center">
                  <h5>Workout name</h5>
                  <span>20 mins</span>
                </div>
              </div>

              <div class="item-workout">
                <div class="item-workout-avtar">
                  <img src="/assets/images/about-1.jpg" alt="">
                </div>
                <div class="item-workout-content text-center">
                  <h5>Workout name</h5>
                  <span>20 mins</span>
                </div>
              </div>
            </div>
          <?php } ?>

          <?php if ($member_group == 'trainer') { ?>
            <div class="past-workout">
              <h4>Exercise Library</h4>
              <hr style="background-color: lightgray;" />
              <a class="add-exercise" href="#">add exercise</a>
              <div class="item-workout">
                <div class="item-workout-avtar">
                  <img src="/assets/images/about-1.jpg" alt="">
                </div>
                <div class="item-workout-content text-center">
                  <h5>Workout name</h5>
                </div>
              </div>
              <br />

              <div class="item-workout">
                <div class="item-workout-avtar">
                  <img src="/assets/images/about-1.jpg" alt="">
                </div>
                <div class="item-workout-content text-center">
                  <h5>Workout name</h5>
                </div>
              </div>
              <br />

              <div class="item-workout">
                <div class="item-workout-avtar">
                  <img src="/assets/images/about-1.jpg" alt="">
                </div>
                <div class="item-workout-content text-center">
                  <h5>Workout name</h5>
                </div>
              </div>
              <br />

              <div class="item-workout">
                <div class="item-workout-avtar">
                  <img src="/assets/images/about-1.jpg" alt="">
                </div>
                <div class="item-workout-content text-center">
                  <h5>Workout name</h5>
                </div>
              </div>
              <br />
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="col-lg-2" style="background: white;">
      <?php if ($member_group == 'member' && $trainer) { ?>
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
            <h4><?= $trainer->first_name ?>   <?= $trainer->last_name ?></h4>
            <p><?= $trainer->email ?></p>
            <!-- category-list -->
          </div>
        </div>
      <?php } ?>

      <?php if ($member_group == 'trainer') { ?>
        <div class="account-profile">
          <h3>My Clients</h3>
          <div class="past-workout">
            <?php if ($clients) {
              foreach ($clients as $client) {
                ?>
                <div class="item-workout">
                  <div class="item-workout-avtar">
                    <img src="/images/template/no_photo.jpg" />
                  </div>
                  <div class="item-workout-content text-center">
                    <h5><?= $client->first_name . ' ' . $client->last_name ?></h5>
                  </div>
                </div>
                <br />
              <?php
              }
            } ?>
          </div>

        </div>
      <?php } ?>
    </div>
  </div>
</section>