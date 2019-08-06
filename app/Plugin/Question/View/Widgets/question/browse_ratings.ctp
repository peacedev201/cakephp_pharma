<?php $helper = MooCore::getInstance()->getHelper("Question_Question");?>
<div class="bar-content">
    <div class="content_center question_user_rating">
    	<h3><?php echo __d("question","Ratings");?></h3>
    	<div>
    		<?php if (count($users)):?>
    			<div class="list_user">
    				<?php foreach ($users as $key=>$user):?>
    					<div class="row">
    						<div class="rating"><?php echo ($this->Paginator->current() - 1)* Configure::read('Question.question_item_per_pages') + $key + 1;?></div>
							<div class="profile-avatar">
								<a class="moocore_tooltip_link" data-item_id="<?php echo $user['User']['id']?>" data-item_type="user" href="<?php echo $user['User']['moo_href']?>">
									<img src="<?php echo $this->Moo->getItemPhoto(array('User' => $user['User']),array( 'prefix' => '50_square'), array(), true);?>" class="avatar" alt=""> 
								</a>
							</div>
							<div class="col-md-10 col-xs-8">
								<div class="profile-wrapper">
									<span class="user-name-profile"><a class="moocore_tooltip_link" data-item_id="<?php echo $user['User']['id']?>" data-item_type="user" href="<?php echo $user['User']['moo_href']?>"><?php echo $user['User']['moo_title']?></a></span>
									<div class="list-bag-profile-wrapper">
										<div class="point-profile"><span><?php echo $user['QuestionUser']['total']?><i class="material-icons">star</i></span><?php echo __d('question','point(s)');?></div>
										<div class="t">
											<span class="point-profile"><span><?php echo $user['QuestionUser']['total_question']?><i class="material-icons">question_answer</i></span><?php echo __d('question','question(s)');?></span>
											&nbsp&nbsp
											<span class="point-profile"><span><?php echo $user['QuestionUser']['total_answer']?><i class="material-icons">comment</i></span><?php echo __d('question','answer(s)');?></span>
										</div>										
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
    				<?php endforeach;?>
    				<div class="row pagination">    					
				        <?php echo $this->Paginator->first('First');?>&nbsp;
				        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('question','Prev')) : '';?>&nbsp;
						<?php echo $this->Paginator->numbers();?>&nbsp;
						<?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__d('question','Next')) : '';?>&nbsp;
						<?php echo $this->Paginator->last('Last');?>
				    </div>
    			</div>    			
    		<?php endif;?>
    	</div>    
    </div>
</div>