<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class QuizSettingsController extends QuizAppController {

    public $components = array('QuickSettings');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function admin_index($iId = null) {
        $this->QuickSettings->run($this, array("Quiz"), $iId);
        if (CakeSession::check('Message.flash')) {
            $oMenuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $aMenu = $oMenuModel->findByUrl('/quizzes');
            if ($aMenu) {
                $oMenuModel->id = $aMenu['CoreMenuItem']['id'];
                $oMenuModel->save(array('is_active' => Configure::check('Quiz.quiz_enabled') ? Configure::read('Quiz.quiz_enabled') : 0));
            }
            Cache::clearGroup('menu', 'menu');
        }

        $this->set('title_for_layout', __d('quiz', 'Quizzes Setting'));
    }

}
