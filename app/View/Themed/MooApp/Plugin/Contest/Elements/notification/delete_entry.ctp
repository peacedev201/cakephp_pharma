<?php
    $mContest =  MooCore::getInstance()->getModel('Contest.Contest');
    $contest = $mContest->findById($notification['Notification']['params']);
?>
<?php echo __d('contest', 'delete your entry on contest') ?> <?php echo $contest['Contest']['moo_title']; ?>