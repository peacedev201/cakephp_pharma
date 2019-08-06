<!DOCTYPE html>
<html style="height: 100%">
<head>
    <meta charset="utf-8">
    <title>
        <?php if ( Configure::read('core.site_offline') ) echo __('[OFFLINE]'); ?>

        <?php if (isset($title_for_layout) && $title_for_layout){ echo $title_for_layout; } else if(isset($mooPageTitle) && $mooPageTitle) { echo $mooPageTitle; } ?> | <?php echo Configure::read('core.site_name'); ?>
    </title>

    <!--
    ===========META====================-->
    <?php $description = "";?>
    <?php if (isset($description_for_layout) && $description_for_layout){ $description = $description_for_layout; }else if(isset($mooPageDescription) && $mooPageDescription) {$description = $mooPageDescription;}else if(Configure::read('core.site_description')){ $description = Configure::read('core.site_description');}?>
    <meta name="description" content="<?php echo $this->Moo->convertDescriptionMeta($description);?>" />
    <meta name="keywords" content="<?php if(isset($mooPageKeyword) && $mooPageKeyword){echo $mooPageKeyword;}else if(Configure::read('core.site_keywords')){ echo Configure::read('core.site_keywords');}?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="robots" content="index,follow" />

    <meta property="og:site_name" content="<?php echo Configure::read('core.site_name'); ?>" />
    <meta property="og:title" content="<?php if (isset($title_for_layout) && $title_for_layout){ echo $title_for_layout; } else if(isset($mooPageTitle) && $mooPageTitle) { echo $mooPageTitle; } ?>" />
    <meta property="og:url" content="<?php echo $this->Html->url( null, true ); ?>" />
    <link rel="canonical" href="<?php echo $this->Html->url( null, true ); ?>" />
    <?php if(isset($og_image)): ?>
        <meta property="og:image" content="<?php echo $og_image?>" />
    <?php else: ?>
        <meta property="og:image" content="<?php echo $this->Moo->ogImage();?>" />
    <?php endif; ?>
    <!--
    ===========META====================-->

    <?php echo  $this->Html->css('https://fonts.googleapis.com/css?family=Roboto:400,300,500,700'); ?>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">



    <!--
    ===========STYLE====================-->
    <?php
    echo $this->Html->meta('icon');
    $this->loadLibarary('mooCore');
    echo $this->fetch('meta');
    echo $this->fetch('css');
    ?>
    <!--
    ===========END STYLE====================-->
</head>
<?php
$cookies_warning = Configure::read('core.enable_cookies');
$deny_url = Configure::read('core.deny_url');
?>
<body class=" <?php if($cookies_warning && $deny_url && empty($accepted_cookie)):
    ?> page_has_cookies<?php endif; ?>" id="<?php echo $this->getPageId(); ?>" style="background-color:#000000">
<div style="height: 100%;
left: 0;
overflow: hidden;
    overflow-y: hidden;
position: absolute;
top: 0;
width: 100%;">
    <?php echo $this->fetch('mooVideoContent'); ?>
</div>

<div class="container " id="content-wrapper" <?php $this->getNgController() ?>>
    <?php echo html_entity_decode( Configure::read('core.header_code') )?>


    <div class="row">
        <?php

        $flash_mess = $this->Session->flash();
        echo $flash_mess;
        if(empty($flash_mess))
            echo $this->Session->flash('confirm_remind');
        ?>

    </div>
    <!-- Modal -->
</div>

<!--
===========SCRIPT====================-->
<?php
echo $this->fetch('config');
echo $this->fetch('mooPhrase');
echo $this->fetch('mooChatScript');
?>
<!--
===========END SCRIPT================-->


</body>
</html>