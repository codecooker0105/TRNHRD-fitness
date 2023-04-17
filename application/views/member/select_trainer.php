<!-- Intro Section -->
<section class="bg-img-home light-color overlay-before parallax-background select-trainer">
  <div class="container">
    <div class="row title">
      <div class="title_row">
        <h1 data-title="Contact">
          Get Started With a trainer
        </h1>
        <h2>
          These trainers are experts in training for your goals. Select a trainer to get started, with the flexibility
          to change at any time.
        </h2>
        <div id="infoMessage">
          <?php echo $message; ?>
        </div>
      </div>
      <div class="slider_select_trainer">
        <?php if ($all_trainers) {
          foreach ($all_trainers as $trainer) {
          ?>
          <div class="courses_popular" data-id="<?= $trainer->id ?>">
            <a href="#" class="select-trainer">
              <div class="top_cours">
                <figure>
                  <?php if ($trainer->photo) { ?>
                    <img src="/images/member_photos/<?php echo $trainer->photo; ?>" class="avatar-img" alt="avatar">
                  <?php } else { ?>
                    <img src="/images/template/no_photo.jpg" class="avatar-img" alt="avatar" />
                  <?php } ?>
                  <!-- <img src="/assets/images/explanation/1.png" alt="" /> -->
                </figure>
                <!-- <div class="apply_box d-flex align-items-center">
                  <div class="full_width">
                    <a href="#" class="btn-text">Read More</a>
                  </div>
                </div> -->
              </div>
              <div class="courses_detail">
                <h3 class="single-line-text-wrap"><?= $trainer->username ?></h3>
                <p>
                  There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration
                  in some form, by injected humour.
                </p>
                <button class="read-more">Read More</button>
              </div>
            </a>
          </div>
          <?php }
          }?>
      </div>
      <div class="select_trainer_button">
        <?php echo form_open("member/select_trainer", 'id="select_trainer_form"'); ?>
        <?php echo form_hidden('selected_trainer_id', 0); ?>
        <div class="form-group">
          <input type="submit" class="submit" value="Select a trainer">
        </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</section>
<!-- End Intro Section -->