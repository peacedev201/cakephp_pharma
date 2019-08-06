<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div id="profile-content-wrap" class="bar-content">  
    <div class="content_center">
        <?php if ($aUser['User']['id'] == $uid): ?>
            <div class="quiz_mo_breadcrumb mo_breadcrumb">
                <div class="pull-left">
                    <?php if($type == 'user'): ?>
                    <h1><?php echo __d('quiz', 'My Quizzes'); ?></h1>
                    <?php else: ?>
                    <h1><?php echo __d('quiz', 'My Recent Taken Quizzes'); ?></h1>
                    <?php endif; ?>
                </div>
                
                <div class="pull-right">
                    <a class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="<?php echo $this->request->base . '/quizzes/create'; ?>"><?php echo __d('quiz', 'Create New Quiz'); ?></a>
                </div>
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
                                    <a href="javascript:void(0)" data-url="<?php echo $this->request->base . '/quizzes/profile/' . $aUser['User']['id'] . '/user'; ?>" rel="profile-content"><?php echo __d('quiz', 'My Quizzes'); ?></a>
                                </li>
                                <li class="no-current">
                                    <a href="javascript:void(0)" data-url="<?php echo $this->request->base . '/quizzes/profile/' . $aUser['User']['id'] . '/taken'; ?>" rel="profile-content"><?php echo __d('quiz', 'My Recent Taken Quizzes'); ?></a>
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
    
    <script type="text/javascript">
        function doRefesh() {
            window.location.reload();
        }
    </script>
    
    <?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["mooQuiz"], function(mooQuiz) {
            mooQuiz.initOnViewAppProfile();
        });
    </script>
    <?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
    mooQuiz.initOnViewAppProfile();<?php $this->Html->scriptEnd(); ?>
    <?php endif; ?>
</div>