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
    require(["jquery", "mooVerifyProfile"], function($, mooVerifyProfile) {
    	mooVerifyProfile.initAjaxUnverify();
    });
</script>

<div class="title-modal">
    <?php echo __d('verify_profile', 'Please tell the user why you are %s this member?', 'denying') ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="error-message" style="display: none;"></div>
    <form id="unverifyForm">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <ul class="list6 reason-unverify">
            <?php foreach ($aReasons as $aReason): ?>
            <li>
                <label>
                    <input type="checkbox" value="<?php echo $aReason['VerifyReason']['id']; ?>" name="reason[]">
                    <span><?php echo $aReason['VerifyReason']['description']; ?></span>
                </label>
            </li>
            <?php endforeach; ?>
            <li>
                <label>
                    <input type="checkbox" value="1" name="other_reason" id="otherReason">
                    <span><?php echo __d('verify_profile', 'Other reason'); ?></span>
                </label>
            </li>
            <li id="otherReasonContent">
                <div class="col-sm-12">
                    <?php echo $this->Form->textarea('other_reason_content'); ?>
                </div>
                <div class="clear"></div>
            </li>
        </ul>
        <a id="unverifyAction" class="btn btn-action" href="javascript:void(0)"><?php echo __d('verify_profile', 'Deny and send email'); ?></a>
    </form>
</div>