<?php
    __d('faq','Can not upload file more than');
    __d('faq','Click to upload');
    __d('faq','Number of categories at home page by default');
    __d('faq','Number of usefull faqs in each category at home page by default');
    __d('faq','Change background');
?>
<?php $faqHelper = MooCore::getInstance()->getHelper('Faq_Faq'); ?>
<?php $id_ima = NULL ?>
<?php if ($setting_groups != null && count($setting_groups) > 1): $count = 0; ?>
    <?php
    foreach ($setting_groups as $setting_group):
        $count++;
        $setting_group = $setting_group['SettingGroup'];
        ?>
        <a href="<?php echo $this->request->base ?>/admin/subscription/settings/index/<?php echo $setting_group['id']; ?>">
            <span <?php echo ($acive_group == $setting_group['id']) ? 'class="bold"' : '' ?>><?php echo $setting_group['name']; ?></span>
        </a>
        <?php echo ($count < count($setting_groups)) ? '|' : ''; ?>
    <?php endforeach; ?>
    <br/><br/>
<?php endif; ?>
<?php if ($settingGuides != null): ?>
    <?php foreach ($settingGuides as $settingGuide): ?>
        <?php if ($settingGuide != ''): ?>
            <div class="Metronic-alerts alert alert-warning fade in" style="max-height: 300px;overflow: auto">
                <?php echo $settingGuide; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
