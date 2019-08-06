<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
$tab = !empty($tab) ? $tab : '';
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
mooContest.initViewEntry();
<?php $this->Html->scriptEnd(); ?> 
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="post_body album_view_detail">
            <div class="mo_breadcrumb contest_breadcrumb">
                <h1><a href="<?php echo $contest['Contest']['moo_href']; ?>"><?php echo htmlspecialchars($contest['Contest']['name']) ?></a></h1>

                <ul class="list7 header-list header-button-list">
                    <?php if ($viewer['User']['id']): ?>
                       <?php if ($helper->canVote($entry, $viewer)): ?>
                            <li class="btn-album">
                               <?php if ($helper->isVote($entry, $viewer)): ?>
                                    <div class="entry_vote">
                                        <a id="contest_unvote_<?php echo $entry['ContestEntry']['id']; ?>"  data-id="<?php echo $entry['ContestEntry']['id']; ?>" class="btn-action btn topButton button-mobi-top voteBtn contest_unvote" href="javascript:void(0);" data-href="<?php echo $this->request->base ?>/contests/ajax_un_vote/<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Un-vote'); ?></a>
                                        <a id="contest_vote_<?php echo $entry['ContestEntry']['id']; ?>" data-id="<?php echo $entry['ContestEntry']['id']; ?>" style="display:none;" class="btn-action btn topButton button-mobi-top voteBtn contest_vote" href="javascript:void(0);" data-href="<?php echo $this->request->base ?>/contests/ajax_vote/<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Vote'); ?></a>
                                    </div>
                                <?php else: ?>
                                    <div class="entry_vote">
                                        <a id="contest_vote_<?php echo $entry['ContestEntry']['id']; ?>" data-id="<?php echo $entry['ContestEntry']['id']; ?>" class="btn-action btn topButton button-mobi-top voteBtn contest_vote" href="javascript:void(0);" data-href="<?php echo $this->request->base ?>/contests/ajax_vote/<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Vote'); ?></a>
                                        <a id="contest_unvote_<?php echo $entry['ContestEntry']['id']; ?>" data-id="<?php echo $entry['ContestEntry']['id']; ?>" style="display:none;" class="btn-action btn topButton button-mobi-top voteBtn contest_unvote" href="javascript:void(0);" data-href="<?php echo $this->request->base ?>/contests/ajax_un_vote/<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Un-vote'); ?></a>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                        <li class="list_option">
                            <div class="dropdown">
                                <button id="dropdown-edit" data-target="#" data-toggle="dropdown"><!--dropdown-user-box-->
                                    <i class="material-icons">more_vert</i>
                                </button>
                                <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit">
                                    <?php if ($entry['User']['id'] == $viewer['User']['id']): ?>
                                        <li><a href="<?php echo $this->request->base ?>/contests/edit_entry/<?php echo $entry['ContestEntry']['id'] ?>" title="<?php echo __d('contest', 'Edit Entry') ?>"><?php echo __d('contest', 'Edit Entry') ?></a></li>
                                    <?php endif; ?>
                                    <?php if ($helper->canDeleteEntryDetail($entry, $viewer)): ?>
                                        <li><a href="javascript:void(0)" class="deleteEntry" data-id="<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Delete Entry') ?></a></li>
                                        <li class="seperate"></li>
                                    <?php endif; ?> 
                                    <?php if ($helper->canApproveEntryDetail($entry, $viewer)): ?>
                                        <li><a href="javascript:void(0)" class="approveEntry" data-id="<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Approve Entry') ?></a></li>
                                    <?php endif; ?> 
                                    <?php if ($helper->canSetwinEntryDetail($entry, $viewer)): ?>
                                        <li><a href="javascript:void(0)" class="winEntry" data-id="<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Set to win') ?></a></li>
                                        <li class="seperate"></li>
                                    <?php endif; ?>                                   
                                    <li><a href="<?php echo $this->request->base ?>/reports/ajax_create/contest_contest_entry/<?php echo $entry['ContestEntry']['id']; ?>" data-target="#portlet-config" data-toggle="modal" title="<?php echo __d('contest', 'Report Entry') ?>"><?php echo __d('contest', 'Report Entry') ?></a></li>
                                </ul>
                            </div>
                        </li>

                    <?php else: ?>
                        <li class="btn-album">
                            <a class="btn-action btn voteBtn" href="<?php echo $this->request->base ?>/users/member_login"><?php echo __d('contest', 'Vote'); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="contest_content entry_content" id="entry_content">
                <?php echo $this->element('ajax/entry_detail'); ?>
            </div>
        </div>
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <?php
        echo $this->element('likes', array('shareUrl' => $this->Html->url(array(
                'plugin' => false,
                'controller' => 'share',
                'action' => 'ajax_share',
                'Contest_Contest_Entry',
                'id' => $entry['ContestEntry']['id'],
                'type' => 'contest_entry_item_detail'
                    ), true), 'item' => $entry['ContestEntry'], 'type' => $entry['ContestEntry']['moo_type']));
        ?>
    </div>
</div>
<div class="bar-content full_content p_m_10 contest-comment">
    <?php echo $this->renderComment(); ?>
</div>