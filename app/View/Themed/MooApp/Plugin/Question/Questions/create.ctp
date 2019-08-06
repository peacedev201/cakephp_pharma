<?php	
	echo $this->Html->css(array('Question.jquery.tokenize'));
	$helper = MooCore::getInstance()->getHelper('Question_Question');
	$this->addPhraseJs(array(
		'upload_button_text' => __d('question','Drag or click here to upload files'),
		'upload_button_text_photo' => '',
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
		                            <div class="thumb_content">
		                                <div class="thumb_item">
			                                <div id="question_thumnail_preview">
			                                   <?php if (!empty($question['Question']['thumbnail'])): ?>
			                                    	<img width="111" height="144" id="item-avatar" class="img_wrapper" style="background-image:url(<?php echo $helper->getImage($question)?>)" src="<?php echo $this->request->webroot?>theme/<?php echo $this->theme ?>/img/s.png" />
			                                    <?php else: ?>
			                                        <img width="111" height="144" id="item-avatar" class="img_wrapper" style="display: none;" src="<?php echo $this->request->webroot?>theme/<?php echo $this->theme ?>/img/s.png" />
			                                    <?php endif; ?>
			                                    </div>
			                                </div>
		                                <div class="thumb_qq" id="question_thumnail"></div>
		                                <div class="thumb_text">
		                                    <h4><?php echo __d('question','Upload Question Thumb') ?></h4>
		                                    <div><?php echo __d('question','Click thumb to upload') ?></div>
		                                </div>
		                            </div>
		                        </li>      					            				
		                        <li>
		                        	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
		                                <input name="title" class="mdl-textfield__input" type="text" value="<?php echo $question['Question']['title'] ?>" />
		                                <label class="mdl-textfield__label" ><?php echo __d('question','Title')?></label>
		                            </div>
		                        </li>
		                        <li>
		                        	<div>
		                                <label><?php echo __d('question','Description')?></label>
		                            </div>
		                            <?php echo $this->Form->tinyMCE('description', array('id'=>'description','value'=>$question['Question']['description'])); ?>
		                        </li>
		                        <li>		                        	
                            		<div data-extension="<?php echo $extension;?>" id="attachments_upload"></div>                            		
			                    </li> 
		                        <li>
		                            <?php echo $this->Form->select('category_id',$categories,array('empty' => false,'value'=>$question['Question']['category_id'])); ?>		                            
		                        </li>	
		                        <li>
	                                 <?php
			                            echo $this->Form->select( 'privacy',
			                                                      array( PRIVACY_EVERYONE => __d('question', 'Everyone'),
			                                                             PRIVACY_FRIENDS  => __d('question', 'Friends Only'),
			                                                             PRIVACY_ME 	  => __d('question', 'Only Me')
			                                                            ),
			                                                      array('empty' => false,'value'=>$question['Question']['privacy'])
			                                                    );
			                         ?>
		                        </li>
		                        <li>
		                        	<div>
		                                <label><?php echo __d('question','Tags')?></label>
		                            </div>
	                                <select name="data[tags][]" multiple="multiple" id="tags">
	                                	<?php if ($question['Question']['tags']):?>
	                                		<?php foreach ($question['Question']['tags'] as $tag):?>
	                                			<option selected="selected" value="<?php echo $tag['QuestionTag']['id']?>"><?php echo $tag['QuestionTag']['title']?></option>
	                                		<?php endforeach;?>
	                                	<?php endif;?>
	                                </select>
		                        </li>
		                        <li>
		                            <div class="col-md-2">
		                                <label>&nbsp;</label>
		                            </div>
		                            <div class="col-md-10">
		                                <button type="submit" id="question_button" class='mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1'><?php echo __d('question' ,'Save')?></button>
		                                <?php if (!empty($question['Question']['id'])) : ?>
											<a href="<?php echo $question['Question']['moo_href'].'?app_no_tab=1'?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1"><?php echo __('Cancel');?></a>											
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