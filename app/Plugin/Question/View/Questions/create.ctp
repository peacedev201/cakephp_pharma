<?php	
	echo $this->Html->css(array('Question.jquery.tokenize'));
	$helper = MooCore::getInstance()->getHelper('Question_Question');
	$this->addPhraseJs(array(
		'upload_button_text' => __d('question','Drag or click here to upload files'),
		'upload_button_text_photo' => __d('question','Drag or click here to upload photo'),
	));
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooQuestion','mooJqueryTokenize'), 'object' => array('$', 'mooQuestion'))); ?>	
	mooQuestion.initCreateQuestion('<?php echo (!empty($question['Question']['id'])) ? $question['Question']['moo_href'] : ''?>');
<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
	<div class="bar-content">
		<div class="content_center">
			<div class="box3">
				<form action="<?php echo  $this->request->base; ?>/questions/question/save" enctype="multipart/form-data" id="createForm" method="post">
					<div class="mo_breadcrumb">
			           <h1><?php if (!$is_edit) echo __d('question','Create a new question'); else echo __d('question','Edit question');?></h1>
			        </div>
			        <input type="hidden" name="privacy" value="1">
			        <div class="full_content p_m_10">
			        	<?php
						if (!empty($question['Question']['id']))
							echo $this->Form->hidden('id', array('value' => $question['Question']['id']));
						
						echo $this->Form->hidden('thumbnail', array('value' => $question['Question']['thumbnail']));
						echo $this->Form->hidden('photo_ids');
						?>
						<?php echo $this->Form->hidden('attachments',array('value'=>(isset($question['Question']['attachments']) && count($question['Question']['attachments'])) ? implode(',', $question['Question']['attachments']) : ''));?>
	            		<div class="form_content">
	            			<ul>	            					            				
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('question','Title')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->text('title',array('value'=>$question['Question']['title'])); ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('question','Description')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->tinyMCE('description', array('id'=>'description','value'=>$question['Question']['description'])); ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
			                    <li>
			                        	<div class="col-md-2">
			                        	</div>
			                        	<div class="col-md-10">
	                            			<div data-extension="<?php echo $extension;?>" id="attachments_upload"></div>
	                            		</div>
	                            		<div class="clear"></div>
			                    </li>   
		                        <!-- <li>
		                            <div class="col-md-2">
		                                <label><?php //echo __d('question','Category')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php //echo $this->Form->select('category_id',$categories,array('value'=>$question['Question']['category_id'])); ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>	 -->
		                        <li>
									<div class="col-md-2">
										<label><?php echo __d('question', 'Thumbnail')?> </label>
									</div>
									<div class="col-md-10">
										<div id="question_thumnail"></div>
										<div id="question_thumnail_preview">
											<?php if (!empty($question['Question']['thumbnail'])): ?>
												<img width="150" src="<?php echo  $helper->getImage($question, array('prefix' => '150_square')) ?>" />
											<?php else: ?>
												<img width="150" style="display: none;" src="" />
											<?php endif; ?>
										</div>
									</div>
									<div class="clear"></div>
								</li>
		                        <!-- <li>
		                            <div class="col-md-2">
		                                <label><?php //echo __d('question', 'Tags')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <select name="data[tags][]" multiple="multiple" id="tags">
		                                	<?php //if ($question['Question']['tags']):?>
		                                		<?php //foreach ($question['Question']['tags'] as $tag):?>
		                                			<option selected="selected" value="<?php //echo $tag['QuestionTag']['id']?>"><?php //echo $tag['QuestionTag']['title']?></option>
		                                		<?php //endforeach;?>
		                                	<?php //endif;?>
		                                </select>
		                            </div>
		                            <div class="clear"></div>
		                        </li> -->
		                        <li>
		                            <div class="col-md-2">
		                                <label>&nbsp;</label>
		                            </div>
		                            <div class="col-md-10">
		                                <button type="submit" id="question_button" class='btn btn-action'><?php echo __d('question' ,'Save')?></button>
		                                <?php if (!empty($question['Question']['id'])) : ?>
											<a href="<?php echo $question['Question']['moo_href']?>" class="button"><?php echo __('Cancel');?></a>
											<a href="javascript:void(0)" data-id="<?php echo $question['Question']['id'];?>" class="button deleteQuestion"><?php echo __('Delete')?></a>											
										<?php endif;?>	                                
		                            </div>
		                            <div class="clear"></div>		                            
		                        </li>
		                        <li>
		                        	<div class="error-message" id="errorMessage" style="display:none"></div>
		                        </li>
	            			</ul>
	            		</div>
            		</div>
				</form>
			</div>
		</div>
	</div>
</div>