<?php
$question = MooCore::getInstance()->getItemByType("Question.Question", $activity['Activity']['params']);
$questionHelper = MooCore::getInstance()->getHelper('Question_Question');
?>
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