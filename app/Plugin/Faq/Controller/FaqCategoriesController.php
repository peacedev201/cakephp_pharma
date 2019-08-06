<?php

class FaqCategoriesController extends FaqAppController {

    public $components = array('Paginator');

    public function admin_index($id = null) {
        $this->_checkPermission(array('super_admin' => true));
        $this->loadModel('Faq.FaqHelpCategorie');

        $this->Paginator->settings = array(
            'limit' => Configure::read('Faq.faq_item_per_pages'),
            'order' => array(
                'FaqHelpCategorie.order' => 'DESC'
            )
        );
        $cond = array();
        $data_search = array();
        //
        $this->request->data = array_merge($this->request->data, $this->request->params['named']);

        if (!empty($this->request->data['parent_id'])) {
            $cond['parent_id'] = $this->request->data['parent_id'];
            $data_search['parent_id'] = $this->request->data['parent_id'];
        } else {
            $cond['parent_id'] = 0;
            $data_search['parent_id'] = 0;
        }

        $categories = $this->Paginator->paginate('FaqHelpCategorie', $cond);

        $this->set('title_for_layout', __d('faq', "F.A.Q Categories Manager"));
        $this->set('data_search', $data_search);
        $this->set('categories', $categories);
    }

    public function admin_ajax_translate($id) {
        $this->_checkPermission(array('super_admin' => true));
        $this->loadModel('Faq.FaqHelpCategorie');
        $this->loadModel('Language');
        if (!empty($id)) {
            $category = $this->FaqHelpCategorie->findById($id);
            $this->set('category', $category);
            $this->set('languages', $this->Language->getLanguages());
        }
    }

    public function admin_save_order() {
        $this->loadModel('Faq.FaqHelpCategorie');
        $this->autoRender = false;
        foreach ($this->request->data['cate'] as $cat_id => $value) {
            $this->FaqHelpCategorie->id = $cat_id;
            $this->FaqHelpCategorie->save(array('order' => $value));
        }
        $this->Session->setFlash(__d('faq', 'Order saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        echo $this->referer();
    }

    public function admin_ajax_translate_save() {
        $this->autoRender = false;
        $this->loadModel('Faq.FaqHelpCategorie');
        $response['result'] = 0;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                // we are going to save the german version
                $this->FaqHelpCategorie->id = $this->request->data['id'];
                foreach ($this->request->data['name'] as $lKey => $sContent) {
                    $this->FaqHelpCategorie->locale = $lKey;
                    if ($this->FaqHelpCategorie->saveField('name', $sContent)) {
                        $response['result'] = 1;
                    }
                }
            }
        }
        echo json_encode($response);
        exit();
    }

    public function admin_create_category($id = null) {
        $this->_checkPermission(array('aco' => 'faq_create'));
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('super_admin' => true));

        $this->loadModel('Faq.FaqHelpCategorie');

        if (!empty($id)) {
            $category = $this->FaqHelpCategorie->findById($id);
            $all_categories = $this->FaqHelpCategorie->getAllCateChild(0, true, Configure::read('Config.language'), $id);
            $bIsEdit = true;
        } else {
            $category = $this->FaqHelpCategorie->initFields();
            $all_categories = $this->FaqHelpCategorie->getAllCateChild(0, true);
            $bIsEdit = false;
        }

