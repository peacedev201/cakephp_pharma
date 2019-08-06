<?php
	$helper = MooCore::getInstance()->getHelper('Poll_Poll');
	$this->addPhraseJs(array(
		'drag_photo' => __d('poll', "Drag or click here to upload photo"),
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
			        <div class="full_content p_m_10">
			        	<?php
						if (!empty($poll['Poll']['id']))
							echo $this->Form->hidden('id', array('value' => $poll['Poll']['id']));
							
						echo $this->Form->hidden('thumbnail', array('value' => $poll['Poll']['thumbnail']));
						?>
	            		<div class="form_content">
	            			<ul>	            				
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('poll','Title')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->text('title',array('value'=>htmlspecialchars_decode($poll['Poll']['title']))); ?>
		                            </div>
		                            <div class="clear"></div>
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
		                            <div class="col-md-2">
		                                <label><?php echo __d('poll','Category')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->select('category_id',$categories,array('value'=>$poll['Poll']['category_id'])); ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <?php if (empty($poll['Poll']['id']) || $helper->canEditAnswer($poll)) : ?>
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('poll','Allow users to select more than one answer?')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->checkbox('type',array('checked'=>$poll['Poll']['type'])); ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                  		<?php endif;?>
		                        <li>
									<div class="col-md-2">
										<label><?php echo __d('poll', 'Thumbnail')?> </label>
									</div>
									<div class="col-md-10">
										<div id="poll_thumnail"></div>
										<div id="poll_thumnail_preview">
											<?php if (!empty($poll['Poll']['thumbnail'])): ?>
												<img width="150" src="<?php echo  $helper->getImage($poll, array('prefix' => '150_square')) ?>" />
											<?php else: ?>
												<img width="150" style="display: none;" src="" />
											<?php endif; ?>
										</div>
									</div>
									<div class="clear"></div>
								</li>
		                        <li>
		                            <div class='col-md-2'>
		                                <label><?php echo __d('poll','Privacy')?></label>
		                            </div>
		                            <div class='col-md-10'>
		                                 <?php
				                            echo $this->Form->select( 'privacy',
				                                                      array( PRIVACY_EVERYONE => __d('poll', 'Everyone'),
				                                                             PRIVACY_FRIENDS  => __d('poll', 'Friends Only'),
				                                                             PRIVACY_ME 	  => __d('poll', 'Only Me')
				                                                            ),
				                                                      array('empty' => false,'value'=>$poll['Poll']['privacy'])
				                                                    );
				                         ?>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <li>
		                            <div class="col-md-2">
		                                <label><?php echo __d('poll', 'Tags')?></label>
		                            </div>
		                            <div class="col-md-10">
		                                <?php echo $this->Form->text('tags',array('value'=>$poll['Poll']['tags'])); ?> <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __d('poll','Separated by commas or space')?>">(?)</a>
		                            </div>
		                            <div class="clear"></div>
		                        </li>
		                        <?php if (Configure::read('Poll.poll_allow_user_create')):?>
			                        <li>
			                            <div class="col-md-2">
			                                <label><?php echo __d('poll', 'Allow voters to add new answer')?></label>
			                            </div>
			                            <div class="col-md-10">
			                                <?php echo $this->Form->checkbox('create_new_answer',array('checked'=>$poll['Poll']['create_new_answer'])); ?>
			                            </div>
			                            <div class="clear"></div>
			                        </li>
		                        <?php endif;?>
		                        <?php if (empty($poll['Poll']['id']) && Configure::read('Poll.poll_allow_user_create_show_feed')):?>
			                        <li>
			                            <div class="col-md-2">
			                                <label><?php echo __d('poll', 'Show on feed')?></label>
			                            </div>
			                            <div class="col-md-10">
			                                <?php echo $this->Form->checkbox('show_feed',array('checked'=>1)); ?>
			                            </div>
			                            <div class="clear"></div>
			                        </li>
		                        <?php endif;?>
		                        <li>
		                            <div class="col-md-2">
		                                <label>&nbsp;</label>
		                            </div>
		                            <div class="col-md-10">
		                                <button type="submit" id="poll_button" class='btn btn-action'><?php echo __d('poll' ,'Save')?></button>
		                                <?php if (!empty($poll['Poll']['id'])) : ?>
											<a href="<?php echo $poll['Poll']['moo_href']?>" class="button"><?php echo __('Cancel');?></a>
											<a href="javascript:void(0)" class="button deletePoll" data-id="<?php echo $poll['Poll']['id']?>" ><?php echo __('Delete')?></a>											
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