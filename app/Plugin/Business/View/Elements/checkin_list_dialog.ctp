<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBehavior"], function($, mooBehavior) {
        mooBehavior.initMoreResults();
    });
</script>
<?php endif?>
<div class="title-modal">
    <?php echo __d('business', 'Check In');?>
    <button data-dismiss="modal" class="close" type="button">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <div id="users_content" class="user_review_dialog">
        <?php echo $this->Element('Business.lists/checkin_list');?>
    </div>
    <div class="clear"></div>
</div>
