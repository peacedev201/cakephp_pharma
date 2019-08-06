<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div class="bar-content">  
    <div class="content_center">
        <?php if ($aUser['User']['id'] == $uid): ?>
            <div class="quiz_mo_breadcrumb mo_breadcrumb">
                <?php if($type == 'user'): ?>
                <h1><?php echo __d('quiz', 'My Quizzes'); ?></h1>
                <?php else: ?>
                <h1><?php echo __d('quiz', 'My Recent Taken Quizzes'); ?></h1>
                <?php endif; ?>
                <a class="button button-action topButton button-mobi-top" href="<?php echo $this->request->base . '/quizzes/create'; ?>"><?php echo __d('quiz', 'Create New Quiz'); ?></a>
            </div>
            <?php if(!empty($bProfileLoadHeader)): ?>
            <div class="post_body">
                <div class="mo_breadcrumb">
                    <h2>&nbsp;</h2>
                    <div class="quiz_sort list_option">
                        <div class="dropdown">
                            <?php echo __d('quiz', 'View by'); ?>
                            <button id="dropdown-sort" data-target="#" data-toggle="dropdown">
                                <i class="material-icons">expand_more</i>
                            </button>
                            <ul role="menu" class="dropdown-menu browseQuizzes" aria-labelledby="dropdown-sort">
                                <li class="no-current">
                                    <a href="<?php echo $aUser['User']['moo_href']; ?>" data-url="<?php echo $this->request->base . '/quizzes/profile/' . $aUser['User']['id'] . '/user'; ?>" rel="profile-content"><?php echo __d('quiz', 'My Quizzes'); ?></a>
                                </li>
                                <li class="no-current">
                                    <a href="<?php echo $aUser['User']['moo_href']; ?>" data-url="<?php echo $this->request->base . '/quizzes/profile/' . $aUser['User']['id'] . '/taken'; ?>" rel="profile-content"><?php echo __d('quiz', 'My Recent Taken Quizzes'); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div id="list-content">
            <?php echo $this->element('lists/quizzes_list'); ?>
        </div>
    </div>
</div>