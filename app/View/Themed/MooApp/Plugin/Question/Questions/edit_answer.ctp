<?php 
	$extension = Configure::read('Question.question_filetype_allow');
	$this->addPhraseJs(array(
		'upload_button_text' => __d('question','Drag or click here to upload files'),
	));
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooQuestion'), 'object' => array('$', 'mooQuestion'))); ?>
	mooQuestion.editAnswerApp(<?php echo $answer['QuestionAnswer']['id']?>);
<?php $this->Html->scriptEnd(); ?>
<div class="bar-content full_content p_m_10 page_questions-view">
    <div class="content_center">
    	<div class="moo_app">
    		<div class="row form-reply">
	        	<div class="col-md-12">
	        		<div id="form_reply" class="question_form_reply create_form">
        				<input type="hidden" name="attachments" id="content_edit_answer">
        				<input type="hidden" name="photo_ids" id="edits_photo_ids">
	        			<div>
	        				<?php echo $this->Form->tinyMCE('content_edit_answer', array('id'=>'content_edit_answer','value'=>$answer['QuestionAnswer']['description'],'class'=>'content_question')); ?>
	        			</div>
	        			<div>
	        				<div data-extension="<?php echo $extension;?>" id="edit_attachments_upload"></div>
	        			</div>
	        			<div class="row submit-wrapper">
	        				<div class="col-md-2">
                                <button id="edit_answer_button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1"><?php echo __d('question','Post answer');?></button>
                            </div>
	        			</div>
	        		</div>
	        	</div>
	        </div>	
    	</div>
    </div>
</div>
<?php $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'),true);?>