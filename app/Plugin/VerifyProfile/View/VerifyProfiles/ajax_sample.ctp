<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $oVerifyProfileHelper = MooCore::getInstance()->getHelper('VerifyProfile_VerifyProfile'); ?>

<div class="title-modal">
    <?php echo $sTitleSample; ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div style="text-align: center">
        <img src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_' . $sType . '_image')); ?>" width="100%">
    </div>
</div>