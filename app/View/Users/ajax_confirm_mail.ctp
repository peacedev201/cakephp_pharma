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
                <li id="input_mail">
                    <div class="col-sm-2">
                        <label><?php echo __('Email') ?></label>
                    </div>
                    <div class="col-sm-10">
                        <?php echo $this->Form->text('email'); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li id="sent_mail" style="display: none;">
                    <div class="col-md-12">
                        <p><?php echo __('Verification email was sent to');?><br/>
                        <div id="mail_input"></div><br/>
                        <?php echo __('Please click the validation link to confirm your new email.');?></p>
                    </div>
                </li>
                <li>
                    <div class="col-sm-2">
                        <label>&nbsp;</label>
                    </div>
                    <div class="col-sm-10">
                        <a href="javascript:void(0);" class="button button-action" data-dismiss="modal" id="btn_cancel"><?php echo __('Cancel') ?>
                        </a>
                        <?php if($type == 'mail'):?>
                            <a class="btn btn-action btn-submit" id="resend_validation_link_1" disabled="disabled"><?php echo __('Send verification') ?></a>
                        <?php else:?>
                            <a class="btn btn-action btn-submit" id="resend_sub_validation_link" disabled="disabled"><?php echo __('Send verification') ?></a>
                        <?php endif;?>
                        <a href="javascript:void(0);" class="btn btn-action" style="display: none" data-dismiss="modal" id="btn_ok"><?php echo __('Ok') ?></a>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>
        </form>
    </div>
    <div class="error-message" id="error_message" style="display:none;"></div>
</div>