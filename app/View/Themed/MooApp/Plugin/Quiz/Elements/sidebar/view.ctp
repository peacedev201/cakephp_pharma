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

<div style="padding-bottom: 0px;">
    <div style="position: relative;">
        <div>
            <div style="vertical-align: top; max-width: 100%; min-width: 100%; width: 100%; background-image: url('<?php echo $quizHelper->getImage($quiz, array('prefix' => '300_square')); ?>'); background-repeat: no-repeat; padding-bottom: 56.25%; display: block; background-size: cover; background-position: center center;"></div>
        </div>
    </div>
    <div class="loadQuizView" style="display: flex; margin: 0px 0px 10px; border-bottom: 1px solid rgb(218, 221, 225);">
        <div style="font-size: 14px; display: block; font-family: Roboto, sans-serif; background: rgb(255, 255, 255); height: 48px; position: relative; flex: 1 1 0%;">
            <div class="app-load-quiz-view current" style="color: rgba(0, 0, 0, 0.87); background-color: rgb(255, 255, 255); transition: all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms; box-sizing: border-box; font-family: Roboto, sans-serif; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); box-shadow: none; border-radius: 2px; display: inline-block; min-width: 100%;">
                <a id="quizDetail" href="javascript:void(0)" data-url="<?php echo $this->request->base . '/quizzes/view_detail/' . $quiz['Quiz']['id']; ?>" rel="quiz-content" tabindex="0" type="button" style="border: 10px; box-sizing: border-box; display: inline-block; font-family: Roboto, sans-serif; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); cursor: pointer; text-decoration: none; margin: 0px; padding: 0px; outline: none; font-size: inherit; position: relative; height: auto; line-height: 36px; width: 100%; border-radius: 2px; transition: all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms; background-color: rgb(255, 255, 255); text-align: center; box-shadow: none;">
                    <span style="position: relative; opacity: 1; font-size: 16px; letter-spacing: 0px; text-transform: none; margin: 0px; padding: 0px; color: rgb(0, 0, 0); text-align: left; display: block;">
                        <span style="display: block; text-align: center; height: 48px; line-height: 48px;">
                            <?php echo __d('quiz', 'Detail'); ?>
                        </span>
                    </span>
                </a>
            </div>
        </div>
        <div style="font-size: 14px; display: block; font-family: Roboto, sans-serif; background: rgb(255, 255, 255); height: 48px; position: relative; flex: 1 1 0%;">
            <div class="app-load-quiz-view" style="color: rgba(0, 0, 0, 0.87); background-color: rgb(255, 255, 255); transition: all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms; box-sizing: border-box; font-family: Roboto, sans-serif; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); box-shadow: none; border-radius: 2px; display: inline-block; min-width: 100%;">
                <a id="quizParticipant" href="javascript:void(0)" data-url="<?php echo $this->request->base . '/quizzes/view_participant/' . $quiz['Quiz']['id']; ?>" rel="quiz-content" style="border: 10px; box-sizing: border-box; display: inline-block; font-family: Roboto, sans-serif; -webkit-tap-highlight-color: rgba(0, 0, 0, 0); cursor: pointer; text-decoration: none; margin: 0px; padding: 0px; outline: none; font-size: inherit; position: relative; height: auto; line-height: 36px; width: 100%; border-radius: 2px; transition: all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms; background-color: rgb(255, 255, 255); text-align: center; box-shadow: none;">
                    <span style="position: relative; opacity: 1; font-size: 16px; letter-spacing: 0px; text-transform: none; margin: 0px; padding: 0px; color: rgb(0, 0, 0); text-align: left; display: block;">
                        <span style="display: block; text-align: center; height: 48px; line-height: 48px;">
                            <?php echo __d('quiz', 'Participant'); ?>
                        </span>
                    </span>
                </a>
            </div>
        </div>             
    </div>                            
</div>