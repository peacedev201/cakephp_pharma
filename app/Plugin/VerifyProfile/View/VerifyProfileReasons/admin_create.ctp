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
        $('#editButton').click(function() {
            disableButton('editButton');
            $.post("<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_reasons', 'action' => 'admin_save'));?>", $("#editForm").serialize(), function(data) {
                enableButton('editButton');
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    window.location.reload();
                } else {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    });
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo $bIsEdit ? __d('verify_profile', 'Edit Reason') : __d('verify_profile', 'Add New Reason'); ?></h4>
</div>
<div class="modal-body">
    <form id="editForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => isset($aReason['VerifyReason']['id']) ? $aReason['VerifyReason']['id'] : '')); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('verify_profile', 'Description');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->textarea('description', array('class' => 'form-control', 'value' => isset($aReason['VerifyReason']['description']) ? $aReason['VerifyReason']['description'] : '')); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">&nbsp;</label>
                <div class="col-md-9">
                    <div class="alert alert-danger error-message" style="display: none; margin-top: 10px;"></div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <a href="javascript:void(0)" id="editButton" class="btn btn-action"><?php echo __d('verify_profile', 'Save'); ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('verify_profile', 'Close'); ?></button>
</div>