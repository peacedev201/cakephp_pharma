<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php 
$oVerifyProfileHelper = MooCore::getInstance()->getHelper('VerifyProfile_VerifyProfile'); 
$this->addPhraseJs(array(
    'confirm' => __d('verify_profile', 'Confirm'),
    'the_number_documents_for_verification_request' => __d('verify_profile', 'The number documents for verification request is %s', Configure::read('VerifyProfile.verify_profile_document'))
));
?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooVerifyProfile'), 'object' => array('$', 'mooVerifyProfile'))); ?>
mooVerifyProfile.initVerifyProfile(<?php echo (int) Configure::read('VerifyProfile.verify_profile_document'); ?>);<?php $this->Html->scriptEnd(); ?>

<?php $this->setNotEmpty('north');?>
<?php $this->start('north'); ?>
<div class="verify-profile-header">
    <div class="box_content">
        <h1><?php echo __d('verify_profile', 'Verify My Profile'); ?></h1>
        <div><?php echo __d('verify_profile', 'WHY_VERIFY_MY_PROFILE'); ?></div>
    </div>
</div>
<?php $this->end(); ?>

<div class="bar-content">
    <div class="content_center">
        <ul class="body-verification" style="min-height: 250px;">
            <?php if(Configure::read('VerifyProfile.verify_profile_passport')): ?>
            <li class="col-md-3 full_content item-verification-accepted">      
                <div class="item-verification">
                    <a class="tip item-verification-photo" original-title="<?php echo __d('verify_profile', "Passport's Sample Image"); ?>" data-backdrop="true" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->request->base . '/profile/verify/ajax_sample/passport'; ?>">
                        <img src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_passport_image')); ?>">
                    </a>
                    <div class="item-verification-photo-status">
                        <?php echo __d('verify_profile', 'Accepted'); ?>
                    </div>
                </div>
            </li>
            <?php endif; ?>
            
            <?php if(Configure::read('VerifyProfile.verify_profile_driver')): ?>
            <li class="col-md-3 full_content item-verification-accepted">      
                <div class="item-verification">
                    <a class="tip item-verification-photo" original-title="<?php echo __d('verify_profile', "Driver License's Sample Image"); ?>" data-backdrop="true" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->request->base . '/profile/verify/ajax_sample/driver'; ?>">
                        <img src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_driver_image')); ?>">
                    </a>
                    <div class="item-verification-photo-status">
                        <?php echo __d('verify_profile', 'Accepted'); ?>
                    </div>
                </div>
            </li>
            <?php endif; ?>
            
            <?php if(Configure::read('VerifyProfile.verify_profile_card')): ?>
            <li class="col-md-3 full_content item-verification-accepted">      
                <div class="item-verification">
                    <a class="tip item-verification-photo" original-title="<?php echo __d('verify_profile', "ID Card's Sample Image"); ?>" data-backdrop="true" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->request->base . '/profile/verify/ajax_sample/card'; ?>">
                        <img src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_card_image')); ?>">
                    </a>
                    <div class="item-verification-photo-status">
                        <?php echo __d('verify_profile', 'Accepted'); ?>
                    </div>
                </div>
            </li>
            <?php endif; ?>
            
            <?php if(Configure::read('VerifyProfile.verify_profile_deny')): ?>
            <li class="col-md-3 full_content item-verification-declined">      
                <div class="item-verification">
                    <a class="tip item-verification-photo" original-title="<?php echo __d('verify_profile', "Deny Photo's Sample Image"); ?>" data-backdrop="true" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->request->base . '/profile/verify/ajax_sample/deny'; ?>">
                        <img src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_deny_image')); ?>">
                    </a>
                    <div class="item-verification-photo-status">
                        <?php echo __d('verify_profile', 'Declined'); ?>
                    </div>
                </div>
            </li>
            <?php endif; ?>
        </ul>
        <div class="clear"></div>
        <div class="full_content p_m_10 form-verification">
            <div class="form_content">
                <form id='uploadPhotoForm' action="<?php echo  $this->request->base; ?>/profile/verify/save" method="post">
                    <div><?php echo __d('verify_profile', 'The number documents for verification request is %s', Configure::read('VerifyProfile.verify_profile_document')); ?></div>
                    <input type="hidden" name="new_photos" id="new_photos">
                    <div id="photos_upload"></div>
                    <div id="photo_review"></div>
                    
                    <a href="javascript:void(0);" class="btn btn-action" id="triggerUpload"><?php echo __d('verify_profile', 'Upload Queued Files'); ?></a>
                    <a href="javascript:void(0);" class="btn btn-action" id="nextStep" style="display: none"><?php echo __d('verify_profile', 'Submit Documents'); ?></a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->setNotEmpty('south');?>
<?php $this->start('south'); ?>
<div class="verify-profile-header">
    <div class="box_content">
        <h1><?php echo __d('verify_profile', 'Profile and page verification badges'); ?></h1>
        <div><?php echo __d('verify_profile', 'PROFILE_AND_PAGE_VERIFICATION_BADGES'); ?></div>
    </div>
</div>
<?php $this->end(); ?>
