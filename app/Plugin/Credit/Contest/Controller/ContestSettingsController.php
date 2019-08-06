<?php

class ContestSettingsController extends ContestAppController {

    public $components = array('QuickSettings');

    public function admin_index($id = null) {
        $this->QuickSettings->run($this, array("Contest"), $id);
        if (CakeSession::check('Message.flash')) {
            $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $menu = $menuModel->findByUrl('/contests');
            if ($menu) {
                $menuModel->id = $menu['CoreMenuItem']['id'];
                $menuModel->save(array('is_active' => Configure::read('Contest.contest_enabled')));
            }
            Cache::clearGroup('menu', 'menu');
        }
        $this->set('title_for_layout', __d('contest', 'Contest Settings'));
    }

    public function admin_credit_integration() {
        $this->loadModel('Contest.ContestSetting');
        if ($this->request->is('post')) {
            $values = $this->request->data;
            $this->ContestSetting->updateSettings('contest_integrate_credit', $values['contest_integrate_credit']);
            // check plguin credit
            if ($values['contest_integrate_credit'] == 1) {
                $this->ContestSetting->integrateCredit();
            }
            $this->Session->setFlash(__d('contest', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $value = 0;
        $value = $this->ContestSetting->getValueSetting('contest_integrate_credit');
        $this->set('value', $value);

        $credit_enable = Configure::read('Credit.credit_enabled');
        $this->set('credit_enable', $credit_enable);

        Cache::clearGroup('contest', 'contest');
        $this->set('title_for_layout', __d('contest', 'Credits Integration'));
    }

}
