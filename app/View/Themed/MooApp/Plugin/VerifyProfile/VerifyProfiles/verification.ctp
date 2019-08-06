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
        <?php if ($sStatus === FALSE || $sStatus == 'unverified'): ?>
            <p><?php echo __d('verify_profile', 'Are you sure that you want request verification? We will review your request and will notify you once the review process is completed.'); ?></p>
            <a href="javascript:void(0)" id="requestVerification" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored"><?php echo __d('verify_profile', 'Confirm'); ?></a>
        <?php else: ?>
            <p><?php echo __d('verify_profile', 'Are you sure that you want to cancel and verify again?') ?></p>
            <a href="javascript:void(0)" id="reverifyVerification" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored"><?php echo __d('verify_profile', 'Confirm'); ?></a>
        <?php endif; ?>
        </form>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooVerifyProfile'), 'object' => array('mooVerifyProfile'))); ?>
mooVerifyProfile.initAppMemberProfile();
<?php $this->Html->scriptEnd(); ?>