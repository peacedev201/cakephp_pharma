<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
<?php if ( $page == 1 ): ?>
    <?php $text = __n("%s People Thank This","%s Peoples Thank This", $count,$count); ?>
    <div class="title-modal">
        <?php echo $text;?>

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
    <ul class="list1 users_list user-like" id="list-content2">
<?php endif; ?>

<?php echo $this->element('lists/forum_users_list', array(), array('plugin' => 'Forum')); ?>

<?php if (count($users) + ($page - 1) * RESULTS_LIMIT < $count):?>

    <?php $this->Html->viewMore($more_url,'list-content2') ?>
	<script>
		require(["mooBehavior"], function(mooBehavior) {
			mooBehavior.initMoreResultsPopup();
        });
	</script>
<?php endif; ?>

<?php if ( $page == 1 ): ?>
    </ul>
    </div>
<?php endif; ?>