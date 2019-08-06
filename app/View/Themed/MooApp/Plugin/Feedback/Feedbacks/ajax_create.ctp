<?php
echo $this->Html->css(array( 'fineuploader', 'Feedback.feedback'));
echo $this->Html->script(array( 
    'Feedback.feedback',
	'jquery.fileuploader',
    'https://www.google.com/recaptcha/api.js?hl=en'), array('inline' => false));

?>

<div class="create_form">
<div class="bar-content">
<div class="content_center">
    <div class="box3">

        <?php if($noPermission):?>
            <?php echo __d('feedback', 'You dont\'t have permission.') ?> 
        <?php else:?>

           <form action="<?php echo  $this->request->base; ?>/feedbacks/save" enctype="multipart/form-data" id="createForm" method="post">
                <input type="hidden" name="data[Feedback][id]" value="<?php echo $aFeedback['Feedback']['id'] ?>" id="FeedbackId">
            <div class="mo_breadcrumb">
                <h1><?php if (empty($aFeedback['Feedback']['id'])) echo __d('feedback', 'Create New Feedback'); else echo __d('feedback', 'Edit Feedback');?></h1>
            </div>
            <?php if(!$isLoggedin):?>
            <p class="create-desc">
                    <?php echo sprintf(__d('feedback', 'To be able to display your Feedback publicly, please %s first'), '<a href="'.$this->request->base.'/users/member_login">'.__d('feedback', 'login').'</a>');?>
            </p>
            <?php else:?>
            <p class="create-desc">
                    <?php echo __d('feedback', 'Share with us your ideas, questions, problems and feedback.');?>
            </p>
            <?php endif;?>
             <div class="full_content">
            <div class="form_content">
            
                <ul>
                    <li>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                             <input name="data[Feedback][title]" class="mdl-textfield__input" type="text" value="<?php echo $aFeedback['Feedback']['title'] ?>" />
                            <label class="mdl-textfield__label" ><?php echo __d('feedback', 'Title') ?></label>
                        </div>
                    </li>
                    <li>
                        <label><?php echo __d('feedback', 'Description') ?></label>
                        <?php echo $this->Form->tinyMCE( 'Feedback.body', array('value' => $aFeedback['Feedback']['body'], 'id' => 'editor', 'label' =>false) ); ?>
                    </li>
                <li>
                      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                             <input name="data[Feedback][tags]" class="mdl-textfield__input" type="text" value="<?php echo !empty($tags) ? implode(',', $tags) : '' ?>" />
                            <label class="mdl-textfield__label" ><?php echo __d('feedback', 'Tags') ?></label>
                        </div>
                </li>    
                <?php if ($aCategories): ?>
                <li>
                    <?php echo $this->Form->select('Feedback.category_id',$aCategories,array('empty' => false,'value'=>$aFeedback['Feedback']['category_id'])); ?>
               </li>
                <?php endif ?>
                <?php if ($aSeverities): ?>
                <li>
                    <?php echo $this->Form->select('Feedback.severity_id',$aSeverities,array('empty' => false,'value'=>$aFeedback['Feedback']['severity_id'])); ?>
                </li>
                <?php endif ?>
                <?php if (!empty($cuser)): ?>
                <li>
                            <?php
                            echo $this->Form->select('Feedback.privacy', array(PRIVACY_EVERYONE => __d('feedback', 'Everyone'),
                                PRIVACY_FRIENDS => __d('feedback', 'Friends Only'),
                                PRIVACY_ME => __d('feedback', 'Only Me')), array('value' => $aFeedback['Feedback']['privacy'],
                                'empty' => false
                            ));
                            ?>
                </li>
                <?php else:?>
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Email') ?><span class="required">*</span></label></div>
                    <div class="col-md-12"><?php echo $this->Form->text('Feedback.email', array('placeholder' => 'Email')); ?></div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Full name') ?><span class="required">*</span></label></div>
                    <div class="col-md-12"><?php echo $this->Form->text('Feedback.fullname', array('placeholder' => 'Full name')); ?></div>
                    <div class="clear"></div>
                </li>
                <?php endif ?>
                <?php if($permission_can_upload_photo):?>
                <li>
                    <div class="col-md-12"><label><?php echo __d('feedback', 'Images')?></label></div>
                    <div class="col-md-12">
                        <input type="hidden" name="data[Feedback][attachments]" id="FeedbackAttachments">
                        <div id="images-uploader" style="margin:0 0px 10px;">
                            <div id="uploader"></div>
                            <a href="javascript:void(0)" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" id="triggerUpload">
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
                     <div id="images-uploader1" style="display:none;margin:10px 0;">
                            <div id="attachments_upload"></div>
                            <a href="javascript:void(0)" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" id="triggerUpload1"><?php echo __( 'Upload Queued Files')?></a>
                        </div>
                        <a id="toggleUploader" href="javascript:void(0)"><?php echo __( 'Toggle Attachments Uploader')?></a>
                </li>
                <?php if(Configure::read('Feedback.feedback_enable_captcha')): ?>
                <li>
                    <div class="col-md-12" id="captcha">
                        <div class="captcha_box">
                            <script src='https://www.google.com/recaptcha/api.js?hl=en'></script>
                            <div class="g-recaptcha" data-sitekey="<?php echo $this->Moo->getRecaptchaPublickey();?>"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </li>
                <?php endif ?>
                <li>
                    <div class="col-md-12">
                        <a href="javascript:void(0)" onclick="jQuery.feedback.createFeedback()" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="createButton">
                            <?php if($aFeedback['Feedback']['id'] > 0):?> 
                                <?php echo __d('feedback', 'Save Feedback') ?>
                            <?php else:?>
                                <?php echo __d('feedback', 'Post Feedback') ?>
                            <?php endif;?>
                        </a>
                         <?php if($aFeedback['Feedback']['id'] > 0):?> 
                        <?php echo __d('feedback', 'or');?>
                        <a href="<?php echo  $this->request->base ?>/feedbacks/view/<?php echo  $aFeedback['Feedback']['id'] ?>/<?php echo seoUrl($aFeedback['Feedback']['title'])?>?app_no_tab=1" class="button cancel-post-feedback">
                            <?php echo __d('feedback', 'cancel');?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>
                
            </div>
            </div>    
            </form>
            <div class="error-message" style="display:none;"></div>         

        <div class="clear"></div>
        <?php endif;?>
        
    </div>    
</div>
</div>
</div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooFeedback'), 'object' => array('$', 'mooFeedback'))); ?>
mooFeedback.initOnCreateInApp();
<?php $this->Html->scriptEnd(); ?>