<?php

App::uses('Widget', 'Controller/Widgets');

class browseFaqWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $faqlModel = MooCore::getInstance()->getModel('Faq.Faq');
        $cateModel = MooCore::getInstance()->getModel('Faq.FaqHelpCategorie');

        $role_id = ROLE_GUEST;
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($viewer)) {
            $role_id = $viewer['User']['role_id'];
        }
        //search 
        $is_search = FALSE;
        $category = array();
        if ($_GET) {
            $dataSerch = isset($_GET['content_search'])?$_GET['content_search']:null;
            if (!empty($dataSerch) && $dataSerch !== null) {
                $is_search = true;
            }
        }
        if ($is_search) {
            $category['Faqs'] = $faqlModel->findFaqByTitle($_GET['content_search'], $role_id);
            $category['is_view_all'] = FALSE;
            $category['is_view_more'] = FALSE;
        }
        //
        $category_id = 0;
        if (isset($controller->request->params['named']['category'])) {
            $category_id = $controller->request->params['named']['category'];
        }
        $type = NULL;
        if (isset($controller->request->params['named']['type']))
            $type = $controller->request->params['named']['type'];

        $page = 1;
        if (isset($controller->request->params['named']['page']))
            $page = $controller->request->params['named']['page'];

        $only_cate = false;
        if (isset($controller->request->params['named']['onlycate']))
            $only_cate = $controller->request->params['named']['onlycate'];
        $breadcrum = $cateModel->getBreadcrum($category_id);
        
        if (!$only_cate) {
            if ($type == 'all') {
                $categories[0] = $cateModel->getCategoryById($category_id, true, Configure::read('Config.language'));
                $cate_id = $categories[0]['FaqHelpCategorie']['id'];
                $total_faq = $faqlModel->getTotalFaqsByCategory($cate_id, TRUE, $role_id);
                $faqs = $faqlModel->getAllFaqsByCategory($cate_id, true, $role_id);
                $categories[0]['Faqs'] = $faqs;
                $categories[0]['is_view_all'] = FALSE;
                $is_view_more = FALSE;
            } else {
                //categories
                $categories = $cateModel->getCategories($category_id, $page, Configure::read('Config.language'));
                for ($i = 0; $i < count($categories); $i++) {
                    $cate_id = $categories[$i]['FaqHelpCategorie']['id'];
                    $total_faq = $faqlModel->getTotalFaqsByCategory($cate_id, TRUE, $role_id);
                    $faqs = $faqlModel->getFaqsByCategory($cate_id, 1, $role_id);
                    $is_view_all = count($faqs) < $total_faq;
                    $categories[$i]['Faqs'] = $faqs;
                    $categories[$i]['is_view_all'] = $is_view_all;
                }
                $total = $cateModel->getTotalCategories($category_id);
                $limit = Configure::read('Faq.faq_categories_per_page');
                $is_view_more = (($page - 1) * $limit + count($categories)) < $total;
            }            

            $url_more = '/faq/faqs/browse/category:' . $category_id . '/page:2';
        } else {
            $categories[0] = $cateModel->getCategoryById($category_id, true, Configure::read('Config.language'));
            $total_faq = $faqlModel->getTotalFaqsByCategory($category_id, TRUE, $role_id);
            $faqs = $faqlModel->getFaqsByCategory($category_id, 1, $role_id);
            $limit = Configure::read('Faq.faq_item_per_pages');
            $is_view_more = (($page - 1) * $limit + count($faqs)) < $total_faq;
            $categories[0]['Faqs'] = $faqs;
            $categories[0]['is_view_all'] = FALSE;
            $categories[0]['is_view_more'] = $is_view_more;
            $url_more = '/faq/faqs/browsefaq/category:' . $category_id . '/onlycate:' . $only_cate . '/page:2';
            $is_view_more = FALSE;
        }
         if ($controller->request->is('androidApp') || $controller->request->is('iosApp')) {
             $is_app = true;
         }else{
             $is_app = false;
         }
        if ($controller->request->is('androidApp') || $controller->request->is('iosApp')) {
            $controller->set('breadcrumb', $breadcrum);
            $controller->set('is_search', $is_search);
            $controller->set('is_view_more', $is_view_more);
            $controller->set('url_more', $url_more);
            $controller->set('categories', $categories);
            $controller->set('is_search', $is_search);
            $controller->set('category', $category);
            $controller->set('floor_category', count($breadcrum));
            $controller->set('is_app', $is_app);
        } else {
            $this->setData('breadcrumb', $breadcrum);
            $this->setData('is_search', $is_search);
            $this->setData('is_view_more', $is_view_more);
            $this->setData('url_more', $url_more);
            $this->setData('categories', $categories);
            $this->setData('is_search', $is_search);
            $this->setData('category', $category);
            $this->setData('floor_category', count($breadcrum));
            $this->setData('is_app', $is_app);
        }
    }

}