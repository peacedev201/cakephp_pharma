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

<?php if(!empty($bLoadHeader)): ?>
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
                        <a href="javascript:void(0)" data-url="<?php echo $this->request->base . '/quizzes/browse/' . (!empty($cat_id) ? 'category/' . $cat_id : (!empty($type) ? $type : 'all')) . '/latest'; ?>" rel="list-content"><?php echo __d('quiz', 'Latest'); ?></a>
                    </li>
                    <li class="no-current">
                        <a href="javascript:void(0)" data-url="<?php echo $this->request->base . '/quizzes/browse/' . (!empty($cat_id) ? 'category/' . $cat_id : (!empty($type) ? $type : 'all')) . '/taken'; ?>" rel="list-content"><?php echo __d('quiz', 'Taken'); ?></a>
                    </li>
                    <li class="no-current">
                        <a href="javascript:void(0)" data-url="<?php echo $this->request->base . '/quizzes/browse/' . (!empty($cat_id) ? 'category/' . $cat_id : (!empty($type) ? $type : 'all')) . '/like'; ?>" rel="list-content"><?php echo __d('quiz', 'Like'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<ul class="quiz-content-list">
<?php if (!empty($quizzes) && count($quizzes) > 0) : ?>
    <?php foreach ($quizzes as $quiz): ?>
	<li class="full_content p_m_10">
            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
               <img width="140" src="<?php echo $quizHelper->getImage($quiz, array('prefix' => '150_square')); ?>" class="img_wrapper2 thumb_mobile" />
            </a>
            <div class="quiz-info">
                <?php 
                    if(!empty($type) && $type == 'taken'):
                        $sResult = $quizHelper->getResult($quiz, $uid);
                    endif;
                ?>
                <a class="title" href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                    <?php if(!empty($type) && $type == 'taken'): ?><?php echo $sResult; ?> - <?php endif; ?>
                    <?php echo h($quiz['Quiz']['title']); ?>
                </a>
                <?php if(!empty($uid) && (($quiz['Quiz']['user_id'] == $uid) || (!empty($cuser) && $cuser['Role']['is_admin']))): ?>
                <div class="list_option">
                    <div class="dropdown">
                        <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="material-icons">more_vert</i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $this->request->base . '/quizzes/create/' . $quiz['Quiz']['id']; ?>"><?php echo __d('quiz', 'Edit'); ?></a></li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                <div class="extra_info">
                    <?php echo __d('quiz', 'Posted by %s', $this->Moo->getName($quiz['User'], false)); ?>
                    <?php echo $this->Moo->getTime($quiz['Quiz']['created'], Configure::read('core.date_format'), $utz); ?>
                    <?php
                        switch($quiz['Quiz']['privacy']){
                            case PRIVACY_EVERYONE:
                                $icon_class = 'public';
                                $tooltip = __d('quiz', 'Shared with: Everyone');
                                break;
                            case PRIVACY_FRIENDS:
                                $icon_class = 'people';
                                $tooltip = __d('quiz', 'Shared with: Friends Only');
                                break;
                            case PRIVACY_ME:
                                $icon_class = 'lock';
                                $tooltip = __d('quiz', 'Shared with: Only Me');
                                break;
                        }
                    ?>
                    &nbsp;
                    <a class="tip quiz-privacy" href="javascript:void(0);" original-title="<?php echo $tooltip; ?>"> <i class="material-icons"><?php echo  $icon_class ?></i></a>
                </div>
                <div class="status">
                    <?php if(empty($quiz['Quiz']['published'])): ?>
                    <span class="status_error"><?php echo __d('quiz', 'Not Published'); ?></span>
                    <?php endif; ?>
                    <?php if(empty($quiz['Quiz']['approved'])): ?>
                    <span class="status_error"><?php echo __d('quiz', 'Not Approved'); ?></span>
                    <?php endif; ?>
                </div>
                <div class="quiz-description-truncate">
                    <div>
                        <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $quiz['Quiz']['description'])), 200, array('eclipse' => '')), Configure::check('Quiz.quiz_enabled_hashtag') ? Configure::read('Quiz.quiz_enabled_hashtag') : 0); ?>
                    </div>
                    <div class="like-section">
                        <div class="like-action">
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <i class="material-icons">question_answer</i>
                            </a>
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <span><?php echo $quiz['Quiz']['take_count']; ?></span>
                            </a>
                            
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <i class='material-icons'>comment</i>
                            </a>
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <span><?php echo $quiz['Quiz']['comment_count']; ?></span>
                            </a>
                            
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <i class='material-icons'>thumb_up</i>
                            </a>
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <span><?php echo $quiz['Quiz']['like_count']; ?></span>
                            </a>
                            
                            <?php if(empty($hide_dislike)): ?>
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <i class='material-icons'>thumb_down</i>
                            </a>
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <span><?php echo $quiz['Quiz']['dislike_count']; ?></span>
                            </a>
                            <?php endif; ?>
                            
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <i class="material-icons">share</i>
                            </a>
                            <a href="<?php echo $quiz['Quiz']['moo_href']; ?>">
                                <span><?php echo $quiz['Quiz']['share_count']; ?></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
	</li>
    <?php endforeach; ?>
<?php else: ?>
        <li><div class="clear text-center"><?php echo __d('quiz', 'No results found'); ?></div></li>
<?php endif; ?>
        
<?php if (isset($more_url)&& !empty($more_result)): ?>
    <?php $this->Html->viewMore($more_url) ?>
<?php endif; ?>
</ul>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooQuiz"], function(mooQuiz) {
        mooQuiz.initOnListingQuiz();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
mooQuiz.initOnListingQuiz();<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
