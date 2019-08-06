<?php

class FaqsController extends FaqAppController {

    public $components = array('Paginator');

    public function admin_index($id = null) {
        $this->_checkPermission(array('super_admin' => true));

        $this->loadModel('Faq.Faq');
        $this->loadModel('Role');
        $this->loadModel('Faq.FaqHelpCategorie');

        $this->Faq->setLanguage(Configure::read('Config.language'));
        $this->Paginator->settings = array(
            'limit' => Configure::read('Faq.faq_item_per_pages'),
            'order' => array(
                'Faq.order' => 'DESC'
            )
        );
        $cond = array();
        $data_search = array();

        $this->request->data = array_merge($this->request->data, $this->request->params['named']);
        if (!empty($this->request->data['title'])) {
            $cond['title LIKE'] = '%' . trim($this->request->data['title']) . '%';
            $data_search['title'] = trim($this->request->data['title']);
        }

        $faqs = $this->Paginator->paginate('Faq', $cond);
        $roles = $this->Role->find('all');
//
        $this->set('title_for_layout', __d('faq', 'F.A.Q'));
        $this->set('faqs', $faqs);
        $this->set('roles', $roles);
        $this->set('data_search', $data_search);
    }

    public function admin_visiable() {
        $this->loadModel('Faq.Faq');
        $id = $this->request->data['id'];
        $value = $this->request->data['value'];
        if ($id) {
            $this->Faq->id = $id;
            $this->Faq->save(array('active' => $value));
        }
        die();
    }

    public function admin_create($id = null, $language = null) {
        $this->_checkPermission(array('aco' => 'faq_create'));
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('super_admin' => true));
        $this->loadModel('Faq.Faq');
        $this->loadModel('Faq.FaqHelpCategorie');
        $this->loadModel('Language');

        $is_edit = FALSE;
        if (!empty($id)) {
            $this->loadModel('Mail.Mailtemplate');
            $langs = $this->Language->find('all');

            if (!$language) {
                foreach ($langs as $lang) {
                    $language = $lang['Language']['key'];
                    break;
                }
            }

            $tmp = array();
            foreach ($langs as $lang) {
                $tmp[$lang['Language']['key']] = $lang['Language']['name'];
            }

            $this->set('languages', $tmp);
            $this->set('language', $language);
            $this->Faq->locale = $language;

            $faq = $this->Faq->findById($id);
            $this->_checkExistence($faq);
            $is_edit = TRUE;
        } else {
            $faq = $this->Faq->initFields();
        }

        $this->loadModel('Role');
        $roles = $this->Role->find('all');
        $categories = $this->FaqHelpCategorie->getAllCateChild(0, TRUE, $language);
//        var_dump($categories);die();

