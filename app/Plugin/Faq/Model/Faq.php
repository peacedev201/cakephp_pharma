<?php

App::uses('FaqAppModel', 'Faq.Model');

class Faq extends FaqAppModel {

//    public $validationDomain = 'faq';
    //public $belongsTo = array('User', 'FaqHelpCategories' => array('className' => 'FaqHelpCategories', 'foreignKey' => 'category_id'));
    public $belongsTo = array('User');
    public $mooFields = array('title', 'body', 'plugin', 'type', 'url', 'thumb', 'privacy', 'body', 'href');
    public $hasMany = array('Comment' => array(
            'className' => 'Comment',
            'foreignKey' => 'target_id',
            'conditions' => array('Comment.type' => 'Faq_Faq'),
            'dependent' => true
        )
    );
    public $actsAs = array(
        'Translate' => array(
            'title' => 'titleTranslation',
            'body' => 'bodyTranslation'
        )
    );
    private $_default_locale = 'eng';

    public function setLanguage($locale) {
        $this->locale = $locale;
    }

    public $order = 'Faq.order DESC';
    public $validate = array(
        'title' => array(
            'rule' => 'notBlank',
            'message' => 'Title is required',
        ),
        'body' => array(
            'rule' => 'notBlank',
            'message' => 'Body is required',
        ),
        'category_id' => array(
            'rule' => 'notBlank',
            'message' => 'Category is required',
        ),
        'sub_category_id' => array(
            'rule' => 'notBlank',
            'message' => 'Sub Category is required',
        ),
        'privacy' => array(
            'rule' => 'notBlank',
            'message' => 'Privacy is required',
        ),
        'alow_comemnt' => array(
            'rule' => 'notBlank',
            'message' => 'alow comemnt is required',
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }

    public function getTotalFaqsByCategory($category_id = null, $active = false, $role_id = false) {
        $cond = array();
        $cond['Faq.category_id'] = $category_id;
        if ($role_id)
            $cond['Faq.permission LIKE'] = '%' . $role_id . '%';
        if ($active)
            $cond['Faq.active'] = 1;
        return $this->find('count', array('conditions' => $cond));
    }

    public function getThumb($row) {
        return 'thumbnail';
    }

    public function getTitle(&$row) {
        if (isset($row['title'])) {
            return $row['title'];
        }
        return '';
    }

    public function getBody($row) {
        if (isset($row['body'])) {
            return $row['body'];
        }
        return '';
    }

    public function getHref($row) {
        $request = Router::getRequest();
        if (isset($row['id']))
            return $request->base . '/faqs/view/' . $row['id'] . '/' . seoUrl($row['moo_title']);
        return false;
    }

    public function updateCounter($id, $field = 'comment_count', $conditions = '', $model = 'Comment') {
        if (empty($conditions)) {
            $conditions = array('Comment.type' => 'Faq_Faq', 'Comment.target_id' => $id);
        }
        parent::updateCounter($id, $field, $conditions, $model);
    }

    public function getByCategoryId($category_id = 0) {
        $cond = array('Faq.category_id' => $category_id);
        return $this->find('all', array('conditions' => $cond));
    }

    public function getBySubCategoryId($parent_category_id = 0) {
        $cond = array('Faq.sub_category_id' => $parent_category_id);
        return $this->find('all', array('conditions' => $cond));
    }

    public function getFaqsByCategory($category_id, $page_number = null, $role_id = false, $limit_number = null, $order_type = null,$remove_id = null) {
        $conditions['Faq.active'] = 1;
        $conditions['Faq.category_id'] = $category_id;
        if ($role_id)
            $conditions['Faq.permission LIKE'] = '%' . $role_id . '%';
        $page = 1;
        if (isset($page_number) && $page_number) {
            $page = $page_number;
        }
        $limit = Configure::read('Faq.faq_item_per_pages');
        if (isset($limit_number) && $limit_number) {
            $limit = $limit_number;
        }
        $order = array('Faq.order desc');
        if (isset($order_type)) {
            $order = array($order_type);
        }
        if($remove_id)
            $conditions['Faq.id !='] = $remove_id;
        return $this->find('all', array(
                    'conditions' => $conditions,
                    'limit' => $limit,
                    'page' => $page,
                    'order' => $order
        ));
    }
    
    public function getAllFaqsByCategory($category_id, $active = false, $role_id = false) {
        if ($active)
            $conditions['Faq.active'] = 1;
        if ($role_id)
            $conditions['Faq.permission LIKE'] = '%' . $role_id . '%';
        $conditions['Faq.category_id'] = $category_id;
        return $this->find('all', array('conditions' => $conditions));
    }

    public function findFaqByTitle($keyword, $role_id = false) {
        $conditions['Faq.title LIKE'] = '%' . $keyword . '%';
        $conditions['Faq.active'] = 1;
        if ($role_id)
            $conditions['Faq.permission LIKE'] = '%' . $role_id . '%';
        return $this->find('all', array('conditions' => $conditions));
    }
     public function getFaqById($faq_id, $active= false, $role_id = false) {
        $conditions['Faq.id'] = $faq_id;
        if ($active)
            $conditions['Faq.active'] = 1;
        if ($role_id)
            $conditions['Faq.permission LIKE'] = '%' . $role_id . '%';
        return $this->find('first', array('conditions' => $conditions));
    }
}
