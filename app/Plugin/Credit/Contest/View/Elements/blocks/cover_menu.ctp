<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
?>
<div class="contest_left_detail">
    <div class="contest_image">
        <a href="<?php echo $contest['Contest']['moo_href']; ?>" class="contest_cover" style="background-image: url('<?php echo $helper->getImage($contest, array('prefix' => '300_square')) ?>')" >
            <?php
            $status = $helper->getContestStatus($contest);
            if (!empty($status)):
                ?>
                <span class="contest_featured"><?php echo $status; ?></span>
            <?php else: ?>
                <?php if ($contest['Contest']['featured']): ?>
                    <span class="contest_featured"><i class="material-icons">star_border</i><?php echo __d('contest', 'Featured') ?></span>
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

    </div>
    <div class="mo_breadcrumb contest_breadcrumb">
        <h1 class="info-home-name"><?php echo htmlspecialchars($contest['Contest']['name']) ?></h1>
    </div>
    <div class="menu block-body menu_top_list">
        <ul class="list2 contest_list_action">
            <li class="contest_action <?php if (empty($this->request->named['tab'])): ?>current<?php endif; ?>">
                <a href="<?php echo $contest['Contest']['moo_href']; ?>" id="entries" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_entries/<?php echo $contest['Contest']['id']; ?>" class="contest_link">
                    <i class="material-icons">folder</i> <?php echo __d('contest', 'Entries'); ?>
                </a>
            </li>
            <?php if ($viewer['User']['id']> 0 && $viewer['User']['id'] !=  $contest['Contest']['user_id'] && $helper->checkCandidate($contest['Contest']['id'], $viewer['User']['id'])): ?>
                <li class="contest_action">
                    <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:my-entries" id="my-entries" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/my_entries/<?php echo $contest['Contest']['id']; ?>" class="contest_link">
                        <i class="material-icons">folder</i> <?php echo __d('contest', 'My Entries'); ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="contest_action">
                <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:detail" id="detail" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_detail/<?php echo $contest['Contest']['id']; ?>" class="contest_link">
                    <i class="material-icons">info_outline</i> <?php echo __d('contest', 'Detail'); ?>
                </a>
            </li>
            <li class="contest_action">
                <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:award" id="award" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_award/<?php echo $contest['Contest']['id']; ?>" class="contest_link">
                    <i class="material-icons">highlight</i> <?php echo __d('contest', 'Award'); ?>
                </a>
            </li>
            <li class="contest_action">
                <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:policy" id="policy" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_policy/<?php echo $contest['Contest']['id']; ?>" class="contest_link">
                    <i class="material-icons">help_outline</i> <?php echo __d('contest', 'Terms & Conditions'); ?>
                </a>
            </li>
            <li class="contest_action">
                <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:candidate" id="candidate" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_candidate/<?php echo $contest['Contest']['id']; ?>" class="contest_link">
                    <i class="material-icons">people</i> <?php echo __d('contest', 'Candidates'); ?>
                </a>
            </li>
            <?php if ($contest['Contest']['contest_status'] == 'closed'): ?>
                <li class="contest_action">
                    <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:winning_entries" id="winning_entries" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/winning_entries/<?php echo $contest['Contest']['id']; ?>" class="contest_link">
                        <i class="material-icons">highlight</i> <?php echo __d('contest', 'Winning Entries'); ?>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="clear"></div>
</div>