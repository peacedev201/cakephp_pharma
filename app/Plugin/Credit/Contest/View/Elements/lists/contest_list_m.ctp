<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<?php if (!empty($contests) && count($contests) > 0) : ?>
    <ul class="csmall_list">
        <?php foreach ($contests as $contest): ?>
            <li class="contest-item-small full_content p_m_10" id ="contest_<?php echo $contest['Contest']['id']; ?>">
                <a class="cs_left contest_list_image" href="<?php echo $contest['Contest']['moo_href']; ?>" style="background-image:url('<?php echo $helper->getImage($contest, array('prefix' => '450')) ?>');">
                    <?php if ($contest['Contest']['featured']): ?>
                        <span class="contest_list_featured"><i class="material-icons">star_border</i> <?php echo __d('contest', 'Featured') ?></span>
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
                </a>

                <div class="cs_right">
                    <p>
                        <a class="small_title" href="<?php echo $contest['Contest']['moo_href']; ?>" title="<?php echo $contest['Contest']['moo_title']; ?>">
                            <?php echo $this->Text->truncate($contest['Contest']['moo_title'], 75, array('eclipse' => '')); ?>    
                        </a>
                    </p><div class="extra_info">                        
                        <p class="contest-day-left">
                            <i class="material-icons">timer</i><?php echo $helper->getTimeLeft($contest['Contest']['to'] . ' ' . $contest['Contest']['to_time'], $contest['Contest']['timezone']); ?>
                        </p>
                        <p><?php echo __d('contest', 'by') ?>: <?php echo $this->Moo->getName($contest['User'], false) ?></p>
                    </div>
                    <?php
                    if ($helper->integrate_credit() && $contest['Contest']['credit'] > 0 && $contest['Contest']['contest_status'] == 'published' && $contest['Contest']['win_percent'] > 0):
                        ?>
                        <div class="highlight_credit_small">
                        <?php echo $this->element('blocks/credit_highlight', array('contest' => $contest)); ?>
                        </div>
                        <?php endif; ?>
                </div>
            </li>
    <?php endforeach; ?>  
    </ul>
    <?php endif; ?>


