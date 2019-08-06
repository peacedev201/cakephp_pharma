<div class="title-modal">
    <?php echo __d('sms_verify','Verify Phone Number')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div id="sms_wrap_phone_number">
    <p><?php echo __d('sms_verify','Your mobile No has not been verified. Please input your mobile No and verify')?></p>
    <input id="sms_phone_number" placeholder="<?php echo __d('sms_verify','Area Code + Phone Number');?>" class="form-control" value="<?php echo $cuser['mobile']?>">
    <?php if (Configure::read("SmsVerify.sms_verify_enable_captcha") && Configure::read('core.recaptcha_publickey')): ?>        			
		<div style="margin-top:3px;" id="recaptcha_content">
			<script src='<?php echo $this->Moo->getRecaptchaJavascript();?>'></script>
			<div class="g-recaptcha" data-sitekey="<?php echo $this->Moo->getRecaptchaPublickey()?>"></div>
		</div>
	<?php endif; ?>
    </div>
    <div id="sms_wrap_phone_code" style="display: none;">
        <p><?php echo __d('sms_verify','6 digit number was sent to your phone. <br>To complete your mobile No change, please enter 6 digit code sent via SMS')?></p>
        <input style="display: none;" id="sms_phone_code" placeholder="<?php echo __('Code');?>" class="form-control">
    </div>
    <div id="sms_error" class="error-message" style="margin-top:3px;display:none;"></div>
</div>
<div class="modal-footer">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <a style="float:left" href="javascript:void(0)" id="smsVerifyButton" class="button button-caution"><?php echo __('Ok')?></a>
                    <a style="float:left; margin-left:3px" href="javascript:void(0)" data-dismiss="modal" class="button button-action"><?php echo __('Cancel')?></a>
                </td>
            </tr>
        </table>
</div>
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooSmsVerify"], function(mooSmsVerify) {
    	mooSmsVerify.initSend();
    });
</script>
<?php else:?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooSmsVerify'), 'object' => array('mooSmsVerify'))); ?>
	mooSmsVerify.initSend();
<?php $this->Html->scriptEnd(); ?>
<?php endif;?>