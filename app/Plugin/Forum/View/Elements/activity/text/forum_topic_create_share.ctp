<?php if (isset($activity['Activity']['parent_id']) && $activity['Activity']['parent_id']): ?><!-- shared feed -->
    <?php
    list($plugin, $name) = mooPluginSplit($activity['Activity']['item_type']);
    $activityModel = MooCore::getInstance()->getModel('Activity');
    $parentFeed = $activityModel->findById($activity['Activity']['parent_id']);
    echo __d('forum',"shared");
    ?>
    <?php if(!empty($parentFeed['User']['id'])):?>
        <a class="moocore_tooltip_link" data-item_id="<?php echo $parentFeed['User']['id'] ?>" data-item_type="user" href="<?php echo $parentFeed['User']['moo_href'] ?>"> <?php echo $parentFeed['User']['name'] ?></a>'s <a href="<?php
        echo $this->Html->url(array(
            'plugin' => false,
            'controller' => 'users',
            'action' => 'view',
            $parentFeed['User']['id'],
            'activity_id' => $activity['Activity']['parent_id']
        ));
        ?>"><?php echo __d('forum','post'); ?></a>
    <?php else:?>
        <a class=""><b><?php echo __d('forum','Deleted Account');?></b></a>
    <?php endif;?>
<?php endif; ?>

    
    <?php if ($activity['Activity']['target_id']): ?>
    <?php
    $subject = MooCore::getInstance()->getItemByType($activity['Activity']['type'], $activity['Activity']['target_id']);

    list($plugin, $name) = mooPluginSplit($activity['Activity']['type']);
    $show_subject = MooCore::getInstance()->checkShowSubjectActivity($subject);

    if ($show_subject):
        ?>
        &rsaquo; <a <?php if ($name == 'User'):?>class="moocore_tooltip_link" data-item_id="<?php echo $subject[$name]['id'] ?>" data-item_type="user"<?php endif;?> href="<?php echo $subject[$name]['moo_href'] ?>"><?php echo h($subject[$name]['moo_title']) ?></a>
    <?php else: ?>
        <?php if (!empty($activity['Activity']['parent_id'])): ?>
            <?php //echo __d('forum','to your timeline'); ?>
        <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>