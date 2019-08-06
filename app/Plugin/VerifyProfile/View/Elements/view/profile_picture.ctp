<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

 // Info user verify
$oVerifyProfileModel = MooCore::getInstance()->getModel('VerifyProfile.VerifyProfile');
$sStatus = $oVerifyProfileModel->getStatus($cuser['id']);

$this->addPhraseJs(array(
    'confirm' => __d('verify_profile', 'Confirm'),
    'please_confirm' => __d('verify_profile', 'Please Confirm'),
    'edit_these_verified_information' => __d('verify_profile', 'If you edit these verified information (%s), your account will become unverified.', 'Profile Picture')
));

?>

<?php if ($sStatus == 'verified'): ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooVerifyProfile'), 'object' => array('mooVerifyProfile'))); ?>
mooVerifyProfile.initEditProfile();<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

