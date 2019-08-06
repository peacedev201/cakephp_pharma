<?php echo $this->element('lists/users_list_bit'); ?>
<?php if (count($users) + ($page - 1) * $limit < $count):?>
    <?php if($active_reaction == REACTION_ALL): ?>
        <?php $this->Html->viewMore($more_url,'list-reaction-all') ?>
    <?php else: ?>
        <?php $this->Html->viewMore($more_url,'list-reaction-'.$active_reaction) ?>
    <?php endif; ?>
    <?php if($this->request->is('ajax')): ?>
    <script>
        require(["mooBehavior"], function(mooBehavior) {
            mooBehavior.initMoreResultsPopup();
        });
    </script>
    <?php else: ?>
        <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('mooBehavior'),'object'=>array('mooBehavior'))); ?>
        mooBehavior.initMoreResultsPopup();
        <?php $this->Html->scriptEnd(); ?>
    <?php endif; ?>
<?php endif; ?>
