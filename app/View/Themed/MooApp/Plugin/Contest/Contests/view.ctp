<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
$tab = !empty($tab) ? $tab : '';
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>
mooContest.initViewContest();
<?php $this->Html->scriptEnd(); ?>
<div class="profile-header">
    <div id="cover" class="contest_cover">
        <div class="boxGradient"></div>
        <img id="cover_img_display" width="100%" src="<?php echo $this->request->webroot ?>theme/<?php echo $this->theme ?>/img/s.png" style="background-image:url(<?php echo $helper->getImage($contest, array()) ?>)" />
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
            <?php if ($contest['Contest']['type'] == 'photo'): ?>
                <i class="contest_type_photo"></i> 
            <?php endif; ?>
            <?php if ($contest['Contest']['type'] == 'video'): ?>
                <i class="contest_type_video"></i> 
            <?php endif; ?>
            <?php if ($contest['Contest']['type'] == 'music'): ?>
                <i class="contest_type_music"></i> 
            <?php endif; ?>
        </span>
    </div>
    <div class="section-menu app_contest_options list_option">
        <div class="profile-action">
            <button id="contest_edit_<?php echo $contest['Contest']['id'] ?>" type="button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon">
                <i class="material-icons">more_vert</i>
            </button>
            <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="contest_edit_<?php echo $contest['Contest']['id'] ?>">
                <?php if ($viewer['User']['id']): ?>
                    <li class="mdl-menu__item">
                        <a data-target="#themeModal" data-toggle="modal" class="" href="<?php echo $this->request->base ?>/contests/invite/<?php echo $contest['Contest']['id'] ?>" title="<?php echo __d('contest', 'Invite') ?>">
                            <?php echo __d('contest', 'Invite') ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="mdl-menu__item">
                        <a class="" href="<?php echo $this->request->base ?>/users/member_login">
                            <?php echo __d('contest', 'Invite'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($helper->canPublish($contest, $viewer)): ?>
                    <li class="mdl-menu__item"><a id="publish-content-onapp" href="javascript:void(0);" data-href="<?php echo $this->request->base ?>/contests/publish_app/<?php echo $contest['Contest']['id'] ?>" title="<?php echo __d('contest', 'Publish Contest') ?>"><?php echo __d('contest', 'Publish Contest') ?></a></li>
                <?php endif; ?>
                <?php if ($helper->canEdit($contest, $viewer)): ?>
                    <li class="mdl-menu__item"><a href="<?php echo $this->request->base ?>/contests/create/<?php echo $contest['Contest']['id'] ?>?app_no_tab=1" title="<?php echo __d('contest', 'Edit Contest') ?>"><?php echo __d('contest', 'Edit Contest') ?></a></li>
                <?php endif; ?>
                <?php if ($helper->canDelete($contest, $viewer)): ?>
                    <?php if ($viewer['Role']['is_admin']): ?>
                        <li class="mdl-menu__item"><a href="javascript:void(0)" class="deleteContest" data-id="<?php echo $contest['Contest']['id']; ?>"><?php echo __d('contest', 'Delete Contest') ?></a></li>
                    <?php else: ?>
                        <?php if ($contest['Contest']['contest_status'] == 'published'): ?>
                            <li class="mdl-menu__item"><a href="javascript:void(0)" class="rdeleteContest" data-id="<?php echo $contest['Contest']['id']; ?>"><?php echo __d('contest', 'Delete Contest') ?></a></li>
                        <?php else: ?>
                            <li class="mdl-menu__item"><a href="javascript:void(0)" class="deleteContest" data-id="<?php echo $contest['Contest']['id']; ?>"><?php echo __d('contest', 'Delete Contest') ?></a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>                       	                        
            <li class="mdl-menu__item"><a href="<?php echo $this->request->base ?>/reports/ajax_create/contest_contest/<?php echo $contest['Contest']['id']; ?>" title="<?php echo __d('contest', 'Report Contest') ?>"><?php echo __d('contest', 'Report Contest') ?></a></li>
            
            <?php if ($contest['Contest']['privacy'] != PRIVACY_ME): ?>
                <?php echo $this->element('share/menu',array('param' => 'Contest_Contest','action' => 'contest_item_detail' ,'id'=>$contest['Contest']['id'])); ?>
            <?php endif ?>
            </ul>  
        </div>
    </div>
</div>
<div class="profile_plg_menu">
    <h1 style="padding: 0 5px 0 5px;"><?php echo htmlspecialchars($contest['Contest']['moo_title']); ?></h1>
    <ul class="list3 profile_info">
        <?php if ($contest['Contest']['approve_status'] == 'approved' && $contest['Contest']['contest_status'] == 'published'): ?>
            <?php if ($viewer['User']['id']): ?>
                <?php if ($viewer['User']['id'] != $contest['User']['id']): ?>
                    <?php if ($helper->checkCandidate($contest['Contest']['id'], $viewer['User']['id'])): ?>
                        <li>
                            <a class="jl_contest contestBtn mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" href="javascript:void(0);"  data-href="<?php echo $this->request->base ?>/contests/contest_leave/<?php echo $contest['Contest']['id']; ?>" data-id="<?php echo $contest['Contest']['id']; ?>">
                                <?php echo __d('contest', 'Leave'); ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a class="jl_contest contestBtn mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" href="javascript:void(0);" data-href="<?php echo $this->request->base ?>/contests/contest_join/<?php echo $contest['Contest']['id']; ?>">
                                <?php echo __d('contest', 'Join'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

            <?php else: ?>
                <li>
                    <a class="jl_contest contestBtn mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" href="<?php echo $this->request->base ?>/users/member_login">
                        <?php echo __d('contest', 'Join'); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <!-- options edit or submit entries -->
        <?php if ($viewer['User']['id']): ?>
            <?php if ($helper->canSubmitEntry($contest, $viewer)): ?>
                <?php $submit_allow = true ?>
                <?php if($isandroidApp): ?>
                <?php $submit_allow = true ?>
                <?php endif; ?>
                <?php if($isiosApp && $contest['Contest']['type'] == 'music'): ?>
                <?php $submit_allow = false ?>
                <?php endif; ?>
                <?php if($submit_allow == true): ?>
                <li>
                    <a class='mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored' href="<?php echo $this->request->base; ?>/contests/submit_entry/<?php echo $contest['Contest']['id'] ?>"><?php echo __d('contest', 'Submit Entry') ; ?></a>
                </li>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <div id="browse" class="menu">
        <ul class="list2 menu_top_list">
            <li class="contest_action <?php if (empty($this->request->named['tab'])): ?>current<?php endif; ?>">
                <a href="<?php echo $contest['Contest']['moo_href']; ?>?app_no_tab=1" id="entries" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_entries/<?php echo $contest['Contest']['id']; ?>" class="contest_link no-ajax">
                    <?php echo __d('contest', 'Entries'); ?>
                </a>
            </li>
            <?php if ($viewer['User']['id']> 0 && $viewer['User']['id'] !=  $contest['Contest']['user_id'] && $helper->checkCandidate($contest['Contest']['id'], $viewer['User']['id'])): ?>
                <li class="contest_action">
                    <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:my-entries?app_no_tab=1" id="my-entries" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/my_entries/<?php echo $contest['Contest']['id']; ?>" class="contest_link no-ajax">
                        <?php echo __d('contest', 'My Entries'); ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="contest_action">
                <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:detail?app_no_tab=1" id="detail" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_detail/<?php echo $contest['Contest']['id']; ?>" class="contest_link no-ajax">
                    <?php echo __d('contest', 'Detail'); ?>
                </a>
            </li>

            <li class="dropdown">
                <span id="contest_mobile_menu" class="mdl-button mdl-js-button mdl-js-ripple-effect"><?php echo __d('contest', 'More') ?></span>
                <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="contest_mobile_menu">

                    <li class="contest_action">
                        <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:candidate?app_no_tab=1" id="candidate" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_candidate/<?php echo $contest['Contest']['id']; ?>" class="contest_link no-ajax" >
                            <?php echo __d('contest', 'Candidates'); ?>
                        </a>
                    </li>
                    <li class="contest_action">
                        <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:award?app_no_tab=1" id="award" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_award/<?php echo $contest['Contest']['id']; ?>" class="contest_link no-ajax">
                            <?php echo __d('contest', 'Award'); ?>
                        </a>
                    </li>
                    <li class="contest_action">
                        <a  href="<?php echo $contest['Contest']['moo_href']; ?>/tab:policy?app_no_tab=1" id="policy" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/contest_policy/<?php echo $contest['Contest']['id']; ?>" class="contest_link no-ajax">
                            <?php echo __d('contest', 'Terms & Conditions'); ?>
                        </a>
                    </li>
                    <?php if ($contest['Contest']['contest_status'] == 'closed'): ?>
                        <li class="contest_action">
                            <a href="<?php echo $contest['Contest']['moo_href']; ?>/tab:winning_entries?app_no_tab=1" id="winning_entries" ref="contest_content" data-url="<?php echo $this->request->base ?>/contests/winning_entries/<?php echo $contest['Contest']['id']; ?>" class="contest_link no-ajax">
                                <?php echo __d('contest', 'Winning Entries'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>

        </ul>
    </div>
</div>
<!---  credit win info -->
<?php
if ($helper->integrate_credit() && $contest['Contest']['credit'] > 0 && $contest['Contest']['contest_status'] == 'published' && $contest['Contest']['win_percent'] > 0):
    ?>
    <div class="box_content highlight_credit">
        <?php echo $this->element('blocks/credit_highlight', array('contest' => $contest)); ?>
    </div>
<?php endif; ?>

<?php if ($contest['Contest']['approve_status'] == 'approved' && $contest['Contest']['contest_status'] == 'published') :
    ?>   
    <?php $text_duration = $helper->getContestDurationText($contest); ?>
    <?php if ($text_duration == __d('contest', 'Coming') || $text_duration == __d('contest', 'End')): ?>
        <div class="box2 box_app">
            <h3><?php echo __d('contest', 'Duration'); ?></h3>
            <div class="box_content ">
                <span class="count_down"><?php echo $this->element('blocks/countdown', array('contest' => $contest, 'type' => 'duration', 'duration_text' => $text_duration)); ?></span>
            </div>
        </div>
    <?php else: ?>
        <div class="box2 box_app">
            <ul class="nav nav-tabs contest_countdown_tab" role="tablist">
                <li role="presentation" class="active"><a href="#duration" aria-controls="duration" role="tab" data-toggle="tab"><?php echo __d('contest', 'Duration'); ?></a></li>
                <li role="presentation"><a href="#submission" aria-controls="submission" role="tab" data-toggle="tab"><?php echo __d('contest', 'Submission'); ?></a></li>
                <li role="presentation"><a href="#voting" aria-controls="voting" role="tab" data-toggle="tab"><?php echo __d('contest', 'Voting') ?></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="box_content tab-pane active" id="duration">
                    <span class="count_down">
                        <?php echo $this->element('blocks/countdown', array('contest' => $contest, 'type' => 'duration', 'duration_text' => $text_duration)); ?>
                    </span>
                </div>
                <div role="tabpanel" class="box_content tab-pane" id="submission">
                    <?php $text_submit = $helper->getContestSubmitText($contest); ?>
                    <span class="count_down"><?php echo $this->element('blocks/countdown', array('contest' => $contest, 'type' => 'submit', 'duration_text' => $text_submit)); ?></span>
                </div>
                <div role="tabpanel" class="box_content tab-pane" id="voting">
                    <?php $text_vote = $helper->getContestVoteText($contest); ?>               
                    <span class="count_down"><?php echo $this->element('blocks/countdown', array('contest' => $contest, 'type' => 'vote', 'duration_text' => $text_vote)); ?></span>
                </div>
            </div>
        </div>

    <?php endif; ?>

<?php endif; ?>
<div class="mini_content">
    <div class="bar-content full_content p_m_10">
        <div class="content_center">
            <div class="post_body">
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
                <?php echo $this->renderLike();?>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <?php echo $this->renderComment();?>
</div>
<script>
function doRefesh()
{
	location.reload();
}
</script>