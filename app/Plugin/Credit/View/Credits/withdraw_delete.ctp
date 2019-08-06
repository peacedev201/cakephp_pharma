<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('credit', 'Delete request'); ?></h4>
</div>
<div class="modal-body">
    <p><?php echo $text;?></p>
    <form action="<?php echo $this->base.$url?>/credits/withdraw_delete/<?php echo $id; ?>/" method="POST">
        <div clas="form-group">
            <button type="submit" class="btn btn-action"><?php echo __d('credit', 'Ok') ?></button>
        </div>
    </form>
</div>
