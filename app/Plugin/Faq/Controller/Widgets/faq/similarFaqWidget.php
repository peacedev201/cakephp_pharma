<?php

App::uses('Widget', 'Controller/Widgets');

class similarFaqWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $faqlModel = MooCore::getInstance()->getModel('Faq.Faq');

        $role_id = ROLE_GUEST;
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($viewer)) {
            $role_id = $viewer['User']['role_id'];
        }
        $faq_id = $controller->request->params['pass'][0];
        $faq = $faqlModel->getFaqById($faq_id, true, $role_id);
        $category_id = $faq['Faq']['category_id'];

        $faqs = $faqlModel->getFaqsByCategory($category_id, 1, $role_id,null,null,$faq_id);
        $category['Faqs'] = $faqs;
        $category['is_view_all'] = FALSE;
        $category['is_view_more'] = FALSE;
        if ($controller->request->is('androidApp') || $controller->request->is('iosApp')) {
            $controller->set('category_similar', $category);
        } else {
            $this->setData('category_similar', $category);
        }
    }

}