<?php
__d('upload_video', 'Enable Upload Video Plugin');
__d('upload_video', 'Select Upload Location');
__d('upload_video', 'Upload to my Server (FFMPEG is required)');
__d('upload_video', 'Upload to Vimeo (Vimeo account is required)');
__d('upload_video', 'FFMPEG params Convert (MP4 extension)');
__d('upload_video', 'FFMPEG params Thumbnail');
__d('upload_video', 'FFMPEG Path');
__d('upload_video', 'Max upload file (Mb)');
__d('upload_video', 'Upload Via Vimeo');
__d('upload_video', 'Access Token');
__d('upload_video', 'Vimeo Key');
__d('upload_video', 'Vimeo Secret');
__d('upload_video', 'Upload Video');
__d('upload_video', 'Disable');
__d('upload_video', 'Enable');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('upload_video', 'Upload Video Setting'), array('controller' => 'upload_video_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Upload Video'));
$this->end();
?>

<?php echo $this->Moo->renderMenu('UploadVideo', __('Settings')); ?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <a target="_blank" href="https://community.moosocial.com/groups/view/20/topic_id:1251"><?php echo __d('upload_video', 'How to setup Vimeo App?'); ?></a>
                                </div>
                                <div class="col-lg-12">
                                    <a href="<?php echo $this->Html->url(array('plugin' => 'upload_video', 'controller' => 'upload_video_settings', 'action' => 'admin_ffmpeg'));?>"><?php echo __d('upload_video', 'Check FFMPEG is Ok on server?'); ?></a>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="alert alert-info">
                                        <?php echo __('Please set one of the the following commands to run in crontab about every 1 minute:'); ?>
                                        <ul>
                                            <?php $request = Router::getRequest(); ?>
                                            <li>
                                                <?php echo __('Requires wget command line utility:');?> "wget -qO- '<?php echo FULL_BASE_URL . $request->webroot . 'video/cron/run'; ?>' &&> /dev/null"
                                            </li>
                                        </ul>
                                </div>
                            </div>

                            <form class="form-horizontal intergration-setting" method="post" enctype="multipart/form-data" action="<?php echo $this->Html->url(array('plugin' => 'upload_video', 'controller' => 'upload_video_settings', 'action' => 'admin_setting')); ?>">

                                <div class="form-body">
                                    <?php
                                    if ($settings != null):
                                    foreach ($settings as $setting):
                                        
                                        /* Translate */
                                        $setting['Setting']['label'] = __d('upload_video', $setting['Setting']['label']);
                                        if ($setting['Setting']['description'])
                                        {
                                                $setting['Setting']['description'] = __d('upload_video', $setting['Setting']['description']);
                                        }
                                        /* Translate */
                                        
                                        /* Select Upload */
                                        $bHidden = false;
                                        $sSettingClass = '';
                                        
                                        if(in_array($setting['Setting']['name'], array('vimeo_secret', 'vimeo_key', 'vimeo_access_token'))) {
                                            $sSettingClass = 'vimeo_settings';
                                        } else if (in_array($setting['Setting']['name'], array('video_ffmpeg_params_convert_mp4', 'video_ffmpeg_params_thumbnail', 'video_ffmpeg_path'))){
                                            $sSettingClass = 'server_settings';
                                        }
                                        
                                        if(empty($bVimeoEnable) && in_array($setting['Setting']['name'], array('vimeo_secret', 'vimeo_key', 'vimeo_access_token'))) {
                                            $bHidden = true;
                                        } else if(!empty($bVimeoEnable) && in_array($setting['Setting']['name'], array('video_ffmpeg_params_convert_mp4', 'video_ffmpeg_params_thumbnail', 'video_ffmpeg_path'))) {
                                            $bHidden = true;
                                        } else if(in_array($setting['Setting']['name'], array('vimeo_upload'))) {
                                            $bHidden = true;
                                        }
                                        /* Select Upload */

                                        ?>
                                        <?php echo $this->Form->hidden('setting_id.', array('value' => $setting['Setting']['id'], 'id' => false)); ?>
                                        <?php echo $this->Form->hidden('setting_name.' . $setting['Setting']['name'], array('value' => $setting['Setting']['id'], 'id' => false)); ?>
                                        <?php echo $this->Form->hidden('type_id.' . $setting['Setting']['id'], array('value' => $setting['Setting']['type_id'], 'id' => false)); ?>
                                        
                                        <div class="form-group <?php echo $sSettingClass; ?>" <?php echo ($bHidden) ? 'style="display: none;"' : ''; ?>>
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('setting', $setting['Setting']['label']); ?>
                                                <?php
                                                if ($setting['Setting']['description'] != ''):
                                                    $href = "";
                                                    $target = "";
                                                    preg_match('/href="(.*)"/i', __d('setting', trim($setting['Setting']['description'])), $match);
                                                    preg_match('/target="(.*)"/i', __d('setting', trim($setting['Setting']['description'])), $target);
                                                    if (!empty($match)) {
                                                        $href = (strpos($match[1], 'http') !== false) ? $match[1] : $this->request->base . $match[1];
                                                    }
                                                    if (!empty($target))
                                                        $target = $target[1];
                                                    ?>
                                                    (<a data-html="true"
                                                        href="<?php echo (empty($href)) ? "javascript:void(0)" : $href; ?>" <?php echo (empty($target)) ? "" : 'target="' . $target . '"' ?>
                                                        class="tooltips"
                                                        data-original-title="<?php echo(str_replace('"', '\'', __d('setting', trim($setting['description'])))); ?>"
                                                        data-placement="top">?</a>)
                                                <?php endif; ?>
                                            </label>
                                            <div class="col-md-7">
                                                <?php
                                                switch ($setting['Setting']['type_id']) {
                                                    case 'text':
                                                        echo $this->Form->text('text.' . $setting['Setting']['id'], array(
                                                            'value' => $setting['Setting']['value_actual'],
                                                            'class' => 'form-control',
                                                            'label' => false
                                                        ));
                                                        break;
                                                    case 'textarea':
                                                        break;
                                                    case 'radio':
                                                        $options = array();
                                                        $checked = '';
                                                        $multis = json_decode($setting['Setting']['value_actual'], true);

                                                        /* 461 */
                                                        $disable = ($setting['Setting']['name'] == 'select_upload' && !Configure::read('UploadVideo.uploadvideo_enabled')) ? true : false;
                                                        /* 461 */
                                                        
                                                        foreach ($multis as $multi) {
                                                            $options[$multi['value']] = __d('upload_video', $multi['name']);
                                                            if ($multi['select'] == 1) {
                                                                $checked = $multi['value'];
                                                            }
                                                        }

                                                        echo $this->Form->radio('multi.' . $setting['Setting']['id'], $options, array('separator' => '<br/>', 'value' => $checked, 'legend' => false, 'disabled' => $disable, 'class' => $setting['Setting']['name'], 'label' => array('class' => 'radio-setting')));
                                                        break;
                                                    case 'checkbox':
                                                        $options = array();
                                                        $checked = '';
                                                        $multis = json_decode($setting['Setting']['value_actual'], true);
                                                        foreach ($multis as $multi) {
                                                            $multi['name'] = __d('upload_video', $multi['name']);
                                                            echo $this->Form->input('multi.' . $setting['Setting']['id'] . '.' . $multi['value'], array(
                                                                'type' => 'checkbox',
                                                                'checked' => $multi['select'],
                                                                'label' => $multi['name'],
                                                            ));
                                                        }
                                                        break;
                                                    case 'select':
                                                        break;
                                                    case 'timezone':
                                                        break;
                                                    case 'language':
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
/*fix settings upload video 461*/ 
$this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function() {
        if ($('.select_upload:radio[value=1]').attr('checked') === 'checked') {
            $('.vimeo_settings').show();
            $('.vimeo_upload:radio[value=1]').attr('checked', true);
            $('.server_settings').hide();
        } else {
            $('.server_settings').show();
            $('.vimeo_settings').hide();
        }
        
        if ($('.uploadvideo_enabled:radio[value=0]').attr('checked') === 'checked') {
            $('.select_upload:radio').attr('disabled', true);
            $('.select_upload:radio').attr('checked', false);
            $('.select_upload:radio').parent().removeClass('checked');
            $('.select_upload:radio').parent().parent().addClass('disabled');
            $('.vimeo_settings').hide();
            $('.server_settings').hide();
        }
    });
    
    $('.uploadvideo_enabled').on('click', function() {
        if ($(this).attr('value') === '1') {
            $('.select_upload:radio').attr('disabled', false);
            $('.select_upload:radio[value=0]').attr('checked', true);
            $('.select_upload:radio[value=0]').parent().addClass('checked');
            $('.select_upload:radio').parent().parent().removeClass('disabled');
            $('.server_settings').show();
        } else {
            $('.select_upload:radio').attr('disabled', true);
            $('.select_upload:radio').attr('checked', false);
            $('.select_upload:radio').parent().removeClass('checked');
            $('.select_upload:radio').parent().parent().addClass('disabled');
            $('.server_settings').hide();
            $('.vimeo_settings').hide();
        }
    });
    
    $('.select_upload:radio').on('click', function() {
        if ($(this).attr('value') === '1') {
            $('.vimeo_upload:radio[value=1]').attr('checked', true);
            $('.server_settings').hide();
            $('.vimeo_settings').show();
        } else {
            $('.vimeo_upload:radio[value=0]').attr('checked', true);
            $('.server_settings').show();
            $('.vimeo_settings').hide();
        }
    });
<?php $this->Html->scriptEnd(); 
/* end fix settings upload video 461*/ ?>

