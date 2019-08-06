<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<script type="text/javascript">
    require(["mooVerifyProfile"], function(mooVerifyProfile) {
        mooVerifyProfile.initAjaxAvatar();
    });
</script>

<div class="title-modal">
    <?php echo __d('verify_profile', 'Please Confirm') ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <?php echo __d('verify_profile', 'If you edit these verified information (%s), your account will become unverified.', 'Profile Picture'); ?>
</div>
<div class="modal-footer">
    <a class="btn btn-action" href="javascript:void(0);" id="loadAjaxAvatar"><?php echo __d('verify_profile', 'Confirm')?></a>
    <a class="button button-action" href="javascript:void(0);" data-dismiss="modal"><span aria-hidden="true"><?php echo __d('verify_profile', 'Cancel')?></span></a>
</div>