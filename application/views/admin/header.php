<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Trnhrd Admin Area</title>
    <meta http-equiv="Page-Enter" content="revealtrans(duration=0.0)" />
    <meta http-equiv="Page-Exit" content="revealtrans(duration=0.0)" />
    <link rel="stylesheet" href="<?= site_url('css/style.css') ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?= site_url('css/superfish.css') ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?= site_url('css/colorbox.css') ?>" type="text/css" media="screen" />
    <script type="text/javascript" src="<?= site_url('js/jquery-1.5.1.min.js') ?>"></script>
    <script type="text/javascript" src="<?= site_url('js/jquery.colorbox-min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.play-exercise').colorbox();
        });
    </script>
    <?
    if (isset($assets)) {
        $this->carabiner->display($assets);
    }
    ?>
</head>

<body>
    <div id="container">
        <div id="header">
            <a href="/admin">
                <img src="/assets/images/logo.png" id="logo" title="Trnhrd" alt="Trnhrd" />

            </a>
            <h1>Admin Panel</h1>
            <div id="menu_container">
                <?php if ($this->ion_auth->logged_in()) { ?>
                    <ul id="menu">
                        <li><a href="/admin">Home</a></li>
                        <li><a href="/admin/members">Members</a></li>
                        <li><a href="/admin/exercises">Exercises</a></li>
                        <li><a href="/admin/skeleton_workouts">Skeleton Workouts</a></li>
                        <li><a href="/admin/progression_plans">Progression Plans</a></li>
                    </ul>
                <?php } ?>
            </div>
        </div>

        <div id="page" class="clearfix">

            <div id="content2" class="login-page-box">

                <div id="main">
                    <div id="copy" class="form-box">