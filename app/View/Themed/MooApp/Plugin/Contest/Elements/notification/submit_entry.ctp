<?php
    $mEntry =  MooCore::getInstance()->getModel('Contest.ContestEntry');
    $entry = $mEntry->findById($notification['Notification']['params']);
?>
<?php echo __d('contest', 'submitted a entry to contest') ?> <?php echo $entry['Contest']['moo_title']; ?>