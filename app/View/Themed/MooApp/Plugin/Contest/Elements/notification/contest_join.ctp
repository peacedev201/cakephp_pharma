<?php
    $contest = MooCore::getInstance()->getItemByType('Contest_Contest', $notification['Notification']['params']);
?>
<?php echo __d('contest', 'joined your contest') ?> <?php echo $contest['Contest']['moo_title']; ?>