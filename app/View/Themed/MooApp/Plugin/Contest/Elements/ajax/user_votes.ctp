<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooContest"], function ($, mooContest) {
            mooContest.initOnViewUserVote();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
    mooContest.initOnViewUserVote();
    <?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>

<?php if ( $page == 1 ): ?>
    <div class="title-modal">
        <?php
            if($user_count > 1) {
                $text = __d('contest',  '%s Peoples Vote This', $user_count);
            }else{
                $text = __d('contest',  '%s People Vote This', $user_count);
            }
        ?>
        <?php echo $text;?>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
    <ul class="list1 users_list user-like" id="list-content2">
<?php endif; ?>

<?php echo $this->element('lists/users_list_bit'); ?>

<?php if (count($users) >= Configure::read('Contest.contest_item_per_pages')):?>
    <?php $this->Html->viewMore($more_url,'list-content2') ?>
<?php endif; ?>

<?php if ( $page == 1 ): ?>
    </ul>
    </div>
<?php endif; ?>