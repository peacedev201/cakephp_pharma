<?php
    echo $this->addPhraseJs(array(
        'tmaxsize' => __d('gift', 'Can not upload file more than ' . $file_max_upload),
        'tdescphoto' => __d('gift', 'Click or Drap your photo here'),
        'tdescaudio' => __d('gift', 'Click or Drap your audio here'),
        'tdescvideo' => __d('gift', 'Click or Drap your video here'),
        'ffmpeg_not_found' => __d('gift', 'FFMPEG is not installed'),
        'gift_ext_photo' => explode(',', GIFT_EXT_PHOTO),
        'gift_ext_audio' => explode(',', GIFT_EXT_AUDIO),
        'gift_ext_video' => explode(',', GIFT_EXT_VIDEO)
    ));
    echo $this->Html->css(array(
        'fineuploader',
        'jquery-ui', 
        'footable.core.min',
        'Gift.autocomplete'), null, array('inline' => false));
    echo $this->Html->script(array(
        'Gift.jquery.fileuploader',
        'jquery-ui', 
        'footable',
        'Gift.gift',
        'Gift.jquery-ui'), array('inline' => false));
    $this->Html->addCrumb(__d('gift',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('gift',  "Manage Gifts"), array('plugin' => 'gift', 'controller' => 'gift'));
    $this->Html->addCrumb(__d('gift',  "Create Gift"));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Gift'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Gift', 'Manage Gifts');?>
<div id="page-wrapper">
    <div class="portlet box">
        <div class="portlet-title">
            <?php echo __d('gift', "Create Gift");?>
            <div class="clear"></div>
        </div>
        <div class="portlet-body form">
            <?php echo $this->Form->create('Gift', array(
                'class' => 'form-horizontal', 
                'id' => 'createForm', 
                'role' => 'form'
            )); ?>
                <?php echo $this->Form->hidden('id'); ?>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('gift', 'Title') ?>
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-9">
                        <?php echo $this->Form->text('title', array(
                            'placeholder' => __d('gift', 'Title'),
                            'class' => 'form-control'
                        )); ?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('gift', 'Credit (0 for free)') ?>
                    </label>
                    <div class="col-md-9">
                        <?php echo $this->Form->text('price', array(
                            'placeholder' => __d('gift', 'Credit (0 for free)'),
                            'class' => 'form-control',
                            'disabled' => (!$gift_integrate_credit || !Configure::read('Credit.credit_enabled'))? true : false,
                        )); ?>
                        <?php if(!$gift_integrate_credit || !Configure::read('Credit.credit_enabled')):?>
                        <p style="color:red"><?php echo __d('gift', 'Please integrate credit module if you want to set fee(cost) for this gift');?></p>
                        <?php endif;?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('gift', 'Category') ?>
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-9">
                        <?php echo $this->Form->select('gift_category_id', $categories, array(
                            'empty' => array('' => __d('gift', 'Select')),
                            'class' => 'form-control',
                            // 'onchange' => 'changeCategory()'
                        )); ?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('gift', 'Type') ?>
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-9">
                        <?php echo $this->Form->select('type', array(
                                GIFT_TYPE_PHOTO => __d('gift', 'Photo'),
                                GIFT_TYPE_AUDIO => __d('gift', 'Audio'),
                                GIFT_TYPE_VIDEO => __d('gift', 'Video')
                            ), array(
                                'empty' => false,
                                'class' => 'form-control',
                                'onchange' => 'selectType()'
                        )); ?>
                        <?php echo $this->Form->hidden('filename'); ?>
                        <div id="select-0" style="margin: 10px 0 0 0px;"></div>
                        <?php if(!empty($gift["Gift"]["filename"]) && !empty($gift["Gift"]["type"]) && $gift["Gift"]["type"] == GIFT_TYPE_PHOTO):?>
                            <img width="150" src="<?php echo $this->request->base.'/'.GIFT_FILE_URL.$gift["Gift"]["filename"];?>" id="item-avatar" class="img_wrapper">
                            <span id="file_name" style="display: none"></span>
                        <?php elseif(!empty($gift["Gift"]["filename"]) && !empty($gift["Gift"]["type"]) && ($gift["Gift"]["type"] == GIFT_TYPE_AUDIO || $gift["Gift"]["type"] == GIFT_TYPE_VIDEO)):?>
                            <img width="150" src="" id="item-avatar" class="img_wrapper" style="display: none;">
                            <span id="file_name"><?php echo $gift["Gift"]["filename"];?></span>
                        <?php else:?>
                            <img width="150" src="" id="item-avatar" class="img_wrapper" style="display: none;">
                            <span id="file_name" style="display: none"></span>
                        <?php endif;?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php echo __d('gift', 'Thumb')?>
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-9">
                        <?php echo $this->Form->hidden('thumb'); ?>
                        <div id="select-1" style="margin: 10px 0 0 0px;"></div>
                        <?php if(!empty($gift["Gift"]["thumb"])):?>
                            <img width="150" src="<?php echo $this->request->base.'/'.GIFT_FILE_URL.$gift["Gift"]["thumb"];?>" id="item-avatar-thumb" class="img_wrapper">
                        <?php else:?>
                            <img width="150" src="" id="item-avatar-thumb" class="img_wrapper" style="display: none;">
                        <?php endif;?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="new_plugin-options">
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo __d('gift', 'Extend( Items Only )') ?>
                            <!-- <span class="required">*</span> -->
                        </label>
                        <div class="col-md-9">
                            <?php 
                            // echo $this->Form->select('extend', array(
                            //         GIFT_EXTEND_3_MONTHS => __d('gift', '3 Months'),
                            //         GIFT_EXTEND_6_MONTHS => __d('gift', '6 Months'),
                            //         GIFT_EXTEND_12_MONTHS => __d('gift', '12 Months'),
                            //         GIFT_EXTEND_FOREVER => __d('gift', 'Forever'),
                            //     ), array(
                            //         'empty' => false,
                            //         'class' => 'form-control',
                            //         'value' =>  $gift["Gift"]["extend"]
                            // )); 
                            echo $this->Form->radio('extend',array(GIFT_EXTEND_NO_BELONG_TO_ITEM=>__d('gift', 'not belong to item'),GIFT_EXTEND_3_MONTHS=>__d('gift', '3 Months'),GIFT_EXTEND_6_MONTHS=>__d('gift', '6 Months'),GIFT_EXTEND_12_MONTHS=>__d('gift', '12 Months'),GIFT_EXTEND_FOREVER=>__d('gift', 'Forever')), array('value'=> $gift["Gift"]["extend"],'legend' => false,'separator' => ' ', 'label' => array('class' => 'radio-setting')));
                            ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            <?php echo __d('gift', 'Control Plugin') ?>
                            <!-- <span class="required">*</span> -->
                        </label>
                        <div class="col-md-9">
                            <?php echo $this->Form->select('plugin', array(
                                    'Ad Plugin'             => __d('gift', 'Ad Plugin'),
                                    'Bookmark Plugin'       => __d('gift', 'Bookmark Plugin'),
                                    'Check-in Plugin'       => __d('gift', 'Check-In Plugin'),
                                    'Contest Plugin'        => __d('gift', 'Contest Plugin'),
                                    'Document Plugin'       => __d('gift', 'Document Plugin'),
                                    'Email Plugin'          => __d('gift', 'Email Plugin'),
                                    'Feeling Plugin'        => __d('gift', 'Feeling Plugin'),
                                    'GIF Plugin'            => __d('gift', 'GIF Plugin'),
                                    'POP Plugin'            => __d('gift', 'POP Plugin'),
                                    'Poll Plugin'           => __d('gift', 'Poll Plugin'),
                                    'Q&A Plugin'            => __d('gift', 'Q&A Plugin'),
                                    'Quiz Plugin'           => __d('gift', 'Quiz Plugin'),
                                    'Reaction Like Facebook Plugin'         => __d('gift', 'Reaction Like Facebook Plugin'),
                                    'User Notes Plugin'     => __d('gift', 'User Notes Plugin'),
                                    'Video Conference Plugin'=> __d('gift', 'Video Conference Plugin'),
                                    'Vote Plugin'=> __d('gift', 'Vote Plugin')
                                ), array(
                                    'empty' => array('' => __d('gift', 'Select')),
                                    'class' => 'form-control',
                                    'value' =>  $gift["Gift"]["plugin"]
                            )); ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
                        <?php echo $this->Form->checkbox('enable', array(
                            'hiddenField' => false,
                            'value' => 1
                        )); ?>
                        <label for="GiftEnable">
                            <?php echo __d('gift', 'Enable')?>
                        </label>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <a href="javascript:void(0)" onclick="saveGift()" class="btn btn-circle btn-action" id="saveButton">
                            <?php echo __d('gift', 'Save') ?>
                        </a>
                        <a href="<?php echo $admin_url;?>" class="btn btn-circle btn-gray" id="cancelButton">
                            <?php echo __d('gift', 'cancel');?>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-offset-3 col-md-6">
                        <div style="display:none;margin-top:10px" class="alert alert-danger error-message"></div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
