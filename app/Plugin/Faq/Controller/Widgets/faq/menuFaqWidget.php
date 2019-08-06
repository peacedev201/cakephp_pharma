<?php

App::uses('Widget', 'Controller/Widgets');

class menuFaqWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $cateModel = MooCore::getInstance()->getModel("Faq.FaqHelpCategorie");
        $faqModel = MooCore::getInstance()->getModel("Faq.Faq");
        $menus = $controller->request->params['named'];
        $category_id = 0;
        if (!empty($menus)) {
            $category_id = $menus['category'];
        }
        $faq_id = 0;
        if (!empty($controller->request->params['pass'][0]))
            $faq_id = $controller->request->params['pass'][0];
        if ($faq_id) {
            $faq = $faqModel->findById($faq_id);
            $category_id = $faq['Faq']['category_id'];
        }
        //check have child category
        $have_child = $cateModel->checkHaveChild($category_id, true);

        if ($have_child) {
            $categories_menu = $cateModel->getAllCateChild($category_id, true, Configure::read('Config.language'));
            $selected_category = $cateModel->findById($category_id);
        } else {
            $parent = $cateModel->getParent($category_id);
            $categories_menu = $cateModel->getAllCateChild($parent['id'], true, Configure::read('Config.language'));
            $selected_category = $cateModel->findById($parent['id']);
        }

        if ($controller->request->is('androidApp') || $controller->request->is('iosApp')) {
            $selected_category = $cateModel->findById($category_id);
            $controller->set('selected_category', $selected_category);
            $controller->set('categories_menu', $categories_menu);
            $controller->set('category_id', $category_id);
        } else {
            $this->setData('selected_category', $selected_category);
            $this->setData('categories_menu', $categories_menu);
            $this->setData('category_id', $category_id);
        }
    }

}