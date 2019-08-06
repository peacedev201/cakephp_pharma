<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<h2><?php echo __d('contest','Contest Information') ?></h2>
<div class="contest_information">
    <div class="contest-detail-info">
        <?php echo __d('contest', 'Posted by %s', $this->Moo->getName($contest['User'])) ?> <?php echo __('in') ?> <a href="<?php echo $this->request->base ?>/contest/search/<?php echo $contest['Contest']['category_id'] ?>/<?php echo seoUrl($contest['Category']['name']) ?>"><?php echo $contest['Category']['name'] ?></a> <?php echo $this->Moo->getTime($contest['Contest']['created'], Configure::read('core.date_format'), $utz) ?>
        &nbsp;&middot;&nbsp;<?php if ($contest['Contest']['privacy'] == PRIVACY_PUBLIC): ?>
            <?php echo __('Public') ?>
        <?php elseif ($contest['Contest']['privacy'] == PRIVACY_PRIVATE): ?>
            <?php echo __('Private') ?>
        <?php elseif ($contest['Contest']['privacy'] == PRIVACY_FRIENDS): ?>
            <?php echo __('Friend') ?>
        <?php endif; ?>
    </div>
    <div class="contest_statistics">
        <div class="statistic_duration">
            <h5><?php echo __d('contest', 'Contest Duration') ?></h5>
            <div class="statistic_info">
                <p><?php echo __d('contest', 'Start Date: %s', '<strong class="contest_end_date">'. $helper->getTime($contest['Contest']['from']. ' ' . $contest['Contest']['from_time'], 'M d, Y H:i', $utz, $contest['Contest']['timezone']) .'</strong>') ?></p>
                <p><?php echo __d('contest', 'End Date: %s', '<strong class="contest_end_date">'. $helper->getTime($contest['Contest']['to']. ' ' . $contest['Contest']['to_time'], 'M d, Y H:i', $utz, $contest['Contest']['timezone']) .'</strong>') ?></p>
            </div>
        </div>
        <div class="statistic_duration">
            <h5><?php echo __d('contest', 'Submit Entries') ?></h5>
            <div class="statistic_info">
                <p><?php echo __d('contest', 'Start Date: %s', '<strong class="contest_end_date">'. $helper->getTime($contest['Contest']['s_from']. ' ' . $contest['Contest']['s_from_time'], 'M d, Y H:i', $utz, $contest['Contest']['timezone']) .'</strong>') ?></p>
                <p><?php echo __d('contest', 'End Date: %s', '<strong class="contest_end_date">'. $helper->getTime($contest['Contest']['s_to']. ' ' . $contest['Contest']['s_to_time'], 'M d, Y H:i', $utz, $contest['Contest']['timezone']) .'</strong>') ?></p>
            </div>
        </div>
        <div class="statistic_duration">
            <h5><?php echo __d('contest', 'Voting') ?></h5>
            <div class="statistic_info">
                <p><?php echo __d('contest', 'Start Date: %s', '<strong class="contest_end_date">'. $helper->getTime($contest['Contest']['v_from']. ' ' . $contest['Contest']['v_from_time'], 'M d, Y H:i', $utz, $contest['Contest']['timezone']) .'</strong>') ?></p>
                <p><?php echo __d('contest', 'End Date: %s', '<strong class="contest_end_date">'. $helper->getTime($contest['Contest']['v_to']. ' ' . $contest['Contest']['v_to_time'], 'M d, Y H:i', $utz, $contest['Contest']['timezone']) .'</strong>') ?></p>
            </div>
        </div>
    </div>
    <?php if($helper->integrate_credit()): ?>
    <p class="contest_field_label"><?php echo __d('contest', 'Submit entry fee') ?>: <strong><?php echo ($contest['Contest']['submit_entry_fee'] == 0) ? 'Free' : __d('contest', '%s Credit', floatval($contest['Contest']['submit_entry_fee'])); ?></strong></p>
    <?php endif; ?>
    <p class="contest_field_label"><?php echo __d('contest', 'Maximum entries a candidate can submit') ?>: <strong><?php echo ($contest['Contest']['maximum_entry'] == 0) ? __d('contest', 'Unlimited') : $contest['Contest']['maximum_entry']; ?></strong></p>
    <?php if (!empty($tags)): ?>
        <?php echo $this->element('blocks/tags_item_block'); ?>
    <?php endif; ?>
    <h2><?php echo __d('contest','Description') ?></h2>
    <div class="contest_information_block contest_desc">
        <?php echo $this->Text->convert_clickable_links_for_hashtags($contest['Contest']['description'], Configure::read('Contest.contest_hashtag_enabled')); ?>
    </div>
</div>