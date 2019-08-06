<?php	
	$helper = MooCore::getInstance()->getHelper('Poll_Poll');
	$this->addPhraseJs(array(
		'drag_photo' => '',
		'min_answer' => str_replace('{total}', Configure::read('Poll.poll_min_answer'), __d('poll', "You must have a minimum of {total} answers"))
	));
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooPoll','mooJqueryUi'), 'object' => array('$', 'mooPoll'))); ?>	
	mooPoll.initCreatePoll({
		'url': '<?php echo (!empty($poll['Poll']['id'])) ? $poll['Poll']['moo_href'] : ''?>',
		'min_answer' : '<?php echo Configure::read('Poll.poll_min_answer');?>'
	});
<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
	<div class="bar-content">
		<div class="content_center">
			<div class="box3">
				<form action="<?php echo  $this->request->base; ?>/polls/poll/save" enctype="multipart/form-data" id="createForm" method="post">
					<div class="mo_breadcrumb">
					   <?php if (!empty($poll['Poll']['id'])) : ?>
							<h1><?php echo __d('poll','Edit poll');?></h1>
					   <?php else: ?>
							<h1><?php echo __d('poll','Add new poll');?></h1>
					   <?php endif; ?>
			        </div>
			        <div class="full_content">
			        	<?php
						if (!empty($poll['Poll']['id']))
							echo $this->Form->hidden('id', array('value' => $poll['Poll']['id']));
							
						echo $this->Form->hidden('thumbnail', array('value' => $poll['Poll']['thumbnail']));
						?>
	            		<div class="form_content">
	            			<ul>
	            				<li>
		                            <div class="thumb_content">
		                                <div class="thumb_item">
			                                <div id="poll_thumnail_preview">
			                                    <?php if (!empty($poll['Poll']['thumbnail'])): ?>
			                                    	<img width="150" id="item-avatar" class="img_wrapper" style="background-image:url(<?php echo $helper->getImage($poll)?>)" src="<?php echo $this->request->webroot?>theme/<?php echo $this->theme ?>/img/s.png" />
			                                    <?php else: ?>
			                                        <img width="150" id="item-avatar" class="img_wrapper" style="display: none;" src="<?php echo $this->request->webroot?>theme/<?php echo $this->theme ?>/img/s.png" />
			                                    <?php endif; ?>
			                                    </div>
			                                </div>
		                                <div class="thumb_qq" id="poll_thumnail"></div>
		                                <div class="thumb_text">
		                                    <h4><?php echo __d('poll','Upload Poll Thumb') ?></h4>
		                                    <div><?php echo __d('poll','Click thumb to upload') ?></div>
		                                </div>
		                            </div>
		                        </li>
		                        <li>
		                        	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
		                                <input name="title" class="mdl-textfield__input" type="text" value="<?php echo htmlspecialchars_decode($poll['Poll']['title']) ?>" />
		                                <label class="mdl-textfield__label" ><?php echo __d('poll','Title')?></label>
		                            </div>
		                        </li>
		                        <?php if (empty($poll['Poll']['id']) || $helper->canEditAnswer($poll)) : ?>
			                        <li>
			                            <div class="col-md-2">
			                                <label><?php echo __d('poll','Answers')?></label>
			                            </div>
			                            <div class="col-md-10">
			                            	<div class="sortable">
			                            		<?php if (empty($poll['Poll']['id'])) : ?>
				                            		<?php for ($i = 1;$i<=Configure::read('Poll.poll_min_answer');$i++):?>
														<div class="placeholder sortable_item_<?php echo $i;?>" id="sortable_item_<?php echo $i;?>">
															<div class="js_prev_block">
																<?php if (!$isMobile):?>
																	<span class="js_arrow_up_down">
																		<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'poll/img/arrow_up_down.png';?>"  alt=""  class="sortingArrows"  height="18px" />
																	</span>
																<?php endif;?>
																<input type="text" tabindex="1" name="data[answers][<?php echo $i;?>][text]" value="" size="30" class="js_answers v_middle" />
																<a class="poll_append_answer" href="javascript:void(0);" >
																	<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'poll/img/add.png';?>"  alt=""  class="v_middle" />
																</a>
																<a class="poll_remove_answer" href="javascript:void(0);">
																	<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'poll/img/delete.png';?>"  alt=""  class="v_middle" />
																</a>
															</div>
															<div class="js_next_block"></div>
														</div>
													<?php endfor;?>
												<?php else:?>
													<?php foreach ($poll['Poll']['items'] as $i=>$item): $i++;?>
														<div class="placeholder sortable_item_<?php echo $i;?>" id="sortable_item_<?php echo $i;?>">
															<div class="js_prev_block">
																<?php if (!$isMobile):?>
																	<span class="js_arrow_up_down">
																		<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'poll/img/arrow_up_down.png';?>"  alt=""  class="sortingArrows"  height="18px" />
																	</span>
																<?php endif;?>
																<input type="text" tabindex="1" name="data[answers][<?php echo $i;?>][text]" value="<?php echo $item['PollItem']['name']?>" size="30" class="js_answers v_middle" />
																<a class="poll_append_answer" href="javascript:void(0);" >
																	<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'poll/img/add.png';?>"  alt=""  class="v_middle" />
																</a>
																<a class="poll_remove_answer" href="javascript:void(0);">
																	<img src="<?php echo FULL_BASE_URL . $this->request->webroot . 'poll/img/delete.png';?>"  alt=""  class="v_middle" />
																</a>
															</div>
															<div class="js_next_block"></div>
														</div>
													<?php endforeach;?>
												<?php endif;?>
											</div>                             
			                            </div>
			                            <div class="clear"></div>
			                        </li>
			                    <?php endif;?>
		                        <li>
		                        	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
		                        		<?php echo $this->Form->select('category_id',$categories,array('empty' => false,'value'=>$poll['Poll']['category_id'])); ?>
		                        	</div>
		                        </li>
		                        <?php if (empty($poll['Poll']['id']) || $helper->canEditAnswer($poll)) : ?>
		                        <li>
		                        	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
		                            	<?php echo $this->Form->checkbox('type',array('checked'=>$poll['Poll']['type'])); ?> <?php echo __d('poll','Allow users to select more than one answer?')?>
		                            </div>
		                        </li>
		                  		<?php endif;?>		                        
		                        <li>
	                                 <?php
			                            echo $this->Form->select( 'privacy',
			                                                      array( PRIVACY_EVERYONE => __d('poll', 'Everyone'),
			                                                             PRIVACY_FRIENDS  => __d('poll', 'Friends Only'),
			                                                             PRIVACY_ME 	  => __d('poll', 'Only Me')
			                                                            ),
			                                                      array('empty' => false,'value'=>$poll['Poll']['privacy'])
			                                                    );
			                         ?>
		                        </li>
		                        <li>
		                        	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
		                                <input name="tags" class="mdl-textfield__input" type="text" value="<?php echo $poll['Poll']['tags'] ? implode(" ", $poll['Poll']['tags']) : ""; ?>" />
		                                <label class="mdl-textfield__label" ><?php echo __d('poll', 'Tags')?></label>
		                            </div>		                            
		                        </li>
		                        <?php if (Configure::read('Poll.poll_allow_user_create')):?>
			                        <li>
			                            <?php echo $this->Form->checkbox('create_new_answer',array('checked'=>$poll['Poll']['create_new_answer'])); ?> <?php echo __d('poll', 'Allow voters to add new answer')?>
			                        </li>
		                        <?php endif;?>
		                        <?php if (empty($poll['Poll']['id']) && Configure::read('Poll.poll_allow_user_create_show_feed')):?>
			                        <li>
			                         	<?php echo $this->Form->checkbox('show_feed',array('checked'=>1)); ?> <?php echo __d('poll', 'Show on feed')?>
			                        </li>
		                        <?php endif;?>
		                        <li>
		                            <div class="col-md-2">
		                                <label>&nbsp;</label>
		                            </div>
		                            <div class="col-md-10">
		                                <button type="submit" id="poll_button" class='mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1'><?php echo __d('poll' ,'Save')?></button>
		                                <?php if (!empty($poll['Poll']['id'])) : ?>
											<a href="<?php echo $poll['Poll']['moo_href']."?app_no_tab=1"?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1"><?php echo __('Cancel');?></a>											
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