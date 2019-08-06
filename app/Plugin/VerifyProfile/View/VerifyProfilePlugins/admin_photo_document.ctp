<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $oPhotoHelper = MooCore::getInstance()->getHelper('Photo_Photo'); ?>

<div class="modal-body">
    <div style="text-align: center">
        <img src="<?php echo $oPhotoHelper->getImage($aPhoto, array('prefix' => '450')); ?>">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('verify_profile', 'Close'); ?></button>
</div>