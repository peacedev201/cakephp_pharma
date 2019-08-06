<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>
<div class="content_center_home">
    <?php if(!empty($bHomeLoadHeader)): ?>
    <div class="quiz_mo_breadcrumb mo_breadcrumb">
        <h1><?php echo __d('quiz', 'My Quizzes'); ?></h1>
        <a class="button button-action topButton button-mobi-top" href="<?php echo $this->request->base . '/quizzes/create'; ?>"><?php echo __d('quiz', 'Create New Quiz'); ?></a>
    </div>
    <div class="post_body">
        <div class="mo_breadcrumb">
            <h2>&nbsp;</h2>
            <div class="quiz_sort list_option">
                <div class="dropdown">
                    <?php echo __d('quiz', 'Sort by'); ?>
                    <button id="dropdown-sort" data-target="#" data-toggle="dropdown">
                        <i class="material-icons">expand_more</i>
                    </button>
                    <ul role="menu" class="dropdown-menu browseQuizzes" aria-labelledby="dropdown-sort">
                        <li class="no-current">
                            <a href="<?php echo $this->request->base . '/home/index/tab:my-taken'; ?>" data-url="<?php echo $this->request->base . '/quizzes/browse/home/latest'; ?>" rel="list-content"><?php echo __d('quiz', 'Latest'); ?></a>
                        </li>
                        <li class="no-current">
                            <a href="<?php echo $this->request->base . '/home/index/tab:my-taken'; ?>" data-url="<?php echo $this->request->base . '/quizzes/browse/home/taken'; ?>" rel="list-content"><?php echo __d('quiz', 'Taken'); ?></a>
                        </li>
                        <li class="no-current">
                            <a href="<?php echo $this->request->base . '/home/index/tab:my-taken'; ?>" data-url="<?php echo $this->request->base . '/quizzes/browse/home/like'; ?>" rel="list-content"><?php echo __d('quiz', 'Like'); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div id="list-content">
        <?php echo $this->element('lists/quizzes_list'); ?>
    </div>
</div>
