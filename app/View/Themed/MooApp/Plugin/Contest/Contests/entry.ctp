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
            <div class="info_header">
                <?php if ($viewer['User']['id']): ?>
                        <div class="list_option">
                            <div class="dropdown">
                                <button id="entry_edit_<?php echo $entry['ContestEntry']['id']?>" type="button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon">
                                    <i class="material-icons">more_vert</i>
                                </button>
                                <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="entry_edit_<?php echo $entry['ContestEntry']['id']?>">
                                    <?php if ($entry['User']['id'] == $viewer['User']['id']): ?>
                                        <li class="mdl-menu__item"><a href="<?php echo $this->request->base ?>/contests/edit_entry/<?php echo $entry['ContestEntry']['id'] ?>?app_no_tab=1" title="<?php echo __d('contest', 'Edit Entry') ?>"><?php echo __d('contest', 'Edit Entry') ?></a></li>
                                    <?php endif; ?>
                                    <?php if ($helper->canDeleteEntryDetail($entry, $viewer)): ?>
                                        <li class="mdl-menu__item"><a href="javascript:void(0)" class="deleteEntry" data-id="<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Delete Entry') ?></a></li>
                                    <?php endif; ?> 
                                    <?php if ($helper->canApproveEntryDetail($entry, $viewer)): ?>
                                        <li class="mdl-menu__item"><a href="javascript:void(0)" class="approveEntry" data-id="<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Approve Entry') ?></a></li>
                                    <?php endif; ?> 
                                    <?php if ($helper->canSetwinEntryDetail($entry, $viewer)): ?>
                                        <li class="mdl-menu__item"><a href="javascript:void(0)" class="winEntry" data-id="<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Set to win') ?></a></li>
                                        
                                    <?php endif; ?>                                   
                                    <li class="mdl-menu__item"><a href="<?php echo $this->request->base ?>/reports/ajax_create/contest_contest_entry/<?php echo $entry['ContestEntry']['id']; ?>" title="<?php echo __d('contest', 'Report Entry') ?>"><?php echo __d('contest', 'Report Entry') ?></a></li>
                                    
                                    <?php if ($entry['Contest']['privacy'] != PRIVACY_ME): ?>
                                        <?php echo $this->element('share/menu',array('param' => 'Contest_Contest_Entry','action' => 'contest_entry_item_detail' ,'id'=>$entry['ContestEntry']['id'])); ?>
                                    <?php endif ?>
                                </ul>
                            </div>
                        </div>
                       <?php  endif; ?>
                    <h1><?php echo htmlspecialchars($contest['Contest']['name']) ?></h1>
                    <?php if ($helper->canVote($entry, $viewer)): ?>
                        <?php if ($helper->isVote($entry, $viewer)): ?>
                            <div class="entry_vote">
                                <a class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored  voteBtn" href="javascript:void(0);" data-href="<?php echo $this->request->base ?>/contests/un_vote/<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Un-vote'); ?></a>
                            </div>
                        <?php else: ?>
                            <div class="entry_vote">
                                <a class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored  voteBtn" href="javascript:void(0);" data-href="<?php echo $this->request->base ?>/contests/vote/<?php echo $entry['ContestEntry']['id']; ?>"><?php echo __d('contest', 'Vote'); ?></a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>    
            <div class="contest_content entry_content" id="entry_content">
                <?php echo $this->element('ajax/entry_detail'); ?>
            </div>
            <?php echo $this->renderLike();?>
            <div class="clear"></div>

        </div>
    </div>
</div>
<div class="blog-comment">
    <?php echo $this->renderComment(); ?>
</div>
<script>
function doRefesh()
{
	location.reload();
}
</script>