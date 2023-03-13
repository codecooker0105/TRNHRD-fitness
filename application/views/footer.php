<?php if (isset($assets) && $assets == 'landing') { ?>
  </div>
<?php } else { ?>

  </div>
  </div>
  </div>
  <div id="footer">
    <div id="inner_footer">
      <ul id="first_links">
        <li>Our Company</li>
        <li><a href="/base/what">About Us</a></li>
        <li><a href="/base/how">Our Features</a></li>
        <li><a href="/base/why">Testimonials</a></li>
        <!--<li><a href="/licensing">Licensing</a></li>
            <li><a href="/contact">Contact Us</a></li>-->
      </ul>
      <ul id="second_links">
        <li>Get Connected</li>
        <li><a href="/member/register">Sign Up</a></li>
        <!--<li><a href="/partnership">Partnership</a></li>-->
        <li><a href="https://twitter.com/HybridFitnessNY" target="_blank">Twitter</a></li>
        <!--<li><a href="">Facebook</a></li>
            <li><a href="">Linked In</a></li>-->
      </ul>
      <!--<p>&copy;<?= date('Y') ?> Trnhrd, all rights reserved. | <a href="/sitemap">Sitemap</a> | <a href="http://www.c2js.com" target="_blank">Website developed by Jumpstart</a></p>-->
      <div id="twitter">
        <a class="twitter-timeline" href="https://twitter.com/HybridFitnessNY" data-widget-id="481213975980302336">Tweets
          by @HybridFitnessNY</a>
        <script>!function (d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https'; if (!d.getElementById(id)) { js = d.createElement(s); js.id = id; js.src = p + "://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs); } }(document, "script", "twitter-wjs");</script>


      </div>
    </div>
  </div>
<?php } ?>

<script>
  $(document).ready(function () {
    $('#menu li a').removeClass("active");
    if ($(".form-box")[0]) {
      $(".form-box").parents("#page").find("#content2").addClass("login-page-box");
    } else {
      $(".form-box").parents("#page").find("#content2").removeClass("login-page-box");
    }

    $(function () {
      var url = window.location.pathname,
        urlRegExp = new RegExp(url.replace(/\/$/, '') + "$");
      $('#menu li a').each(function () {
        if (urlRegExp.test(this.href.replace(/\/$/, ''))) {
          $(this).addClass('active');
          $(this).parent().previoussibling().find('a').removeClass('active');
        }
      });
    });

  });
</script>
<!-- Swiper JS -->
<script src="<?= site_url('css/swiper.min.js') ?>"></script>

<!-- Initialize Swiper -->
<script>
  var swiper = new Swiper('.swiper-container', {
    direction: 'vertical',
    autoplay: {
      delay: 2000,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });
</script>
</body>

</html>