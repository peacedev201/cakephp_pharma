<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<style>
    .reason-unverify .col-sm-1{
        width: 4%;
        line-height: 0px;
    }
    
    #other_reason{
        display: none;
    }
    
    #other_reason .col-sm-12{
        padding-left: 7px;
    }
    
    .clear{
        clear: both
    }
</style>

<script>
    function otherReason(element) {
        if(element.checked === true){
            $('#other_reason').show();
        } else {
            $('#other_reason').hide();
        }
    }
    
    $(document).ready(function() {
        $('#unverifyButton').click(function() {
            disableButton('unverifyButton');
            $.post("<?php echo $this->Html->url(array('admin' => false, 'plugin' => 'verify_profile', 'controller' => 'verify_profiles', 'action' => 'ajax_unverify_process'));?>", $("#unverifyForm").serialize(), function(data) {
                enableButton('unverifyButton');
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    window.location.href = '<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_plugins', 'action' => 'admin_index'));?>';
                } else {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
        
        $("input:checkbox").uniform();
    });
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('verify_profile', 'Please tell the user why you are %s this member?', strtolower($sButton) . 'ing'); ?></h4>
</div>
<div class="modal-body">
    <form id="unverifyForm" class="form-horizontal" role="form">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <div>
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
                        <input type="checkbox" value="1" name="other_reason" onclick="otherReason(this)">
                        <span><?php echo __d('verify_profile', 'Other reason'); ?></span>
                    </label>
                </li>
                <li id="other_reason">
                    <div class="col-sm-12">
                        <textarea class="form-control" name="other_reason_content"></textarea>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="alert alert-danger error-message" style="display: none; margin: 10px 15px 0 7px;"></div>
                </li>
            </ul>
        </div>
    </form>
</div>
<div class="modal-footer">
    <a href="javascript:void(0)" id="unverifyButton" class="btn btn-action"><?php echo __d('verify_profile', '%s and send email', $sButton); ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('verify_profile', 'Close'); ?></button>
</div>