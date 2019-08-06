<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooGlobal"], function($,mooGlobal) {
        mooGlobal.initConversationSendBtn();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true,'requires'=>array('jquery', 'mooGlobal'), 'object' => array('$', 'mooGlobal'))); ?>
mooGlobal.initConversationSendBtn();
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<div class="title-modal">
    <?php echo __('Send New Message')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="create_form">
<form id="sendMessage">
<ul class="list6 list6sm2" style="position:relative">
    <input type="hidden" name="data[friends]" value="<?php echo $groupUsers ?>">
    <li>
        <div class="col-sm-2">
            <label><?php echo __('To') ?></label>
        </div>
        <div class="col-sm-10">
            <?php echo __('All Group Members') ?>
        </div>
        <div class="clear"></div>
    </li>
    <li>
            <div class="col-sm-2">
            <label><?php echo __('Subject')?></label>
            </div>
            <div class="col-sm-10">
                <?php echo $this->Form->text('subject'); ?>
            </div>
             <div class="clear"></div>
        </li>
    <li>
            <div class="col-sm-2">
                <label><?php echo __('Message')?></label>
            </div>
            <div class="col-sm-10">
                <?php echo $this->Form->textarea('message', array('style' => 'height:120px')); ?>
            </div>
             <div class="clear"></div>
        </li>
    <li>
            <div class="col-sm-2">
                <label>&nbsp;</label>
            </div>
            <div class="col-sm-10">
                <a href="javascript:void(0);" class="button button-action" id="sendButton"><?php echo __('Send Message')?>
                </a>
            </div>
             <div class="clear"></div>
        </li>
</ul>
</form>
</div>
<div class="error-message" style="display:none;"></div>
</div>