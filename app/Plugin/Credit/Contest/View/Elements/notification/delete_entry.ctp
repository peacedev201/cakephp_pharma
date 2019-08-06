<?php
    $mContest =  MooCore::getInstance()->getModel('Contest.Contest');
    $contest = $mContest->findById($notification['Notification']['params']);
?>
<?php echo __d('contest', 'delete your entry on contest') ?> <b><?php echo $contest['Contest']['moo_title']; ?></b>