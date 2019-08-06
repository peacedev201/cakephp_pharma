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
                endpoint: '<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'upload_thumbnail'));?>'
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
                            //jQuery('#icon').val(response.filename);
                            $('#icon').val(response.file_path);
                        }
                    }
                }
            }
        });

        $('.close_thumb').click(function() {
            $("#background_thumbnail_preview").css("display", 'none');
//            $('#thumbnail').removeAttr("value");
            $('#icon').removeAttr("value");
        });

        $('#btnSave').click(function() {
            disableButton('btnSave');
            $.post("<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_save_validate'));?>", $("#createForm").serialize(), function(data) {
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

        $('select#type').change(function () {
            if($(this).val() === 'icon'){
                $('#linkGroup').hide();
            }else if( $(this).val() === 'link' ) {
                $('#linkGroup').show();
            }
        });
        function checkSelectType() {
            if($('select#type').val() === 'icon'){
                $('#linkGroup').hide();
            }else if($('select#type').val() === 'link' ) {
                $('#linkGroup').show();
            }
        }
        checkSelectType();

    });
</script>

<?php $oFeelingHelper = MooCore::getInstance()->getHelper('Feeling_Feeling'); ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo (!empty($aFeeling['Feeling']['id'])) ? __d('feeling', 'Edit Feeling Status') : __d('feeling', 'Add New Feeling Status'); ?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" action="<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_save')); ?>" method="post" enctype="multipart/form-data">
        <?php echo $this->Form->hidden('id', array('value' => $aFeeling['Feeling']['id'])); ?>
        <div class="form-body">
            <?php //echo $this->Form->hidden('role_id', array('value' => $aFeeling['Feeling']['role_id'])); ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('feeling', 'Status');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('label', array('value' => $aFeeling['Feeling']['label'] , 'class' => 'form-control', 'disabled' => false)); ?>
                    <?php if (!$bIsEdit) : ?>
                        <div class="tips">*<?php echo  __d('feeling', 'You can add translation language after creating feeling') ?></div>
                    <?php else : ?>
                        <div class="tips">
                            <?php
                            $this->MooPopup->tag(array(
                                'href'=>$this->Html->url(array("controller" => "feelings",
                                    "action" => "admin_ajax_translate",
                                    "plugin" => 'feeling',
                                    $aFeeling['Feeling']['id']

                                )),
                                'title' => __('Translation'),
                                'innerHtml'=> __('Translation'),
                            ));
                            ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('feeling', 'Group');?></label>
                <div class="col-md-9">
                    <?php if(!empty($pCategoryId)): ?>
                        <?php echo $this->Form->hidden('category_id', array('value' => !empty($aFeeling['Feeling']['category_id']) ? $aFeeling['Feeling']['category_id'] : $pCategoryId)); ?>
                        <div style="height: 32px; padding-left: 35px; padding-top: 8px; position: relative;">
                            <span style="display: inline-block; width: 32px; height: 32px; position: absolute; top: 0; left: 0; background-repeat: no-repeat; background-size: cover; background-position: center; background-image: url(<?php echo $oFeelingHelper->getCategoryImage($aCategory, array('prefix' => '32_square')); ?>)"></span>
                            <?php echo $aCategory['FeelingCategory']['label'] ?>
                        </div>
                    <?php else: ?>
                        <?php echo $this->Form->select('category_id', $aCategories, array('empty' => false, 'value' => $aFeeling['Feeling']['category_id'], 'class' => 'form-control', 'disabled' => false)); ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('feeling', 'Type');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->select('type', array('icon' => __d('feeling', 'Default'), 'link' => __d('feeling', 'Link')), array('empty' => false, 'value' => !empty($aFeeling['Feeling']['type']) ? $aFeeling['Feeling']['type'] : 'icon', 'class' => 'form-control', 'disabled' => false)); ?>
                </div>
            </div>

            <div id="linkGroup" class="form-group" style="display: none;">
                <label class="col-md-3 control-label"><?php echo __d('feeling', 'Link');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('link', array('value' => $aFeeling['Feeling']['link'] , 'class' => 'form-control', 'disabled' => false)); ?>
                    <small class="form-text text-muted">http://your-link.com</small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('feeling', 'Image');?></label>
                <div class="col-md-9">
                    <div id="background_thumbnail"></div>
                    <?php $thumb = $oFeelingHelper->getFeelingImage($aFeeling);?>
                    <div style="<?php if (!$thumb) echo "display:none;"?>" id="background_thumbnail_preview">
                        <img class="img-background-thumb" style="float: left;" width="32" height="32" src="<?php echo ($thumb ? $thumb : '')  ?>"/><button style="float: left" type="button" class="close close_thumb"></button>
                    </div>
                    <?php
                    echo $this->Form->hidden('icon', array(
                        'value' => !empty($aFeeling['Feeling']['icon']) ? $aFeeling['Feeling']['icon'] : ''
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
                        <?php echo $this->Form->checkbox('active', array('checked' => $aFeeling['Feeling']['active'])); ?>
                    </div>
                </div>
            </div>
            <?php
                if(!empty($pCategoryId)){
                    echo $this->Form->hidden('parentCategoryId', array('value' => !empty($aFeeling['Feeling']['category_id']) ? $aFeeling['Feeling']['category_id'] : $pCategoryId ));
                }
            ?>
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