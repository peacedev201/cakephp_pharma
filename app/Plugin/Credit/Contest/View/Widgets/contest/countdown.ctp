<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>
<?php if($contest['Contest']['approve_status'] == 'approved' 
        && ($contest['Contest']['contest_status'] == 'published' || $contest['Contest']['contest_status'] == 'closed')) : ?>
<div class="box2">        
    <?php if (isset($title_enable) && $title_enable): ?>       
        <h3><?php echo $title ?></h3>
    <?php endif; ?>
    <div class="box_content">
        <div class="contest_duration_content">
            <div class="contest_duration">
                <?php $text_duration = $helper->getContestDurationText($contest); ?>
                <h3><?php echo __d('contest', 'Contest Duration'); ?></h3>
                <span class="count_down"><?php echo $this->element( 'blocks/countdown', array('contest' => $contest, 'type' => 'duration', 'duration_text' => $text_duration)); ?></span>
                <span class="contest_day_lefts">
                    <?php echo $text_duration; ?>		
                </span>
            </div>
            <div class="contest_duration c_submitentries">
                <?php $text_submit = $helper->getContestSubmitText($contest); ?>
                <h3><?php echo __d('contest', 'Submit Entries'); ?></h3>
                <span class="count_down"><?php echo $this->element( 'blocks/countdown', array('contest' => $contest, 'type' => 'submit', 'duration_text' => $text_submit)); ?></span>
                <span class="contest_day_lefts">
                    <?php echo $text_submit; ?>		
                </span>
            </div>
            <div class="contest_duration c_voteentries">
                <?php $text_vote = $helper->getContestVoteText($contest); ?>
                <h3><?php echo __d('contest', 'Voting'); ?></h3>
                <span class="count_down"><?php echo $this->element( 'blocks/countdown', array('contest' => $contest, 'type' => 'vote', 'duration_text' => $text_vote)); ?></span>
                <span class="contest_day_lefts">
                    <?php echo $text_vote; ?>		
                </span>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>