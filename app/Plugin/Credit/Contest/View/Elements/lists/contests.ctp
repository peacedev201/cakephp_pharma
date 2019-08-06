<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
$no_id = isset($no_list_id) ? $no_list_id : false;
?>
<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooContest"], function ($, mooContest) {
            mooContest.initOnListing();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
    mooContest.initOnListing();
    <?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>

<?php if (count($contests)): ?>
    <?php if ($no_id): ?>
        <ul id="list-content" class="contest-content-list">
        <?php endif; ?>
        <li class="grid-sizer"></li>
        <?php foreach ($contests as $contest): ?>
            <li class="contest-item full_content p_m_10" id ="contest_<?php echo $contest['Contest']['id']; ?>">
                <div>
                    <a class="contest_list_image" href="<?php echo $contest['Contest']['moo_href']; ?>">
                        <img  src="<?php echo $helper->getImage($contest, array('prefix' => '450')) ?>">
                        <?php $status_text = $helper->getContestStatus($contest); if (!empty($status_text)): ?>
                            <span class="contest_list_featured"><?php echo $helper->getContestStatus($contest); ?></span>
                        <?php else: ?>
                            <?php if ($contest['Contest']['featured']): ?>
                                <span class="contest_list_featured"><i class="material-icons">star_border</i> <?php echo __d('contest', 'Featured') ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <span class="contest_type">
                            <?php if($contest['Contest']['type'] == 'photo'): ?>
                                <i class="contest_type_photo"></i> 
                            <?php endif; ?>
                            <?php if($contest['Contest']['type'] == 'music'): ?>
                                <i class="contest_type_music"></i> 
                            <?php endif; ?>
                            <?php if($contest['Contest']['type'] == 'video'): ?>
                                <i class="contest_type_video"></i> 
                            <?php endif; ?>
                        </span>                        
                    </a>
                    <ul class="contest_list_statistics">
                        <li>
                            <p><i class="material-icons">people</i> <?php echo __d('contest', 'Candidates'); ?></p>
                            <strong class="act_numcounter"><?php echo $contest['Contest']['contest_candidate_count']; ?></strong>
                        </li>
                        <li>
                            <p><i class="material-icons">folder</i> <?php echo __d('contest', 'Entries'); ?></p>
                            <strong class="act_numcounter"><?php echo $contest['Contest']['contest_entry_count']; ?></strong>
                        </li>
                    </ul>
                    <div class="contest_list_footer">
                        <a class="contest_list_small_title" href="<?php echo $contest['Contest']['moo_href']; ?>" title="<?php echo $contest['Contest']['moo_title']; ?>">
                            <?php echo $this->Text->truncate( $contest['Contest']['moo_title'], 65, array('eclipse' => '')) ; ?>		
                        </a>
                        <div class="extra_info">
                            <p class="contest-day-left">
                                <i class="material-icons">timer</i>
                                <?php echo $helper->getTimeLeft($contest['Contest']['to'] . ' ' . $contest['Contest']['to_time'], $contest['Contest']['timezone']); ?>
                            </p>
                            <p><?php echo __d('contest', 'by') ?>: <?php echo $this->Moo->getName($contest['User'], false) ?></p>
                        </div>
                        <?php
                        if ($helper->integrate_credit() && $contest['Contest']['credit'] > 0 
                                && $contest['Contest']['contest_status'] == 'published' 
                                && $contest['Contest']['win_percent'] > 0):
                        ?>
                        <div class="highlight_credit_small">
                            <?php echo $this->element('blocks/credit_highlight', array('contest' => $contest)); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
        <?php if (isset($is_view_more) && $is_view_more): ?>
            <?php $this->Html->viewMore($url_more) ?>
        <?php endif; ?>
        <?php if ($no_id): ?>
        </ul>
    <?php endif; ?>
<?php else: ?>
    <div class="clear text-center"><?php echo __d('contest', 'No results found'); ?></div>
<?php endif; ?>
