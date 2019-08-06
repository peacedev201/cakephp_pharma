<?php if (isset($activity['Activity']['parent_id']) && $activity['Activity']['parent_id']): ?><!-- shared feed -->
    <?php
    $feedback = MooCore::getInstance()->getItemByType('Feedback_Feedback',$activity['Activity']['parent_id']);
    echo __("shared");
    ?><a class="moocore_tooltip_link" data-item_id="<?php echo $feedback['User']['id'] ?>" data-item_type="user" href="<?php echo $feedback['User']['moo_href'] ?>"> <?php echo $feedback['User']['name'] ?></a>'s <a href="<?php echo $feedback['Feedback']['moo_href']; ?>"><?php echo __d('feedback','feedback'); ?></a>
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