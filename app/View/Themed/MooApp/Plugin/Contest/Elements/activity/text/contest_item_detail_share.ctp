<?php if (isset($activity['Activity']['parent_id']) && $activity['Activity']['parent_id']): ?><!-- shared feed -->
    <?php
    $mModel = MooCore::getInstance()->getModel('Contest.Contest');
    $item = $mModel->findById($activity['Activity']['parent_id']);
    echo __d('contest', "shared");
    ?><a href="<?php echo $item['User']['moo_href'] ?>"> <?php echo $item['User']['name'] ?></a>'s <a href="<?php echo $item[key($item)]['moo_href']; ?>"><?php echo __d('contest', 'contest'); ?></a>
<?php endif; ?>

<?php if ($activity['Activity']['target_id']): ?>
    <?php
    $subject = MooCore::getInstance()->getItemByType($activity['Activity']['type'], $activity['Activity']['target_id']);

    list($plugin, $name) = mooPluginSplit($activity['Activity']['type']);
    $show_subject = MooCore::getInstance()->checkShowSubjectActivity($subject);

    if ($show_subject):
        ?>
        &rsaquo; <a href="<?php echo $subject[$name]['moo_href'] ?>"><?php echo htmlspecialchars($subject[$name]['moo_title']) ?></a>
    <?php endif; ?>

<?php endif; ?>