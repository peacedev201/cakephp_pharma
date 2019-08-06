<?php if (!$check):?>
<div class="alert alert-success" id="sms_content_message" style="display: none;" role="alert">
	<?php echo __d('sms_verify','You has been verified with sms');?>
</div>
<div id="sms_content">
	<div class="title-modal">
	    <?php echo __d('sms_verify','Verify Phone Number')?>
	    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
	    <input id="sms_phone_number" placeholder="<?php echo __d('sms_verify','Area Code + Phone Number');?>" class="form-control">
	    <?php if (Configure::read("SmsVerify.sms_verify_enable_captcha") && Configure::read('core.recaptcha_publickey')): ?>        			
			<div style="margin-top:3px;" id="recaptcha_content">
				<script src='<?php echo $this->Moo->getRecaptchaJavascript();?>'></script>
				<div class="g-recaptcha" data-sitekey="<?php echo $this->Moo->getRecaptchaPublickey()?>"></div>
			</div>
		<?php endif; ?>
	    <input style="display: none;" id="sms_phone_code" placeholder="<?php echo __('Code');?>" class="form-control">
	    <div id="sms_error" class="error-message" style="margin-top:3px;display:none;"></div>
	    
	</div>
	<div class="modal-footer">
	        <table width="100%" cellpadding="0" cellspacing="0">
	            <tr>
	                <td>
	                    <a style="float:left" href="javascript:void(0)" id="smsVerifyButton" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1"><?php echo __('Ok')?></a>
	                </td>
	            </tr>
	        </table>
	</div>
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

<script>
	function afterDone()
	{
 		window.mobileAction.backAndRefesh({});
	}
 </script>
 <?php else:?>
 	<div style="padding-top: 5px;" class="modal-body">
	    <?php echo __d('sms_verify','You has been verified with sms');?>
	</div>
 <?php endif;?>