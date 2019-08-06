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

<div class="modal-body review-photo">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
    <div class="review-photo-detail">
        <img src="<?php echo $oPhotoHelper->getImage($aPhoto, array('prefix' => '850')); ?>">
    </div>
</div>