<form class="form-horizontal intergration-setting" method="post" enctype="multipart/form-data" action="<?php echo $this->request->base ?>/admin/system_settings/quick_save">
    <div class="form-body">
        <?php
        if ($settings != null):
            foreach ($settings as $setting):
                $plugin = strtolower($setting['SettingGroup']['module_id']);
                $setting = $setting['Setting'];

                if ( $setting['label'] == $setting['label'])
                    $setting['label'] = $setting['label'];
                else
                    $setting['label'] = $setting['label'];

                if ($setting['description']) {
                    if ($setting['description'] == $setting['description'])
                        $setting['description'] = $setting['description'];
                    else
                        $setting['description'] = $setting['description'];
                }
                ?>
                <?php echo $this->Form->hidden('setting_id.', array('value' => $setting['id'], 'id' => false)); ?>
                <?php echo $this->Form->hidden('type_id.' . $setting['id'], array('value' => $setting['type_id'], 'id' => false)); ?>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        <?php     
                        echo __d('faq',$setting['label']);
                        ?>
                        <?php
                        if ($setting['description'] != ''):
                            $href = "";
                            $target = "";
                            preg_match('/href="(.*)"/i', trim($setting['description']), $match);
                            preg_match('/target="(.*)"/i', trim($setting['description']), $target);
                            if (!empty($match)) {
                                $href = (strpos($match[1], 'http') !== false) ? $match[1] : $this->request->base . $match[1];
                            }
                            if (!empty($target))
                                $target = $target[1];
                            ?>
                            (<a data-html="true"  href="<?php echo (empty($href)) ? "javascript:void(0)" : $href; ?>" <?php echo (empty($target)) ? "" : 'target="' . $target . '"' ?> class="tooltips" data-original-title="<?php echo __d('faq',$setting['label']);?>" data-placement="top">?</a>)
                        <?php endif; ?>
                        <?php if (Configure::read('core.production_mode')): ?>
                            <br/><?php echo $setting['name']; ?>
                        <?php endif; ?>
                    </label>
                    <div class="col-md-7">
                        <?php
                        switch ($setting['type_id']) {
                            case 'text':
                                if ($setting['name'] == 'faq_back_ground'):
                                    ?><div id="faq_background"></div>
                                    <div id="faq_background_preview">
                                        <?php
                                        if (Configure::read('Faq.faq_back_ground') != '') {
                                            ?>
                                            <img height="300" width="1000" src="<?php echo ($faqHelper->getBackground(Configure::read('Faq.faq_back_ground'))); ?>" />
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    echo $this->Form->hidden('text.' . $setting['id'], array('value' => Configure::read('Faq.faq_back_ground')));
                                    $id_ima = 'text' . $setting['id'];
                                    ?>
                                    <?php
                                else:
                                    echo $this->Form->text('text.' . $setting['id'], array(
                                        'value' => $setting['value_actual'],
                                        'class' => 'form-control',
                                        'label' => false
                                    ));
                                endif;
                                break;
                            case 'textarea':
                                echo $this->Form->textarea('textarea.' . $setting['id'], array(
                                    'value' => $setting['value_actual'],
                                    'class' => 'form-control',
                                    'label' => false
                                ));
                                break;
                            case 'radio':
                                $options = array();
                                $checked = '';
                                $multis = json_decode($setting['value_actual'], true);
                                foreach ($multis as $multi) {
                                    if ($multi['name'] != $multi['name']) {
                                        $options[$multi['value']] = __d('faq',$multi['name']);
                                    } else {
                                        $options[$multi['value']] = __d('faq',$multi['name']);
                                    }

                                    if ($multi['select'] == 1) {
                                        $checked = $multi['value'];
                                    }
                                }
                                echo $this->Form->radio('multi.' . $setting['id'], $options, array('separator' => '<br/>', 'value' => $checked, 'legend' => false, 'label' => array('class' => 'radio-setting')));

                                break;
                            case 'checkbox':
                                $options = array();
                                $checked = '';
                                $multis = json_decode($setting['value_actual'], true);
                                foreach ($multis as $multi) {
                                    echo $this->Form->input('multi.' . $setting['id'] . '.' . $multi['value'], array(
                                        'type' => 'checkbox',
                                        'checked' => $multi['select'],
                                        'label' => $multi['name'],
                                        'id' => 'ch' . $setting['id'] . $multi['value']
                                    ));
                                }
                                break;
                            case 'select':
                                $options = array();
                                $selected = '';
                                $multis = json_decode($setting['value_actual'], true);
                                foreach ($multis as $multi) {
                                    if ($multi['name'] != $multi['name']) {
                                        $options[$multi['value']] = $multi['name'];
                                    } else {
                                        $options[$multi['value']] = $multi['name'];
                                    }

                                    if ($multi['select'] == 1) {
                                        $selected = $multi['value'];
                                    }
                                }
                                $readonly = '';
                                if ($setting['name'] == 'facebook_sdk_version') {
                                    $readonly = 'readonly';
                                }
                                echo $this->Form->input('multi.' . $setting['id'], array(
                                    'options' => $options,
                                    'value' => $selected,
                                    'class' => 'form-control',
                                    'label' => false,
                                    $readonly
                                ));
                                if ($readonly != '')
                                    $this->Form->hidden('multi.' . $setting['id'], array(
                                        'options' => $options,
                                        'value' => $selected,
                                        'class' => 'form-control',
                                    ));
                                break;
                            case 'timezone':
                                echo $this->Form->select('timezone.' . $setting['id'], $this->Moo->getTimeZones(), array(
                                    'value' => $setting['value_actual'],
                                    'empty' => false,
                                    'class' => 'form-control'
                                ));
                                break;
                            case 'language':
                                echo $this->Form->select('language.' . $setting['id'], $site_langs, array(
                                    'value' => $setting['value_actual'],
                                    'empty' => false,
                                    'class' => 'form-control'
                                ));
                                break;
                        }
                        ?>
                    </div>
                   
                </div>
            <?php endforeach; ?>
        </div>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-3 col-md-9">
                    <input type="submit" class="btn btn-circle btn-action" value="<?php echo __('Save Settings'); ?>">
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php echo __('No settings found'); ?>
    <?php endif; ?>
</form>

<?php
echo $this->Html->script(array(
    'vendor/jquery.fileuploader',
    //'jquery-ui', 
    'footable'), array('inline' => false));
?>
<?php echo $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function() {
            //update icon
            mooPhrase.add("click_to_upload", '<?php echo __d('faq', 'Click to upload') ?>');
            mooPhrase.add("can_not_upload_file_more_than", '<?php echo __d('faq', 'Can not upload file more than ') . ' ' . $file_max_upload ?>');
            if (mooPhrase.__('drag_photo') != '')
                text_upload_button = '<div class="upload-section"><i class="icon-camera"></i>' + mooPhrase.__('drag_photo') + '</div>';
            else
                text_upload_button = '<div class="upload-section"></div>';
            var uploader = new qq.FineUploader({
                element: $('#faq_background')[0],
                multiple: false,
                text: {
                    uploadButton: '<div class="upload-section"><i class="icon-camera"></i>' + mooPhrase.__('click_to_upload') + '</div>'
                },
                validation: {
                    allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                },
                request: {
                    endpoint: mooConfig.url.base + "/faq/faq_settings/upload_background/"
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
                                var baseurl = '<?php echo $this->request->base ?>';
                                jQuery('#faq_background_preview').empty().append('<img height="300" width="1000" src="' + baseurl + response.path + '" />');
                                jQuery('#ads_image').val(response.filename);
                                jQuery('#<?php echo $id_ima ?>').val(response.path);
                            }
                        }
                    }
                }
            });
        });

<?php echo $this->Html->scriptEnd(); ?>