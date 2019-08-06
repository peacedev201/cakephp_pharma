<?php
echo $this->addPhraseJs(array(
    'tmaxsize' => __d('feedback', 'Can not upload file more than ' . $file_max_upload),
    'tdesc' => __d('feedback', 'Drag or click here to upload photo'),
    'tdescfile' => __d('feedback', 'Click or Drap your file here')
));
echo $this->Html->css(array(
    'Feedback.feedback.css',
    'fineuploader'
));
echo $this->Html->script(array( 
    'Feedback.feedback',
	'jquery.fileuploader'), array('inline' => false));

$tags_value = '';
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>


<div class="create_form create_form_feedback">
    <div class="bar-content">
        <div class="content_center">
            <div class="box3">                
                <?php echo  $this->Form->create('Feedback', array('class' => 'form-horizontal', 'id' => 'createForm', 'role' => 'form')); ?>
                <?php echo  $this->Form->hidden('id'); ?>
                <div class="mo_breadcrumb">
                    <h1><?php echo  __d('feedback', 'Edit Feedback');?></h1>    
                </div>
                <div class="full_content p_m_10">
                    <div class="form_content">
                        <ul class="list6 list6sm2" style="position:relative">
                            <li>
                                <div class="col-md-2"><label><?php echo __d('feedback', 'Title')?></label></div>
                                <div class="col-md-10"><?php echo  $this->Form->text('title',array('placeholder'=>'Title')); ?></div>
                                <div class="clear"></div>
                            </li>
                            <li>
                                <div class="col-md-2"><label><?php echo __d('feedback', 'Description')?></label></div>
                                <div class="col-md-10">
                                    <?php echo $this->Form->tinyMCE( 'body', array( 'id' => 'editor' , 'placeholder'=>'Description') ); ?>
                                <div class="clear"></div>
                            </li>
                            <li>
                                <div class="col-md-2"><label><?php echo __d('feedback', 'Tags')?></label></div>
                                <div class="col-md-10"><?php echo  $this->Form->text('tags',array('value' => $tags_value, 'placeholder'=>'Tags')); ?> 
                                </div>
                                <div class="clear"></div>
                            </li>    
                            <?php if($aCategories): ?>
                            <li>
                                <div class="col-md-2"><label><?php echo __d('feedback', 'Category')?></label></div>
                                <div class="col-md-10"><?php echo  $this->Form->select('category_id', $aCategories); ?></div>
                                <div class="clear"></div>
                            </li>
                            <?php endif ?>
                            <?php if($aSeverities): ?>
                            <li>
                                <div class="col-md-2"><label><?php echo __d('feedback', 'Severity')?></label></div>
                                <div class="col-md-10"><?php echo  $this->Form->select('severity_id', $aSeverities);?></div>
                                <div class="clear"></div>
                            </li>
                            <?php endif ?>

                            <?php if( !empty($cuser) && $cuser['Role']['is_admin'] ): ?>
                            <li>
                                <div class="col-md-2"><label><?php echo __d('feedback', 'Feedback Visibility')?></label></div>
                                <div class="col-md-10">
                                <?php echo $this->Form->select( 'privacy', 
                                                        array( PRIVACY_EVERYONE => __d('feedback', 'Everyone'), 
                                                                   PRIVACY_FRIENDS  => __d('feedback', 'Friends Only'), 
                                                                   PRIVACY_ME       => __d('feedback', 'Only Me') ), 
                                                        array(  
                                                                'empty' => false
                                                 ) ); 
                                ?>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <?php endif ?>
                            <li>
                                <div class="col-md-2"><label><?php echo __d('feedback', 'Images')?></label></div>
                                <div class="col-md-10">
                                    <?php echo $this->Form->hidden('attachments');?>
                                    <div id="images-uploader" style="margin:10px 0;">
                                        <div id="uploader"></div>
                                        <a href="javascript:void(0)" class="button button-primary" id="triggerUpload">
                                            <?php echo __d('Addonsstore', 'Upload Queued Files') ?>
                                        </a>
                                    </div>
                                    <?php if (!empty($aFeedback['FeedbackImage'])): ?>
                                        <ul class="list6 list6sm" id="attachments_list" style="overflow: hidden;">
                                            <?php foreach ($aFeedback['FeedbackImage'] as $feedbackImage): ?>
                                                <li id="attach<?php echo $feedbackImage['id'] ?>">
                                                     <i class="material-icons">attach</i>
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
                            <li>
                                <div class="col-md-2"><label>&nbsp;</label></div>
                                <div class="col-md-10">
                                    <a href="javascript:void(0)" onclick="jQuery.feedback.createFeedback()" class="btn-action button button-action" id="createButton">
                                        <?php echo __d('feedback', 'Post Feedback')?>
                                    </a>
                                </div>
                                <div class="clear"></div>
                            </li>
                        </ul>
                    </div>
                </div>
                </form>
            <div class="error-message" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

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