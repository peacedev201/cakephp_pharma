<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php
$this->MooPopup->tag(array(
    'href' => $this->Html->url(array("controller" => "upload_videos", "action" => "ajax_upload", "plugin" => "upload_video")),
    'class' => 'button button-action topButton button-mobi-top',
    'innerHtml' => __('Upload Video'),
    'title' => __('Upload Video'),
));


