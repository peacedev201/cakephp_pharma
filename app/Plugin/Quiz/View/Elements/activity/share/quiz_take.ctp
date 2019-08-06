<?php
$aQuiz = $object;
$quizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz');
?>

<div class="activity_left">
    <a target="_blank" href="<?php echo $aQuiz['Quiz']['moo_href']; ?>">
        <img width="150" class="thum_activity" src="<?php echo $quizHelper->getImage($aQuiz, array('prefix' => '150_square')); ?>" />
    </a>
</div>
<div class="activity_right ">
    <div class="activity_header">
        <a target="_blank" href="<?php echo $aQuiz['Quiz']['moo_href']; ?>"><?php echo  h($aQuiz['Quiz']['moo_title']); ?></a>
    </div>
    <div class="post_content">
        <?php echo  $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $aQuiz['Quiz']['description'])), 200, array('exact' => false)), Configure::check('Quiz.quiz_enabled_hashtag') ? Configure::read('Quiz.quiz_enabled_hashtag') : 0); ?>
    </div>
    <div>
        <strong><?php echo __d('quiz', 'Result'); ?>:</strong> <?php echo $quizHelper->getResult($aQuiz, $activity['Activity']['user_id']); ?>
    </div>
</div>
<div class="clear"></div>
