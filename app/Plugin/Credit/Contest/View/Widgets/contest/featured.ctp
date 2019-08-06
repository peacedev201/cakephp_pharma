
<?php if (!empty($f_contests)): ?>
    <div class="box2">
        <?php if (isset($title_enable) && $title_enable): ?>       
            <h3><?php echo $title ?></h3>
        <?php endif; ?>
        <?php
        $helper = MooCore::getInstance()->getHelper('Contest_Contest');
        $mEntry = MooCore::getInstance()->getModel('Contest.ContestEntry');
        $mCandidate = MooCore::getInstance()->getModel('Contest.ContestCandidate');
        ?>
        <div id="contestCarousel" class="carousel slide" data-ride="carousel">
            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <?php foreach ($f_contests as $key => $contest): ?>
                    <div class="item <?php if ($key == 0): ?>active<?php endif; ?> cf_item"  data-url="<?php echo $contest['Contest']['moo_href']; ?>">
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
                        <img src="<?php echo $helper->getImage($contest, array()); ?>" alt="<?php echo $contest['Contest']['moo_title']; ?>">
                        <div class="cf_slider_des">
                            <a class="cf_title" href="<?php echo $contest['Contest']['moo_href']; ?>"><?php echo $this->Text->truncate($contest['Contest']['moo_title'], 125, array('eclipse' => '')); ?></a>
                            <div class="cf_slider_info">							
                                <p class="cf_extra_info">
                                    <?php if ($contest['Contest']['contest_status'] == 'closed'): ?>
                                        <?php echo __d('contest', 'Closed'); ?>
                                    <?php else: ?>
                                        <?php if ($contest['Contest']['duration_start'] > date('Y-m-d H:i:s')): ?>
                                            <?php echo __d('contest', 'Start Contest On: %s', '<strong class="contest_end_date">' . $helper->getTime($contest['Contest']['from'] . ' ' . $contest['Contest']['from_time'], 'M d, Y H:i', $utz, $contest['Contest']['timezone']) . '</strong>') ?>
                                        <?php else: ?>
                                            <?php echo __d('contest', 'End Contest On: %s', '<strong class="contest_end_date">' . $helper->getTime($contest['Contest']['to'] . ' ' . $contest['Contest']['to_time'], 'M d, Y H:i', $utz, $contest['Contest']['timezone']) . '</strong>') ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <ul class="cf_slider_date">
                                <li>
                                    <i class="material-icons">folder</i>
                                    <span><?php echo __d('contest', 'Entries'); ?></span>
                                    <b><?php echo $contest['Contest']['contest_entry_count']; ?></b>
                                </li>
                                <li>
                                    <i class="material-icons">people</i>
                                    <span><?php echo __d('contest', 'Candidates'); ?></span>
                                    <b><?php echo $contest['Contest']['contest_candidate_count']; ?></b>
                                </li>
                                <li class="cf_last">
                                    <i class="material-icons">timer</i>
                                    <span><?php echo __d('contest', 'Submit Entries'); ?></span>
                                    <b><?php echo $helper->getTimeLeft($contest['Contest']['s_to'] . ' ' . $contest['Contest']['s_to_time'], $contest['Contest']['timezone']); ?></b>
                                </li>
                                 <?php
                                if ($helper->integrate_credit() && $contest['Contest']['credit'] > 0 && $contest['Contest']['contest_status'] == 'published' && $contest['Contest']['win_percent'] > 0):
                                    ?>
                                    <li class="highlight_credit_small">
                                        <?php echo $this->element('blocks/credit_highlight', array('contest' => $contest)); ?>
                                    </li>
                                    <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if(count($f_contests) > 1): ?>
                <!-- Left and right controls -->
                <a class="left carousel-control" href="#contestCarousel" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only"><?php echo __d('contest', 'Previous') ?></span>
                </a>
                <a class="right carousel-control" href="#contestCarousel" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only"><?php echo __d('contest', 'Next') ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; 