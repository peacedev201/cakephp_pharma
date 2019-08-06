<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBehavior"], function($, mooBehavior) {
        mooBehavior.initMoreResults();
    });
</script>
<?php endif?>

<?php if ( $page == 1 ): ?>
    <div class="title-modal">
        <?php echo __n("%s People Like This","%s Peoples Like This", $total_user_like,$total_user_like);?>

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
    <ul class="list1 users_list user-like" id="list-content2">
<?php endif; ?>

<?php echo $this->element('lists/users_list_bit'); ?>

<?php if (count($users) >= RESULTS_LIMIT):?>

    <?php $this->Html->viewMore($more_url,'list-content2') ?>
<?php endif; ?>

<?php if ( $page == 1 ): ?>
    </ul>
    </div>
<?php endif; ?>