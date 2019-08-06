<?php

$helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<div class="entry_detail_voted">
    <div class="extra_info"><?php echo __d('contest', 'Posted by') ?>: <?php echo $this->Moo->getName($entry['User'], false) ?></div>
    <div class="entry-footer">
        <div class="entry_vote_count">
            <p><i class="material-icons">check</i> <a id="" href="<?php echo $this->request->base ?>/contests/ajax_show_voted/<?php echo $entry['ContestEntry']['id']; ?>" data-target="#themeModal" data-toggle="modal" class="" title="<?php echo __d('contest', 'People Who Vote This'); ?>" data-dismiss="modal" data-backdrop="true" style=""><span id="vote_count_<?php echo $entry['ContestEntry']['id']; ?>"><?php echo $entry['ContestEntry']['contest_vote_count']; ?></span></a> <?php echo ($entry['ContestEntry']['contest_vote_count'] > 1) ? __d('contest', 'Votes') : __d('contest', 'Vote');?></p>
        </div>
        <div class="entry_view_count">
            <p><i class="material-icons">remove_red_eye</i> <span >
            <?php if(!empty($viewer['User']['id']) && $viewer['User']['id'] != $entry['User']['id']): ?>
                <?php echo $entry['ContestEntry']['view_count']+1; ?>
            <?php else: ?>
                <?php echo $entry['ContestEntry']['view_count'] ; ?>
            <?php endif; ?></span> 
            <?php if(!empty($viewer['User']['id']) && $viewer['User']['id'] != $entry['User']['id']): ?>
                <?php echo ( $entry['ContestEntry']['view_count']+1 > 1) ? __d('contest', 'Views') : __d('contest', 'View');?>
            <?php else: ?>
                <?php echo ( $entry['ContestEntry']['view_count'] > 1) ? __d('contest', 'Views') : __d('contest', 'View');?>
            <?php endif; ?>
            </p>
        </div>
    </div>
    <?php if($entry['ContestEntry']['source'] != 'music'): ?>
        <div class="caption_full">
            <?php echo nl2br($entry['ContestEntry']['caption']); ?>
        </div>
    <?php endif;?>
    <div class="entry_detail_image">
        <?php ?>
        <div class="entry_image_large <?php if($entry['ContestEntry']['source'] != 'photo'): ?>video-detail<?php endif; ?>">
            <?php if($entry['ContestEntry']['source'] == 'photo'): ?>
                <img src="<?php echo $helper->getEntryImage($entry, array('prefix' => '1500')); ?>">
            <?php elseif($entry['ContestEntry']['source'] == 'music'): ?>
                <?php echo $this->element('blocks/music_player', array('entry' => $entry)); ?>
            <?php else: ?>
                <?php echo $this->element('blocks/video_snippet', array('entry' => $entry)); ?>
            <?php endif; ?>
            <?php if ($entry['ContestEntry']['entry_status'] != 'published'): ?>
                <div class="entry_status">
                    <?php echo $helper->getEntryStatus($entry); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($prev_entry) || !empty($next_entry)): ?>
    <div class="entry_pager_links_holder">
        <div class="entry_pager_links">
                <?php if (!empty($prev_entry)): ?>
            <a class="pager_previous_link" href="<?php echo $prev_entry['ContestEntry']['moo_href']; ?>?app_no_tab=1" title="<?php echo __d('contest', 'Previous Entry') ?>"><i class="material-icons">navigate_before</i> <?php echo __d('contest', 'Previous Entry'); ?></a>
                <?php else: ?>
            <a class="pager_previous_link pager_next_link_not" href="javascript:void(0);" title="<?php echo __d('contest', 'Previous Entry') ?>"><i class="material-icons">navigate_before</i> <?php echo __d('contest', 'Previous Entry'); ?></a>
                <?php endif; ?>
                <?php if (!empty($next_entry)): ?>
            <a class="pager_previous_link" href="<?php echo $next_entry['ContestEntry']['moo_href']; ?>?app_no_tab=1" title="<?php echo __d('contest', 'Next Entry') ?>"><?php echo __d('contest', 'Next Entry'); ?> <i class="material-icons">navigate_next</i></a>
                <?php else: ?>
            <a class="pager_previous_link pager_next_link_not" href="javascript:void(0);" title="<?php echo __d('contest', 'Next Entry') ?>"><?php echo __d('contest', 'Next Entry'); ?> <i class="material-icons">navigate_next</i></a>
                <?php endif; ?>
            <div class="clear"></div>
        </div>
    </div>
    <?php endif; ?>

</div>
