<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

class UploadVideoSettingsController extends UploadVideoAppController {

    public $components = array('QuickSettings');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');
    }

    public function admin_index() {
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');

        $aSettingGroup = $this->SettingGroup->find('first', array('conditions' => array('group_type' => 'UploadVideo', 'module_id' => 'UploadVideo'), 'fields' => array('id')));
        $aSetting = $this->Setting->find('all', array('conditions' => array('group_id' => $aSettingGroup['SettingGroup']['id']), 'order' => array('Setting.id ASC')));

        $bVimeoEnable = (Configure::read('UploadVideo.select_upload') == 1) ? true : false;
        $this->set('bVimeoEnable', $bVimeoEnable);

        $this->set('settings', $aSetting);
        $this->set('setting_groups', $aSettingGroup);
        $this->set('title_for_layout', __d('upload_video', 'Upload Videos Setting'));
    }

    public function admin_setting() {
        if ($this->request->is('post')) {
            if (!empty($this->request->data['setting_id'])) {

                // fix setting video upload 461
                $id_select_upload = $this->request->data['setting_name']['select_upload'];
                $id_upload_enabled = $this->request->data['setting_name']['uploadvideo_enabled'];
                $id_vimeo_upload = $this->request->data['setting_name']['vimeo_upload'];

                $select_upload = "";
                $is_enable = false;
                if ($this->request->data['multi'][$id_upload_enabled] == UPLOAD_VIDEO_ENABLE) {
                    $is_enable = true;
                    if ($this->request->data['multi'][$id_select_upload] == UPLOAD_SERVER) {
                        $select_upload = 'server';
                    } elseif ($this->request->data['multi'][$id_select_upload] == UPLOAD_VIMEO) {
                        $select_upload = 'vimeo';
                    }
                }

                // end fix setting video upload 461
                foreach ($this->request->data['setting_id'] as $item) {

                    if (isset($this->request->data['multi'][$item]) == FALSE) {
                        $this->request->data['multi'][$item] = 0;
                    }

                    switch ($this->request->data['type_id'][$item]) {
                        case 'text':
                            $values['value_actual'] = $this->request->data['text'][$item];
                            break;
                        case 'textarea':
                            $values['value_actual'] = $this->request->data['textarea'][$item];
                            break;
                        case 'radio':
                        case 'select':
                            $setting = $this->Setting->findById($item);
                            $multiValue = json_decode($setting['Setting']['value_actual'], true);

                            foreach ($multiValue as $k => $multi) {
                                if ($is_enable == true || ($select_upload != 'server' && $id_vimeo_upload != $item)) {
                                    if ($multi['value'] == $this->request->data['multi'][$item]) {
                                        $multiValue[$k]['select'] = 1;
                                    } else {
                                        $multiValue[$k]['select'] = 0;
                                    }
                                } else {
                                    $multiValue[0]['select'] = 1;
                                    $multiValue[1]['select'] = 0;
                                }
                            }

                            $values['value_actual'] = json_encode($multiValue);
                            if ($setting['Setting']['name'] == 'production_mode') {
                                $this->_saveGeneralSettings(array('production_mode' => $this->request->data['multi'][$setting['Setting']['id']]));
                            }
                            break;
                        case 'checkbox':
                            $setting = $this->Setting->findById($item);
                            $multiValue = json_decode($setting['Setting']['value_actual'], true);
                            foreach ($multiValue as $k => $multi) {
                                $multiValue[$k]['select'] = $this->request->data['multi'][$item][$multi['value']];
                            }
                            $values['value_actual'] = json_encode($multiValue);
                            break;
                        case 'timezone':
                            $values['value_actual'] = $this->request->data['timezone'][$item];
                            break;
                        case 'language':
                            $values['value_actual'] = $this->request->data['language'][$item];
                            break;
                    }

                    if (!is_writeable(APP . 'Config' . DS . 'settings.php') || !is_writeable(APP . 'Config' . DS . 'general.php')) {
                        $this->Session->setFlash(__('Updates Failed. Unable to save due to file permissions, please check your file permissions for') . '<br />"' . APP . 'Config' . DS . 'settings.php' . '"<br />"' . APP . 'Config' . DS . 'general.php' . '"', 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
                        $this->redirect($this->referer());
                    }

                    $this->Setting->id = $item;
                    $this->Setting->save($values);
                }
            }
        }

        $this->Session->setFlash(__('Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }

    public function admin_ffmpeg() {
        $bError = false;
        $aOutput = array();
        if (!function_exists('shell_exec') || !function_exists('exec')) {
            $bError = true;
            $sMessage = __('Unable to execute shell commands using exec(); or shell_exec();. The functions are disabled.');
        } else {
            exec($this->ffmpeg_path . ' 2>&1', $aOutput);

            if (preg_match("/ffmpeg version/i", $aOutput[0])) {
                $sMessage = __('FFMPEG is OK on server');
            } else {
                $bError = true;
                $sMessage = $aOutput[0];
            }
        }

        $this->set('bError', $bError);
        $this->set('sMessage', $sMessage);
    }

}
