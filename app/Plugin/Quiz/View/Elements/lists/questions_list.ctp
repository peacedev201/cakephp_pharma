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

<ul class="quiz-content-list" id="sortable_questions">
<?php if (!empty($questions) && count($questions) > 0) : ?>
    <?php foreach ($questions as $question): ?>
	<li class="full_content p_m_10 item_question" id="<?php echo $question['QuizQuestion']['id']; ?>">
            <a href="javascript:void(0);" class="reorder">
               <img src="<?php echo $this->request->base . '/quiz/img/icon/move.png'?>" class="img_wrapper2 icon-move-reorder"/>
            </a>
            <div class="quiz-info question-info">
                <h3 class="title"><?php echo h($question['QuizQuestion']['title']); ?></h3>
                <?php if(!empty($uid) && (($quiz['Quiz']['user_id'] == $uid) || (!empty($cuser) && $cuser['Role']['is_admin']))): ?>
                <div class="list_option">
                    <div class="dropdown">
                        <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="material-icons">more_vert</i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:void(0);" class="createQuestion" data-quiz_id="<?php echo $quiz['Quiz']['id']; ?>" data-quiz_question_id="<?php echo $question['QuizQuestion']['id']; ?>"><?php echo __d('quiz', 'Edit'); ?></a></li>
                            <li>
                                <?php
                                $this->MooPopup->tag(array(
                                    'href' => $this->request->base . '/quizzes/question/ajax_delete/' . $quiz['Quiz']['id'] . '/' . $question['QuizQuestion']['id'],
                                    'innerHtml'=> __d('quiz', 'Delete'),
                                    'title' => __d('quiz', 'Delete'),
                                    'data-backdrop' => 'static'
                                ));
                                ?>
                            </li>
                            <li class="seperate"></li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                <div class="extra_info">
                    <ul class="answers-content-list create_form">
                        <?php foreach($question['QuizAnswer'] as $aAnswers): ?>
                        <li class="placeholder">
                            <div class="col-md-12 text-left col-result">
                                <?php echo h($aAnswers['title']); ?>
                                <?php if($aAnswers['correct']): ?>
                                <a href="javascript:void(0);" class="tip pull-right" original-title="<?php echo __d('quiz', 'Correct Answer'); ?>"><i class="material-icons">done</i></a>
                                <?php endif; ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
	</li>
    <?php endforeach; ?>
<?php else: ?>
        <li><div class="clear text-center"><?php echo __d('quiz', 'No questions found'); ?></div></li>
<?php endif; ?>
</ul>
