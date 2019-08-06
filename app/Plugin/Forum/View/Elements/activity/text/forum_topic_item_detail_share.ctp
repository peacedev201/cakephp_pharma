<?php if (isset($activity['Activity']['parent_id']) && $activity['Activity']['parent_id']): ?><!-- shared feed -->
    <?php
    $topic = MooCore::getInstance()->getItemByType('Forum_Forum_Topic',$activity['Activity']['parent_id']);
    echo __("shared");
    ?>
    <?php if(!empty($topic['User']['id'])):?>
        <a class="moocore_tooltip_link" data-item_id="<?php echo $topic['User']['id'] ?>" data-item_type="user" href="<?php echo $topic['User']['moo_href'] ?>"> <?php echo $topic['User']['name'] ?></a>'s <a href="<?php echo $topic['ForumTopic']['moo_href']; ?>"><?php echo __d('forum','forum topic'); ?></a>
    <?php else :?>
        <a class=""><b><?php echo __d('forum','Deleted Account');?></b></a>'s <a href="<?php echo $topic['ForumTopic']['moo_href']; ?>"><?php echo __d('forum','forum topic'); ?></a>
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
            <?php //echo __('to your timeline'); ?>
        <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>