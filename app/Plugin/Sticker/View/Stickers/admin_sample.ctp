<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('sticker', 'Sample Sticker');?>    </h4>
</div>
<div class="modal-body">
    <div class="sample_sticker">
        <img src="<?php echo $this->request->base."/sticker/images/sample.png";?>"/>
        <div class="sample_sticker_description">
            <?php echo __d('sticker', 'There are 5 blocks and total quantity is 20. Each image is moved from left to right and from top to bottom.');?>
        </div>
    </div>
</div>