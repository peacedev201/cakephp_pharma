<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

$vShowHide = false;
$userModel = MooCore::getInstance()->getModel('User');
$userModel->cacheQueries = false;
$user = $userModel->find('first', array('conditions' => array('User.id' => $_SESSION['Auth']['User']['id'])))['User'];

if ($user['VIDEO'] == VIDEO_EXTEND_FOREVER || ($user['VIDEO'] && !empty($user['FEELING_valid'])  && date('Ymd') <= date('Ymd',strtotime($user['VIDEO_valid']))))
    $vShowHide = true;
?>

<?php echo $this->Form->hidden('video_destination', array('value' => '')); ?>
<div id="videoPcFeed" data-toggle="tooltip" class="<?= ($vShowHide) ? 'show-video' : '' ?>" title="<?php echo __('Upload Video'); ?>"></div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooUploadVideo'), 'object' => array('mooUploadVideo'))); ?>
mooUploadVideo.initVideoUploadActivityForm();<?php $this->Html->scriptEnd(); ?>


