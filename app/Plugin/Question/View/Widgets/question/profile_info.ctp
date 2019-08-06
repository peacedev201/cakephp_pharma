<?php if ($question_user):?>
	<div class="box2">        
        <?php if (isset($title_enable) && $title_enable):?>       
        	<h3><?php echo $title ?></h3>
        <?php endif;?>
        <div class="box_content profile_question_info">
        	<?php $helper = MooCore::getInstance()->getHelper('Question_Question');?>
        	<div class="list_question">
        		<?php echo $helper->getHtmlBadge($question_user['QuestionUser']['user_id']); ?>
        	</div>
        	<ul>
        		<li><?php echo __d('question','Total point');?>: <?php echo $question_user['QuestionUser']['total']?></li>
	        	<li><?php echo __d('question','Questions');?>: <?php echo $question_user['QuestionUser']['total_question']?></li>
	        	<li><?php echo __d('question','Answers');?>: <?php echo $question_user['QuestionUser']['total_answer']?></li>
	        	<li><?php echo __d('question','Best Answers');?>: <?php echo $question_user['QuestionUser']['total_best_answer']?></li>
        	</ul>
        	
        </div>
    </div>
<?php endif;?>