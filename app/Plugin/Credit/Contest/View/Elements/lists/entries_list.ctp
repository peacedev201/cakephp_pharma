<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest'); ?>
<?php if (!empty($entries) && count($entries) > 0) : ?>
    <li class="grid-sizer-entry"></li>
    <?php foreach ($entries as $entry): ?>
        <li class="entry-item full_content p_m_10" id ="entry_<?php echo $entry['ContestEntry']['id']; ?>">
            <div>
                <a class="entry_image ajax-popup-link" href="<?php echo $entry['ContestEntry']['moo_href']; ?>">
                    <img src="<?php echo $helper->getEntryImage($entry, array('prefix' => '450')); ?>">
                    <?php if ($entry['ContestEntry']['entry_status'] != 'published'): ?>
                        <div class="entry_status">
                            <?php echo $helper->getEntryStatus($entry); ?>
                        </div>
                    <?php endif; ?>
                    <span class="contest_type">
                        <?php if($entry['ContestEntry']['source'] == 'photo'): ?>
                            <i class="contest_type_photo"></i> 
                        <?php elseif($entry['ContestEntry']['source'] == 'music'): ?>
                            <i class="contest_type_music"></i> 
                        <?php else: ?>
                            <i class="contest_type_video"></i> 
                        <?php endif; ?>
                    </span>
                </a>
                <?php if($contest['Contest']['contest_status'] != 'closed'): ?>
                    <?php if ($type == 'approved' || $type == 'pending'): ?>
                        <?php if ($helper->canManageEntries($contest, $viewer)): ?>
                            <input id="entry_checkbox_<?php echo $entry['ContestEntry']['id'] ?>" type="checkbox" data-id="<?php echo $entry['ContestEntry']['id'] ?>" value="1" <?php if(in_array($entry['ContestEntry']['entry_status'], array('draft', 'denied'))): ?>data-prevent="1" <?php endif; ?> class="photo_edit_checkbox entry_edit_checkbox" >
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($type == 'my_approved' || $type == 'my_pending'): ?>
                        <input type="checkbox" data-id="<?php echo $entry['ContestEntry']['id'] ?>" value="1" class="photo_edit_checkbox entry_edit_checkbox" >
                    <?php endif; ?>
                <?php endif; ?>
                <div class="entry_info_content"> 
                    <?php if ($entry['ContestEntry']['entry_status'] == 'published' || $entry['ContestEntry']['entry_status'] == 'win'): ?>
                        <div class="entry-footer">
                            <div class="entry_vote_count">
                                <p><i class="material-icons">check</i> <a id="" href="<?php echo $this->request->base ?>/contests/ajax_show_voted/<?php echo $entry['ContestEntry']['id']; ?>" data-target="#themeModal" data-toggle="modal" class="" title="<?php echo __d('contest', 'People Who Vote This'); ?>" data-dismiss="modal" data-backdrop="true" style=""><span id="vote_count_<?php echo $entry['ContestEntry']['id']; ?>"><?php echo $entry['ContestEntry']['contest_vote_count']; ?></span></a> <?php echo ($entry['ContestEntry']['contest_vote_count'] > 1) ? __d('contest', 'Votes') : __d('contest', 'Vote');?></p>
                            </div>
                            <div class="entry_view_count">
                                <p><i class="material-icons">remove_red_eye</i> <span ><?php echo $entry['ContestEntry']['view_count']; ?></span> <?php echo ($entry['ContestEntry']['view_count'] > 1) ? __d('contest', 'Views') : __d('contest', 'View');?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="entry_info">
                        <p class="entry_caption"><?php echo $this->Text->truncate( $entry['ContestEntry']['caption'], 50, array('eclipse' => '')) ; ?></p>
                        <?php echo __d('contest', 'By') ?> <?php echo $this->Moo->getName($entry['User'], false) ?>
                    </div>

                    <?php if ($viewer['User']['id']): ?>
                        <?php if ($helper->canVote($entry, $viewer)): ?>
                            <?php if ($helper->isVote($entry, $viewer)): ?>
                                <div class="entry_vote">
                                    <a id="contest_unvote_<?php echo $entry['ContestEntry']['id']; ?>"  data-id="<?php echo $entry['ContestEntry']['id']; ?>" class="voteBtn contest_unvote" href="javascript:void(0);" data-url="<?php echo $this->request->base ?>/contests/ajax_un_vote/<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Un-vote'); ?></a>
                                    <a id="contest_vote_<?php echo $entry['ContestEntry']['id']; ?>" data-id="<?php echo $entry['ContestEntry']['id']; ?>" style="display:none;" class="voteBtn contest_vote" href="javascript:void(0);" data-url="<?php echo $this->request->base ?>/contests/ajax_vote/<?php echo $entry['ContestEntry']['id']; ?>"><i class="material-icons">check</i> <?php echo __d('contest', 'Vote'); ?></a>
                                </div>
                            <?php else: ?>
                                <div class="entry_vote">
                                    <a id="contest_vote_<?php echo $entry['ContestEntry']['id']; ?>" data-id="<?php echo $entry['ContestEntry']['id']; ?>" class="voteBtn contest_vote" href="javascript:void(0);" data-url="<?php echo $this->request->base ?>/contests/ajax_vote/<?php echo $entry['ContestEntry']['id']; ?>"><i class="material-icons">check</i> <?php echo __d('contest', 'Vote'); ?></a>
                                    <a id="contest_unvote_<?php echo $entry['ContestEntry']['id']; ?>" data-id="<?php echo $entry['ContestEntry']['id']; ?>" style="display:none;" class="voteBtn contest_unvote" href="javascript:void(0);" data-url="<?php echo $this->request->base ?>/contests/ajax_un_vote/<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Un-vote'); ?></a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="entry_vote">
                            <a class="voteBtn" href="<?php echo $this->request->base ?>/users/member_login"><i class="material-icons">check</i> <?php echo __d('contest', 'Vote'); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </li>
    <?php endforeach; ?>  
    <?php if (isset($more_url) && $is_more_url): ?>
        <script> var searchParams = <?php echo (isset($params)) ? json_encode($params) : 'false'; ?>;</script>
        <?php $this->Html->viewMore($more_url, 'list-content') ?>
    <?php endif; ?>
<?php else: ?>
    <div class="clear text-center"><?php echo __d('contest', 'No results found'); ?></div>
<?php endif; ?>

<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooContest"], function ($, mooContest) {
            mooContest.initOnEntries();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
    mooContest.initOnEntries();
    <?php $this->Html->scriptEnd(); ?> 
<?php endif; 


