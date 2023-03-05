<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!--- SEO META TAGS---->
    <meta name="description" content="<?php echo $meta_description; ?>" />
    <meta name="keywords" content="<?php echo $meta_keywords; ?>" />


    <title><?php echo $page_title; ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="/assets/fav.png" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700" rel="stylesheet" />
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/ionicons.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/magnific-popup.css" type="text/css" rel="stylesheet" />

    <!--Main Slider-->
    <link href="/assets/css/settings.css" type="text/css" rel="stylesheet" media="screen" />
    <link href="/assets/css/layers.css" type="text/css" rel="stylesheet" media="screen" />
    <link href="/assets/css/layers.css" type="text/css" rel="stylesheet" media="screen" />
    <link href="/assets/css/owl.carousel.css" type="text/css" rel="stylesheet" media="screen" />
    <link href="/assets/css/style.css" rel="stylesheet" />
    <link href="/assets/css/header1.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/footer1.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/index1.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/theme-color/default.css" rel="stylesheet" type="text/css" id="theme-color" />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-151191007-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-151191007-1');
    </script>

    <!-- google map -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAm-gRSVrsjGjc00jQSkNDVKIzxU8SlkSM&libraries=places">
    </script>

    <link rel="stylesheet" href="http://localhost/css/swiper.min.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="http://localhost/css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="http://localhost/css/colorbox.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
        integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <script type="text/javascript" src="http://localhost/js/jquery-1.5.1.min.js"></script>
    <script type="text/javascript" src="http://localhost/js/jquery.colorbox-min.js"></script>
    <link rel="stylesheet" href="http://localhost/css/home.css" type="text/css" media="screen" />

    <?php
  if (isset($assets)) {
    $this->carabiner->display($assets);
  }
  ?>
</head>

<body>
    <!--loader-->
    <div id="preloader">
        <div class="sk-circle">
            <div class="sk-circle1 sk-child"></div>
            <div class="sk-circle2 sk-child"></div>
            <div class="sk-circle3 sk-child"></div>
            <div class="sk-circle4 sk-child"></div>
            <div class="sk-circle5 sk-child"></div>
            <div class="sk-circle6 sk-child"></div>
            <div class="sk-circle7 sk-child"></div>
            <div class="sk-circle8 sk-child"></div>
            <div class="sk-circle9 sk-child"></div>
            <div class="sk-circle10 sk-child"></div>
            <div class="sk-circle11 sk-child"></div>
            <div class="sk-circle12 sk-child"></div>
        </div>
    </div>

    <!--loader-->

    <!-- HEADER -->
    <header id="header" class="header header-1 header_tran">
        <div class="nav-wrap">
            <div class="reletiv_box">
                <div class="container">
                    <div class="row d-flex align-items-center">
                        <div class="col-md-3">
                            <div class="logo">
                                <a href="/">
                                    <img src="/assets/images/logo.png" alt="" />
                                </a>
                            </div>
                            <!-- Phone Menu button -->
                            <button id="menu" class="menu hidden-md-up"></button>
                        </div>
                        <div class="col-md-9 nav-bg">
                            <nav class="navigation">
                                <ul>
                                    <li>
                                        <a href="/">Home</a>
                                    </li>
                                    <li>
                                        <a href="/about">About us</a>
                                    </li>
                                    <li>
                                        <a href="/testimonial">Testimonial</a>
                                    </li>
                                    <li>
                                        <a href="/contact">Contact</a>
                                    </li>
                                    <?php if ($this->ion_auth->logged_in()) { ?>
                                    <li>
                                        <a href="/member" class="login">Dashboard</a>
                                    </li>
                                    <li>
                                        <a href="/member/logout" class="signup">Logout</a>
                                    </li>
                                    <?php } else { ?>

                                    <li>
                                        <a href="/member/login" class="login">Login</a>
                                    </li>
                                    <li>
                                        <a href="/member/register" class="signup">Sign up</a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- END HEADER -->