<?php
	$helper = MooCore::getInstance()->getHelper('Forum_Forum');
    $new_files = $new_original_files = array();
?>
<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooViewForum"], function($,mooViewForum) {
            mooViewForum.initReplyTopic('<?php echo $id;?>', '<?php echo implode(',', $helper->support_extention());?>');
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooViewForum'), 'object' => array('$', 'mooViewForum'))); ?>
        mooViewForum.initReplyTopic('<?php echo $id;?>', '<?php echo implode(',', $helper->support_extention());?>');
    <?php $this->Html->scriptEnd('<?php echo $id;?>', '<?php echo implode(',', $helper->support_extention());?>'); ?>
<?php endif; ?>

<div class="modal-body">
    <div class="create_form">
    <div class="bar-content">
        <div class="content_center">
            <div class="mo_breadcrumb">
                <h1><?php echo __d('forum','Edit reply');?></h1>
            </div>
            <div class="box3">
                <form action="<?php echo  $this->request->base; ?>/forums/topic/save_reply" enctype="multipart/form-data" id="topicReplyForm<?php echo $id;?>" method="post">
                <?php echo $this->Form->hidden('id', array('value' => $id));?>
                <?php echo $this->Form->hidden('parent_id', array('value' => $reply['ForumTopic']['parent_id']));
                    echo $this->Form->hidden('forum_id', array('value' => $reply['ForumTopic']['forum_id']));
                    echo $this->Form->hidden('forum_topic_photo_ids', array('id' => 'forum_topic_photo_ids_'.$id));
                    ?>
                    <ul class="list6">
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __d('forum','Description')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->tinyMCE('description', array('value' => $reply['ForumTopic']['description'], 'class' => 'forum-textarea', 'id' => 'description'.$id)); ?>
                                <span><?php echo __d('forum', "To tag member, please click on icon %s at the editor header","<img src='{$this->request->base}/forum/img/user-64x64.png' style='max-width: 16px;' />")?></span><br>
                                <div id="images-uploader-<?php echo $id;?>" style="display:none;margin:10px 0;">
                                    <div id="attachments_upload_<?php echo $id;?>"></div>
                                    <a href="javascript:void(0)" class="button button-primary" id="triggerUpload_<?php echo $id;?>"><?php echo __d('forum', 'Upload Files')?></a>
                                </div>
                                <?php if(empty($isMobile)): ?>
                                <a id="toggleUploader_<?php echo $id;?>" href="javascript:void(0)"><?php echo __d('forum', 'Image Upload')?></a>
                                <?php endif; ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __d('forum','Attachements')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php if(!empty($files)): ?>
                                    <?php foreach ($files as $file):
                                            $new_files[] = $file['ForumFile']['file_name'];
                                            $new_original_files[] = $file['ForumFile']['download_url'];
                                        ?>
                                    <div class="tp-file-item" id="file_item_<?php echo $file['ForumFile']['id'];?>"><?php echo $file['ForumFile']['download_url'];?>
                                        <a class="btn-delete-file" data-id="<?php echo $file['ForumFile']['id'];?>" data-file="<?php echo $file['ForumFile']['file_name'];?>" data-originalfile="<?php echo $file['ForumFile']['download_url'];?>"><?php echo __d('forum','delete');?></a>
                                    </div>
                                    <?php endforeach;?>
                                <?php endif;?>

                                <div id="topic_file_upload_<?php echo $id;?>"></div>
                                <div id="topic_file_review_<?php echo $id;?>"></div>

                                <input type="hidden" name="new_files" id="new_files_<?php echo $id;?>" value="<?php echo implode(',', $new_files);?>">
                                <input type="hidden" name="new_original_files" id="new_original_files_<?php echo $id;?>" value="<?php echo implode(',', $new_original_files);?>">
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->checkbox('subscribe', array('checked' => $is_subscribe, 'id' => 'chk_subscribe')); ?>
                                <?php echo __d('forum','Send me notifications when other members reply to this topic');?>
                            </div>
                        </li>
                        <li>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                            </div>
                            <div class="col-md-10">
                                <a id="topic_reply_button" class='btn btn-action topic_reply_button' data-id="<?php echo $id;?>"><?php echo __d('forum' ,'Post')?></a>
                                <a href="<?php echo $this->request->base.'/forums/topic/view/'.$reply['ForumTopic']['parent_id'];?>" class="button"><?php echo __d('forum' ,'Cancel')?></a>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="forum-error-message" id="errorMessage" style="display:none"></div>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</div>
</div>