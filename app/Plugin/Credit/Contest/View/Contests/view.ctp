<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
$tab = !empty($tab) ? $tab : '';
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
mooContest.initViewContest();
<?php $this->Html->scriptEnd(); ?>
<?php if ($helper->integrate_credit() 
        && $contest['Contest']['credit'] > 0
        && $contest['Contest']['contest_status'] == 'published'
        && $contest['Contest']['win_percent'] > 0): ?>
    <?php $this->setNotEmpty('north'); ?>
    <?php $this->start('north'); ?>
        <div class="box_content highlight_credit">
            <?php echo $this->element('blocks/credit_highlight', array('contest' => $contest)); ?>
        </div>
    <?php $this->end(); ?>
<?php endif; ?>
<?php $this->setNotEmpty('west'); ?>
<?php $this->start('west'); ?>

<?php echo $this->element('Contest.blocks/cover_menu'); ?>

<?php if ($contest['Contest']['approve_status'] == 'approved' && $contest['Contest']['contest_status'] == 'published') :
    ?>   
    <?php $text_duration = $helper->getContestDurationText($contest); ?>
    <?php if ($text_duration == __d('contest', 'Coming') || $text_duration == __d('contest', 'End')): ?>
        <div class="box2">
            <h3><?php echo __d('contest', 'Duration'); ?></h3>
            <div class="box_content ">
                <span class="count_down"><?php echo $this->element('blocks/countdown', array('contest' => $contest, 'type' => 'duration', 'duration_text' => $text_duration)); ?></span>
            </div>
        </div>
    <?php else: ?>

        <div class="box2">
            <h3><?php echo __d('contest', 'Duration'); ?></h3>
            <div class="box_content ">
                <span class="count_down">
                    <?php echo $this->element('blocks/countdown', array('contest' => $contest, 'type' => 'duration', 'duration_text' => $text_duration)); ?>
                </span>
            </div>
        </div>
        <div class="box2">
            <?php $text_submit = $helper->getContestSubmitText($contest); ?>
            <h3><?php echo __d('contest', 'Submission'); ?></h3>
            <div class="box_content ">
                <span class="count_down"><?php echo $this->element('blocks/countdown', array('contest' => $contest, 'type' => 'submit', 'duration_text' => $text_submit)); ?></span>
            </div>
        </div>
        <div class="box2">
            <?php $text_vote = $helper->getContestVoteText($contest); ?>
            <h3><?php echo __d('contest', 'Voting'); ?></h3>
            <div class="box_content ">
                <span class="count_down"><?php echo $this->element('blocks/countdown', array('contest' => $contest, 'type' => 'vote', 'duration_text' => $text_vote)); ?></span>
            </div>
        </div>

    <?php endif; ?>

<?php endif; ?>

