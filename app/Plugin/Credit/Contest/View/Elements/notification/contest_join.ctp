<?php
    $contest = MooCore::getInstance()->getItemByType('Contest_Contest', $notification['Notification']['params']);
?>
<?php echo __d('contest', 'joined your contest') ?> <b><?php echo $contest['Contest']['moo_title']; ?></b>