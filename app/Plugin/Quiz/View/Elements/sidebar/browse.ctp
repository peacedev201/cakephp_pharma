<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php 
$quizHelper = MooCore::getInstance()->getHelper('Quiz_Quiz'); 
$bVerifyQuestion = $this->requestAction('quizzes/verify_question/' . $quiz['Quiz']['id']);
?>

<div class="bar-content">
    <div class="box2 filter_block quiz-content-browse-edit">
        <div class="box_content">
            <ul class="list2 quiz-menu-list">
                <li>
                    <a href="<?php echo $quiz['Quiz']['moo_href']; ?>" class="image_edit_content">
                        <img width="100%" src="<?php echo $quizHelper->getImage($quiz, array('prefix' => '300_square')); ?>">
                    </a>
                </li>
                <li class="separate"></li>
            </ul>                 
            <ul class="list2 menu_top_list">
                <li<?php echo ($active == 'basic' ? ' class="current"' : ''); ?>>
                    <a href="<?php echo $this->request->base . '/quizzes/create/' . $quiz['Quiz']['id']; ?>">
                        <i class="material-icons">border_color</i>
                        <?php echo __d('quiz', 'Basic'); ?>
                        <span class="badge_counter">
                            <i class="material-icons">done</i>
                        </span>
                    </a>
                </li>
                <li<?php echo ($active == 'questions' ? ' class="current"' : ''); ?>>
                    <a href="<?php echo $this->request->base . '/quizzes/question/' . $quiz['Quiz']['id']; ?>">
                        <i class="material-icons">menu</i>
                        <?php echo __d('quiz', 'Questions'); ?>
                        <span class="badge_counter">
                            <i class="material-icons"><?php echo $bVerifyQuestion ? 'done' : 'https'; ?></i>
                        </span>
                    </a>
                </li>
                <li<?php echo ($active == 'publish' ? ' class="current"' : ''); ?>>
                    <a href="<?php echo $this->request->base . '/quizzes/publish/' . $quiz['Quiz']['id']; ?>">
                        <i class="material-icons">public</i>
                        <?php echo __d('quiz', 'Publish'); ?>
                        <span class="badge_counter">
                            <i class="material-icons"><?php echo ($bVerifyQuestion && $quiz['Quiz']['published']) ? 'done' : 'https'; ?></i>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>