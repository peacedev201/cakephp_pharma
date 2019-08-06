<?php 
	$currency = Configure::read('Config.currency');
?>
<div class="title-modal">
    <?php echo __d('forum','Pin topic on top')?>
    <button style="width: inherit" type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="">
        <form id="pinTopicForm" method="post" action="<?php echo !$is_moderator ? $this->request->base .'/forum/forum_pins/gateway' : $this->request->base .'/forum/forum_pins/moderator';?>">
        	<input type="hidden" name="id" value="<?php echo $topic['ForumTopic']['id']?>">
        	<?php if (!$is_moderator):?>
	            <div class="form-group">
	                <span><?php echo __d('forum','Price per day');?></span>: <?php echo $currency['Currency']['symbol']; ?><?php echo Configure::read('Forum.forum_price_pin_per_day');?>
	            </div>
            <?php endif;?>
            <div class="form-group">
                <div><?php echo __d('forum','How many days do you want to pin?');?></div>
                <?php echo $this->Form->text('time', array('type'=>'number','min'=>'1','value' => '30', 'class' => 'input-pin-days')); ?>
                <?php if (!$is_moderator):?><?php echo __d('forum','Total price');?>: <?php echo $currency['Currency']['symbol']; ?><span id="pin_total"><?php echo 30 * Configure::read('Forum.forum_price_pin_per_day');?></span><?php endif;?>
                <div class="clear"></div>
            </div>
            <div class="pin-form-action">
                <button class="btn btn-action" id="btn_pin"><?php echo __d('forum','Pin now');?></button>
                <a href="javascript:void(0)" class="button" data-dismiss="modal"><?php echo __d('forum','Cancel');?></a>
                <div class="clear"></div>
            </div>
            <div class="form-group"><br/><div class="error-message" id="errorMessage" style="display:none"></div></div>
        </form>
    </div>
</div>
<script>
require(["mooForum"], function(mooForum) {
	mooForum.initPinTopic(<?php echo Configure::read('Forum.forum_price_pin_per_day');?>);
});
</script>