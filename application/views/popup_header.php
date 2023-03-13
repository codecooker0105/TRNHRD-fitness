<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Trnhrd</title>
    <meta http-equiv="Page-Enter" content="revealtrans(duration=0.0)" />
    <meta http-equiv="Page-Exit" content="revealtrans(duration=0.0)" />
    <link rel="stylesheet" href="<?= site_url('css/style.css') ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?= site_url('css/superfish.css') ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?= site_url('css/colorbox.css') ?>" type="text/css" media="screen" />
    <script type="text/javascript" src="<?= site_url('js/jquery-1.5.1.min.js') ?>"></script>
    <script type="text/javascript" src="<?= site_url('js/jquery.colorbox-min.js') ?>"></script>
    <?php
    if (isset($assets)) {
        $this->carabiner->display($assets);
    }
    ?>
</head>

<body id="popup">
    <div id="popup_container">