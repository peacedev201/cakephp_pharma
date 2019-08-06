<div class="title-modal">
    <?php echo __d('feedback', 'Thank you for your Feedback') ?> 
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="comment_message">
        <?php echo __d('feedback', 'Your Feedback was successfully posted.') ?> 
        <?php if(!$approved):?>
            <?php echo __d('feedback', 'Please wait for publication approval from Admin.') ?> 
        <?php endif;?>
    </div>
    <br>
    <div class="clear"></div>
    <?php if($approved):?>
        <a href="<?php echo $this->request->base . $url_feedback ?>/view/<?php echo $feedback_id;?>" class="btn-action button button-action" id="createButton">
            <?php echo __d('feedback', 'View feedback') ?> 
        </a>
    <?php else:?>
        <a href="javascript:void(0)" class="button button-action" data-dismiss="modal">
            <?php echo __d('feedback', 'Close') ?> 
        </a>
    <?php endif;?>
</div>
