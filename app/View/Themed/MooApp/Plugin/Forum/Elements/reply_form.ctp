<?php
$helper = MooCore::getInstance()->getHelper('Forum_Forum');
?>
<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooViewForum"], function($,mooViewForum) {
            mooViewForum.initReplyTopic('0','<?php echo implode(',', $helper->support_extention());?>');
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooViewForum'), 'object' => array('$', 'mooViewForum'))); ?>
    mooViewForum.initReplyTopic('0', '<?php echo implode(',', $helper->support_extention());?>');
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>


<form id="topicReplyForm<?php echo $topic['ForumTopic']['id'];?>" class="form-horizontal forum-topic-post-form" action="<?php echo  $this->request->base; ?>/forums/topic/save_reply" enctype="multipart/form-data" method="post">
    <?php echo $this->Form->hidden('parent_id', array('value' => $topic['ForumTopic']['id']));
    echo $this->Form->hidden('forum_id', array('value' => $topic['ForumTopic']['forum_id']));
    echo $this->Form->hidden('forum_topic_photo_ids', array('id' => 'forum_topic_photo_ids_0'));
    ?>
    <div class="form-group" id="post_reply_form">
        <div class="col-sm-12">
            <h2 class="forum-post-reply-title"><?php echo __d('forum', 'Post a reply');?></h2>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php if(!$topic['ForumTopic']['status']):?>
                <div class="forum-form-note"><?php echo __d('forum', "This topic is marked as locked to new reply, however your posting capabilities still allow to do so") ?></div>
            <?php endif;?>

            <?php echo $this->Form->textarea('description', array('value' => '', 'class' => 'forum-textarea', 'id' => 'description'.$topic['ForumTopic']['id'])); ?>

            <div class="forum-form-note"><?php echo __d('forum', "To tag member, please click on icon %s at the editor header","<img src='{$this->request->base}/forum/img/user-64x64.png' style='max-width: 16px;' />")?></div>

            <div id="images-uploader-0" style="display:none;margin:10px 0;">
                <div id="attachments_upload_0"></div>
                <a href="javascript:void(0)" class="button button-primary" id="triggerUpload_0"><?php echo __d('forum', 'Upload Files')?></a>
            </div>
            <?php if(empty($isMobile)): ?>
                <a id="toggleUploader_0" class="forum-attachment-toggle" href="javascript:void(0)"><?php echo __d('forum', 'Image Upload')?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">

        <div class="col-sm-12">
            <label class="control-label text-left"><?php echo __d('forum','Attachements')?></label>
            <div id="topic_file_upload_0"></div>
            <div id="topic_file_review_0"></div>
            <input type="hidden" name="new_files" id="new_files_0" value="">
            <input type="hidden" name="new_original_files" id="new_original_files_0" value="">
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <?php echo $this->Form->checkbox('subscribe', array('checked' => $is_subscribe, 'id' => 'chk_subscribe')); ?>
            <?php echo __d('forum','Send me notifications when other members reply to this topic');?>
        </div>
    </div>
    <?php if ($helper->isReplyRecaptchaEnabled()): ?>
        <div class="form-group">
            <div class="col-sm-12">
                <div class="captcha_box">
                    <script src='<?php echo $this->Moo->getRecaptchaJavascript();?>'></script>
                    <div class="g-recaptcha" data-sitekey="<?php echo $this->Moo->getRecaptchaPublickey(); ?>"></div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <div class="col-sm-12">
            <a id="topic_reply_button" class='mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1 topic_reply_button' data-id="<?php echo $topic['ForumTopic']['id'];?>"><?php echo __d('forum' ,'Post')?></a>
        </div>
    </div>
    <div class="forum-error-message" id="errorMessage" style="display:none"></div>
</form>