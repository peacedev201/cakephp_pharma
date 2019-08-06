<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<script>    
    $(document).ready(function() {
        $('#verifyButton').click(function() {
            disableButton('verifyButton');
            $.post("<?php echo $this->Html->url(array('admin' => false, 'plugin' => 'verify_profile', 'controller' => 'verify_profiles', 'action' => 'ajax_verify'));?>", $("#verifyForm").serialize(), function() {
                window.location.reload();
            });
            return false;
        });
    });
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('verify_profile', 'Please Confirm'); ?></h4>
</div>
<div class="modal-body">
    <form id="verifyForm" class="form-horizontal" role="form">
        <input type="hidden" name="id" value="<?php echo $user_id; ?>">
        <div>
            <?php echo __d('verify_profile', 'Are you sure that you want to verify this profile?'); ?>
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display: none; margin-top: 10px;"></div>
</div>
<div class="modal-footer">
    <a href="javascript:void(0)" id="verifyButton" class="btn btn-action"><?php echo __d('verify_profile', 'Verify'); ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('verify_profile', 'Close'); ?></button>
</div>