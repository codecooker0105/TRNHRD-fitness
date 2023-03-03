<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page_title; ?></title>
<meta http-equiv="Page-Enter" content="revealtrans(duration=0.0)" />
<meta http-equiv="Page-Exit" content="revealtrans(duration=0.0)" />

<meta name="google-site-verification" content="vx9fWjwCSDM-NIhpZ2MWM26Aa4eWinkzVYFHoBguKb8" />
<!--- SEO META TAGS---->
<meta name="description" content="<?php echo $meta_description; ?>" />
<meta name="keywords" content="<?php echo $meta_keywords; ?>" />
<link rel="icon image_src" href="<?=site_url('images/icon-hf.png')?>">
<link rel="stylesheet" href="<?=site_url('css/swiper.min.css')?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?=site_url('css/style.css')?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?=site_url('css/superfish.css')?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?=site_url('css/colorbox.css')?>" type="text/css" media="screen" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script type="text/javascript" src="<?=site_url('js/jquery-1.5.1.min.js')?>"></script>

<link rel="stylesheet" href="<?=site_url('css/home.css')?>" type="text/css" media="screen" />

<!-- jQuery library -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->

<script type="text/javascript" src="<?=site_url('js/jquery.colorbox-min.js')?>"></script>
<?
if(isset($assets)){
    $this->carabiner->display($assets);
} 
?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-151191007-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-151191007-1');
</script>


</head>
<? if(isset($assets) && $assets == 'landing'){ ?>
<body class="land_body" id="landing_page_body">

<div id="landing_page_sct">
<? }else{ ?>
<body class="page_body">
<div id="container">
	<div id="header">
		<? if ($this->ion_auth->logged_in()){ ?><a href="/member"><? }else{ ?><a href="/"><? } ?><img src="/assets/images/logo.png" id="logo" title="Trnhrd" alt="Trnhrd" /></a>
        <div id="login">
            <? if ($this->ion_auth->logged_in()){ 
                $user = $this->ion_auth->get_user();?>
                <div class="logout-dropdown">
                    <div class="logged_in_name">
                        <div class="screenname"><?=$user->first_name?></div>
                        <img src="/images/template/right_screenname.gif" />
                    </div> 
                    <a href="/member/logout" class="singout-link">Sign Out</a>
                </div>
            <? }else{ ?>
            	<div id="have_account">Have an account?</div> <a href="/member/login" class="login_link">Sign In</a>
            <? } ?>
        </div>	
        <div id="menu_container">
        	<? if ($this->ion_auth->logged_in()){ ?>
            	<ul id="menu">
                	<li><a href="/member">Home</a></li>
                    <? if($this->ion_auth->is_group('trainers')){ ?><li><a href="/member/workout_generator">Workout Generator</a></li><? } ?>
                    <li><a href="/member/calendar">Calendar</a></li>
                    <li><a href="/member/log_book">Log Book</a></li>
                </ul>
            <? }else{ ?>
            	<ul id="menu">
                	<li><a href="/base/what">About Us</a></li>
                    <li><a href="/base/how">Features</a></li>
                    <li><a href="/base/why">Testimonials</a></li>
                    <li><a href="/member/register">Start training</a></li>
                </ul>
            <? } ?>
        </div>
	</div>	

	<div id="page" class="clearfix"> 
		<div id="content2">
<? } ?>