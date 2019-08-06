<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>
<?php if ($duration_text == __d('contest', 'End')): ?>
    <div class="coutdown_timer" id="countdown_<?php echo $type; ?>">
        <?php echo $duration_text; ?>
    </div>  
<?php else: ?>
    <?php
    $countdown_desc = '';
    switch ($type) {
        case 'duration':
            if ($duration_text == __d('contest', 'Coming')) {
                //$count_down_date = $helper->getTimeCountdown($contest['Contest']['from'] . ' ' . $contest['Contest']['from_time'], $contest['Contest']['timezone']);
                $count_down_date = $contest['Contest']['duration_start'];
                $countdown_desc = __d('contest', 'Contest will be started in');
            }
            if ($duration_text == __d('contest', 'On Going')) {
               // $count_down_date = $helper->getTimeCountdown($contest['Contest']['to'] . ' ' . $contest['Contest']['to_time'], $contest['Contest']['timezone']);
                $count_down_date = $contest['Contest']['duration_end'];
                $countdown_desc = __d('contest', 'Contest will be finished in');
            }
            break;
        case 'submit':
            if ($duration_text == __d('contest', 'Coming')) {
                // $count_down_date = $helper->getTimeCountdown($contest['Contest']['s_from'] . ' ' . $contest['Contest']['s_from_time'], $contest['Contest']['timezone']);
                $count_down_date = $contest['Contest']['submission_start'];
                $countdown_desc = __d('contest', 'Submission will be started in');
            }
            if ($duration_text == __d('contest', 'On Going')) {
                //$count_down_date = $helper->getTimeCountdown($contest['Contest']['s_to'] . ' ' . $contest['Contest']['s_to_time'], $contest['Contest']['timezone']);
                $count_down_date = $contest['Contest']['submission_end'];
                $countdown_desc = __d('contest', 'Submission will be finished in');
            }
            break;
        case 'vote':
            if ($duration_text == __d('contest', 'Coming')) {
                //$count_down_date = $helper->getTimeCountdown($contest['Contest']['v_from'] . ' ' . $contest['Contest']['v_from_time'], $contest['Contest']['timezone']);
                $count_down_date = $contest['Contest']['voting_start'];
                $countdown_desc = __d('contest', 'Voting will be started in');
            }
            if ($duration_text == __d('contest', 'On Going')) {
                //$count_down_date = $helper->getTimeCountdown($contest['Contest']['v_to'] . ' ' . $contest['Contest']['v_to_time'], $contest['Contest']['timezone']);
                $count_down_date = $contest['Contest']['voting_end'];
                $countdown_desc = __d('contest', 'Voting will be finished in');
            }
            break;
        default:
            break;
    }
    ?>
    <?php if ($this->request->is('ajax')): ?>
        <script>
            require(["jquery", "mooContest"], function ($, mooContest) {
                mooContest.initCountdownMobile('countdown_<?php echo $type; ?>', '<?php echo $count_down_date; ?>');
            });
        </script>
    <?php else: ?>
        <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
        mooContest.initCountdownMobile('countdown_<?php echo $type; ?>', '<?php echo $count_down_date; ?>');
        <?php $this->Html->scriptEnd(); ?> 
    <?php endif; ?>
    <div class="countdown_desc">
        <?php echo $countdown_desc; ?>
    </div>
    <div class="coutdown_timer" id="countdown_<?php echo $type; ?>">
    </div>  
<?php endif; ?>