<?php

//echo $this->Html->script(array('jquery.fileuploader', 'Gift.gift', 'Gift.jquery-ui'), array('inline' => false));
echo $this->Html->css(array('fineuploader', 'Gift.autocomplete','Gift.gift'), array('inline' => false));
echo $this->addPhraseJs(array(
    'tmaxsize' => __d('gift', 'Can not upload file more than ' . $file_max_upload),
    'tdescphoto' => __d('gift', 'Click or Drag your photo here'),
    'tdescaudio' => __d('gift', 'Click or Drag your audio here'),
    'tdescvideo' => __d('gift', 'Click or Drag your video here'),
    'ffmpeg_not_found' => __d('gift', 'FFMPEG is not installed'),
    'gift_ext_photo' => explode(',', GIFT_EXT_PHOTO),
    'gift_ext_audio' => explode(',', GIFT_EXT_AUDIO),
    'gift_ext_video' => explode(',', GIFT_EXT_VIDEO)
));
?>
<style>
.qq-uploader .icon-camera {display:none;}
</style>
<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1>
                <?php if($gift['Gift']['id'] > 0):?>
                    <?php echo __d('gift', 'Edit gift')?>
                <?php else:?>
                    <?php echo __d('gift', 'Create your own gift and send it to your friends')?>
                <?php endif;?>
            </h1>
        </div>
        <div class="create_form">
            <?php echo $this->Form->create('Gift', array(
                'class' => 'form-horizontal', 
                'id' => 'createForm', 
                'role' => 'form'
            )); ?>
            <?php echo $this->Form->hidden('id'); ?>
            <?php echo $this->Form->hidden('saved', array('value' => 0)); ?>
            <ul>
                <?php if(empty($gift['Gift']['id'])):?>
                <li>
                        <?php echo $this->Form->hidden('type'); ?>
                    <?php if($permission_allow_photo_gift):?>
                    <div class="choose-type">
                        <div class="choose-photo choose-photo-m type-img active" value="Photo" id="choose_photo"></div>
                        <div>
                        <?php echo __d('gift','Photo-Gift');?>
                        <span class="count-credits">
                            <?php echo Configure::read('Gift.gift_photo_price') > 0 ? Configure::read('Gift.gift_photo_price')." ".__d('gift',' credits') : __d('gift', 'Free');?>
                        </span>
                        </div>
                    </div>
                    <?php endif;?>

                </li>
                <li class="top-create-form-m">
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <label><?php echo __d('gift', 'Title') ?></label>
                        <?php echo $this->Form->text('title', array('class' => 'mdl-textfield__input')); ?>
                    </div>
                </li>
                <li>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <div class="col-md-2">
                            <label>
                                    <?php echo __d('gift', 'File')?>
                                <span class="required">*</span>
                            </label>
                        </div>
                        <div class="col-md-10">
                                <?php echo $this->Form->hidden('filename'); ?>
                            <div id="select-0" style="margin: 10px 0 0 0px;"></div>
                                <?php if($gift['Gift']['id'] > 0 && $gift['Gift']['type'] == GIFT_TYPE_PHOTO):?>
                            <img width="150" src="<?php echo $this->request->base.'/'.GIFT_THUMB_URL.$gift['Gift']['thumb'];?>" id="item-avatar" class="img_wrapper">
                                <?php else:?>
                            <img width="150" src="" id="item-avatar" class="img_wrapper" style="display: none;">
                                <?php endif;?>
                        </div>
                    </div>
                </li>
                <?php else:?>
                <li>
                    <div class="col-md-2">
                        <label>
                                <?php echo __d('gift', 'Title') ?>
                        </label>
                    </div>
                    <div class="col-md-10">
                            <?php echo $gift['Gift']['title']; ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <?php endif;?>
                <?php if($gift['Gift']['id'] > 0):?>
                <li>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <div class="col-md-2">
                            <label>
                                    <?php echo __d('gift', 'Credits') ?>
                            </label>
                        </div>
                        <div class="col-md-10">
                            <?php if($gift['Gift']['price'] > 0):?>
                                <?php echo $gift['Gift']['price'];?> <?php echo __d('gift', 'Credits'); ?>
                            <?php else:?>
                                <?php echo __d('gift', 'Free'); ?>
                            <?php endif;?>
                        </div>
                    </div>
                </li>
                <?php endif;?>
                <li>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <div class="col-md-2">
                            <label>
                                <?php echo __d('gift', 'Select a friend') ?>
                                <span class="required">*</span>
                            </label>
                        </div>
                        <div class="col-md-10">
                            <div class="wrapper_suggestion">
                                <div id="wrapper_friend">
                                    <?php if(!empty($gift['GiftFriend']['name'])):?>
                                        <div class="friend_item">
                                            <div class="name"><?php echo $gift['GiftFriend']['name'];?></div>
                                            <div class="remove">X</div>
                                        </div>
                                    <?php endif;?>
                                </div>
                                <?php echo $this->Form->hidden('friend_id', array(
                                    'value' => $gift['Gift']['friend_id']
                                )); ?>
                                <?php echo $this->Form->text('friend', array(
                                    'placeholder' => __d('gift', 'Select a friend')
                                )); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <label for="sample3"><?php echo __d('gift', 'Message') ?></label>
                        <?php echo $this->Form->textarea('message', array(
                            'value' => $gift['Gift']['message'],
                            'class' => "mdl-textfield__input"
                        )); ?>
                    </div>
                </li>
                <li>
                    <div style="margin:20px 0">
                        <?php if(empty($gift['Gift']['id']) || $is_clone):?>
                        <a href="javascript:void(0)" id="button_send" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="sendButton">
                                <?php echo __d('gift', 'Send') ?>
                        </a>
                        <?php endif;?>
                        <a href="javascript:void(0)" id="button_save" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="saveButton">
                            <?php echo __d('gift', 'Save') ?>
                        </a>
                        <a href="javascript:void(0)" id="button_preview" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="previewButton">
                            <?php echo __d('gift', 'Preview') ?>
                        </a>
                        <a href="<?php echo $this->request->base;?>/gifts?app_no_tab=1" class=" mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="cancelButton">
                            <?php echo __d('gift', 'cancel');?>
                        </a>
                    </div>
                    <div class="error-message" id="errorMessage" style="display: none;"></div>
                </li>
            </ul>
            </form>

        </div>
    </div>
