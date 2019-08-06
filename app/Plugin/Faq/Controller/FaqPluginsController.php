<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

class FaqPluginsController extends FaqAppController {

    public function admin_delete() {
        $this->loadModel('Faq.Faq');
        $this->loadModel('Faq.FaqResult');
        $this->_checkPermission(array('super_admin' => 1));
        if (!empty($this->request->data['faq'])) {
            $faqs = $this->Faq->findAllById($this->request->data['faq']);
            foreach ($faqs as $faq) {
                $this->Faq->delete($faq['Faq']['id']);
                $this->FaqResult->deleteResults($faq['Faq']['id']);
            }
            $this->Session->setFlash(__d('faq','Faqs have been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $this->redirect($this->referer());
    }
}