var baseUrl = '<?php echo $this->request->base?>';
jQuery.gift.selectCreateGiftType('<?php echo !empty($gift["Gift"]["type"]) ? $gift["Gift"]["type"] : GIFT_TYPE_PHOTO;?>', '<?php echo $is_ffmpeg_installed;?>');
jQuery.gift.initSuggestFriend();
jQuery.gift.initUploader('', '', '', 'select-1', 'GiftThumb', 'item-avatar-thumb');

function selectType()
{
    jQuery.gift.selectCreateGiftType(jQuery('#GiftType').val(), '<?php echo $is_ffmpeg_installed;?>');
    jQuery("#item-avatar").hide();
}

function saveGift()
{
    disableButton('sendButton');
    disableButton('saveButton');
    disableButton('previewButton');
    disableButton('cancelButton');

    //save data
    jQuery.post("<?php echo $admin_url;?>save", jQuery("#createForm").serialize(), function(data){
        var json = $.parseJSON(data);
        if(json.result == 0)
        {
            jQuery(".error-message").show();
            jQuery(".error-message").html(json.message);
            enableButton('sendButton');
            enableButton('saveButton');
            enableButton('previewButton');
            enableButton('cancelButton');
        }
        else
        {
            window.location = json.url;
        } 
    });
}
function changeCategory(){
    if(jQuery('#GiftGiftCategoryId').val() == 4)
        jQuery('.new_plugin-options').css('display','');
    else
        jQuery('.new_plugin-options').css('display','none');
}

<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>