        $this->set('category', $category);
        $this->set('bIsEdit', $bIsEdit);
        $this->set('all_categories', $all_categories);
    }

    public function admin_savecategory() {
        $this->loadModel('Faq.FaqHelpCategorie');
        $this->_checkPermission(array('aco' => 'faq_create'));
        $this->_checkPermission(array('confirm' => true));
        $this->_checkPermission(array('super_admin' => true));
        $this->autoRender = false;
        if (empty($this->request->data['name'])) {
            $this->autoRender = false;
            $response['result'] = 0;
            $response['message'] = __d('faq', 'Name is required');
            echo json_encode($response);
            exit();
        }
        //add new
        $data['parent_id'] = $this->request->data['parent_id'];
//        $data['name'] = $this->request->data['name'];
        $data['active'] = $this->request->data['active'];
        $data['icon'] = $this->request->data['icon'];
        unset($data['name']);
        $old = 0;
        if (!empty($this->request->data['id'])) {
            $data['id'] = $this->request->data['id'];
            $old_category = $this->FaqHelpCategorie->findById($data['id']);
            $old = 1;
        }
        if ($this->FaqHelpCategorie->save($data)) {
        	if (isset($old_category) && $old_category['FaqHelpCategorie']['icon'] != $data['icon'])
        	{
        		$objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
        		$objectModel->destroy($old_category['FaqHelpCategorie']['id'],'fa_category_icon');
        	}
            //translate
            if (!$old) {
                foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                    $this->FaqHelpCategorie->locale = $lKey;
                    $this->FaqHelpCategorie->saveField('name', $this->request->data['name']);
                }
            } else {
                $this->FaqHelpCategorie->saveField('name', $this->request->data['name']);
            }
            $this->FaqHelpCategorie->clear();
            //update counter for parent category
            $this->FaqHelpCategorie->updateCounter($data['parent_id']);
            $response['result'] = 1;
            echo json_encode($response);
            exit();
        } else {
            $response['result'] = 0;
            $response['message'] = __d('faq', 'Something is wrong!');
            echo json_encode($response);
            exit();
        }
    }

    public function admin_visiable() {
        $this->loadModel('Faq.FaqHelpCategorie');
        $id = $this->request->data['id'];
        $value = $this->request->data['value'];
        if (!empty($id)) {
            $level_category = $this->FaqHelpCategorie->getLevelCategory($id);
            if ($level_category == 1) {
                $this->FaqHelpCategorie->id = $id;
                $this->FaqHelpCategorie->save(array('active' => $value));
                $this->FaqHelpCategorie->clear();
                $this->FaqHelpCategorie->setActiveAllChild($id, $value);
            } else if ($level_category == 2) {
                //check parent active
                $active = $this->FaqHelpCategorie->getActiveOfParent($id);
                if (!$active) {
                    $response['result'] = 0;
                    echo json_encode($response);
                    die();
                } else {
                    $this->FaqHelpCategorie->id = $id;
                    $this->FaqHelpCategorie->save(array('active' => $value));
                    $this->FaqHelpCategorie->clear();
                }
            }
        }
        $response['result'] = 1;
        echo json_encode($response);
        die();
    }

    public function upload_icon($max_width = null, $max_height = null) {
        $this->autoRender = false;
        // save this picture to album

        $path = 'uploads' . DS . 'faqs';
        $url = 'uploads/faqs/';

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            $result['url'] = $url;
            $result['path'] = $this->request->base . '/uploads/faqs/' . $result['filename'];
            $result['file'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }

    public function admin_delete_category($id) {
        $this->_checkPermission(array('super_admin' => 1));
        $this->loadModel('Faq.FaqHelpCategorie');
        $this->autoRender = false;
        //check faq in category
        $check_exist_faq = $this->FaqHelpCategorie->checkCateHaveFaq($id);
        if ($check_exist_faq) {
            $this->Session->setFlash(__d('faq', 'Cant not delete category ! Please delete faqs first !'), 'default', array('class' => 'error-message'));
            $this->redirect($this->referer());
        }
        //check category have child category
        $check_exist_child = $this->FaqHelpCategorie->checkHaveChild($id);
        if ($check_exist_child) {
            $this->Session->setFlash(__d('faq', 'Cant not delete this category. Please delete child categories first!'), 'default', array('class' => 'error-message'));
            $this->redirect($this->referer());
        }
        $data = $this->FaqHelpCategorie->findById($id);
        $this->FaqHelpCategorie->clear();
        $this->FaqHelpCategorie->delete($id);
        $this->FaqHelpCategorie->clear();
        $this->Session->setFlash(__d('faq', 'Category is deleted!'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        if ($data['FaqHelpCategorie']['parent_id'] != '0') {
            //update counter for parent category
            $this->FaqHelpCategorie->updateCounter($data['FaqHelpCategorie']['parent_id']);
            $this->redirect($this->referer());
        } else {
            $this->redirect('/admin/faq/faq_categories/');
        }
    }

}