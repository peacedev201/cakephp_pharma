<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $quizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz'); ?>

<div class="box2 filter_block quiz-content-browse-view">
    <div class="box_content">
        <ul class="list2">
            <li><img width="100%" src="<?php echo $quizHelper->getImage($quiz, array('prefix' => '300_square')); ?>"></li>
            <li class="separate"></li>
        </ul>                 
        <ul class="list2 loadQuizView">
            <li class="current">
                <a id="quizDetail" href="<?php echo $quiz['Quiz']['moo_href']; ?>" data-url="<?php echo $this->request->base . '/quizzes/view_detail/' . $quiz['Quiz']['id']; ?>">
                    <i class="material-icons">library_books</i> <?php echo __d('quiz', 'Detail'); ?>
                </a>
            </li>
            <li>
                <a id="quizParticipant" href="<?php echo $quiz['Quiz']['moo_href']; ?>" data-url="<?php echo $this->request->base . '/quizzes/view_participant/' . $quiz['Quiz']['id']; ?>">
                    <i class="material-icons">people</i> <?php echo __d('quiz', 'Participant'); ?>
                </a>
            </li>
        </ul>		
    </div>
</div>