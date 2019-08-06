<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div id="video_pc_feed_preview" style="display: none;">
    <div id="video_upload_preview" class="<?php echo !empty($target_id) ? 'video_thumb_target' : ''; ?>">
        <div class="pull-right">
            <a href="javascript:void(0)" class="close-upload-video" id="closeUploadVideo" data-toggle="tooltip" title="<?php echo __('Close'); ?>">X</a>
        </div>
        <div class="clear"></div>
        <div class="left">
            <div id="video_thumb_preview" class="video_thumb">
                <img src="" style="display: none;"/>
            </div>
        </div>
        <div class="right">
            <div>
                <?php echo $this->Form->text('title', array('value' => 'Untitled video', 'placeholder' => __('Untitled video'))); ?>
            </div>
            <?php if (empty($target_id)): ?>
            <div>
                <?php echo $this->Form->select('category_id', $categories, array('empty' => false, 'value' => '')); ?>
            </div>
            <?php endif; ?>
            <div>
                <?php echo $this->Form->textarea('description', array('value' => '', 'placeholder' => __('Description'))); ?>
            </div>
        </div>
    </div>
</div>


