<?php
if ($value == CREDIT_STATUS_COMPLETED) {
    ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><?php echo __d('credit', 'Withdrawal request'); ?></h4>
    </div>
    <div class="modal-body">
        <form action="<?php echo $this->base.$url?>ajax_change_status_withdraw/<?php echo $id; ?>/<?php echo $value; ?>" method="POST">
            <div class="form-group">
                <label><?php echo __d('credit', 'Enter your transaction ID'); ?></label>
            </div>
            <div class="form-group">
                <?php echo $this->Form->text('transaction_id', array('class' => 'form-control')); ?>
            </div>
            <div clas="form-group">
                <button type="submit" class="btn btn-action"><?php echo __d('credit', 'Complete') ?></button>
            </div>
        </form>
    </div>
<?php } else { ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title"><?php echo __d('credit', 'Delete request'); ?></h4>
    </div>
    <div class="modal-body">
        <p><?php echo $text;?></p>
        <form action="<?php echo $this->base.$url?>ajax_change_status_withdraw/<?php echo $id; ?>/<?php echo $value; ?>" method="POST">
            <div clas="form-group">
                <button type="submit" class="btn btn-action"><?php echo __d('credit', 'Ok') ?></button>
            </div>
        </form>
    </div>
<?php } ?>
