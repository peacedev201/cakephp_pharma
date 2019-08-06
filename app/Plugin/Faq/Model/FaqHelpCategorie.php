<?php

App::uses('FaqAppModel', 'Faq.Model');

class FaqHelpCategorie extends FaqAppModel {

//    public $validationDomain = 'FaqHelpCategorie';
    public $mooFields = array('name', 'icon', 'thumb');
    public $actsAs = array(
        'Translate' => array('name' => 'nameTranslation')
    );
    public $recursive = 2;
    private $_default_locale = 'eng';

    public function setLanguage($locale) {
        $this->locale = $locale;
    }

    public $order = array('FaqHelpCategorie.order' => 'DESC', 'FaqHelpCategorie.id' => 'ASC');

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }

    public function getPrivacy($row) {
        if (isset($row['privacy'])) {
            return $row['privacy'];
        }
        return false;
    }

    public function getName($row) {
        if (isset($row['name'])) {
            return $row['name'];
        }
        return false;
    }

    public function getThumb($row) {
        $request = Router::getRequest();
//        if (!empty($row['icon'])) {
//            return $request->base . '/uploads/faqs/' . $row['icon'];
//        } else {
//            return $request->base . '/faq/img/default-category.png';
//        }
        if (!empty($row['icon'])) {
            return $request->base . '/uploads/faqs/' . $row['icon'];
        }
        return null;
    }

    public function getAllCateChild($category_id = 0, $active = false, $language = 'eng', $cate_remove = NULL) {
        $this->setLanguage($language);
        $conditions = array();
        $conditions['FaqHelpCategorie.parent_id'] = $category_id;
        if ($active)
            $conditions['FaqHelpCategorie.active'] = 1;
        if ($cate_remove)
            $conditions['FaqHelpCategorie.id <>'] = $cate_remove;
        $categories = $this->find('all', array('conditions' => $conditions));
        return $categories;
    }

    public function countChildCategory($id = null, $active = false) {
        $conditions = array();
        $conditions['FaqHelpCategorie.parent_id'] = $id;
        if ($active)
            $conditions['FaqHelpCategorie.active'] = 1;
        $data = $this->find('count', array('conditions' => $conditions));
        return $data;
    }

    public function getCategories($category_id, $page_number = null, $language = 'eng', $limit_number = null, $order_type = null) {
        $this->setLanguage($language);
        $conditions['FaqHelpCategorie.active'] = 1;
//        $conditions['FaqHelpCategorie.parent_id'] = $category_id;
//        $conditions['FaqHelpCategorie.id'] = $category_id;
        $conditions['OR'] = array(
            'FaqHelpCategorie.id' => $category_id,
            'FaqHelpCategorie.parent_id' => $category_id,
        );
        $conditions['FaqHelpCategorie.faq_count <>'] = 0;
        $page = 1;
        if (isset($page_number) && $page_number) {
            $page = $page_number;
        }
        $limit = Configure::read('Faq.faq_categories_per_page');
        if (isset($limit_number) && $limit_number) {
            $limit = $limit_number;
        }
        $order = array('FaqHelpCategorie.order DESC');
        if (isset($order_type)) {
            $order = array($order_type);
        }

        return $this->find('all', array(
                    'conditions' => $conditions,
                    'limit' => $limit,
                    'page' => $page,
                    'order' => $order
        ));
    }

    public function getTotalCategories($category_id = null) {
        $cond = array();
        $cond['FaqHelpCategorie.parent_id'] = $category_id;
        $cond['FaqHelpCategorie.active'] = 1;
        $cond['FaqHelpCategorie.faq_count <>'] = 0;
        return $this->find('count', array('conditions' => $cond));
    }

    public function checkHaveChild($category_id, $active = false) {
        $cond = array();
        $cond['FaqHelpCategorie.parent_id'] = $category_id;
        if ($active)
            $cond['FaqHelpCategorie.active'] = 1;
        $data = $this->find('count', array('conditions' => $cond));
        if ($data == '0') {
            return false;
        }
        return true;
    }

    public function checkCateHaveFaq($category_id) {
        $faqModel = MooCore::getInstance()->getModel('Faq.Faq');
        $cate = $faqModel->getByCategoryId($category_id);
        $sub_cate = $faqModel->getBySubCategoryId($category_id);
        if (!empty($cate) || !empty($sub_cate)) {
            return true;
        }
        return false;
    }

    public function getParent($category_id) {
        $data = $this->findById($category_id);
        if (empty($data)) {
            $result['id'] = 0;
            $result['name'] = __d('faq', 'Home');
            return $result;
        }
        $data = $this->findById($data['FaqHelpCategorie']['parent_id']);
        $result = array();
        if (!empty($data)) {
            $result['id'] = $data['FaqHelpCategorie']['id'];
            $result['name'] = $data['FaqHelpCategorie']['name'];
        } else {
            $result['id'] = 0;
            $result['name'] = __d('faq', 'Home');
        }
        return $result;
    }

    public function getCategoryById($category_id = 0, $active = false, $language = 'eng') {
        $this->setLanguage($language);
        $conditions = array();
        $conditions['FaqHelpCategorie.id'] = $category_id;
        if ($active)
            $conditions['FaqHelpCategorie.active'] = 1;
        $category = $this->find('first', array('conditions' => $conditions));
        return $category;
    }

    public function getActiveOfParent($category_id) {
        $category = $this->findById($category_id);
        if ($category && $category['FaqHelpCategorie']['parent_id'] != 0) {
            $category_parent = $this->findById($category['FaqHelpCategorie']['parent_id']);
            if ($category_parent) {
                if ($category_parent['FaqHelpCategorie']['active'])
                    return true;
            }
        }
        return false;
    }

    public function setActiveAllChild($category_id, $active) {
        $categories = $this->getAllCateChild($category_id, false);
        foreach ($categories as $category) {
            $this->clear();
            $data['id'] = $category['FaqHelpCategorie']['id'];
            $data['active'] = $active;
            $this->save($data);
        }
        return true;
    }

    public function updateCounter($category_id, $field = 'comment_count', $conditions = '',$model = 'Comment') {
        if ($category_id) {
            $data['id'] = $category_id;
            $data['count'] = $this->countChildCategory($category_id, false);
            $this->save($data);
        }
    }

    public function getBreadcrum($category_id) {
        $breadcrumb = array();
        $numOfBredcrum = $this->getLevelCategory($category_id);
        $data['name'] = __d('faq', 'Home');
        $data['link'] = "/faqs";
        $breadcrumb[0] = $data;
        if ($numOfBredcrum == 1) {
            $item = $this->findById($category_id);
            $data['name'] = $item['FaqHelpCategorie']['name'];
            $data['link'] = "/faq/faqs/index/category:" . $item['FaqHelpCategorie']['id'];
            $breadcrumb[1] = $data;
        }
        if ($numOfBredcrum == 2) {
            $item_parent = $this->getParent($category_id);
            $data['name'] = $item_parent['name'];
            $data['link'] = "/faq/faqs/index/category:" . $item_parent['id'];
            $breadcrumb[1] = $data;
            $item = $this->findById($category_id);
            $data['name'] = $item['FaqHelpCategorie']['name'];
            $data['link'] = "/faq/faqs/index/category:" . $item['FaqHelpCategorie']['id'];
            $breadcrumb[2] = $data;
        }
        return $breadcrumb;
    }

    public function getLevelCategory($category_id) {
        if ($category_id == 0)
            return 0;
        $cate = $this->findById($category_id);
        if (!empty($cate)) {
            if ($cate['FaqHelpCategorie']['parent_id'] == 0) {
                return 1;
            } else {
                return 2;
            }
        }
        return 0;
    }

    //return false is disable, true is enable
    public function getEnable($category_id = null) {
        $cate = $this->findById($category_id);
        if (!empty($cate)) {
            if ($cate['FaqHelpCategorie']['active'])
                return true;
        }
        return false;
    }

}
