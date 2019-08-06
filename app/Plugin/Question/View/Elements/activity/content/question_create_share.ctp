<?php
$questionHelper = MooCore::getInstance()->getHelper('Question_Question');
?>
<div class="comment_message">
	<?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
    
    <div class="share-content">
    	<?php
	    	$activityModel = MooCore::getInstance()->getModel('Activity');
	    	$parentFeed = $activityModel->findById($activity['Activity']['parent_id']);
	    	$question = MooCore::getInstance()->getItemByType("Question_Question", $parentFeed['Activity']['params']);
    	?>
    	<div class="activity_feed_content">
            
            <div class="activity_text">
                <?php echo $this->Moo->getName($parentFeed['User']) ?>
                <?php echo __d('question','created a new question'); ?>
            </div>
            
            <div class="parent_feed_time">
                <span class="date"><?php echo $this->Moo->getTime($parentFeed['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
            </div>
            
        </div>
		<div class="activity_item activity_question">
		     <div class="activity_left">
				<a href="<?php echo $question['Question']['moo_href']?>">
					<img alt="" src="<?php echo $questionHelper->getImage($question);?>">			
				</a>
			</div>
		    <div class="activity_right ">
		        <div class="activity_header <?php if ($question['Question']['feature']) echo "has_feature"?> ">
		            <a href="<?php echo  $question['Question']['moo_href'] ?>"><?php echo  ($question['Question']['moo_title']) ?></a>
		        </div>
		        <?php if ($question['Question']['feature']):?>
		        	<span class="tip question-icon-fetured" original-title="<?php echo __d("question","Featured");?>"></span>
		        <?php endif;?>
		        <?php echo  $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $question['Question']['description'])), 200, array('exact' => false)),Configure::read('Question.question_hashtag_enabled') )?>
		      </div>
		    <div class="clear"></div>
		</div>
	</div>
</div>