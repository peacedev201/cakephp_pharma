<?php 	
	$helper = MooCore::getInstance()->getHelper('Question_Question');
	$uid = MooCore::getInstance()->getViewer(true);
	$viewer = MooCore::getInstance()->getViewer();
	$canVoteUp = $helper->can('vote_up',$viewer);
	$canVoteDown = $helper->can('vote_down',$viewer);	
	$questionVoteModel = MooCore::getInstance()->getModel("Question.QuestionVote");
	$questionTagMapModel = MooCore::getInstance()->getModel("Question.QuestionTagMap");
	$questionFavoriteModel = MooCore::getInstance()->getModel("Question.QuestionFavorite");	
	$commentModel = MooCore::getInstance()->getModel("Question.QuestionComment");
	$comments = $commentModel->getComments("Question",$question['Question']['id']);
	$comment_count = $commentModel->getCountComments("Question",$question['Question']['id']);
	$historyModel = MooCore::getInstance()->getModel("Question.QuestionContentHistory");
	$canEdit = $helper->canEdit($question,MooCore::getInstance()->getViewer());
	
	$this->addPhraseJs(array(
		'please_confirm_remove_this_comment' => __d('question','Are you sure to remove this comment?'),
		'confirm_delete_answer' => __d('question','Are you sure to remove this answer?'),
		'upload_button_text' => __d('question','Drag or click here to upload files'),
	));
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooQuestion'), 'object' => array('$', 'mooQuestion'))); ?>
	mooQuestion.initViewQuestion();
