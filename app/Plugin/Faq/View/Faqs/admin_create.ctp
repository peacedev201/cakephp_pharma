<?php
$faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
echo $this->Html->script(array('tinymce/tinymce.min'), array('inline' => false));
echo $this->Html->script(array('admin/layout/scripts/compare.js?' . Configure::read('core.version')), array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('faq', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('faq', 'FAQ'), '/admin/faq/faqs');
$this->Html->addCrumb(__d('faq', 'FAQ Manager'), '/admin/faq/faqs');

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'FAQ'));
$this->end();
?>
<?php
echo $this->Html->script(array(
    'vendor/jquery.fileuploader',
    //'jquery-ui', 
    'footable'), array('inline' => false));
?>
<?php echo $this->Moo->renderMenu('Faq', __d('faq', 'Faq Manager')); ?>
<script>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function() {
    tinymce.init({
            selector: "textarea",
            language: mooConfig.tinyMCE_language,
            theme: "modern",
            skin: 'light',
            plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor"
            ],
    setup: function(editor) {
            editor.addButton('mybutton', {
            text: 'My button',
    icon: false,
                    onclick: function() {
            editor.insertContent('&nbsp;<b>It\'s my button!</b>&nbsp;');
                    }
                });
            },
            toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code",
            image_advtab: true,
            height: 500,
            relative_urls: false,
            remove_script_host: true,
            document_base_url: '<?php echo FULL_BASE_URL . $this->request->root ?>'
        });

        $('#createButton').click(function() {
            disableButton('createButton');
            var checked = false;
            $('#permission_list :checkbox').each(function() {
                if ($(this).is(':checked'))
                    checked = true;
            })
            if (!checked)
            {
                $(".error-message").show();
                $(".error-message").html('<strong>Error!</strong>' + '<?php echo __d('faq', 'Please check at least one user role in the Permissions tab') ?>');
                enableButton('createButton');
                return;
            }

            if (tinyMCE.activeEditor.getContent() == '')
            {
                $(".error-message").show();
                $(".error-message").html('<strong>Error!</strong>' + '<?php echo __d('faq', 'FAQ content is required') ?>');
                enableButton('createButton');
                return;
            }
            $('#faq-body-textarea').val(tinyMCE.activeEditor.getContent());

            mooAjax.post({
                url: "<?php echo $this->request->base ?>/admin/faq/faqs/save",
                data: jQuery("#createForm").serialize()
            }, function(data) {

                var json = $.parseJSON(data);
                if (json.result == 1)
                {
                    window.location = '<?php echo $this->request->base ?>/admin/faq/faqs';
                }
                else
                {
                    //mooAlert(json.message);
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error!</strong>' + json.message);
                    enableButton('createButton');
                }
            });
        });


        $('#language').change(function(e) {
            window.location.href = "<?php echo $this->request->base; ?>/admin/faq/faqs/create/<?php echo $faq['Faq']['id']; ?>/" + $('#language').val();
        });

        //update icon
        mooPhrase.add("click_to_upload", '<?php echo __d('faq', 'Toggle Attachments Uploader') ?>');
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
                            var img = '<img src="' + response.path + '" />';
                            tinyMCE.activeEditor.execCommand("mceInsertContent", true, img);
                        }
                    }
                }
            }
        });
    });
<?php $this->Html->scriptEnd(); ?>
</script>
<div class="portlet box">
    <div class="portlet-body form">
        <form id="createForm" class="form-horizontal" action="" method="post" >
            <div class="form-body">
                <div class="form-group">
                    <div class="col-md-9">
                        <?php if ($is_edit): ?>
                            <h3 class="col-md-3 control-label" style="color: #666"><?php echo __d('faq', 'Edit FAQ'); ?></h3>
                        <?php else: ?>
                            <h3 class="col-md-3 control-label" style="color: #666"><?php echo __d('faq', 'Add New FAQ'); ?></h3>
                        <?php endif; ?>

                    </div>
                </div>

                <?php echo $this->Form->hidden('id', array('value' => $faq['Faq']['id'])); ?>
                <?php if ($faq['Faq']['id']): ?>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo __d('faq', 'Language Pack'); ?>(<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('faq', 'Select a language to translate for page title and page content only'); ?>" data-placement="top">?</a>)</label>
                        <div class="col-md-9">
                            <?php echo $this->Form->select('language', $languages, array('class' => 'form-control', 'value' => $language, 'empty' => false)); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('faq', 'Faq Title'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo $this->Form->text('title', array('placeholder' => __d('faq', 'Enter title'), 'class' => 'form-control ', 'value' => $faq['Faq']['title']));
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('faq', 'Category'); ?></label>
                    <div class="col-md-9">
                        <select name="data[category_id]" class="form-control" id="category_id">
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?php echo $category['FaqHelpCategorie']['id'] ?>" <?php if ($category['FaqHelpCategorie']['id'] == $faq['Faq']['category_id']) echo 'selected'; ?>><?php echo $category['FaqHelpCategorie']['name'] ?></option>
                                <?php if ($faqHelper->checkCateHaveChild($category['FaqHelpCategorie']['id'], true)): ?>
                                    <?php $cate_child = $faqHelper->getCateChild($category['FaqHelpCategorie']['id'], TRUE, $category['FaqHelpCategorie']['locale']) ?>
                                    <?php foreach ($cate_child as $cate): ?>
                                        <option value="<?php echo $cate['FaqHelpCategorie']['id'] ?>"<?php if ($cate['FaqHelpCategorie']['id'] == $faq['Faq']['category_id']) echo 'selected'; ?>><?php echo '-- ' . $cate['FaqHelpCategorie']['name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('faq', 'Permissions'); ?></label>
                    <div class="col-md-9" id="permission_list">
                        <?php foreach ($roles as $role): ?>
                            <label><?php echo __d('faq', $role['Role']['name']); ?></label>
                            <input type="checkbox" name="permission[]" value="<?php echo $role['Role']['id'] ?>" <?php if (in_array($role['Role']['id'], explode(',', $faq['Faq']['permission']))) echo 'checked'; ?>>
                        <?php endforeach; ?>
                    </div>
                </div>
<!--                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('faq', 'Permissions'); ?></label>
                    <div class="col-md-9" id="permission_list">
                        <?php// echo $this->element('admin/permissions', array('permission' => $faq['Faq']['permission'])); ?>
                    </div>
                </div>-->
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('faq', 'Content'); ?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->textarea('body', array('value' => $faq['Faq']['body'], 'id' => 'faq-body-textarea', 'class' => 'faq_content')); ?>
                    <div id="faq_thumnail"></div>
                </div>
            </div>

            <hr>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('faq', 'Allow Comments'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <label class="checkbox-inline">
                            <?php echo $this->Form->checkbox('alow_comment', array('checked' => $faq['Faq']['alow_comment'])); ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('faq', 'Enable'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <label class="checkbox-inline">
                            <?php echo $this->Form->checkbox('active', array('checked' => $faq['Faq']['active'])); ?>
                        </label>
                    </div>
                </div>

            </div>
            <hr>

            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-6">
                        <div class="alert alert-danger error-message" style="display:none;margin-top:10px"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button id="createButton" type="button" class="btn btn-circle btn-action"><?php echo __d('faq', 'Save'); ?></button>
                    </div>
                </div>

            </div>
        </form>
        <!-- END FORM-->
    </div>
</div>
<?php $this->end(); ?>