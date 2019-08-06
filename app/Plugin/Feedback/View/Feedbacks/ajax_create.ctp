<?php

echo $this->Html->css(array( 'fineuploader', 'Feedback.feedback'));
echo $this->Html->script(array( 
    'Feedback.feedback',
	'jquery.fileuploader',
    'https://www.google.com/recaptcha/api.js?hl=en'), array('inline' => false));

?>

<?php if ($aFeedbacks): ?>
<style type="text/css" media="screen">
    .modal-dialog{
        width: 1000px;
    }
    .create .create_form{
        float: left;
        width: 450px;
    }
    @media (max-width:991px) {
        .modal-dialog {
            width: auto !important;
        }
        .create .create_form {
            float: none !important;
            width: 100% !important;
        }
    }
</style>
<?php else: ?>
<style type="text/css" media="screen">
@media (max-width:991px) {
    .modal-dialog {
        width: auto !important;
    }
    .create .create_form {
        float: none !important;
        width: 100% !important;
    }
}
</style>
<?php endif;?>

<div class="create">
    <div class="title-modal">
        <?php echo !empty($aFeedback['Feedback']['id'])?__d('feedback', 'Edit Feedback'):__d('feedback', 'Create New Feedback') ?> 
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <?php if($noPermission):?>
            <?php echo __d('feedback', 'You dont\'t have permission.') ?> 
        <?php else:?>
        <div class="create_form create_form_feedback">
            <?php echo $this->Form->create('Feedback', array('class' => 'form-horizontal', 'id' => 'createForm', 'role' => 'form')); ?>
            <?php echo $this->Form->hidden('id'); ?>
            <?php echo $this->Form->hidden('plugin_feedback_id', array('value' => $plugin_feedback_id)); ?>
            <?php if(!$isLoggedin):?>
            <p class="create-desc">
                    <?php echo sprintf(__d('feedback', 'To be able to display your Feedback publicly, please %s first'), '<a href="'.$this->request->base.'/users/member_login">'.__d('feedback', 'login').'</a>');?>
            </p>
            <?php else:?>
            <p class="create-desc">
                    <?php echo __d('feedback', 'Share with us your ideas, questions, problems and feedback.');?>
            </p>
            <?php endif;?>
            <ul class="list6 list6sm2" style="position:relative">
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Title') ?><span class="required">*</span></label></div>
                    <div class="col-md-12"><?php echo $this->Form->text('title', array('placeholder' => __d('feedback', 'Title'))); ?></div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Description') ?><span class="required">*</span></label></div>
                    <div class="col-md-12">
                        <?php echo $this->Form->tinyMCE( 'body', array( 'id' => 'editor', 'placeholder'=>'Description') ); ?>
                    <div class="clear"></div>
                </li>                  
                <?php if ($aCategories): ?>
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Category') ?></label></div>
                    <div class="col-md-12"><?php echo $this->Form->select('category_id', $aCategories); ?></div>
                    <div class="clear"></div>
                </li>
                <?php endif ?>
                <?php if ($aSeverities): ?>
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Severity') ?></label></div>
                    <div class="col-md-12"><?php echo $this->Form->select('severity_id', $aSeverities); ?></div>
                    <div class="clear"></div>
                </li>
                <?php endif ?>
                <?php if (!empty($cuser)): ?>
                <li>
                    <div class="col-md-12"><label style="width: 145px;"><?php echo __d('feedback', 'Feedback Visibility') ?></label></div>
                    <div class="col-md-12">
                            <?php
                            echo $this->Form->select('privacy', array(PRIVACY_EVERYONE => __d('feedback', 'Everyone'),
                                PRIVACY_FRIENDS => __d('feedback', 'Friends Only'),
                                PRIVACY_ME => __d('feedback', 'Only Me')), array('value' => $aFeedback['Feedback']['privacy'],
                                'empty' => false
                            ));
                            ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <?php else:?>
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Email') ?><span class="required">*</span></label></div>
                    <div class="col-md-12"><?php echo $this->Form->text('email', array('placeholder' => 'Email')); ?></div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Full name') ?><span class="required">*</span></label></div>
                    <div class="col-md-12"><?php echo $this->Form->text('fullname', array('placeholder' => 'Full name')); ?></div>
                    <div class="clear"></div>
                </li>
                <?php endif ?>
                <?php if($permission_can_upload_photo):?>
                <li style="margin-bottom: 5px;">
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Images')?></label></div>
                    <div class="col-md-12">
                        <?php echo $this->Form->hidden('attachments');?>
                        <div id="images-uploader" style="margin:0 0px 10px;">
                            <div id="uploader"></div>
                            <a href="javascript:void(0)" class="button button-primary" id="triggerUpload">
                                <?php echo __d('feedback', 'Upload Queued Files') ?>
                            </a>
                        </div>
                        <?php if (!empty($aFeedback['FeedbackImage'])): ?>
                        <ul class="list6 list6sm" id="attachments_list" style="overflow: hidden;">
                                <?php foreach ($aFeedback['FeedbackImage'] as $feedbackImage): ?>
                            <li id="attach<?php echo $feedbackImage['id'] ?>">
                                <i class="material-icons">attach_file</i>
                                        <?php echo $feedbackImage['name'] ?>
                                &nbsp;
                                <a href="javascript:void(0)" onclick="jQuery.feedback.removeFeedbackImage(<?php echo $feedbackImage['id'] ?>)" data-id="<?php echo $feedbackImage['id'] ?>" class="attach_remove" title="<?php echo __d('feedback', 'Delete') ?>">
                                    <i class="material-icons">delete</i>
                                </a>	            
                            </li>
                                <?php endforeach; ?>
                        </ul>
                        <div class="clear"></div>
                        <?php endif;?>
                    </div>
                    <div class="clear"></div>
                </li>
                <?php endif;?>
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Tags') ?></label></div>
                    <div class="col-md-12">
                        <?php echo $this->Form->text('tags', array(
                            'placeholder' => __d('feedback', 'Tags'),
                            'value' => !empty($tags) ? implode(',', $tags) : ''
                        )); ?> 
                    </div>
                    <div class="clear"></div>
                </li>  
                <li>
                     <div id="images-uploader1" style="display:none;margin:10px 0;">
                            <div id="attachments_upload"></div>
                            <a href="javascript:void(0)" class="button button-primary" id="triggerUpload1"><?php echo __( 'Upload Queued Files')?></a>
                        </div>
                        <?php if(empty($isMobile)): ?>
                            <a id="toggleUploader" href="javascript:void(0)"><?php echo __( 'Toggle Attachments Uploader')?></a>
                        <?php endif; ?>
                </li>
                <?php if(Configure::read('Feedback.feedback_enable_captcha') && $this->Moo->isRecaptchaEnabled()): ?>
                <li>
                    <div class="col-md-12" id="captcha">
                        <div class="captcha_box">
                            <script src='<?php echo $this->Moo->getRecaptchaJavascript();?>'></script>
                             <div class="g-recaptcha" data-sitekey="<?php echo $this->Moo->getRecaptchaPublickey()?>"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </li>
                <?php endif ?>              
                <li>
                    <div class="col-md-12">                      
                        <a href="javascript:void(0)" onclick="jQuery.feedback.createFeedback()" class="button button-action post-feedback btn-action" id="createButton">
                            <?php if($aFeedback['Feedback']['id'] > 0):?> 
                                <?php echo __d('feedback', 'Save Feedback') ?>
                            <?php else:?>
                                <?php echo __d('feedback', 'Post Feedback') ?>
                            <?php endif;?>
                        </a>
                        <?php echo __d('feedback', 'or');?>
                        <a href="javascript:void(0)" data-dismiss="modal" class="button cancel-post-feedback" id="feedbackDialogCancel">
                            <?php echo __d('feedback', 'cancel');?>
                        </a>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>
            </form>
            <div class="error-message" style="display:none;"></div>         
        </div>
        <?php if ($aFeedbacks): ?>
        <div class="col-md-sl5 pull-right hidden-xs hidden-sm" style="display: block;">
            <ul class="list6 comment_wrapper list-feedback" id="list-content">
                    <?php foreach ($aFeedbacks as $key => $Feedback): ?>
                <li>
                    <div class="feedbacks_list_vote_button">
                        <div class="feedback_votes_counts">
                            <p id="feedback_voting_2" class="feedback_<?php echo $Feedback['Feedback']['id'] ?>"><?php echo $Feedback['Feedback']['total_votes'] ?></p>
                            <span><?php echo $Feedback['Feedback']['total_votes'] > 1 ? __d('feedback', 'votes') : __d('feedback', 'vote')?></span>
                                    <?php if($Feedback['Feedback']['featured']): ?>
                                        <i class="feedback_featured"><?php echo __d('feedback','Featured') ?></i>
                                    <?php endif ?>
                        </div>
                        <div id="feedback_vote_2" class="feedback_vote_button">
                                    <?php $bRemoveVote = false ?>
                                    <?php foreach ($Feedback['FeedbackVote'] as $aFeedbackVote): ?>
                                        <?php if ($aFeedbackVote['user_id'] == $uid): ?>
                                            <?php $bRemoveVote = true;
                                            break; ?>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                    <?php if ($bRemoveVote): ?>
                            <a class="a_feedback_<?php echo $Feedback['Feedback']['id'] ?>" href="javascript:void(0);" onclick="jQuery.feedback.vote(<?php echo $Feedback['Feedback']['id'] ?>,$(this));"><?php echo __d('feedback', 'Unvote') ?></a>
                                    <?php else: ?>
                            <a class="a_feedback_<?php echo $Feedback['Feedback']['id'] ?>" href="javascript:void(0);" onclick="jQuery.feedback.vote(<?php echo $Feedback['Feedback']['id'] ?>,$(this));"><?php echo __d('feedback', 'Vote') ?></a>
                                    <?php endif ?>
                        </div>
                    </div>
                    <div class="comment create-feedback-list">
                        <a href="<?php echo $this->request->base . $url_feedback ?>/view/<?php echo $Feedback['Feedback']['id'] ?>/<?php echo seoUrl($Feedback['Feedback']['title']) ?>">
                            <b><?php echo h($Feedback['Feedback']['title']) ?></b>
                        </a>

                        <div class="date">
                            <?php echo  __d('feedback', 'Posted by ').(!empty($Feedback['User']['name']) ? $this->Moo->getName($Feedback['User']) : '<b>'.$Feedback['Feedback']['fullname'].'</b>') ?>
                            <?php echo  __d('feedback', 'about ').$this->Moo->getTime( $Feedback['Feedback']['created'], Configure::read('core.date_format'), $utz )?>
                            <?php
                                switch($Feedback['Feedback']['privacy']){
                                    case 1:
                                        $icon_class = 'fa fa-globe';
                                        $tooltip = 'Shared with: Everyone';
                                        break;
                                    case 2:
                                        $icon_class = 'fa fa-group';
                                        $tooltip = 'Shared with: Friends Only';
                                        break;
                                    case 3:
                                        $icon_class = 'fa fa-user';
                                        $tooltip = 'Shared with: Only Me';
                                        break;
                                }
                            ?>
                            <a style="color:#888;" class="tip" href="javascript:void(0);" original-title="<?php echo  __d('feedback', $tooltip) ?>"> 
                                <i class="<?php echo  $icon_class ?>"></i>
                            </a>
                        </div>
                        <div>
                                    <?php if ($Feedback['FeedbackCategory']['id']): ?> 
                                        <?php echo __d('feedback', 'Category: ') ?>
                            <a href="<?php echo $this->request->base;?>/feedbacks/index/cat/<?php echo  $Feedback['FeedbackCategory']['id']?>"><?php echo $Feedback['FeedbackCategory']['name'] ?></a>
                                    <?php endif ?>
                        </div>
                        <div class="comment_message">
                                    <?php if ($Feedback['FeedbackStatus']['id']): ?>
                            <div class="status comment_message">
                                <b><?php echo __d('feedback', 'Status: ') ?></b>
                                <a style='color:<?php echo $Feedback['FeedbackStatus']['color'] ?>' class="feedback_status" href="<?php echo $this->request->base;?>/feedbacks/index/sta/<?php echo  $Feedback['FeedbackStatus']['id']?>">
                                                <?php echo $Feedback['FeedbackStatus']['name'] ?>
                                </a>
                            </div>
                                    <?php endif ?>
                        </div>
                        <div class="like-section pull-left">
                            <div class="like-action">
                                <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $Feedback['Feedback']['id'] ?>/<?php echo seoUrl($Feedback['Feedback']['title'])?>">
                                    <i class="material-icons">comment</i>&nbsp;<span id="comment_count"><?php echo $Feedback['Feedback']['comment_count']?></span>
                                </a>

                                <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $Feedback['Feedback']['id'] ?>/<?php echo seoUrl($Feedback['Feedback']['title'])?>" class="<?php if (!empty($uid) && !empty($like['Like']['thumb_up'])): ?>active<?php endif; ?>">
                                    <i class="material-icons">thumb_up</i>&nbsp;<span id="comment_count"><?php echo $Feedback['Feedback']['like_count']?></span>
                                </a>
                                <?php if(empty($hide_dislike)): ?>
                                <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $Feedback['Feedback']['id'] ?>/<?php echo seoUrl($Feedback['Feedback']['title'])?>" class="<?php if (!empty($uid) && isset($like['Like']['thumb_up']) && $like['Like']['thumb_up'] == 0): ?>active<?php endif; ?>">
                                    <i class="material-icons">thumb_down</i>&nbsp;<span id="comment_count"><?php echo $Feedback['Feedback']['dislike_count']?></span>
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $Feedback['Feedback']['id'] ?>/<?php echo seoUrl($Feedback['Feedback']['title'])?>">
                                    <i class="material-icons">visibility</i>&nbsp;<span id="view_count"><?php echo $Feedback['Feedback']['views']?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                    <?php endforeach ?>
                <li>
                    <a href="<?php echo $this->request->base . $url_feedback ?>" class="button button-action" id="createButton"><?php echo __d('feedback', 'Go to Feedback Forum') ?> </a>
                </li>
            </ul>
        </div>
        <?php endif ?>
        <div class="clear"></div>
        <?php endif;?>
    </div>
</div>

<?php if(!$noPermission):?>
        <?php if ($is234): ?>
            <script>
                require(["jquery","mooFeedback"], function($,mooFeedback) {$(document).ready(function(){mooFeedback.initOnCreate()});});      
            </script>
        <?php else: ?>

            <?php if($this->request->is('ajax')): ?>
        <script>
        <?php else: ?>
        <?php $this->Html->scriptStart(array('inline' => false)); ?>
        <?php endif; ?>

            jQuery.feedback.initFeedbackUploader();
            jQuery.feedback.initFeedbackImage();

        <?php if($this->request->is('ajax')): ?>
        </script>
            <?php else: ?>
            <?php $this->Html->scriptEnd(); ?>
            <?php endif; ?>

    <?php endif; ?>
<?php endif; ?>