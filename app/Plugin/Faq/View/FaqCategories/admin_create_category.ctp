<?php
$faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
?>
<script>
    $(document).ready(function() {
        //update icon
        mooPhrase.add("click_to_upload", '<?php echo __d('faq', 'Click to upload') ?>');
        mooPhrase.add("can_not_upload_file_more_than", '<?php echo __d('faq', 'Can not upload file more than ') . ' ' . $file_max_upload ?>');
        if (mooPhrase.__('drag_photo') != '')
            text_upload_button = '<div class="upload-section"><i class="icon-camera"></i>' + mooPhrase.__('drag_photo') + '</div>';
        else
            text_upload_button = '<div class="upload-section"></div>';
        var uploader = new qq.FineUploader({
            element: $('#faq_thumnail')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="icon-camera"></i>' + mooPhrase.__('click_to_upload') + '</div>'
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            },
            request: {
                endpoint: mooConfig.url.base + "/faq/faq_categories/upload_icon/"
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
                            jQuery("#faq_thumnail_preview").css("display", 'block');
//                            jQuery('#faq_thumnail_preview').empty().append('<img width="100" height="100" src="' + response.path + '" />');
                            jQuery(".img-category-thumb").attr('src', response.path);
                            jQuery('#icon').val(response.filename);
                        }
                    }
                }
            }
        });

        $('#createButton').click(function() {
            disableButton('createButton');
            var datacate = jQuery("#createForm").serialize();
            mooAjax.post({
                url: "<?php echo $this->request->base ?>/admin/faq/faq_categories/savecategory",
                data: jQuery("#createForm").serialize()
            }, function(data) {
                enableButton('createButton');
                var json = $.parseJSON(data);
                if (json.result == 1)
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error!</strong>' + json.message);
                }
            });
        });
        $('.close_thumb').click(function() {
            $("#faq_thumnail_preview").css("display", 'none');
            $('#icon').removeAttr("value");
        });


    });

    function toggleField()
    {
        $('.opt_field').toggle();
    }
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <?php if (!$bIsEdit) : ?>
        <h4 class="modal-title"><?php echo __d('faq', 'Add New Category'); ?></h4>
    <?php else: ?>
        <h4 class="modal-title"><?php echo __d('faq', 'Edit Category'); ?></h4>
    <?php endif; ?>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form" method="POST">
        <?php echo $this->Form->hidden('id', array('value' => $category['FaqHelpCategorie']['id'])); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('faq', 'Name'); ?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('faq', 'Enter category name'), 'class' => 'form-control', 'value' => $category['FaqHelpCategorie']['name'])); ?>

                </div>
                <?php if (!$bIsEdit) : ?>
                    <div class="tips" style="margin-left: 165px;">*<?php echo __d('faq', 'You can add translation language after adding new category') ?></div>
                <?php else : ?>
                    <div class="tips" style="margin-left: 165px;">
                        <a href="<?php echo $this->request->base ?>/admin/faq/faq_categories/ajax_translate/<?php echo $category['FaqHelpCategorie']['id'] ?>"  data-toggle="modal" data-target="#ajax-translate" ><?php echo __d('faq', 'Translation') ?></a>
                    </div>
                <?php endif; ?>

            </div>
            <div class ="form-group">
                <label class="col-md-3 control-label"><?php echo __d('faq', 'Icon'); ?></label>
                <div class="col-md-9">
                    <div id="faq_thumnail"></div>
                    <?php $thumb = $faqHelper->getImage($category);?>
                    <div style="<?php if (!$thumb) echo "display:none;"?>" id="faq_thumnail_preview">                    	
                        <img class="img-category-thumb" style="float: left" width="100" height="100" src="<?php echo ($thumb ? $thumb : '')  ?>"/><button style="float: left" type="button" class="close close_thumb"></button>
                    </div>
                    <?php
                    echo $this->Form->hidden('icon', array(
                        'value' => !empty($category['FaqHelpCategorie']['icon']) ? $category['FaqHelpCategorie']['icon'] : ''
                    ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('faq', 'Parent Category'); ?></label>
                <div class="col-md-9" id="permission_list">
                    <select class="form-control" name="parent_id">
                        <option value="0">Root</option>
                        <?php if (!$faqHelper->checkCateHaveChild($category['FaqHelpCategorie']['id'])): ?>
                            <?php foreach ($all_categories as $cate) : ?>
                                <option <?php if ($cate['FaqHelpCategorie']['id'] == $category['FaqHelpCategorie']['parent_id']) echo 'selected' ?>  value="<?php echo $cate['FaqHelpCategorie']['id']; ?>"><?php echo $cate['FaqHelpCategorie']['name']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('faq', 'Enable'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <label class="checkbox-inline"></label>
                        <?php echo $this->Form->checkbox('active', array('checked' => $category['FaqHelpCategorie']['active'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('faq', 'Close') ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo __d('faq', 'Save') ?></a>
</div>
