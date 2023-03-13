<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css">

    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="icon" type="image/png" href="/images/fav.png">

    <title>Hybrid Fitness</title>
    <style type="text/css">
        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            overflow-x: hidden;
            color: #39393c;
            font-family: "Heebo", Helvetica, Arial, sans-serif;
            font-weight: 300
        }

        a,
        a:hover {
            text-decoration: none;
        }

        .slider-section {
            margin-top: 128px;
            padding: 100px 0px;
            position: relative;
            background-color: #f8f8f8;
        }

        .main-slider-fitness-thumb img {
            width: 100%;
        }

        .slider-info {
            transition: none 0s ease 0s;
            border-width: 0px 0px 0px 1px;
            letter-spacing: 0px;
            font-size: 18px;
            margin: 20px auto 0px auto;
            color: #000000;
        }

        .slide-grow {
            transition: none 0s ease 0s;
            border-width: 0px;
            margin-top: 20px;
            letter-spacing: 0px;
            font-weight: 400;
            font-size: 35px;
            color: #000000;
        }

        .slide-title {
            font-weight: bold !important;
            color: #efbc3f;
            font-size: 55px !important;
            text-transform: uppercase;
            transition: none 0s ease 0s;
            border-width: 0px;
            padding: 0px;
            letter-spacing: 0px;
        }

        .slider-section .owl-theme .owl-dots .owl-dot.active span {
            height: 14px;
            width: 14px !important;
            background: #efbc3f;
            border: 3px solid #999999;
            border-radius: 50%;
            cursor: pointer;
        }

        #topbar {
            background-color: #000000;
            position: fixed;
            top: 0;
            width: 100%;
            display: block;
            transition: 0.3s ease;
            z-index: 999;
            border-bottom: 1px solid #ffffff;
        }

        .comn-section-heading {
            margin-bottom: 40px;
            text-align: center;
        }

        .comn-section-heading h2 {
            font-size: 38px;
            line-height: 48px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .comn-section-heading p {
            color: #efbc3f;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .back-dark {
            background-color: #f8f8f8;
        }

        .tb-padding {
            padding: 100px 0px;
        }

        .normal-tb-padding {
            padding: 80px 0px;
        }

        .small-tb-padding {
            padding: 80px 0px;
        }

        .about-fitness-detail {
            padding: 0px 0px 0px 35px;
            border-left: 4px solid #efbc3f;
        }

        .about-fitness-detail p {
            font-size: 16px;
            line-height: 30px;
        }

        .about-fitness-title {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 18px;
            line-height: 2rem;
        }

        .about-fitness-section .read-more {
            display: inline-block;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            color: #efbc3f;
            margin-top: 25px;
        }

        .about-fitness-thumb {
            display: flex;
            justify-content: center;
        }

        .navbar-expand-lg .navbar-nav .nav-link {
            padding: 1rem !important;
            position: relative;
        }

        .navbar-expand-lg .navbar-nav .nav-link::before {
            content: "";
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 0%;
            height: 2px;
            background-color: #efbc3f;
            bottom: 10px;
            transition: 0.5s ease;
        }

        .custom-nav .nav-item.active .nav-link::before {
            width: 55%;
        }

        .custom-nav .nav-item .nav-link:hover::before {
            width: 55%;
        }

        .light-logo img {
            width: 200px;
            padding: 10px;
        }

        .sticky {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 9999;
            background-color: #ffffff !important;
            border-bottom: 1px solid #f2f2f2;
            transition: top 0.3s ease;
        }

        .contact-section {
            height: 380px;
            position: relative;
            background-color: #efbc3f;
        }

        .contact-area {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%);
            text-align: center;
        }

        .contact-area h4 {
            font-weight: 500;
            font-style: normal;
            font-size: 22px;
            line-height: 1.4;
            letter-spacing: 0px;
            text-transform: uppercase;
            color: #ffffff;
            font-family: 'Raleway', sans-serif;
            margin-bottom: 15px;
        }

        .contact-area p {
            font-weight: 500;
            font-style: normal;
            font-size: 20px;
            line-height: 1.4;
            letter-spacing: 0px;
            text-transform: uppercase;
            font-family: 'Raleway', sans-serif;
            margin-bottom: 15px;
        }

        .contact-section .comn-btn {
            background-color: #000000;
            border-radius: 0px 4px 4px 0px !important;
            color: #ffffff;
            font-size: 14px;
            padding: 15px 25px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border: 1px solid #efbc3f !important;
            transition: 0.5s ease;
        }

        .contact-section .comn-btn:hover {
            background-color: #000000;
            color: #ffffff;
        }

        .comn-btn {
            background: #efbc3f;
            border-radius: 0px;
            color: #ffffff;
            font-size: 14px;
            padding: 10px 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
            border: 1px solid #efbc3f !important;
            transition: 0.5s ease;
        }

        .comn-btn:hover {
            background: transparent;
            color: #efbc3f;
            border: 1px solid #efbc3f !important;
        }

        .subscribe-box {
            display: flex;
            align-items: center;
            width: 400px;
            margin: 40px auto auto auto;
        }

        .subscribe-box .form-control {
            height: 52.4px;
            border-radius: 4px 0px 0px 4px;
        }

        button:focus {
            border: 0px !important;
            outline: 0px !important;
        }

        .footer-area {
            background-color: #000000;
            padding-top: 80px;
        }

        .footer-widgets {
            margin-bottom: 40px;
        }

        .footer-about-widget .footer-logo {
            margin-bottom: 30px;
        }

        .footer-about-widget p {
            color: #949494;
            font-size: 16px;
            line-height: 28px;
            margin-bottom: 0;
        }

        .footer-about-widget .read-more {
            display: inline-block;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            color: #efbc3f;
            margin-top: 25px;
        }

        .footer-widgets .widget-title {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 50px;
            color: #fff;
        }

        .latest-news .news-wrap {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 20px;
        }

        .latest-news .news-wrap .news-img {
            width: 80px;
            margin-right: 20px;
        }

        .latest-news .news-wrap .news-img a {
            display: block;
            overflow: hidden;
        }

        .latest-news .news-wrap .news-img a img {
            width: 100%;
        }

        .latest-news .news-wrap .news-content {
            -ms-flex-preferred-size: 0;
            flex-basis: 0;
            -ms-flex-positive: 1;
            flex-grow: 1;
            max-width: 100%;
        }

        .latest-news .news-wrap .news-content h4 {
            font-size: 18px;
            line-height: 28px;
            font-weight: 600;
            margin-bottom: 0;
        }

        .latest-news .news-wrap .news-content span {
            display: inline-block;
            color: #949494;
            font-size: 14px;
            font-weight: 500;
            font-family: "Barlow", sans-serif;
        }

        .contact-widget ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .contact-widget ul li {
            display: block;
            color: #949494;
            font-size: 16px;
            line-height: 24px;
            margin-bottom: 10px;
            position: relative;
            padding-left: 30px;
        }

        .contact-widget ul li i {
            position: absolute;
            left: 0;
            top: 5px;
            color: #efbc3f;
        }

        .contact-widget ul li span {
            overflow: hidden;
        }

        .footer-bottom {
            background: #202020;
            padding: 17px 0;
        }

        .latest-news .news-wrap .news-content h4 a {
            color: #949494;
        }

        .latest-news .news-wrap .news-content h4 a:hover {
            color: #fff;
        }

        .contact-widget ul li a {
            color: #949494;
        }

        .contact-widget ul li i {
            position: absolute;
            left: 0;
            top: 5px;
            color: #efbc3f;
        }

        .footer-social-link ul li a:before {
            content: "";
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            border-radius: 50%;
            position: absolute;
            visibility: hidden;
            opacity: 0;
            z-index: -1;
            -webkit-transition: all 0.3s linear 0s;
            -moz-transition: all 0.3s linear 0s;
            -ms-transition: all 0.3s linear 0s;
            -o-transition: all 0.3s linear 0s;
            transition: all 0.3s linear 0s;
            background: #efbc3f;
            background-image: -moz-linear-gradient(-72deg, #efbc3f 0%, #ee7349 99%);
            background-image: -webkit-linear-gradient(-72deg, #efbc3f 0%, #ee7349 99%);
            background-image: -ms-linear-gradient(-72deg, #efbc3f 0%, #ee7349 99%);
        }

        .footer-social-link ul li a:hover:before {
            opacity: 1;
            visibility: visible;
        }

        .footer-social-link ul li a:hover i {
            color: #ffffff;
        }

        .copyright p {
            color: #949494;
            margin-bottom: 0;
        }

        .copyright p a {
            color: #efbc3f;
        }

        .footer-social-link {
            text-align: right;
        }

        .footer-social-link ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .footer-social-link ul li {
            display: inline-block;
        }

        .footer-social-link ul li a {
            position: relative;
            display: block;
            width: 40px;
            height: 40px;
            line-height: 38px;
            font-size: 14px;
            color: #a9a9a9;
            border: 2px solid #2e2e2e;
            text-align: center;
            border-radius: 50%;
            z-index: 1;
        }

        .section-title-3 .bars {
            padding-top: 12px;
            position: relative;
        }

        .section-title-3 h3 {
            font-size: 24px;
            line-height: 34px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .section-title-3 .bars:before {
            content: "";
            background: #efbc3f;
            width: 50px;
            height: 4px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .section-title-3 h3 span {
            color: #efbc3f;
        }

        .feature-area-3 {
            background-image: url(/images/bg1.svg);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .feature-area-3::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .feature-title .read-more {
            display: inline-block;
            font-size: 16px;
            font-weight: 700;
            color: #efbc3f;
            border-bottom: 2px solid #efbc3f;
            text-transform: uppercase;
        }

        .feature-title .read-more i {
            margin-left: 5px;
            transition: margin-left 0.3s linear 0s;
        }

        .feature-area-3 .comn-section-heading h2 {
            color: #ffffff;
        }

        .feature-wrap-3 {
            padding: 0 30px 30px 95px;
            position: relative;
        }

        .feature-wrap-3 .feature-icon {
            position: absolute;
            left: 0;
            top: 8px;
            width: 70px;
            height: 70px;
            line-height: 60px;
            border-radius: 50%;
            font-size: 32px;
            color: #fff;
            text-align: center;
            background-color: #efbc3f;
            background-image: -moz-linear-gradient(-72deg, #efbc3f 0%, #ee7349 99%);
            background-image: -webkit-linear-gradient(-72deg, #efbc3f 0%, #ee7349 99%);
            background-image: -ms-linear-gradient(-72deg, #efbc3f 0%, #ee7349 99%);
        }

        .feature-icon img {
            width: 30px;
        }

        .feature-wrap-3 .feature-content h3 {
            font-size: 24px;
            line-height: 38px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 5px;
        }

        .feature-wrap-3 .feature-content p {
            font-size: 16px;
            line-height: 28px;
            color: #cccccc;
            margin-bottom: 0;
        }

        .feature-wrap-3 .feature-content .read-more {
            font-size: 14px;
            font-weight: 700;
            color: #efbc3f;
            text-transform: uppercase;
            margin-top: 14px;
            display: inline-block;
        }

        .feature-title .read-more {
            display: inline-block;
            font-size: 16px;
            font-weight: 700;
            color: #efbc3f;
            border-bottom: 2px solid #efbc3f;
            text-transform: uppercase;
        }

        /*-----Testimonial-------*/
        .testimonial4_header {
            top: 0;
            left: 0;
            bottom: 0;
            width: 550px;
            display: block;
            margin: 30px auto;
            text-align: center;
            position: relative;
        }

        .testimonial4_header h4 {
            color: #ffffff;
            font-size: 30px;
            font-weight: 600;
            position: relative;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .testimonial4_slide {
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            position: relative;
            background: #ffffff;
            padding: 25px;
            box-shadow: 0px 8px 15px #dddddd;
            margin: 20px 15px;
            border-radius: 5px;
        }

        .testimonial4_slide .testimonial-client-img {
            top: 0;
            left: 0;
            right: 0;
            width: 100px !important;
            height: 100px;
            margin: auto;
            color: #f2f2f2;
            font-size: 18px;
            line-height: 46px;
            text-align: center;
            position: relative;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .testimonial4_slide p {
            color: #000000e6;
            font-size: 16px;
            line-height: 1.6;
        }

        .testimonial4-info {
            position: relative;
            text-align: center;
        }

        .testimonial4_slide h4 {
            color: #000000e6;
            font-size: 22px;
        }

        .testimonial-quote-icon {
            position: absolute !important;
            left: 0px !important;
            box-shadow: none !important;
            width: 25px !important;
            margin: unset !important;
            top: 0px !important;
            opacity: 0.4;
        }

        .testimonial-heading {
            position: relative;
            margin: 20px 0px 30px 0px;
        }

        .testimonial-heading::before {
            content: "";
            background: #efbc3f;
            width: 50px;
            height: 4px;
            position: absolute;
            left: 0;
            top: -20px;
        }

        .testimonial-section .owl-theme .owl-dots .owl-dot.active span {
            background: #efbc3f !important;
            width: 35px;
            transition: 0.5s ease;
        }

        .testimonial-sub-heading {
            color: #000000e6;
            line-height: 1.8rem;
            font-size: 15px;
        }

        /* ------testimonial  close-------*/

        .account-link {
            color: #ffffff !important;
        }

        .top-account-area {
            list-style: none !important;
            display: flex;
            justify-content: flex-end;
            padding: 0px 17px 0px 0px;
        }

        .top-account-area li:not(:last-child) {
            border-right: 1px solid #cccccc80;
        }

        .top-account-area li {
            padding: 5px 12px;
        }

        .top-account-area a {
            color: #ffffffd1 !important;
            font-size: 14px;
            font-weight: 500;
            padding: 8px;
        }

        #ScreenSlider .carousel-inner {
            position: relative;
            width: 100%;
            overflow: hidden;
            max-width: 250px;
            margin: auto;
            height: 100%;
            background: white;
            border-radius: 39px !important;
            box-shadow: -6px 8px 15px 0px #00000036;
        }

        #ScreenSlider .carousel-inner:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url(images/phone-blank.png);
            z-index: 1;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            border: 1px solid #fff;
        }

        #ScreenSlider .carousel-inner .carousel-item {
            padding: 11px;
        }

        #ScreenSlider img {
            max-width: 300px;
            width: 100%;
        }

        /*key section*/

        .key-feature-single-box {
            text-align: center;
            background: #ffffff;
            padding: 40px 30px;
            box-shadow: 0px 0px 15px 0px #dddddd;
            border-radius: 20px;
            height: 355px;
            transition: 0.5s ease;
        }

        .key-feature-single-box:hover {
            transform: translateY(-10px);
            box-shadow: 0px 0px 40px 0px #dddddd;
        }

        .key-feature-icon img {
            width: 105px;
            height: 85px;
            margin: auto;
        }

        .key-feature-info {
            font-size: 16px;
            line-height: 26px;
            margin-top: 15px;
        }

        .key-feature-title {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 0px;
            margin-top: 20px;
        }

        /*category section*/

        .category-title {
            font-size: 20px;
            font-weight: 400;
            line-height: 1.1;
            margin-bottom: 0px;
            margin-top: 20px;
            color: #efbc3f;
        }

        .category-info {
            font-size: 16px;
            line-height: 26px;
            margin-top: 20px;
        }

        .category-heading {
            font-size: 42px;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 0px;
            margin-top: 25px;
        }

        .category-thumb img {
            width: 100%;
            border-radius: 5px;
        }

        /*.screen-slider-tab-list{
            width: 50px !important;
            height: 50px !important;
            background-color: #efbc3f !important;
            box-shadow: 0px 0px 40px 0px rgba(0,0,0,0.25);
            color: #ffffff !important;
            opacity: 1 !important;
            padding-left: 0px !important;
            text-indent: unset !important;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 22px;
            border-radius: 5px;
            border-top: 0px solid transparent !important;
            border-bottom: 0px solid transparent !important;
        }
        .carousel-indicators {
            bottom: -25px !important;
        }*/

        @media(max-width: 992px) {
            .navbar-expand-lg .navbar-nav .nav-link::before {
                content: unset;
            }

            .light-logo img {
                width: 150px;
            }

            .navbar-light .navbar-toggler {
                border-color: transparent !important;
            }

            .slider-section {
                margin-top: 115px;
            }
        }

        @media(max-width: 768px) {
            .slider-section .carousel-item .slider-main-image {
                height: 600px !important;
            }

            .slider-info {
                border-left: 0px solid transparent;
                padding: 0px;
                line-height: 25px;
            }

            .slide-title {
                font-size: 45px !important;
            }

            .about-fitness-detail {
                padding: 0px;
                border-left: 0px solid #0000;
            }

            .footer-social-link {
                text-align: center;
            }

            .copyright p {
                text-align: center;
            }

            .footer-social-link ul {
                margin-top: 8px;
            }

            .section-title-3 h3 {
                font-size: 26px;
            }

            .feature-title {
                margin-bottom: 25px;
            }

            .feature-wrap-3 {
                padding: 100px 20px 20px 20px;
                margin-top: 25px;
            }

            .feature-wrap-3 .feature-icon {
                left: 50%;
                transform: translateX(-50%);
            }

            .feature-content {
                text-align: center;
            }

            .slider-info {
                width: 100%;
            }

            .category-detail {
                margin-bottom: 20px;
            }

            .key-feature-single-box {
                margin-bottom: 22px;
            }

            .about-fitness-thumb {
                margin-top: 25px;
            }

            .main-slider-fitness-thumb {
                margin-top: 25px;
            }
        }

        @media(max-width: 480px) {
            .subscribe-box {
                width: 315px;
            }
        }
    </style>
</head>

<body data-spy="scroll" data-target=".navigation-section" data-offset="1">

    <!-- top section start -->
    <section class="top-section" id="topbar">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <ul class="top-account-area mb-0">
                        <li><a href="#" class="account-link"><i class="fa fa-unlock"></i> Login</a></li>
                        <li><a href="#" class="account-link"><i class="fa fa-user"></i> Register</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- top section end -->

    <!-- navigation section start -->
    <header class="navigation-section" id="navigation-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <nav class="navbar navbar-expand-lg navbar-light bg-default custom-nav">
                        <a class="navbar-brand light-logo" href="#"><img src="/images/logo1.svg"></a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse d-lg-flex justify-content-end" id="navbarSupportedContent">
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a class="nav-link" href="#home">HOME</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#about">ABOUT US</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#testimonial">TESTIMONIAL</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#contact">CONTACT</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- navigation section end -->

    <!-- slider section start -->
    <section class="slider-section" id="home">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6 d-flex align-items-center">
                    <div class="main-slider-content">
                        <h4 class="slide-title">The Best Fitness</h4>
                        <h4 class="slide-grow">grow your Strenght</h4>
                        <p class="slider-info">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur
                            adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                        <button class="comn-btn mt-5">Explore More</button>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="main-slider-fitness-thumb">
                        <img src="/images/top-banner.svg" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- slider section end -->

    <!-- key features section start -->
    <section class="key-feature-section small-tb-padding">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="key-feature-single-box">
                        <div class="key-feature-icon">
                            <img src="/images/key-features-1.svg">
                        </div>
                        <div class="key-feature-detail">
                            <h4 class="key-feature-title">Ways to Train</h4>
                            <p class="key-feature-info">Train online, in-person, or both using Trainerize and deliver a
                                connected fitness experience for all your clients. Use groups to train multiple clients
                                at once, or build a community for your clients and members.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="key-feature-single-box">
                        <div class="key-feature-icon">
                            <img src="/images/key-features-2.svg">
                        </div>
                        <div class="key-feature-detail">
                            <h4 class="key-feature-title">Get paid</h4>
                            <p class="key-feature-info">Our fully-integrated payment feature is built specifically for
                                fitness businesses and makes it easier than ever to manage your client and member
                                purchases and get paid fast.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="key-feature-single-box mb-0">
                        <div class="key-feature-icon">
                            <img src="/images/key-features-3.svg">
                        </div>
                        <div class="key-feature-detail">
                            <h4 class="key-feature-title">Build your brand</h4>
                            <p class="key-feature-info">Show the world what makes your fitness business or club unique
                                with a Custom Branded Fitness App. Our technology. Your branding.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- key features section end -->

    <!-- about fitness section start -->
    <section class="about-fitness-section back-dark tb-padding" id="about">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="comn-section-heading">
                        <h2>FITNESS AND HEALTH</h2>
                        <p>A PLACE FOR YOUR FITNESS GOAL</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="about-fitness-detail">
                        <h4 class="about-fitness-title">TYPE SETTING REMAINING, ESSENTIALLY UNCHANGED ITS WAS
                            POPULARISED</h4>

                        <p class="mb-2">With the release of Letraset sheets containing Lorem Ipsum passages, and more
                            recently with desktop publishing software like Aldus PageMaker including versions.</p>

                        <p class="mb-0">We approach our projects with strategic and creative thinking. We partner with
                            our clients to create big ideas and digital experiences. And we spend each day doing so by
                            sharpening the tools of the digital trade.</p>
                    </div>
                    <!-- <a class="read-more" href="#">
                        Learn more <i class="fa fa-angle-double-right"></i>
                    </a> -->
                    <button class="comn-btn mt-5">Explore More</button>
                </div>
                <div class="col-12 col-md-6">
                    <div class="about-fitness-thumb">
                        <img src="/images/fitness5.svg" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- about fitness section end -->

    <!-- category section start -->
    <section class="category-section tb-padding">
        <div class="container">
            <div class="row pb-5">
                <div class="col-12 col-md-6">
                    <div class="category-detail">
                        <div class="category-title">ONLINE TRAINING</div>
                        <div class="category-heading">
                            Lorem ipsum dolor
                        </div>
                        <div class="category-info">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="category-thumb">
                        <img src="/images/cat2.jpg">
                    </div>
                </div>
            </div>

            <div class="row pb-5 flex-column-reverse flex-lg-row">
                <div class="col-12 col-md-6">
                    <div class="category-thumb">
                        <img src="/images/cat3.jpg">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="category-detail">
                        <div class="category-title">CLIENT ENGAGEMENT</div>
                        <div class="category-heading">
                            Lorem ipsum dolor
                        </div>
                        <div class="category-info">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="category-detail">
                        <div class="category-title">BUSINESS GROWTH</div>
                        <div class="category-heading">
                            Lorem ipsum dolor
                        </div>
                        <div class="category-info">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                            consequat.
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="category-thumb">
                        <img src="/images/cat1.jpg">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- category section end -->

    <!-- feature-area start -->
    <section class="feature-area-3 tb-padding">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 col-md-12">
                    <div class="comn-section-heading">
                        <h2>WELCOME TO HYBRID FITNESS</h2>
                        <p>A PLACE FOR YOUR FITNESS GOAL</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-12 text-center align-self-center">
                    <div id="ScreenSlider" class="carousel slide slider-screen aos-init aos-animate"
                        data-ride="carousel" data-aos="fade-up">
                        <!-- The slideshow -->
                        <div class="carousel-inner screen-slider">
                            <div class="carousel-item active" id="0">
                                <img src="images/scr1.png" alt="screen0">
                            </div>
                            <div class="carousel-item" id="1">
                                <img src="images/scr2.png" alt="screen1">
                            </div>
                            <div class="carousel-item" id="2">
                                <img src="images/scr3.png" alt="screen2">
                            </div>
                            <div class="carousel-item" id="3">
                                <img src="images/scr4.png" alt="screen3">
                            </div>
                            <div class="carousel-item" id="4">
                                <img src="images/scr5.png" alt="screen4">
                            </div>
                            <div class="carousel-item" id="5">
                                <img src="images/scr6.png" alt="screen5">
                            </div>
                            <div class="carousel-item" id="6">
                                <img src="images/scr7.png" alt="screen6">
                            </div>
                            <div class="carousel-item" id="7">
                                <img src="images/scr8.png" alt="screen7">
                            </div>
                            <div class="carousel-item" id="8">
                                <img src="images/scr9.png" alt="screen8">
                            </div>
                        </div>
                    </div>
                    <!-- Slider Closed -->
                </div>
                <div class="col-12 col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="feature-wrap-3 mb-3">
                                <div class="feature-icon">
                                    <img src="/images/weight.svg">
                                </div>
                                <div class="feature-content">
                                    <h3>Crosfit Tools</h3>
                                    <p>
                                        Ullam corporis suscipit laborio sam nisi ut aliquid exea commode consequatur
                                        autem velum
                                    </p>
                                    <a href="service.html" class="read-more">
                                        Read more <i class="fa fa-angle-double-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-wrap-3 mb-3">
                                <div class="feature-icon">
                                    <img src="/images/pushup-tool.svg">
                                </div>
                                <div class="feature-content">
                                    <h3>Crosfit Tools</h3>
                                    <p>
                                        Ullam corporis suscipit laborio sam nisi ut aliquid exea commode consequatur
                                        autem velum
                                    </p>
                                    <a href="service.html" class="read-more">
                                        Read more <i class="fa fa-angle-double-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-wrap-3 mb-3">
                                <div class="feature-icon">
                                    <img src="/images/pushup-man.svg">
                                </div>
                                <div class="feature-content">
                                    <h3>GYM Strategies</h3>
                                    <p>
                                        Ullam corporis suscipit laborio sam nisi ut aliquid exea commode consequatur
                                        autem velum
                                    </p>
                                    <a href="service.html" class="read-more">
                                        Read more <i class="fa fa-angle-double-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-wrap-3 mb-3">
                                <div class="feature-icon">
                                    <img src="/images/wellness.svg">
                                </div>
                                <div class="feature-content">
                                    <h3>Beauty &amp; Spa</h3>
                                    <p>
                                        Ullam corporis suscipit laborio sam nisi ut aliquid exea commode consequatur
                                        autem velum
                                    </p>
                                    <a href="service.html" class="read-more">
                                        Read more <i class="fa fa-angle-double-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="screen-slider-tab">
                        <ol class="carousel-indicators">
                            <li data-target="#ScreenSlider" data-slide-to="0" class="active screen-slider-tab-list">
                                <i class="fa fa-facebook"></i>
                            </li>
                            <li data-target="#ScreenSlider" data-slide-to="1" class="screen-slider-tab-list">
                                <i class="fa fa-twitter"></i>
                            </li>
                            <li data-target="#ScreenSlider" data-slide-to="2" class="screen-slider-tab-list">
                                <i class="fa fa-instagram"></i>
                            </li>
                            <li data-target="#ScreenSlider" data-slide-to="3" class="screen-slider-tab-list">
                                <i class="fa fa-linkedin"></i>
                            </li>
                            <li data-target="#ScreenSlider" data-slide-to="4" class="screen-slider-tab-list">
                                <i class="fa fa-youtube-play"></i>
                            </li>
                            <li data-target="#ScreenSlider" data-slide-to="5" class="screen-slider-tab-list">
                                <i class="fa fa-google-plus"></i>
                            </li>
                            <li data-target="#ScreenSlider" data-slide-to="6" class="screen-slider-tab-list">
                                <i class="fa fa-pinterest-p"></i>
                            </li>
                            <li data-target="#ScreenSlider" data-slide-to="7" class="screen-slider-tab-list">
                                <i class="fa fa-skype"></i>
                            </li>
                            <li data-target="#ScreenSlider" data-slide-to="8" class="screen-slider-tab-list">
                                <i class="fa fa-github"></i>
                            </li>
                        </ol>
                    </div> -->
                </div>
            </div>
        </div>
    </section>
    <!-- feature-area end -->

    <!-- testimonial section start -->
    <section class="testimonial-section tb-padding" id="testimonial">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-5">
                    <div class="">
                        <h2 class="testimonial-heading">TESTIMONIAL</h2>
                        <p class="testimonial-sub-heading">Lorem Ipsum is simply dummy text of the printing and
                            typesetting industry. Lorem Ipsum has been the industry's Lorem Ipsum is simply dummy text
                            of the printing and typesetting industry. Lorem Ipsum has been the industry's</p>
                    </div>
                </div>
                <div class="col-12 col-md-7">

                    <div class="contain">
                        <div id="testimonialSlider" class="owl-carousel owl-theme">
                            <div class="item">
                                <div class="testimonial4_slide">
                                    <img src="http://hybridfitness.com/images/user2.png"
                                        class="testimonial-client-img img-responsive" />
                                    <div class="testimonial4-info">
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                            Lorem Ipsum has been the industry's</p>
                                        <h4>Client 1</h4>
                                        <img src="/images/quote.svg" class="testimonial-quote-icon">
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="testimonial4_slide">
                                    <img src="http://hybridfitness.com/images/user2.png"
                                        class="testimonial-client-img img-responsive" />
                                    <div class="testimonial4-info">
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                            Lorem Ipsum has been the industry's</p>
                                        <h4>Client 2</h4>
                                        <img src="/images/quote.svg" class="testimonial-quote-icon">
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="testimonial4_slide">
                                    <img src="http://hybridfitness.com/images/user2.png"
                                        class="testimonial-client-img img-responsive" />
                                    <div class="testimonial4-info">
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                            Lorem Ipsum has been the industry's</p>
                                        <h4>Client 2</h4>
                                        <img src="/images/quote.svg" class="testimonial-quote-icon">
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <div class="testimonial4_slide">
                                    <img src="http://hybridfitness.com/images/user2.png"
                                        class="testimonial-client-img img-responsive" />
                                    <div class="testimonial4-info">
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                                            Lorem Ipsum has been the industry's</p>
                                        <h4>Client 2</h4>
                                        <img src="/images/quote.svg" class="testimonial-quote-icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- testimonial section end -->

    <!-- contact section start -->
    <section class="contact-section tb-padding" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="contact-area">
                        <h4>JOIN HYBRID FITNESS WHO ARE GETTING FITNESS PROGRAMS.</h4>
                        <p>WE'RE HERE PROVIDE BEST TRAINER.</p>
                        <div class="form-group mb-0 subscribe-box">
                            <input type="email" class="form-control" placeholder="Enter your email">
                            <button class="comn-btn"> SUBSCRIBE </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact section end -->

    <!-- footer section start  -->
    <footer class="footer-area">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="footer-widgets footer-about-widget">
                            <div class="footer-logo">
                                <a href="index.html">
                                    <img src="/images/logo1.svg">
                                </a>
                            </div>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                consequat.
                            </p>
                            <a class="read-more" href="about.html">
                                Learn more <i class="fa fa-angle-double-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="footer-widgets latest-news">
                            <h3 class="widget-title">Latest news</h3>
                            <div class="news-wrap">
                                <div class="news-img">
                                    <a href="#">
                                        <img src="https://www.devsnews.com/template/gymee/gymee/assets/img/widget/widget-1.png"
                                            alt="widget">
                                    </a>
                                </div>
                                <div class="news-content">
                                    <h4>
                                        <a href="blog-details.html">
                                            Monthly Web Develop UpdateFunctional CSS, Android
                                        </a>
                                    </h4>
                                    <span><i class="fa fa-calendar"></i> 05 Jan 20</span>
                                </div>
                            </div>
                            <div class="news-wrap">
                                <div class="news-img">
                                    <a href="#">
                                        <img src="https://www.devsnews.com/template/gymee/gymee/assets/img/widget/widget-2.png"
                                            alt="widget">
                                    </a>
                                </div>
                                <div class="news-content">
                                    <h4>
                                        <a href="blog-details.html">
                                            We're Touring Southeast Asia Join To Mozilla Developer
                                        </a>
                                    </h4>
                                    <span><i class="fa fa-calendar"></i> 05 Jan 20</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="footer-widgets contact-widget">
                            <h3 class="widget-title">Contact Us</h3>
                            <ul>
                                <li>
                                    <i class="fa fa-home"></i>
                                    <span>No.123 Chalingt Gates, Supper market New York</span>
                                </li>
                                <li>
                                    <a href="#&quot;&quot;">
                                        <i class="fa fa-envelope"></i>
                                        <span>support@gmail.com</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-phone"></i>
                                        <span>+012 (4567) 789</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-7 col-lg-6">
                        <div class="copyright">
                            <p>Copyright <a href="index.html">HYBRID FITNESS</a> ©2020. All Rights Reserved</p>
                        </div>
                    </div>
                    <div class="col-md-5 col-lg-6">
                        <div class="footer-social-link">
                            <ul>
                                <li><a href="#"><i class="fa fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                <li><a href="#"><i class="fa fa-google"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer section end -->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
        crossorigin="anonymous"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.js"></script>

    <script>
        window.onscroll = function () { myFunction() };

        var navbar = document.getElementById("navigation-section");
        var sticky = navbar.offsetTop;

        var prevScrollpos = window.pageYOffset;

        function myFunction() {
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("sticky")
            } else {
                navbar.classList.remove("sticky");
            }

            var currentScrollPos = window.pageYOffset;
            if (prevScrollpos > currentScrollPos) {
                document.getElementById("topbar").style.top = "0";
                document.getElementById("navigation-section").style.top = "34px";
            } else {
                document.getElementById("topbar").style.top = "-50px";
                document.getElementById("navigation-section").style.top = "0";
            }
            prevScrollpos = currentScrollPos;

        }

        // show top bar on scroll page

        // var prevScrollpos = window.pageYOffset;
        // window.onscroll = function() {
        // var currentScrollPos = window.pageYOffset;
        //   if (prevScrollpos > currentScrollPos) {
        //     document.getElementById("topbar").style.top = "0";
        //   } else {
        //     document.getElementById("topbar").style.top = "-50px";
        //   }
        //   prevScrollpos = currentScrollPos;
        // }


        $('#testimonialSlider').owlCarousel({
            loop: true,
            margin: 0,
            dots: true,
            nav: false,
            items: 2,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                },
                600: {
                    items: 2,
                },
                1000: {
                    items: 2,
                    loop: false
                }
            }
        })
    </script>
</body>

</html>