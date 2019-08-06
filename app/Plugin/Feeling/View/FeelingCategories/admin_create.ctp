<?php
/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<script>
    $(document).ready(function() {
        //update icon
        mooPhrase.add("click_to_upload", '<?php echo __d('feeling', 'Click to upload') ?>');
        mooPhrase.add("can_not_upload_file_more_than", '<?php echo __d('feeling', 'Can not upload file more than') . ' ' . $file_max_upload ?>');
        if (mooPhrase.__('drag_photo') != '')
            text_upload_button = '<div class="upload-section"><i class="icon-camera"></i>' + mooPhrase.__('drag_photo') + '</div>';
        else
            text_upload_button = '<div class="upload-section"></div>';
        var uploader = new qq.FineUploader({
            element: $('#background_thumbnail')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="icon-camera"></i>' + mooPhrase.__('click_to_upload') + '</div>'
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png']
            },
            request: {
                //endpoint: mooConfig.url.base + "/feeling/feeling_categories/upload_thumbnail/"
                endpoint: '<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feeling_categories', 'action' => 'upload_thumbnail'));?>'
            },
            callbacks: {
                onError: function(event, id, fileName, reason) {
                    if ($('.qq-upload-list .errorUploadMsg').length > 0) {
                        $('.qq-upload-list .errorUploadMsg').html(mooPhrase.__('can_not_upload_file_more_than'));
                    }
                    else
                    {
                        $('.qq-upload-list').prepend('<div class="errorUploadMsg">' + mooPhrase.__('can_not_upload_file_more_than') + '</div>');
                    }
                    $('.qq-upload-fail').remove();
                },
                onComplete: function(id, fileName, response) {
                    if (response.success == 0)
                    {
                        jQuery('.qq-upload-fail:last .qq-upload-status-text').empty().append(response.message);

                    }
                    else
                    {
                        if (response.filename)
                        {
                            $("#background_thumbnail_preview").show();
//                            jQuery('#background_thumbnail_preview').empty().append('<img width="100" height="100" src="' + response.path + '" />');
                            $(".img-background-thumb").attr('src', response.thumb);
                            $(".img-background-thumb").show();
                            //jQuery('#thumbnail').val(response.filename);
                            //jQuery('#image').val(response.filename);
                            $('#photo').val(response.file_path);
                        }
                    }
                }
            }
        });

        $('.close_thumb').click(function() {
            $("#background_thumbnail_preview").css("display", 'none');
//            $('#thumbnail').removeAttr("value");
            $('#photo').removeAttr("value");
        });

        $('#btnSave').click(function() {
            disableButton('btnSave');
            $.post("<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feeling_categories', 'action' => 'admin_save_validate'));?>", $("#createForm").serialize(), function(data) {
                enableButton('btnSave');
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    $("#createForm").submit();
                } else {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });

    });
</script>

<?php $oFeelingHelper = MooCore::getInstance()->getHelper('Feeling_Feeling'); ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo (!empty($aFellingCategories['FeelingCategory']['id'])) ? __d('feeling', 'Edit Group') : __d('feeling', 'Add New Group'); ?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" action="<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feeling_categories', 'action' => 'admin_save')); ?>" method="post" enctype="multipart/form-data">
        <?php echo $this->Form->hidden('id', array('value' => $aFellingCategories['FeelingCategory']['id'])); ?>
        <div class="form-body">
            <?php //echo $this->Form->hidden('role_id', array('value' => $aFellingCategories['FeelingCategory']['role_id'])); ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('feeling', 'Group');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('label', array('value' => $aFellingCategories['FeelingCategory']['label'] , 'class' => 'form-control', 'disabled' => false)); ?>
                    <?php if (!$bIsEdit) : ?>
                        <div class="tips">*<?php echo  __d('feeling','You can add translation language after creating group') ?></div>
                    <?php else : ?>
                        <div class="tips">
                            <?php
                            $this->MooPopup->tag(array(
                                'href'=>$this->Html->url(array("controller" => "feeling_categories",
                                    "action" => "admin_ajax_translate",
                                    "plugin" => 'feeling',
                                    $aFellingCategories['FeelingCategory']['id']

                                )),
                                'title' => __('Translation'),
                                'innerHtml'=> __('Translation'),
                            ));
                            ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="imageGroup" class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('feeling', 'Image');?></label>
                <div class="col-md-9">
                    <div id="background_thumbnail"></div>
                    <?php $thumb = $oFeelingHelper->getCategoryImage($aFellingCategories);?>
                    <div style="<?php if (!$thumb) echo "display:none;"?>" id="background_thumbnail_preview">
                        <img class="img-background-thumb" style="float: left" width="32" height="32" src="<?php echo ($thumb ? $thumb : '')  ?>"/><button style="float: left" type="button" class="close close_thumb"></button>
                    </div>
                    <?php
                    echo $this->Form->hidden('photo', array(
                        'value' => !empty($aFellingCategories['FeelingCategory']['photo']) ? $aFellingCategories['FeelingCategory']['photo'] : ''
                    ));
                    ?>
                    <div style="clear: both;">
                        <small class="form-text text-muted"><?php echo __d('feeling', 'You should upload an image file size of 32x32 px');?></small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('feeling', 'Enable'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('active', array('checked' => $aFellingCategories['FeelingCategory']['active'])); ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label">&nbsp;</label>
                <div class="col-md-9">
                    <div class="alert alert-danger error-message" style="display: none; margin-top: 10px;"></div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <a href="javascript:void(0)" id="btnSave" class="btn btn-action"><?php echo __d('feeling', 'Save'); ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('feeling', 'Close'); ?></button>
</div>