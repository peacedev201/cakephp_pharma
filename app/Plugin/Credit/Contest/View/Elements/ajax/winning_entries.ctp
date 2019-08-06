<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<?php if(!empty($entries)): ?>
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
    <h2><?php echo __d('contest', 'Winning Etries') ?></h2>
        <div class="contest_entries_content">
            <ul id="list-content" class="entry-content-list">
                <?php echo $this->element('lists/entries_list', array('entries' => $entries, 'type' => '', 'contest' => $contest)); ?>
            </ul>
        </div>
<?php else : ?>
    <p><?php echo __d('contest', 'No more results found') ?></p>
<?php endif; 