        $this->set('title_for_layout', __d('faq', 'F.A.Q'));
        $this->set('faq', $faq);
        $this->set('is_edit', $is_edit);
        $this->set('categories', $categories);
        $this->set('roles', $roles);
    }

    public function admin_save_order() {
        $this->loadModel('Faq.Faq');
        $this->autoRender = false;
        foreach ($this->request->data['faqs'] as $cat_id => $value) {
            $this->Faq->id = $cat_id;
            $this->Faq->save(array('order' => $value));
        }
        //clear cache

        $this->Session->setFlash(__d('faq', 'Order saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        echo $this->referer();
    }

    public function admin_save() {
        $this->autoRender = false;
        $this->_checkPermission(array('aco' => 'faq_create'));
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('super_admin' => true));
//        $this->loadModel('CoreContent');
        $this->loadModel('Faq.FaqHelpCategorie');
        $this->loadModel('Faq.Faq');

        if (empty($this->request->data['title'])) {
            $this->autoRender = false;
            $response['result'] = 0;
            $response['message'] = __d('faq', 'Title is required');
            echo json_encode($response);
            exit();
        }
        if (empty($this->request->data['category_id'])) {
            $this->autoRender = false;
            $response['result'] = 0;
            $response['message'] = __d('faq', 'Category is required');
            echo json_encode($response);
            exit();
        }
        if (empty($this->request->data['body'])) {
            $this->autoRender = false;
            $response['result'] = 0;
            $response['message'] = __d('faq', 'FAQ content is required');
            echo json_encode($response);
            exit();
        }

        $old = 0;
        if (!empty($this->request->data['id'])) {
            $this->Faq->id = $this->request->data['id'];
            $old = 1; //to check if page existed or not
        }

        $permision = implode(',', $_POST['permission']);
        $sub_category = $this->FaqHelpCategorie->getParent($this->request->data['category_id']);

        $data = $this->request->data;
        $data['sub_category_id'] = $sub_category['id'];
        $data['permission'] = $permision;
        $data['user_id'] = $this->Auth->user('id');

        unset($data['title']);
        unset($data['body']);

        $this->Faq->set($data);
        $this->_validateData($this->Faq);
        $this->Faq->save();
        if (!$old) {
            foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                $this->Faq->locale = $lKey;
                $this->Faq->saveField('title', $this->request->data['title']);
                $this->Faq->saveField('body', $this->request->data['body']);
            }
        } else {
            $this->Faq->locale = $this->request->data['language'];
            $this->Faq->saveField('title', $this->request->data['title']);
            $this->Faq->saveField('body', $this->request->data['body']);
        }
        $this->Session->setFlash(__d('faq', 'Faq has been saved!'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        //update category count
        $cate['id'] = $this->request->data['category_id'];
        $cate['faq_count'] = $this->Faq->getTotalFaqsByCategory($this->request->data['category_id']);
        $this->FaqHelpCategorie->save($cate);
        $response['result'] = 1;
        $response['faq_id'] = $this->Faq->id;

        echo json_encode($response);
        exit();
    }

    public function admin_delete($id = null) {
        $this->_checkPermission(array('super_admin' => 1));
        $this->_checkPermission(array('aco' => 'faq_create'));
        $this->_checkPermission(array('confirm' => true));
        $this->loadModel('Faq.Faq');
        $this->loadModel('Faq.FaqResult');

        if ($id) {
            $_POST['faq'] = $id;
        }
        if (!empty($_POST['faq'])) {
            $faqs = $this->Faq->findAllById($_POST['faq']);
            foreach ($faqs as $faq) {
                $cakeEvent = new CakeEvent('Plugin.Controller.Faq.afterDeleteFaq', $this, array('item' => $faq));
                $this->getEventManager()->dispatch($cakeEvent);

                $this->Faq->delete($faq['Faq']['id']);
                $this->FaqResult->deleteResults($faq['Faq']['id']);
            }
            $this->Session->setFlash(__d('faq', 'Faqs have been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }
        $this->redirect('/admin/faq/faqs/index');
    }

//
    public function index() {
        $this->set('title_for_layout', __d('faq', 'Faq Browse Pages'));
        if ($this->isApp()) {
            App::uses('browseFaqWidget', 'Faq.Controller' . DS . 'Widgets' . DS . 'faq');
            $widget = new browseFaqWidget(new ComponentCollection(), null);
            $widget->beforeRender($this);
            App::uses('menuFaqWidget', 'Faq.Controller' . DS . 'Widgets' . DS . 'faq');
            $widget_menu = new menuFaqWidget(new ComponentCollection(), null);
            $widget_menu->beforeRender($this);
        }
    }

    public function browse($category_id = null, $page = null) {
        $faqlModel = MooCore::getInstance()->getModel('Faq.Faq');
        $cateModel = MooCore::getInstance()->getModel('Faq.FaqHelpCategorie');

        $role_id = ROLE_GUEST;
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($viewer)) {
            $role_id = $viewer['User']['role_id'];
        }
        //
        $category_id = 0;
        if (isset($this->request->params['named']['category'])) {
            $category_id = $this->request->params['named']['category'];
        }


        $page = 1;
        if (isset($this->request->params['named']['page']))
            $page = $this->request->params['named']['page'];

        $breadcrum = $cateModel->getBreadcrum($category_id);
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

        $url_more = '/faq/faqs/browse/category:' . $category_id . '/page:' . ($page + 1);

        $this->set('breadcrumb', $breadcrum);
        $this->set('is_view_more', $is_view_more);
        $this->set('url_more', $url_more);
        $this->set('categories', $categories);
        $this->set('floor_category', count($breadcrum));
    }

    public function browsefaq() {
        $faqlModel = MooCore::getInstance()->getModel('Faq.Faq');

        $role_id = ROLE_GUEST;
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($viewer)) {
            $role_id = $viewer['User']['role_id'];
        }
        //
        $category_id = 0;
        if (isset($this->request->params['named']['onlycate'])) {
            $category_id = $this->request->params['named']['onlycate'];
        }

        $page = 1;
        if (isset($this->request->params['named']['page']))
            $page = $this->request->params['named']['page'];

        $total_faq = $faqlModel->getTotalFaqsByCategory($category_id, TRUE, $role_id);
        $faqs = $faqlModel->getFaqsByCategory($category_id, $page, $role_id);
        $limit = Configure::read('Faq.faq_item_per_pages');
        $is_view_more = (($page - 1) * $limit + count($faqs)) < $total_faq;
        $category['Faqs'] = $faqs;
        $category['is_view_all'] = FALSE;
        $category['is_view_more'] = $is_view_more;
        $url_more = '/faq/faqs/browsefaq/onlycate:' . $category_id . '/page:' . ($page + 1);

        $this->set('url_more', $url_more);
        $this->set('category', $category);
    }

    public function view($faq_id = null,$name = null,$is_redirect = FALSE) {
        $this->loadModel('Faq.Faq');
        $this->loadModel('Faq.FaqHelpCategorie');
        $this->loadModel('Faq.FaqResult');

        $uid = $viewer = MooCore::getInstance()->getViewer(TRUE);
        $role_id = ROLE_GUEST;
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($viewer)) {
            $role_id = $viewer['User']['role_id'];
        }
        $faq = $this->Faq->getFaqById($faq_id, TRUE, $role_id);
        if (empty($faq)) {
            $this->Session->setFlash(__d('faq', 'FAQ not found'), 'default', array('class' => 'error-message'));
            $this->redirect('/faqs');
        }
        //check enable category
        $category_enable = $this->FaqHelpCategorie->getEnable($faq['Faq']['category_id']);
        if (!$category_enable) {
            $this->Session->setFlash(__d('faq', 'FAQ not found'), 'default', array('class' => 'error-message'));
            $this->redirect('/faqs');
        }

        MooCore::getInstance()->setSubject($faq);
        $this->set('title_for_layout', htmlspecialchars($faq['Faq']['title']));
        $description = $this->getDescriptionForMeta($faq['Faq']['body']);
        if ($description) {
            $this->set('description_for_layout', $description);
            $this->set('mooPageKeyword', $this->getKeywordsForMeta($description));
        }

        $breadcrum = $this->FaqHelpCategorie->getBreadcrum($faq['Faq']['category_id']);
        $total_vote = (intval($faq['Faq']['total_yes']) + intval($faq['Faq']['total_no']));
        $last_update = $faq['Faq']['modified'];
        $last_result = $this->FaqResult->getLastUpdate($faq_id);
        if (!empty($last_result))
            $last_update = $last_result['FaqResult']['modified'];
        $date = date_create($last_update);
        $result = $this->FaqResult->getResults($faq_id, $uid);
        $choice = 2; //not choise
        $choice_id = 0; //not choise
        if (!empty($result)) {
            $choice = $result[0]['FaqResult']['vote'];
            $choice_id = $result[0]['FaqResult']['helpfull_id'];
        }
        $this->set('faq', $faq);
        $this->set('breadcrum', $breadcrum);
        $this->set('total_vote', $total_vote);
        $this->set('last_update', date_format($date, "d/m/Y"));
        $this->set('choice', $choice);
        $this->set('choice_id', $choice_id);
        $this->set('uid', $uid);
        $this->set('is_submit', $is_redirect);
        if ($this->isApp()) {
            App::uses('similarFaqWidget', 'Faq.Controller' . DS . 'Widgets' . DS . 'faq');
            $widget = new similarFaqWidget(new ComponentCollection(), null);
            $widget->beforeRender($this);
            App::uses('menuFaqWidget', 'Faq.Controller' . DS . 'Widgets' . DS . 'faq');
            $widget_menu = new menuFaqWidget(new ComponentCollection(), null);
            $widget_menu->beforeRender($this);
        }
    }

}
