<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["mooSmsVerify"], function(mooSmsVerify) {
            mooSmsVerify.initCheck();
        });
    </script>
<?php else:?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooSmsVerify'), 'object' => array('mooSmsVerify'))); ?>
    mooSmsVerify.initCheck();
    <?php $this->Html->scriptEnd(); ?>
<?php endif;?>

<div class="title-modal">
    <?php echo __('Confirm')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="create_form">
    <ul class="list6 list6sm2" style="position:relative">
        <li>
            <div class="col-sm-12">
                <p><?php echo __('An message has been sent to your new Mobile no. Please input the code sent to you into below box, and clik \'OK\'.')?></p>
                <?php echo $this->Form->text('sms_phone_code'); ?>
            </div>
             <div class="clear"></div>
        </li>
        <li>
                <div class="col-sm-12">
                    <a href="javascript:void(0);" class="button button-action" id="checkButton"><?php echo __('OK')?>
                    </a>
                </div>
                 <div class="clear"></div>
            </li>
    </ul>
    </div>
<div class="error-message" id="sms_error" style="display:none;"></div>
</div>