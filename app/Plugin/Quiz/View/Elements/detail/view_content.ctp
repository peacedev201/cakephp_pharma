<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>
<div class="mo_breadcrumb">
    <h1><?php echo h($quiz['Quiz']['title']); ?></h1>
    <div class="list_option">
        <div class="dropdown">
            <button data-toggle="dropdown" data-target="#" id="dropdown-edit">
                <i class="material-icons">more_vert</i>
            </button>
            <ul aria-labelledby="dropdown-edit" class="dropdown-menu" role="menu">
                <?php if ($quiz['Quiz']['user_id'] == $uid || (!empty($cuser) && $cuser['Role']['is_admin'])): ?>
                    <li><a href="<?php echo $this->request->base . '/quizzes/create/' . $quiz['Quiz']['id']; ?>"><?php echo __d('quiz', 'Edit'); ?></a></li>
                    <li><a href="javascript:void(0);" data-id="<?php echo $quiz['Quiz']['id']; ?>" class="deleteQuiz"><?php echo __d('quiz', 'Delete'); ?></a></li>
                    <li class="seperate"></li>
                <?php endif; ?>
                <li>
                    <?php
                    $this->MooPopup->tag(array(
                        'href'=>$this->Html->url(array(
                            "controller" => "reports",
                            "action" => "ajax_create",
                            "plugin" => false,
                            "Quiz_Quiz",
                            $quiz['Quiz']['id']
                        )),
                        'title' => __d('quiz', 'Report'),
                        'innerHtml'=> __d('quiz', 'Report'),
                    ));
                    ?>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="extra_info">
    <?php echo __d('quiz', 'Posted by %s', $this->Moo->getName($quiz['User'], false)); ?>
    <?php echo $this->Moo->getTime($quiz['Quiz']['created'], Configure::read('core.date_format'), $utz); ?>
</div>

<div class="post_content">
    <div><?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags($quiz['Quiz']['description'], Configure::check('Quiz.quiz_enabled_hashtag') ? Configure::read('Quiz.quiz_enabled_hashtag') : 0)); ?></div>            
</div>