<?php $this->Html->scriptEnd(); ?>
<div class="bar-content full_content p_m_10 page_questions-view">
    <div class="content_center">
    	<div class="post_body">	
    		<h1 class="question-detail-title"><?php echo $question['Question']['moo_title'];?></h1>	    		    	
	        <div class="question-detail-action">
	            <div class="list_option">
	            	<?php if ($uid):?>    
		                <div class="dropdown">
		                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
		                        <i class="material-icons">more_vert</i>
		                    </button>
		                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
		                        <?php if ($canEdit):?>
			                        <li><a href="<?php echo $this->request->base?>/questions/create/<?php echo $question['Question']['id']?>" title="<?php echo __d('question','Edit Question')?>"><?php echo __d('question','Edit Question')?></a></li>
			                        <li><a href="javascript:void(0)" class="deleteQuestion" data-id="<?php echo $question['Question']['id'];?>"><?php echo __d('question','Delete Question')?></a></li>
			                        <li class="seperate"></li>
		                        <?php endif; ?>
		                         	                        
		                        <li>
		                        	<?php
		                            $this->MooPopup->tag(array(
		
		                                'href'=>$this->Html->url(array(
		                                    "controller" => "reports",
		                                    "action" => "ajax_create",
		                                    "plugin" => false,
		                                    'Question_Question',
		                                    $question['Question']['id'],
		                                )),
		                                'title' => __d('question','Report Question'),
		                                'innerHtml'=> __d('question','Report Question'),
		                            ));
		                            ?>		                        	
		                        </li>
		                        <?php if ($question['Question']['privacy'] != PRIVACY_ME): ?>
			                        <!-- not allow sharing only me item -->
			                        <li>
			                            <a href="javascript:void(0);" share-url="<?php echo $this->Html->url(array(
			                                'plugin' => false,
			                                'controller' => 'share',
			                                'action' => 'ajax_share',
			                                'Question_Question',
			                                'id' => $question['Question']['id'],
			                                'type' => 'question_item_detail'
			                            ), true); ?>" class="shareFeedBtn"><?php echo __d('question','Share'); ?></a>        
			                        </li>
	                        	<?php endif; ?>
		                    </ul>
		                </div>
	               	<?php endif;?>
	            </div>
	        </div>
	        <div class="clear"></div>
	        <?php if (!$question['Question']['approve']):?>
		    	<div class="document_alert-message">
		       		<?php echo __d('question',"Question is pending admin's approval.");?>
		       	</div>
	       	<?php endif;?>
	        <div id="question_content" class="row question-main-content">
	        	<div class="col-md-1 col-xs-1 vote-block">
				   <!-- vote group -->
				   <ul>
				   	  <?php if ($uid != $question['User']['id']):?>
					      <!-- vote up -->
					      <li <?php if (!$isMobile):?> original-title="<?php echo __d("question","This is useful.");?>" class="tip" <?php endif;?>>
					         <a href="javascript:void(0);" <?php if ($canVoteUp == QUESTION_CAN_ERROR_NONE):?>data-type="Question" data-id="<?php echo $question['Question']['id']?>"<?php else:?> data-container="body" tabindex="0" data-toggle="popover" role="button" data-trigger="focus" role="button" data-content="<?php if ($canVoteUp == QUESTION_CAN_ERROR_LOGIN) echo __d("question","You must be logged in to perform this action"); else echo __d("question","You do not have enough points to vote up");?>" <?php endif;?> class="<?php if ($questionVoteModel->checkVote("Question",$question['Question']['id'],$uid)) echo "active";?> <?php if ($canVoteUp == QUESTION_CAN_ERROR_NONE):?>question_vote_up<?php endif;?> img-circle" >
					         	<i class="material-icons">keyboard_arrow_up</i>
					         </a>
					      </li>
					      <!--// vote up -->
				      <?php endif;?>
				      <!--vote point -->
				      <li>
				         <span class="vote-count"><?php echo $question['Question']['vote_count']?></span>
				      </li>
				      <!--// vote point -->
				      <?php if ($uid != $question['User']['id']):?>
				      <!-- vote down -->
				      <li <?php if (!$isMobile):?> class="tip" original-title="<?php echo __d("question","This is not useful");?>" <?php endif;?>>
				         <a href="javascript:void(0);" <?php if ($canVoteDown == QUESTION_CAN_ERROR_NONE):?> data-type="Question" data-id="<?php echo $question['Question']['id']?>"<?php else:?> data-container="body" tabindex="0" data-toggle="popover" role="button" data-trigger="focus" role="button" data-content="<?php if ($canVoteDown == QUESTION_CAN_ERROR_LOGIN) echo __d("question","You must be logged in to perform this action"); else echo __d("question","You do not have enough points to vote down");?>" <?php endif;?> class="<?php if ($questionVoteModel->checkVote("Question",$question['Question']['id'],$uid,0)) echo "active";?> <?php if ($canVoteDown == QUESTION_CAN_ERROR_NONE):?>question_vote_down<?php endif;?> img-circle" >
				         	<i class="material-icons">keyboard_arrow_down</i>
				         </a>
				      </li>
				      <!--// vote down -->
				      <?php endif;?>
				      <?php if ($uid && $uid != $question['Question']['user_id']):?>
				      	  <?php $checkFavorite = $questionFavoriteModel->checkFavorite($question['Question']['id'],$uid);?>
					      <li class="favorite">
					      	<i data-id="<?php echo $question['Question']['id']?>" class="question_add_favorite favorite_t material-icons <?php if ($checkFavorite) echo 'star_active'?>">star</i>
					      </li>
					      <li>
					      	<div id="favorite_count" class="favorite_t <?php if ($checkFavorite) echo 'star_active'?>"><?php echo $question['Question']['favorite_count']?></div>
					      </li>
				      <?php elseif ($question['Question']['favorite_count'] && $uid == $question['Question']['user_id']):?>
				      	  <li class="favorite_view">
				      	  	<a href="<?php echo $this->request->base?>/questions/ajax_show_user_favorite/<?php echo $question['Question']['id']?>" data-target="#questionModal" data-toggle="modal" data-dismiss="modal" data-backdrop="true"><?php echo $question['Question']['favorite_count']?><i class="favorite_t material-icons star_active">star</i></a>
				      	  </li>
				      <?php endif;?>
				   </ul>
				   <!--// vote group -->
				</div>
				<div class="list_question col-md-11 col-xs-9">
					<div class="question-content question_des">
						<?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $question['Question']['description']  , Configure::read('Question.question_hashtag_enabled') ))?>
	                </div>
					<?php if (count($tags)):?>
					<div>   
						<ul class="question-tags">
							<?php foreach ($tags as $tag):?>
					    	<li>
					        	<a class="q-tag" href="<?php echo $tag['QuestionTag']['moo_href']?>">
					        		<?php echo $tag['QuestionTag']['title']?>
					        	</a>
					   	   	</li>
					   	   	<?php endforeach;?>
					   	</ul>
					</div>
					<?php endif;?>
					<div class="row question_info">
						<div class="clear">
							<a class="moocore_tooltip_link" data-item_id="<?php echo $question['User']['id']?>" data-item_type="user" href="<?php echo $question['User']['moo_href']?>">
								<span class="author-avatar">
									<img class="avatar" src="<?php echo $this->Moo->getItemPhoto(array('User' => $question['User']),array( 'prefix' => '50_square'), array(), true);?>">
								</span>
								<span class="author-name"><?php echo $question['User']['moo_title']?></span>
							</a>
							<?php echo $helper->getHtmlBadge($question['User']['id']); ?>
							<span class="question-time">
								<?php echo $this->Moo->getTime( $question['Question']['created'], Configure::read('core.date_format'), $utz )?>
								<?php if ($question['Question']['edited']):?>
									<a href="<?php echo $this->base?>/questions/content_history/Question/<?php echo $question['Question']['id']?>" data-target="#questionModal" data-toggle="modal" class="edit-btn" title="<?php echo __d('question','Show edit history');?>" data-dismiss="modal" data-backdrop="true"><?php echo $historyModel->getText('Question',$question['Question']['id']);?></a>
								<?php endif;?>
							</span>
							<?php if ($question['Category']):?>
								<span class="question-category">
									<span class="question-in"><?php echo __d('question','in');?><span></span> <a href="<?php echo $this->base;?>/questions/index/?category=<?php echo $question['Category']['id']?>"><?php echo $question['Category']['name']?></a>
								</span>                
							<?php endif;?>
						</div>
					</div>
					<div class="comments-container">
						<?php echo $this->element('comments',array('type'=>'Question','id'=>$question['Question']['id'],'comments'=>$comments,'comment_count'=>$comment_count),array('plugin'=>'Question'));?>
					</div>
				</div>
	        </div>
	        <div class="row answer_menu">
	        	<div class="col-md-2 number_answer"><span id="count_answer"><?php echo $question['Question']['answer_count'];?></span> <?php echo __d('question','Answer(s)');?></div>
	        	<div class="col-md-10">
	        		<ul class="sort-questions">
						<li><a <?php if ($tab == 'active') echo 'class="active"'?> href="<?php echo $question['Question']['moo_href']?>?tab=active"><?php echo __d('question','Active');?></a></li>
						<li><a <?php if ($tab == 'vote') echo 'class="active"'?> href="<?php echo $question['Question']['moo_href']?>?tab=vote"><?php echo __d('question','Votes');?></a></li>
						<li><a <?php if ($tab == 'old') echo 'class="active"'?> href="<?php echo $question['Question']['moo_href']?>?tab=old"><?php echo __d('question','Oldest');?></a></li>
					</ul>
	        	</div>
	        </div>
	        <?php if (count($answers)):?>
	       		<?php foreach ($answers as $answer):?>
	        	<div class="row question-main-content item_answer" id="answer_<?php echo $answer['QuestionAnswer']['id']?>">
	        		<?php 
	        			$comments = $commentModel->getComments("Answer",$answer['QuestionAnswer']['id']);
	        			$comment_count = $commentModel->getCountComments("Answer",$answer['QuestionAnswer']['id']);
	        		?>
					<div class="col-md-1 col-xs-1 vote-block">
					   <!-- vote group -->
					   <ul>
						  <?php if ($uid != $answer['User']['id']):?>
							  <!-- vote up -->
							  <li <?php if (!$isMobile):?> original-title="<?php echo __d("question","This is useful.");?>" class="tip" <?php endif;?>>
								 <a href="javascript:void(0);" <?php if ($canVoteUp == QUESTION_CAN_ERROR_NONE):?>data-type="Answer" data-id="<?php echo $answer['QuestionAnswer']['id']?>"<?php else:?> data-container="body" tabindex="0" data-toggle="popover" role="button" data-trigger="focus" role="button" data-content="<?php if ($canVoteUp == QUESTION_CAN_ERROR_LOGIN) echo __d("question","You must be logged in to perform this action"); else echo __d("question","You do not have enough points to vote up");?>" <?php endif;?> class="<?php if ($questionVoteModel->checkVote("Answer",$answer['QuestionAnswer']['id'],$uid)) echo "active";?> <?php if ($canVoteUp == QUESTION_CAN_ERROR_NONE):?>question_vote_up<?php endif;?> img-circle" >
									<i class="material-icons">keyboard_arrow_up</i>
								 </a>
							  </li>
							  <!--// vote up -->
						  <?php endif;?>
						  <!--vote point -->
						  <li>
							 <span class="vote-count"><?php echo $answer['QuestionAnswer']['vote_count']?></span>
						  </li>
						  <!--// vote point -->
						  <?php if ($uid != $answer['User']['id']):?>
						  <!-- vote down -->
						  <li <?php if (!$isMobile):?> class="tip" original-title="<?php echo __d("question","This is not useful");?>" <?php endif;?>>
							 <a href="javascript:void(0);" <?php if ($canVoteDown == QUESTION_CAN_ERROR_NONE):?>data-type="Answer" data-id="<?php echo $answer['QuestionAnswer']['id']?>"<?php else:?> data-container="body" tabindex="0" data-toggle="popover" role="button" data-trigger="focus" role="button" data-content="<?php if ($canVoteDown == QUESTION_CAN_ERROR_LOGIN) echo __d("question","You must be logged in to perform this action"); else echo __d("question","You do not have enough points to vote down");?>" <?php endif;?> class="<?php if ($questionVoteModel->checkVote("Answer",$answer['QuestionAnswer']['id'],$uid,0)) echo "active";?> <?php if ($canVoteDown == QUESTION_CAN_ERROR_NONE):?><?php endif;?> question_vote_down img-circle" >
								<i class="material-icons">keyboard_arrow_down</i>
							 </a>
						  </li>
						  <!--// vote down -->
						  <?php endif;?>
						  
						  <?php if ($helper->canMarkBestAnswer($question,$answer,MooCore::getInstance()->getViewer())):?>
						  	<li class="tip" original-title="<?php echo __d("question","Mark as best answer");?>">
								<a href="javascript:void(0);" data-id="<?php echo $answer['QuestionAnswer']['id']?>" class="question_mark_best_answer <?php if ($answer['QuestionAnswer']['best_answers']) echo "active";?> mark_answer img-circle" >
									<i class="material-icons">check</i>
								</a>
						  	</li>
						  <?php endif; ?>
					   </ul>
					   <!--// vote group -->
					</div>
					<div class="list_question col-md-11 col-xs-9">
						<?php if ($helper->canEditAnswer($answer,MooCore::getInstance()->getViewer())):?>
							<div class="question-detail-action">
					            <div class="list_option">
					                <div class="dropdown">
					                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
					                        <i class="material-icons">more_vert</i>
					                    </button>
					                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
					                        <li><a href="javascript:void(0)" class="question_edit_answer" data-id="<?php echo $answer['QuestionAnswer']['id']?>" title="<?php echo __d('question','Edit Answer')?>"><?php echo __d('question','Edit Answer')?></a></li>
					                        <li><a href="javascript:void(0)" class="question_delete_answer" data-id="<?php echo $answer['QuestionAnswer']['id']?>"><?php echo __d('question','Delete Answer')?></a></li>
					                    </ul>
					                </div>
					            </div>
					        </div>
				        <?php endif;?>
						<?php if (!$helper->canMarkBestAnswer($question,$answer,MooCore::getInstance()->getViewer()) && $answer['QuestionAnswer']['best_answers']):?>
							<div>
								<span class="best-answer"><?php echo __d('question','Best answer');?></span>
							</div>
						<?php endif;?>
						<div class="question-content question_des" id="content_answer_<?php echo $answer['QuestionAnswer']['id'];?>">
							<?php echo $answer['QuestionAnswer']['description'];?>
						</div>
						<div class="row question_info">
							<div class="clear">
								<a class="moocore_tooltip_link" data-item_id="<?php echo $answer['User']['id'];?>" data-item_type="user" href="<?php echo $answer['User']['moo_href']?>">
									<span class="author-avatar">
										<img class="avatar" src="<?php echo $this->Moo->getItemPhoto(array('User' => $answer['User']),array( 'prefix' => '50_square'), array(), true);?>">
									</span>
									<span class="author-name"><?php echo $answer['User']['moo_title']?></span>
								</a>
								<?php echo $helper->getHtmlBadge($answer['User']['id']); ?>
								<span class="question-time">
									<?php echo $this->Moo->getTime( $answer['QuestionAnswer']['created'], Configure::read('core.date_format'), $utz )?>
									<a id="history_answer_<?php echo $answer['QuestionAnswer']['id']?>" href="<?php echo $this->base?>/questions/content_history/Answer/<?php echo $answer['QuestionAnswer']['id']?>" <?php if (!$answer['QuestionAnswer']['edited']):?>style="display:none;"<?php endif;?> data-target="#questionModal" data-toggle="modal" class="edit-btn" title="<?php echo __d('question','Show edit history');?>" data-dismiss="modal" data-backdrop="true"><?php echo $historyModel->getText('Answer',$answer['QuestionAnswer']['id']);?></a>
								</span>
							</div>
						</div>
						<div class="comments-container">
							<?php echo $this->element('comments',array('type'=>'Answer','id'=>$answer['QuestionAnswer']['id'],'comments'=>$comments,'comment_count'=>$comment_count),array('plugin'=>'Question'));?>
						</div>
					</div>
	        	</div>
	        	<?php endforeach;?>
	        
		        <div class="pagination">
			        <?php echo $this->Paginator->first(__d('question','First'));?>&nbsp;
			        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('question','Prev')) : '';?>&nbsp;
					<?php echo $this->Paginator->numbers();?>&nbsp;
					<?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__d('question','Next')) : '';?>&nbsp;
					<?php echo $this->Paginator->last(__d('question','Last'));?>
			    </div>      
		    <?php endif?>	  
	        <?php if ($helper->canAnswer($question,$uid)):?>
		        <div class="row form-reply">
		        	<div class="col-md-12">
		        		<div class="number_answer"><?php echo __d('question','Your Answer');?></div>
		        		<form id="form_reply" action="<?php echo $this->base?>/questions/post_answer" method="post" class="question_form_reply create_form">
	        				<input type="hidden" name="question_id" value="<?php echo $question['Question']['id'];?>">
	        				<input type="hidden" name="attachments" id="attachments">
	        				<input type="hidden" name="photo_ids" id="photo_ids">
		        			<div>
		        				<?php echo $this->Form->tinyMCE('content', array('id'=>'content','class'=>'content_question')); ?>
		        			</div>
		        			<div>
		        				<div data-extension="<?php echo $extension;?>" id="attachments_upload"></div>
		        			</div>
		        			<div class="row submit-wrapper">
		        				<div class="col-md-2">
	                                <button id="submit_reply" class="btn btn-action"><?php echo __d('question','Post answer');?></button>
	                            </div>
		        			</div>
		        		</form>
		        	</div>
		        </div>
	        <?php endif;?>
    	</div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="edit_answer" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel"><?php echo  __d('question','Edit answer');?></h4>
      </div>
      <div class="modal-body">
        <div class="row">        
	        <input type="hidden" id="attachments_edit" name="attachments">
	        <input type="hidden" id="edits_photo_ids" name="photo_ids">
        	<div>
        		 <?php echo $this->Form->tinyMCE('content_edit_answer', array('class'=>'content_question','id'=>'content_edit_answer')); ?>
        	</div>
        	<div>
				<div data-extension="<?php echo $extension;?>" id="edit_attachments_upload"></div>
			</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __d('question','Close')?></button>
        <button id="edit_answer_button" type="button" class="btn btn-action"><?php echo __d('question','Save changes');?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->