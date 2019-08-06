<?php

echo $this->addPhraseJs(array(
    'tmaxsize' => __d('feedback', 'Can not upload file more than ' . $file_max_upload),
    'tdesc' => __d('feedback', 'Drag or click here to upload photo'),
    'tdescfile' => __d('feedback', 'Click or Drap your file here')
));
echo $this->Html->css(array('jquery.mp', 'Feedback.feedback'), null, array('inline' => false));
echo $this->Html->script(array('jquery.mp.min', 'Feedback.feedback'), array('inline' => false)); 
?>

<?php if ($is234): ?>
            <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery','mooFeedback','hideshare'), 'object' => array('$','mooFeedback','hideshare'))); ?>
                mooFeedback.initOnView();
                $(".sharethis").hideshare({media: '<?php echo FULL_BASE_URL . $this->request->webroot?>img/og-image.png', linkedin: false});
            <?php $this->Html->scriptEnd(); ?>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function(){
    registerImageOverlay();
    $(".sharethis").hideshare({media: '<?php echo FULL_BASE_URL . $this->request->webroot?>img/og-image.png', linkedin: false});
    });
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
    
<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>

<?php echo $this->Element('Feedback.nav_detail_feedback');?>

<?php $this->end(); ?>

<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="comment_message post_body">
            <div class="feedback_view_votes">
                <div class="feedback_votes_counts">
                    <?php if($aFeedback['Feedback']['approved']):?>
                        <p id="feedback_voting_2" class="feedback_<?php echo  $aFeedback['Feedback']['id']?>"><?php echo  $aFeedback['Feedback']['total_votes']?></p>
                        <span><?php echo $aFeedback['Feedback']['total_votes'] > 1 ? __d('feedback', 'votes') : __d('feedback', 'vote')?></span>
                    <?php else:?>
                        <p id="feedback_voting_2" class="feedback_<?php echo  $aFeedback['Feedback']['id']?> feedback_pending">
                            <?php echo __d('feedback', 'Pending publication');?>
                        </p>
                    <?php endif;?>
                </div>
            </div>
            <div class="feedback-detail-wrapper">
                <div class="list_option">
                    <?php if($permission_set_status): ?>
                        <a class="btn btn-action" href="<?php echo $this->request->base.$url_feedback.$url_ajax_add_status.'/'.$aFeedback['Feedback']['id']?>" data-target="#themeModal" data-toggle="modal">
                            <?php if($aFeedback['FeedbackStatus']['id']): ?>                            
                                <?php echo  $aFeedback['FeedbackStatus']['name'] ?>
                            <?php else: ?>
                                <?php echo __d('feedback', 'Add Status')?>
                            <?php endif ?>  
                        </a>
                    <?php endif; ?>
                    <?php if ($permission_edit_own_feedback || $permission_edit_all_feedbacks || $permission_delete_own_feedback || $permission_delete_all_feedbacks): ?>
                    <div class="dropdown">
                        <button data-toggle="dropdown" data-target="#" id="dropdown-edit"><!--dropdown-user-box-->
                            <i class="material-icons">edit</i>
                        </button>
                        <ul aria-labelledby="dropdown-edit" class="dropdown-menu feedback-dropdown" role="menu">
                            <?php if ($cUser != null && $cUser['Role']['is_admin']): ?>
                                <li>
                                    <?php if ($is234): ?>
                                        <a href="javascript:void(0)" data-id="<?php echo  $aFeedback['Feedback']['id'] ?>" data-status="<?php echo ($aFeedback['Feedback']['approved']) ? 0 : 1 ?>" data-confirm="<?php echo sprintf(__d('feedback', 'Are you sure you want to %s this feedback?'), ($aFeedback['Feedback']['approved'] ? __d('feedback', 'unapprove') : __d('feedback', 'approve')));?>" class="fb_approve">
                                    <?php else:  ?>
                                        <a href="javascript:void(0)" onclick="mooConfirm('<?php echo sprintf(__d('feedback', 'Are you sure you want to %s this feedback?'), ($aFeedback['Feedback']['approved'] ? __d('feedback', 'unapprove') : __d('feedback', 'approve')));?>', '<?php echo $this->request->base.$url_feedback.'/do_active/'.$aFeedback['Feedback']['id'].'/approved/'.($aFeedback['Feedback']['approved'] ? 0 : 1)?>')">
                                    <?php endif; ?>    
                                        <?php if($aFeedback['Feedback']['approved']):?>
                                            <?php echo __d('feedback', 'Unpublish Feedback')?>
                                        <?php else:?>
                                            <?php echo __d('feedback', 'Publish Feedback')?>
                                        <?php endif;?>
                                    </a>
                                </li>
                                <li>
                                    <?php if ($is234): ?>
                                        <a href="javascript:void(0)" data-id="<?php echo  $aFeedback['Feedback']['id'] ?>" data-status="<?php echo ($aFeedback['Feedback']['featured']) ? 0 : 1 ?>" data-confirm="<?php echo sprintf(__d('feedback', 'Are you sure you want to %s this feedback?'), ($aFeedback['Feedback']['featured'] ? __d('feedback', 'unfeature') : __d('feedback', 'feature')));?>" class="fb_feature">
                                    <?php else: ?>
                                        <a href="javascript:void(0)" onclick="mooConfirm('<?php echo sprintf(__d('feedback', 'Are you sure you want to %s this feedback?'), ($aFeedback['Feedback']['featured'] ? __d('feedback', 'unfeature') : __d('feedback', 'feature')));?>', '<?php echo $this->request->base.$url_feedback.'/do_active/'.$aFeedback['Feedback']['id'].'/featured/'.($aFeedback['Feedback']['featured'] ? 0 : 1)?>')">
                                    <?php endif; ?>        
                                        <?php if($aFeedback['Feedback']['featured']):?>
                                            <?php echo __d('feedback', 'Unfeature Feedback')?>
                                        <?php else:?>
                                            <?php echo __d('feedback', 'Feature Feedback')?>
                                        <?php endif;?>
                                    </a>
                                </li>
                            <?php endif;?>
                            <?php if(MooCore::getInstance()->getViewer(true) > 0):?>
                                <?php if ($permission_edit_own_feedback || $permission_edit_all_feedbacks): ?>
                                <li>
                                    <a data-toggle="modal" data-backdrop="static" data-target="#themeModal" href="<?php echo $this->request->base.$url_feedback?>/ajax_create/<?php echo $aFeedback['Feedback']['id']?>">
                                        
                                        <?php echo __d('feedback', 'Edit Feedback')?>
                                    </a>
                                </li>
                                <?php endif;?>
                                <?php if ($permission_delete_own_feedback || $permission_delete_all_feedbacks): ?>
                                <li>
                                    <?php if ($is234): ?>
                                        <a href="javascript:void(0)" data-id="<?php echo  $aFeedback['Feedback']['id'] ?>" data-confirm="<?php echo __d('feedback', 'Are you sure you want to delete this feedback?');?>" class="fb_delete">
                                    <?php else: ?>        
                                        <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('feedback', 'Are you sure you want to delete this feedback?');?>', '<?php echo $this->request->base.$url_feedback.'/delete/'.$aFeedback['Feedback']['id']?>')">
                                    <?php endif; ?>    
                                            <?php echo __d('feedback', 'Delete Feedback')?>
                                    </a>
                                </li>
                                <?php endif; ?>
                            <?php endif;?>
                            <li><a href="<?php echo $this->request->base?>/reports/ajax_create/feedback_feedback/<?php echo $aFeedback['Feedback']['id']?>" data-target="#themeModal" data-toggle="modal" class="" title="<?php echo __d('feedback', 'Report Feedback')?>"><?php echo __d('feedback', 'Report Feedback')?></a></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <h1><?php echo htmlspecialchars($aFeedback['Feedback']['title'])?></h1>
                <span class="date">
                    <?php if(!empty($aFeedback['FeedbackCategory']['name'])):?>
                        <?php echo  __d('feedback', 'Posted in ') ?>
                        <a href="<?php echo $this->request->base;?>/feedbacks/index/cat/<?php echo  $aFeedback['FeedbackCategory']['id']?>">
                            <b><?php echo $aFeedback['FeedbackCategory']['name'];?></b>
                        </a>
                    <?php else:?>
                        <?php echo  __d('feedback', 'Posted ') ?>
                        <?php echo $this->Moo->getTime($aFeedback['Feedback']['created'], Configure::read('core.date_format'), $utz)?>
                    <?php endif;?>
                    <?php
                        switch($aFeedback['Feedback']['privacy']){
                            case 1:
                                $icon_class = 'fa fa-globe';
                                $tooltip = __d('feedback', 'Shared with: Everyone');
                                break;
                            case 2:
                                $icon_class = 'fa fa-group';
                                $tooltip = __d('feedback', 'Shared with: Friends Only');
                                break;
                            case 3:
                                $icon_class = 'fa fa-user';
                                $tooltip = __d('feedback', 'Shared with: Only Me');
                                break;
                        }
                    ?>
                    <a style="color:#888;" class="tip" href="javascript:void(0);" original-title="<?php echo  $tooltip ?>"> 
                        <i class="<?php echo  $icon_class ?>"></i>
                    </a>
                </span>
                
                <?php if($aFeedback['Feedback']['approved']):?>
                    <div id="feedback_vote_2" class="feedback_vote_button">
                    <?php $bRemoveVote = false ?>
                    <?php foreach($aFeedback['FeedbackVote'] as $aFeedbackVote): ?>
                        <?php if($aFeedbackVote['user_id'] == $uid): ?>
                            <?php $bRemoveVote = true;break; ?>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php if($bRemoveVote): ?>
                        <a class="a_feedback_<?php echo  $aFeedback['Feedback']['id']?> fb_vote" ref="<?php echo  $aFeedback['Feedback']['id'] ?>" href="javascript:void(0);"><?php echo __d('feedback', 'Unvote')?></a>
                    <?php else: ?>
                        <a class="a_feedback_<?php echo  $aFeedback['Feedback']['id']?> fb_vote" ref="<?php echo  $aFeedback['Feedback']['id'] ?>" href="javascript:void(0);"><?php echo __d('feedback', 'Vote')?></a>
                    <?php endif ?>
                    </div>
                <?php endif ?>
            </div>
            <div class="clear"></div>

            <p class="feedback-description">
                <?php echo $this->Text->convert_clickable_links_for_hashtags(str_replace(array('<br>','&nbsp;'), array(' ',''), $aFeedback['Feedback']['body']), Configure::read('Feedback.feedback_hashtag_enabled'));?>
            </p>

            <?php if($aFeedback['FeedbackStatus']['id']): ?>
            <div class="feedback_status_box">
                <b><?php echo __d('feedback', 'Status: ')?></b>
                <a style='color:<?php echo  $aFeedback['FeedbackStatus']['color']?>' class="feedback_status" href="<?php echo $this->request->base;?>/feedbacks/index/sta/<?php echo  $aFeedback['FeedbackStatus']['id']?>">
					<?php echo htmlspecialchars($aFeedback['FeedbackStatus']['name']) ?>
                </a>
                <div>
					<?php if(!$aFeedback['Feedback']['status_body']): ?>
						<?php echo  htmlspecialchars($aFeedback['FeedbackStatus']['default_comment']) ?>
					<?php else: ?>
						<?php echo  htmlspecialchars($aFeedback['Feedback']['status_body']) ?>						
					<?php endif ?>
                </div>
            </div>
			<?php endif ?>

			<?php if ($aFeedback['FeedbackImage']):
                            $feedbackHelper = MooCore::getInstance()->getHelper('Feedback_Feedback');
                            ?>				
            <div class="feedback_attached_img">
                <div><span class="date"><?php echo __d('feedback', 'Attached Images')?></span></div>
				<?php foreach ($aFeedback['FeedbackImage'] as $aFeedbackImage): ?>
                 <a class="attached-image" href="<?php echo $feedbackHelper->getImage($aFeedbackImage, array())?>">
                    <img src="<?php echo $feedbackHelper->getImage($aFeedbackImage, array())?>" />
                </a>
				<?php endforeach; ?>
            </div>				
			<?php endif; ?>



        </div>
    </div>
</div>

<?php if($aFeedback['Feedback']['user_id'] > 0):?>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <?php echo $this->element('likes', array('shareUrl' => $this->Html->url(array(
                                'plugin' => false,
                                'controller' => 'share',
                                'action' => 'ajax_share',
                                'Feedback_Feedback',
                                'id' => $aFeedback['Feedback']['id'],
                                'type' => 'feedback_item_detail'
                            ), true), 'item' => $aFeedback['Feedback'], 'type' => $aFeedback['Feedback']['moo_type'])); ?>  
    </div>
</div>
<div class="bar-content full_content p_m_10 feedback-comment">
   	<?php echo $this->renderComment();?>
</div>
<?php endif; ?>