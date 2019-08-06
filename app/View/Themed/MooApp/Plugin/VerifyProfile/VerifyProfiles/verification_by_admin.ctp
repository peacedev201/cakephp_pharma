<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div class="bar-content full_content p_m_10">
    <div class="content_center ">
        <form>
            <p><?php echo __d('verify_profile', 'Are you sure that you want to verify this profile?'); ?></p>
            
            <?php if ($sStatus === FALSE || $sStatus == 'unverified'): ?>
            <a href="javascript:void(0)" id="verifyVerification" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" data-id="<?php echo $aSUser['User']['id']; ?>"><?php echo __d('verify_profile', 'Confirm'); ?></a>
            <?php else: ?>
            <a href="javascript:void(0)" id="verifyVerification" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" data-id="<?php echo $aSUser['User']['id']; ?>"><?php echo __d('verify_profile', 'Verify'); ?></a>
            <a href="javascript:void(0)" id="denyVerification" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" data-id="<?php echo $aSUser['User']['id']; ?>"><?php echo __d('verify_profile', 'Deny'); ?></a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooVerifyProfile'), 'object' => array('mooVerifyProfile'))); ?>
mooVerifyProfile.initAppMemberProfile();
<?php $this->Html->scriptEnd(); ?>