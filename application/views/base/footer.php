<!-- Footer -->
<?php if (!isset($footer_visibility)) { ?>
    <footer class="main-footer">
        <div class="container-fluid">
            <div class="row footer_logo">
                <div class="col-12">
                    <img src="/assets/images/logo.png" alt="logo">
                </div>
            </div>
            <div class="row footer-links">
                <div class="col-md-8 offset-md-2">
                    <hr />
                    <div class="row">
                        <div class="col-md-4 col-4">
                            <div>
                                <a href="/">Home</a>
                            </div>
                            <div>
                                <a href="/about">About Us</a>
                            </div>
                            <div>
                                <a href="/testimonial">Testimonial</a>
                            </div>
                            <div>
                                <a href="/contact">Contact</a>
                            </div>
                        </div>
                        <div class="col-md-4 col-4">
                            <div>
                                <a href="">FAT & Contact</a>
                            </div>
                            <div>
                                <a href="#">Careers</a>
                            </div>
                            <div>
                                <a href="#">Corporate Wellness</a>
                            </div>
                        </div>
                        <div class="col-md-4 col-4">
                            <div>
                                <a href="#">Instagram</a>
                            </div>
                            <div>
                                <a href="#">Facebook</a>
                            </div>
                            <div>
                                <a href="https://twitter.com/HybridFitnessNY" target="_blank">Twitter</a>
                            </div>
                            <!-- div id="twitter">
              <a class="twitter-timeline"  href="https://twitter.com/HybridFitnessNY"  data-widget-id="481213975980302336">Tweets by @HybridFitnessNY</a>
              <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            </div -->
                        </div>
                    </div>
                </div>
            </div>
    </footer>
<?php } ?>
<!-- Footer_End -->

<!-- Site Wraper End -->
<script type="text/javascript" src="/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="/assets/js/tether.min.js"></script>
<script type="text/javascript" src="/assets/js/bootstrap.min.js"></script>
<script src="/assets/js/jquery.easing.js" type="text/javascript"></script>

<!-- fancybox Js -->
<script src="/assets/js/jquery.mousewheel-3.0.6.pack.js" type="text/javascript"></script>
<script src="/assets/js/jquery.fancybox.pack.js" type="text/javascript"></script>
<!-- popup -->
<script src="/assets/js/jquery.magnific-popup.min.js" type="text/javascript"></script>

<!-- carousel Js -->
<script src="/assets/js/owl.carousel.js" type="text/javascript"></script>

<!-- imagesloaded Js -->
<script src="/assets/js/imagesloaded.pkgd.min.js" type="text/javascript"></script>
<!-- masonry,isotope Effect Js -->
<script src="/assets/js/imagesloaded.pkgd.min.js" type="text/javascript"></script>
<script src="/assets/js/isotope.pkgd.min.js" type="text/javascript"></script>
<script src="/assets/js/masonry.pkgd.min.js" type="text/javascript"></script>
<script src="/assets/js/jquery.appear.js" type="text/javascript"></script>
<!-- Mail Function Js -->
<script src="/assets/js/mail.js" type="text/javascript"></script>

<!-- revolution Js -->
<script type="text/javascript" src="/assets/js/jquery.themepunch.tools.min.js"></script>
<script type="text/javascript" src="/assets/js/jquery.themepunch.revolution.min.js"></script>
<script type="text/javascript" src="/assets/extensions/revolution.extension.slideanims.min.js"></script>
<script type="text/javascript" src="/assets/extensions/revolution.extension.layeranimation.min.js"></script>
<script type="text/javascript" src="/assets/extensions/revolution.extension.navigation.min.js"></script>
<script type="text/javascript" src="/assets/extensions/revolution.extension.parallax.min.js"></script>
<script type="text/javascript" src="/assets/js/jquery.revolution.js"></script>
<!-- custom Js -->
<script src="/assets/js/custom1.js" type="text/javascript"></script>

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
<script src="/css/swiper.min.js"></script>

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
    
    function select_member(value) {
        // Get the value of the selected radio button
        var selectedOption = document.querySelectorAll('input[name="member_type"]');
        if (value == 'members') {
            selectedOption[0].checked = true;
            selectedOption[1].checked = false;
        } else {
            selectedOption[0].checked = false;
            selectedOption[1].checked = true;
        }
    }
</script>

</body>

</html>