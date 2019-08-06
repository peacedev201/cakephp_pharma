<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<?php if (!empty($contest)): ?>
    <?php if ($this->request->is('ajax')): ?>
        <script>
            require(["jquery", "mooContest"], function ($, mooContest) {
                mooContest.initListEntries();
            });
        </script>
    <?php else: ?>
        <?php $this->Html->scriptStart(array('inline' => false, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
        mooContest.initListEntries();
        <?php $this->Html->scriptEnd(); ?>
    <?php endif; ?>
    <h2><?php echo __d('contest', 'My Entries') ?></h2>
        <div class="contest_entries_content">
            <?php if(!$no_entries): ?>
                <?php if(!$contest['Contest']['auto_approve']): ?>
                    <div class="contest_nav">
                        <a class="active" href="javascript:void(0);" class="allBtn" data-ctype="my_approved" data-url="<?php echo $this->request->base ?>/contests/browse_entries/my_approved/<?php echo $contest['Contest']['id']; ?>"><?php echo __d('contest', 'Approved Entries'); ?></a>
                        <a href="javascript:void(0);" class="pendingBtn" data-ctype="my_pending" data-url="<?php echo $this->request->base ?>/contests/browse_entries/my_pending/<?php echo $contest['Contest']['id']; ?>"><?php echo __d('contest', 'Pending Entries'); ?></a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <ul id="list-content" class="entry-content-list">
                <?php echo $this->element('lists/entries_list', array('entries' => $entries, 'more_url' => $more_url, 'is_more_url' => $is_more_url, 'params' => $params, 'type' => $type, 'contest' => $contest)); ?>
            </ul>
            <?php if($contest['Contest']['contest_status'] != 'closed'): ?>
                <form id="manage_entries">
                    <?php echo $this->Form->hidden('contest_id', array('value' => $contest['Contest']['id'])); ?>
                    <?php echo $this->Form->hidden('entry_id_list', array('value' => '')); ?>
                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <?php echo $this->Form->select('entry_action', $entry_action, array('empty' => __d('contest', 'With selected...'))); ?>
                    </div>
                </form>  
            <?php endif; ?>
        </div>
<?php else : ?>
    <p><?php echo __d('contest', 'Contest does not exist') ?></p>
<?php endif; 
