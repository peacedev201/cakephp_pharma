<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class VerifyProfileSettingsController extends VerifyProfileAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Setting');
    }

    public function admin_index() {
        $this->loadModel('Role');
        $aRoles = $this->Role->find('list', array('field' => array('name')));
        unset($aRoles[ROLE_GUEST]);

        $sRoleGroup = Configure::read('VerifyProfile.verify_profile_group');
        $aRoleGroup = explode('_', $sRoleGroup);

        $aUnverifyGroup = array();
        if (!empty($aRoleGroup[0])) {
            $aUnverifyGroup = json_decode($aRoleGroup[0]);
        }

        $aVerifyGroup = array();
        if (!empty($aRoleGroup[1])) {
            $aVerifyGroup = json_decode($aRoleGroup[1]);
        }

        $sRowGroup = "";
        $aVGroup = array();
        $aUGroup = array();
        foreach ($aUnverifyGroup as $key => $value) {

            $aRole = $this->Role->findById($value);
            $sRoleName = $aRole['Role']['name'];
            $aUGroup[$value] = $aRole['Role']['name'];

            $aRoleV = $this->Role->findById($aVerifyGroup[$key]);
            $sRoleNameV = $aRoleV['Role']['name'];
            $aVGroup[$aVerifyGroup[$key]] = $aRoleV['Role']['name'];

            $sRowGroup .= "<tr>";
            $sRowGroup .= "<td>" . $sRoleName . "<input type='hidden' value='" . $value . "' name='unverified_group_mapping[]' /></td>";
            $sRowGroup .= "<td>" . $sRoleNameV . "<input type='hidden' value='" . $aVerifyGroup[$key] . "' name='verified_group_mapping[]' /></td>";
            $sRowGroup .= "<td><a href='javascript:void(0);' onclick='removeGroup(this);' class='btn btn-gray'>" . __d('verify_profile', 'Remove') . "</a></td>";
            $sRowGroup .= "</tr>";
        }

        $this->set('sRowGroup', $sRowGroup);
        $this->set('aVRoles', array_diff($aRoles, $aVGroup));
        $this->set('aURoles', array_diff($aRoles, $aUGroup));
        $this->set('title_for_layout', __d('verify_profile', 'Profile Verify Settings'));
    }

    public function admin_save_validate() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($this->request->data['number_review']) && ((int) $this->request->data['number_review'] <= 0 || !is_numeric($this->request->data['number_review']))) {
            $response['result'] = 0;
            $response['message'] = __d('verify_profile', 'Min review: Only number is allowed!');

            echo json_encode($response);
            exit;
        }

        if (!empty($this->request->data['average_review']) && ((int) $this->request->data['average_review'] <= 0 || !is_numeric($this->request->data['average_review']))) {
            $response['result'] = 0;
            $response['message'] = __d('verify_profile', 'Average score: Only number is allowed!');

            if ((int) $this->request->data['average_review'] > 5) {
                $response['message'] = __d('verify_profile', 'Average score: Max is 5!');
            }

            echo json_encode($response);
            exit;
        }
        
        if (!empty($this->request->data['average_review']) && (int) $this->request->data['average_review'] > 5) {
            $response['result'] = 0;
            $response['message'] = __d('verify_profile', 'Average score: Max is 5!');

            echo json_encode($response);
            exit;
        }

        if ((int) $this->request->data['document'] <= 0 || !is_numeric($this->request->data['document'])) {
            $response['result'] = 0;
            $response['message'] = __d('verify_profile', 'Number of Document: Only number is allowed!');

            echo json_encode($response);
            exit;
        }

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_save() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        if (isset($_FILES['Filedata'])) {
            // import ThumbLib
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

            foreach ($_FILES['Filedata']['name'] as $sKey => $sName) {
                if (is_uploaded_file($_FILES['Filedata']['tmp_name'][$sKey])) {
                    $aSize = getimagesize($_FILES['Filedata']['tmp_name'][$sKey]);
                    if (empty($aSize) || !in_array($aSize['mime'], array('image/png', 'image/jpeg', 'image/gif'))) {
                        continue;
                    }

                    $sFilename = $sKey . '_' . md5(time()) . '.' . ltrim(strrchr($_FILES['Filedata']['name'][$sKey], '.'), '.');
                    $sPathUpload = WWW_ROOT . 'verify_profile' . DS . 'img' . DS . 'setting' . DS;

                    $oPhoto = PhpThumbFactory::create($_FILES['Filedata']['tmp_name'][$sKey]);
                    if ($sKey == 'badge' || $sKey == 'unverify') {
                        $oPhoto->resize(9999, 50)->save($sPathUpload . DS . $sFilename);
                    } else {
                        $oPhoto->resize(9999, 248)->save($sPathUpload . DS . $sFilename);
                    }
                    $this->request->data[$sKey . '_image'] = $sFilename;
                }
            }
        }

        $jsonEncode = "";
        if (!empty($this->request->data['unverified_group_mapping']) && !empty($this->request->data['verified_group_mapping'])) {
            $jsonEncode = json_encode($this->request->data['unverified_group_mapping']) . "_" . json_encode($this->request->data['verified_group_mapping']);
            unset($this->request->data['unverified_group_mapping']);
            unset($this->request->data['verified_group_mapping']);
        }
        unset($this->request->data['unverified_group']);
        unset($this->request->data['verified_group']);
        
        // check enable user review
        $this->loadModel('Plugin');
        $aPlugin = $this->Plugin->findByKey('Review', array('enabled'));
        if(empty($aPlugin) || empty($aPlugin['Plugin']['enabled']) || !Configure::read('Review.review_enabled')){
            $this->request->data['auto_verify_after_review'] = 0;
        }

        $this->request->data['group'] = $jsonEncode;
        foreach ($this->request->data as $sKey => $sValue) {
            if ($sKey == 'enable') {
                $sValueActual = ((int) $sValue) ? '[{"name":"Disable","value":"0","select":0},{"name":"Enable","value":"1","select":1}]' : '[{"name":"Disable","value":"0","select":1},{"name":"Enable","value":"1","select":0}]';
                $aSetting = $this->Setting->findByName('verify_profile_enable');
                $this->Setting->id = $aSetting['Setting']['id'];

                $this->Setting->save(array('value_actual' => $sValueActual));
                continue;
            }

            $this->Setting->updateAll(array('Setting.value_actual' => "'$sValue'"), array('Setting.name' => 'verify_profile_' . $sKey));
        }

        $this->Session->setFlash(__d('verify_profile', 'Setting has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect(Router::url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_settings', 'action' => 'admin_index'), true));
    }

    public function admin_reset() {

        $this->autoRender = false;

        $aData = array(
            'enable' => 1,
            'auto_verify_after_review' => 0,
            'number_review' => 5,
            'average_review' => 5,
            'auto_unverify_after_review' => 0,
            'document_request_verification' => 0,
            'show_activity_feed' => 1,
            'show_profile_page' => 1,
            'show_profile_popup' => 1,
            'unverify' => 0,
            'full_name' => 1,
            'avatar' => 1,
            'birthday' => 1,
            'gender' => 1,
            'passport' => 1,
            'driver' => 1,
            'card' => 1,
            'deny' => 1,
            'document' => 3
        );

        $aFiledata = array(
            'badge' => Configure::read('VerifyProfile.verify_profile_badge_default_image'),
            'unverify' => Configure::read('VerifyProfile.verify_profile_unverify_default_image'),
            'passport' => Configure::read('VerifyProfile.verify_profile_passport_default_image'),
            'driver' => Configure::read('VerifyProfile.verify_profile_driver_default_image'),
            'card' => Configure::read('VerifyProfile.verify_profile_card_default_image'),
            'deny' => Configure::read('VerifyProfile.verify_profile_deny_default_image'),
        );

        foreach ($aFiledata as $sKey => $sValue) {
            $sPathUpload = APP . 'webroot' . DS . 'verify_profile' . DS . 'img' . DS . 'setting' . DS;
            $sFile = $sPathUpload . Configure::read('VerifyProfile.verify_profile_' . $sKey . '_default_image');
            if (file_exists($sFile)) {
                $sFilename = $sKey . '_' . md5(time()) . '.' . ltrim(strrchr($sFile, '.'), '.');
                if (copy($sFile, $sPathUpload . $sFilename)) {
                    $aData[$sKey . '_image'] = $sFilename;
                }
            }
        }

        foreach ($aData as $sKey => $sValue) {
            if ($sKey == 'enable') {
                $sValueActual = ((int) $sValue) ? '[{"name":"Disable","value":"0","select":0},{"name":"Enable","value":"1","select":1}]' : '[{"name":"Disable","value":"0","select":1},{"name":"Enable","value":"1","select":0}]';
                $aSetting = $this->Setting->findByName('verify_profile_enable');
                $this->Setting->id = $aSetting['Setting']['id'];

                $this->Setting->save(array('value_actual' => $sValueActual));
                continue;
            }

            $this->Setting->updateAll(array('Setting.value_actual' => "'$sValue'"), array('Setting.name' => 'verify_profile_' . $sKey));
        }

        $this->Session->setFlash(__d('verify_profile', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }

}
