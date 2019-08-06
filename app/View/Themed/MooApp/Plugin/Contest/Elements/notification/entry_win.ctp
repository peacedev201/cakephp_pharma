<?php
    $mContest =  MooCore::getInstance()->getModel('Contest.Contest');
    $contest = $mContest->findById($notification['Notification']['params']);
?>
<?php echo __d('contest', 'congrats, you have won the'); ?> <?php echo $contest['Contest']['moo_title']; ?>