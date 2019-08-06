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

<?php if (!empty($quiz['Quiz']['id'])): ?>
<?php $this->setNotEmpty('north'); ?>
<?php $this->start('north'); ?>
<?php echo $this->element('sidebar/browse', array('active' => 'basic')); ?>
<?php $this->end(); ?>
<?php endif; ?>

<div class="create_form">
    <div class="bar-content">
        <div class="content_center">
            <div class="box3">
                <form id="createForm">
                    <?php
                    echo $this->Form->hidden('thumbnail', array('value' => $quiz['Quiz']['thumbnail']));
                    echo $this->Form->hidden('id', array('value' => $quiz['Quiz']['id']));
                    ?>
                    <div class="mo_breadcrumb">
                        <?php if (!empty($quiz['Quiz']['id'])): ?>
                            <?php if (!$quiz['Quiz']['published'] || !$quiz['Quiz']['approved']): ?>
                                <a href="<?php echo $this->request->base . '/quizzes/areview/' . $quiz['Quiz']['id']; ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored"><?php echo __d('quiz', 'Review'); ?></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="full_content p_m_10">
                        <div class="form_content">
                            <ul>
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('quiz', 'Title'); ?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->text('title', array('value' => $quiz['Quiz']['title'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>

                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('quiz', 'Category'); ?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->select('category_id', $cats, array('value' => $quiz['Quiz']['category_id'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>

                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('quiz', 'Description'); ?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->tinyMCE('description', array('value' => $quiz['Quiz']['description'], 'id' => 'editor')); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                
                                <li>
                                    <div class="col-md-10 quiz-attachments">
                                        <div id="images-uploader" style="display: none; margin: 5px 0;">
                                            <div id="attachments_upload"></div>
                                            <a href="javascript:void(0)" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="triggerUpload"><?php echo __d('quiz', 'Upload Queued Files'); ?></a>
                                        </div>
                                        <a id="toggleUploader" href="javascript:void(0)"><?php echo __d('quiz', 'Toggle Attachments Uploader'); ?></a>
                                    </div>
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="clear"></div>
                                </li>

                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('quiz', 'Thumbnail'); ?>(<a original-title="<?php echo __d('quiz', 'Thumbnail only display on quiz listing and share quiz to facebook'); ?>" class="tip" href="javascript:void(0);">?</a>)</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div id="quiz_thumnail"></div>
                                        <div id="quiz_thumnail_preview">
                                            <?php if (!empty($quiz['Quiz']['thumbnail'])): ?>
                                            <img width="150" src="<?php echo $quizHelper->getImage($quiz, array('prefix' => '150_square')); ?>" />       
                                            <?php else: ?>
                                            <img width="150" src="" style="display: none;" />
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>

                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('quiz', 'Timer'); ?>(<a original-title="<?php echo __d('quiz', 'Timer format is minutes'); ?>" class="tip" href="javascript:void(0);">?</a>)
                                        </label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->text('timer', array('value' => $quiz['Quiz']['timer'], 'disabled' => empty($quiz['Quiz']['timer']) ? true : false)); ?>
                                        <br />
                                        <?php echo __d('quiz', 'Or'); ?>
                                        <br />
                                        <?php echo $this->Form->checkbox('unlimit_timer', array('value' => 1, 'checked' => !empty($quiz['Quiz']['timer']) ? 0 : 1)); ?> <?php echo __d('quiz', 'Unlimit Timer'); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>

                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('quiz', 'Pass Score'); ?>(<a original-title="<?php echo __d('quiz', 'Pass Score format is percent'); ?>" class="tip" href="javascript:void(0);">?</a>)</label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->text('pass_score', array('value' => $quiz['Quiz']['pass_score'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>

                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('quiz', 'Tags'); ?>(<a original-title="<?php echo __d('quiz', 'Separated by commas or space'); ?>" class="tip" href="javascript:void(0);">?</a>)</label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->text('tags', array('value' => !empty($tags) ? implode(', ', $tags) : '')); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>

                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('quiz', 'Participant Privacy'); ?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php
                                        echo $this->Form->select('privacy', array(
                                            PRIVACY_EVERYONE => __d('quiz', 'Everyone'),
                                            PRIVACY_FRIENDS => __d('quiz', 'Friends Only'),
                                            PRIVACY_ME => __d('quiz', 'Only Me')), array('value' => $quiz['Quiz']['privacy'],
                                            'empty' => false
                                        ));
                                        ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>

                                <li>
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="col-md-10">
                                        <button type="button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored" id="saveBtn"><?php echo __d('quiz', 'Save'); ?></button>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                
                                <li>
                                    <div class="col-md-12">
                                        <div class="error-message" id="errorMessage" style="display:none"></div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooQuiz'), 'object' => array('mooQuiz'))); ?>
mooQuiz.initOnCreate();<?php $this->Html->scriptEnd(); ?>

<script type="text/javascript">
function doRefesh() {
    window.location.reload();
}
</script>