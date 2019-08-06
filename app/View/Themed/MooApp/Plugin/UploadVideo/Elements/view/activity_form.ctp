<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php echo $this->Form->hidden('video_destination', array('value' => '')); ?>
<div id="videoPcFeed" class="moo-app-video-upload" data-toggle="tooltip" title="<?php echo __('Upload Video'); ?>"></div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooUploadVideo'), 'object' => array('mooUploadVideo'))); ?>
mooUploadVideo.initVideoUploadActivityForm();<?php $this->Html->scriptEnd(); ?>