</div>
<script type="text/template" id="friendItemTemplate">
    <div class="friend_item">
        <div class="name"></div>
        <div class="remove">X</div>
    </div>
</script

<?php 
    $select_type = '';
    if($permission_allow_photo_gift)
    {
        $select_type = GIFT_TYPE_PHOTO;
    }
    else if($permission_allow_audio_gift)
    {
        $select_type = GIFT_TYPE_AUDIO;
    }
    else if($permission_allow_video_gift)
    {
        $select_type = GIFT_TYPE_VIDEO;
    }
?>
<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["mooGift"], function(mooGift) {
            mooGift.initSuggestFriend();
            mooGift.selectCreateGiftType({
                'type' : '<?php echo $select_type;?>'
            });
            mooGift.initCreate({
                'is_ffmpeg_installed' : '<?php echo $is_ffmpeg_installed;?>'
            });
        });
    </script>
<?php else:?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooGift', 'jqueryUi'), 'object' => array('mooGift'))); ?>
    mooGift.initSuggestFriend();
    <?php if(empty($gift['Gift']['id'])):?>
    mooGift.selectCreateGiftType({
        'type' : '<?php echo $select_type;?>'
    });
    <?php endif;?>
    mooGift.initCreate({
        'is_ffmpeg_installed' : '<?php echo $is_ffmpeg_installed;?>'
    });
<?php $this->Html->scriptEnd(); ?>
<?php endif;?>