<?php $this->end(); ?>
<div class="mini_content">

    <div class="bar-content full_content p_m_10">
        <div class="content_center">
            <div class="post_body album_view_detail">
                <div class="contest_menu_right">
                    <?php if ($contest['Contest']['approve_status'] == 'approved' && $contest['Contest']['contest_status'] == 'published'): ?>
                        <?php if ($viewer['User']['id']): ?>
                            <?php if ($viewer['User']['id'] != $contest['User']['id']): ?>
                                <?php if ($helper->checkCandidate($contest['Contest']['id'], $viewer['User']['id'])): ?>
                                    <a class="contestBtn btn-action btn" href="<?php echo $this->request->base ?>/contests/contest_leave/<?php echo $contest['Contest']['id']; ?>">
                                        <?php echo __d('contest', 'Leave'); ?>
                                    </a>
                                <?php else: ?>
                                    <a class="contestBtn btn-action btn" href="<?php echo $this->request->base ?>/contests/contest_join/<?php echo $contest['Contest']['id']; ?>">
                                        <?php echo __d('contest', 'Join'); ?>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php else: ?>
                            <a class="contestBtn btn-action btn" href="<?php echo $this->request->base ?>/users/member_login">
                                <?php echo __d('contest', 'Join'); ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <!----- options edit or submit entries -->
                    <?php if ($viewer['User']['id']): ?>
                        <?php if ($helper->canSubmitEntry($contest, $viewer)): ?>
                            <?php
                            $this->MooPopup->tag(array(
                                'href' => $this->Html->url(array("controller" => "contests",
                                    "action" => "submit_entry",
                                    "plugin" => 'contest',
                                    $contest['Contest']['id']
                                )),
                                'title' => __d('contest', 'Submit Entry'),
                                'innerHtml' => __d('contest', 'Submit Entry'),
                                'class' => 'btn-action btn',
                                'data-backdrop' => 'static'
                            ));
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="list_option">
                    <div class="dropdown">
                        <button id="dropdown-edit" data-target="#" data-toggle="dropdown"><!--dropdown-user-box-->
                            <i class="material-icons">more_vert</i>
                        </button>
                        <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit">
                             <?php if ($viewer['User']['id']): ?>
                                <li>
                                    <a data-target="#themeModal" data-toggle="modal" class="" href="<?php echo $this->request->base ?>/contests/invite/<?php echo $contest['Contest']['id'] ?>" title="<?php echo __d('contest', 'Invite') ?>">
                                        <?php echo __d('contest', 'Invite') ?>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a class="" href="<?php echo $this->request->base ?>/users/member_login">
                                        <?php echo __d('contest', 'Invite'); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($helper->canPublish($contest, $viewer)): ?>
                                <li><a href="<?php echo $this->request->base ?>/contests/publish/<?php echo $contest['Contest']['id'] ?>" title="<?php echo __d('contest', 'Edit Contest') ?>"><?php echo __d('contest', 'Publish Contest') ?></a></li>
                            <?php endif; ?>
                            <?php if ($helper->canEdit($contest, $viewer)): ?>
                                <li><a href="<?php echo $this->request->base ?>/contests/create/<?php echo $contest['Contest']['id'] ?>" title="<?php echo __d('contest', 'Edit Contest') ?>"><?php echo __d('contest', 'Edit Contest') ?></a></li>
                            <?php endif; ?>
                            <?php if ($helper->canDelete($contest, $viewer)): ?>
                                <?php if ($viewer['Role']['is_admin']): ?>
                                    <li><a href="javascript:void(0)" class="deleteContest" data-id="<?php echo $contest['Contest']['id']; ?>"><?php echo __d('contest', 'Delete Contest') ?></a></li>
                                <?php else: ?>
                                    <?php if ($contest['Contest']['contest_status'] == 'published'): ?>
                                        <li><a href="javascript:void(0)" class="rdeleteContest" data-id="<?php echo $contest['Contest']['id']; ?>"><?php echo __d('contest', 'Delete Contest') ?></a></li>
                                    <?php else: ?>
                                        <li><a href="javascript:void(0)" class="deleteContest" data-id="<?php echo $contest['Contest']['id']; ?>"><?php echo __d('contest', 'Delete Contest') ?></a></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <li class="seperate"></li>
                            <?php endif; ?>                       	                        
                            <li><a href="<?php echo $this->request->base ?>/reports/ajax_create/contest_contest/<?php echo $contest['Contest']['id']; ?>" data-target="#portlet-config" data-toggle="modal" title="<?php echo __d('contest', 'Report Contest') ?>"><?php echo __d('contest', 'Report Contest') ?></a></li>
                        </ul>
                    </div>
                </div>

                <div class="contest_content" id="contest_content">
                    <div class="tab" data-id="<?php echo $tab; ?>"></div>
                    <?php if (empty($tab)): ?>
                        <?php
                        if (!empty($this->request->named['tab']))
                            echo __d('contest', 'Loading...');
                        else
                        // echo $this->element('ajax/contest_detail');
                            echo $this->element('ajax/contest_entries');
                        ?>
                    <?php else: ?>
                        <?php echo __d('contest', 'Loading...') ?>
                    <?php endif; ?>
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
                    'Contest_Contest',
                    'id' => $contest['Contest']['id'],
                    'type' => 'contest_item_detail'
                        ), true), 'item' => $contest['Contest'], 'type' => $contest['Contest']['moo_type']));
            ?>
        </div>
    </div>
    <div class="bar-content full_content p_m_10 contest-comment">
        <?php echo $this->renderComment(); ?>
    </div>
</div>