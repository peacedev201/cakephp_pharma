<?php 
	$helper = MooCore::getInstance()->getHelper('Question_Question');	
	$QuestionTagMapModel =  MooCore::getInstance()->getModel("Question.QuestionTagMap");
	$no_id = isset($no_list_id) ? $no_list_id : false;
?>
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooQuestion"], function($,mooQuestion) {
    	mooQuestion.initListingQuestion();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooQuestion'), 'object' => array('$', 'mooQuestion'))); ?>
	mooQuestion.initListingQuestion();
<?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>

<?php if (count($questions)): ?>
	<?php if ($no_id):?>
		<ul id="list-content" class="list_question_browse question-content-list">
	<?php endif;?>
	<?php foreach ($questions as $question):?>		
		<li class="list_question full_content question-item">
			<div class="col-md-8 col-xs-8 q-left-content">
				<div class="q-ltop-content">
					<h2>
						<a href="<?php echo $question['Question']['moo_href']; ?>" class="question-title"><?php echo $question['Question']['moo_title']?><?php if ($question['Question']['feature']):?>  <span class="tip question-icon-fetured" original-title="<?php echo __d("question","Featured");?>"></span><?php endif;?></a>						
					</h2>
				</div>					
				<?php if ($helper->canEdit($question,MooCore::getInstance()->getViewer())):?>
				<div class="list_option">
					<div class="dropdown">
						<button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
							<?php echo __d("question","Action");?>
						</button>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">					
							<li><a href="<?php echo $this->request->base?>/questions/create/<?php echo $question['Question']['id']?>" title="<?php echo __d('question','Edit Question')?>"><?php echo __d('question','Edit Question')?></a></li>
							<li><a href="javascript:void(0)" class="deleteQuestion" data-id="<?php echo $question['Question']['id']?>"><?php echo __d('question','Delete Question')?></a></li>					
						</ul>
					</div>
				</div>
				<?php endif; ?>
				
				<div class="q-lbtm-content">
					<div class="question-excerpt">
						<p>
							<?php 									
								echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $question['Question']['description'])), 200, array('eclipse' => '')), Configure::read('Question.question_hashtag_enabled'));
							?>
						</p>
					</div>
					<div class="question-cat">
						<?php $tags = $QuestionTagMapModel->getTag($question['Question']['id']);?>
						<?php if (count($tags)):?>
							<ul class="question-tags">
								<?php foreach ($tags as $tag):?>
								<li>
									<a href="<?php echo $tag['QuestionTag']['moo_href'];?>" class="q-tag">
										<?php echo $tag['QuestionTag']['title'];?>                                
									</a>
								</li>
								<?php endforeach;?>
							</ul>
						<?php endif;?>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-xs-4 q-right-content">
				<ul class="question-statistic">
					<li>
						<span class="question-views"><?php echo $question['Question']['view_count']?></span> <?php if ($question['Question']['view_count'] > 1) echo __d('question','Views'); else echo __d('question','View')?>
					</li>
					<li <?php if ($question['Question']['has_best_answers']):?>class="active"<?php endif;?>>
						<span class="question-answers"><?php echo $question['Question']['answer_count']?></span> <?php if ($question['Question']['answer_count'] > 1) echo __d('question','Answers'); else echo __d('question','Answer')?>               
					</li>
					<li>
						<span class="question-votes"><?php echo $question['Question']['vote_count']?></span> <?php if ($question['Question']['vote_count'] > 1) echo __d('question','Votes'); else echo __d('question','Vote')?>
					</li>
				</ul>
			</div>
			<div class="clear">
				<ul class="info-review-question">
		            <li>
		                <span><?php echo $question['Question']['view_count']?></span><i class="material-icons">remove_red_eye</i>
		            </li>
		            <li <?php if ($question['Question']['has_best_answers']):?>class="active"<?php endif;?>>
		                <span><?php echo $question['Question']['answer_count']?></span><i class="material-icons">check_circle</i>
		            </li>
		            <li>
		                <span><?php echo $question['Question']['vote_count']?></span><i class="material-icons">expand_less</i></i>
		            </li>
		        </ul>
			</div>
			<div class="clear">
				<a class="moocore_tooltip_link" data-item_id="<?php echo $question['User']['id']?>" data-item_type="user" href="<?php echo $question['User']['moo_href']?>">
					<span class="author-avatar">
						<img class="avatar" src="<?php echo $this->Moo->getItemPhoto(array('User' => $question['User']),array( 'prefix' => '50_square'), array(), true);?>">
					</span>
					<span class="author-name"><?php echo $question['User']['moo_title']?></span>
				</a>
				<?php echo $helper->getHtmlBadge($question['User']['id']); ?>
				<span class="question-time"><?php echo $this->Moo->getTime( $question['Question']['created'], Configure::read('core.date_format'), $utz )?></span>
				<?php if ($question['Category']):?>
					<span class="question-category">
						<span class="question-in"><?php echo __d('question','in');?></span> <a href="<?php echo $this->base;?>/questions/index/?category=<?php echo $question['Category']['id']?>"><?php echo $helper->getCategoryName($question['Category']['id'])?></a>
					</span>                
				<?php endif;?>
			</div>
		</li>	
	<?php endforeach;?>
	<?php if (isset($is_view_more) && $is_view_more): ?>
		<?php $this->Html->viewMore($url_more) ?>
	<?php endif; ?>
	<?php if ($no_id):?>
		</ul>
	<?php endif;?>
<?php else: ?>
	<li class="clear text-center"><?php echo __d('question','No more results found');?></li>
<?php endif;?>