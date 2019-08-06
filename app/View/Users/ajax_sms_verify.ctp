<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooUser"], function($,mooUser) {
        mooUser.resendValidationLink();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true,'requires'=>array('jquery', 'mooUser'), 'object' => array('$', 'mooUser'))); ?>
    mooUser.resendValidationLink();
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<div class="title-modal">
    <?php echo __('Verification') ?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="create_form">
        <form id="sendMessage">
            <ul class="list6 list6sm2" style="position:relative">
                <li id="input_phone">
                    <div class="col-sm-2">
                        <label><?php echo __('New mobile No. (without "-")') ?></label>
                    </div>
                    <div class="col-sm-10">
                        <?php echo $this->Form->text('sms_verify_phone'); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li id="update_phone_section" style="display: none;">
                    <div class="col-md-12">
                        <p><?php echo __('6 digit number was sent to your phone');?><br/>
                        <?php echo __('To complete your mobile No. change, please enter 6 digit code sent via SMS');?></p>
                        <?php echo $this->Form->text('sms_verify_code'); ?>
                    </div>
                </li>
                <li>
                    <div class="col-sm-2">
                        <label>&nbsp;</label>
                    </div>
                    <div class="col-sm-10">
                        <a href="javascript:void(0);" class="button button-action" data-dismiss="modal" id="btn_cancel"><?php echo __('Cancel') ?>
                        </a>
                        <a class="btn btn-action btn-submit" id="send_verify_mobile" disabled="disabled"><?php echo __('Send verification') ?></a>
                        <a class="btn btn-action" id="btn_update_phone" style="display: none"><?php echo __('Done') ?></a>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>
        </form>
    </div>
    <div class="error-message" id="error_message" style="display:none;"></div>
</div>