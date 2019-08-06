<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

$oVerifyProfileHelper = MooCore::getInstance()->getHelper('VerifyProfile_VerifyProfile');
$oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
$sStatus = $oVerifyProfileModel->getStatus($user['User']['id']);

$this->addPhraseJs(array(
    'deny' => __d('verify_profile', 'Deny'),
    'verify' => __d('verify_profile', 'Verify'),
    'please_confirm' => __d('verify_profile', 'Please Confirm'),
    'verify_by_admin' => __d('verify_profile', 'Verify By Admin'),
    'request_verification' => __d('verify_profile', 'Request Verification'),
    'verification_pending' => __d('verify_profile', 'Verification Pending'),
    'are_you_sure_verify_this_profile' => __d('verify_profile', 'Are you sure that you want to verify this profile?'),
    'are_you_sure_cancel_and_verify_again' => __d('verify_profile', 'Are you sure that you want to cancel and verify again?'),
    'are_you_sure_request_verification' => __d('verify_profile', 'Are you sure that you want request verification? We will review your request and will notify you once the review process is completed.'),
));

$sImageVerify = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_badge_image'));
$sImageUnverify = $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_unverify_image'));
?>

<?php $this->Html->scriptStart(array('inline' => false, 'requires' => array('mooVerifyProfile'), 'object' => array('mooVerifyProfile'))); ?>
mooVerifyProfile.initMemberProfile();<?php $this->Html->scriptEnd(); ?>

<?php if (!empty($cuser['id']) && $sStatus != "verified"): ?>
<ul class="mooApp_pv">
    <li>
    <?php if ($cuser['Role']['is_admin']): ?>
        <?php if ($sStatus === FALSE || $sStatus == 'unverified'): ?>
            <a id="verifyByAdmin" class="verified_profile" href="javascript:void(0)" data-id="<?php echo $user['User']['id']; ?>">
                <img class="icon-verification" src="<?php echo $sImageUnverify; ?>">
            </a>
        <?php elseif ($sStatus == 'pending'): ?>
            <a id="requestPending" class="verified_profile" href="javascript:void(0)" data-id="<?php echo $user['User']['id']; ?>">
                <img class="icon-verification" src="<?php echo $sImageUnverify; ?>">
            </a>
        <?php endif; ?>
    <?php elseif ($cuser['id'] == $user['User']['id']): ?>
        <?php if ($sStatus === FALSE || $sStatus == 'unverified'): ?>
            <?php if (Configure::read('VerifyProfile.verify_profile_document_request_verification')): ?>
                <a class="verified_profile" href="<?php echo $this->request->base . '/profile/verify'; ?>">
                    <img class="icon-verification" src="<?php echo $sImageUnverify; ?>">
                </a>
            <?php else: ?>
                <a id="requestVerification" class="verified_profile" href="javascript:void(0)" data-id="<?php echo $user['User']['id']; ?>">
                    <img class="icon-verification" src="<?php echo $sImageUnverify; ?>">
                </a>
            <?php endif; ?>
        <?php elseif ($sStatus == 'pending'): ?>
            <a id="reverifyProfile" class="verified_profile" href="javascript:void(0)" data-id="<?php echo $user['User']['id']; ?>">
                <img class="icon-verification" src="<?php echo $sImageUnverify; ?>">
            </a>
        <?php endif; ?>
    <?php endif; ?>
    </li>
</ul>
<?php endif; ?>
                
<?php if ($sStatus == "verified" && Configure::read('VerifyProfile.verify_profile_show_profile_page')): ?>
<ul class="mooApp_pv">
    <li>
        <a href="javascript:void(0)" class="verified_profile tip" original-title="<?php echo __d('verify_profile', 'Verified'); ?>">
            <img class="icon-verification" src="<?php echo $sImageVerify; ?>">
        </a>
    </li>
</ul>
<?php endif; ?>



