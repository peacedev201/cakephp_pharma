<?php

class StickerCategory extends StickerAppModel {

    public $actsAs = array(
        'Translate' => array('name' => 'nameTranslation')
    );
    public $validate = array(
        'icon' => array(
            'rule' => 'notBlank',
            'message' => 'Icon is required'
        ),
        'background_color' => array(
            'rule' => 'notBlank',
            'message' => 'Background color is required'
        ),
        'name' => array(
            'rule' => 'notBlank',
            'message' => 'Name is required'
        ),
    );

    public function setLanguage($locale)
    {
        $this->locale = $locale;
    }

    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }

    public function beforeSave($options = array())
    {
        parent::beforeSave($options);
        foreach ($this->actsAs['Translate'] as $field => $item)
        {
            $this->data['StickerCategory']['trans_' . $field] = $this->data['StickerCategory'][$field];
            $this->data['StickerCategory'][$field] = reset($this->data['StickerCategory'][$field]);
        }
    }

    public function afterSave($created, $options = array())
    {
        parent::afterSave($created, $options);

        //save multi language
        foreach ($this->actsAs['Translate'] as $field => $item)
        {
            $data = !empty($this->data['StickerCategory']['trans_' . $field]) ? $this->data['StickerCategory']['trans_' . $field] : null;
            $this->saveMultiLanguage($data, $field, $this->data['StickerCategory']['id']);
        }
    }

    public function isStickerCategoryExist($id, $enable = null)
    {
        $cond = array(
            'StickerCategory.id' => $id
        );
        if (is_bool($enable))
        {
            $cond['StickerCategory.enable'] = $enable;
        }
        return $this->hasAny($cond);
    }

    function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StickerCategory.' . $task => $value
                ), array(
            'StickerCategory.id' => $id,
        ));
    }

    function saveOrdering($id, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StickerCategory.ordering' => $value == null ? 0 : $value,
                ), array(
            'StickerCategory.id' => $id,
        ));
    }

    public function loadAdminPaging($obj, $search = array())
    {
        //pagination
        $cond = array();
        if (!empty($search['keyword']))
        {
            $cond['I18n__nameTranslation.content LIKE'] = '%'.$search['keyword'].'%';
        }
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'limit' => 15,
            'order' => array('StickerCategory.ordering' => 'DESC'),
        );
        try
        {
            return $obj->paginate('StickerCategory');
        }
        catch (Exception $ex)
        {
            return null;
        }
    }
    
    public function loadAll($params = array())
    {
        $cond = array();
        if(isset($params['enabled']))
        {
            $cond['StickerCategory.enabled'] = $params['enabled'];
        }
        return $this->find('all', array(
            'conditions' => $cond,
            'order' => array('StickerCategory.ordering ASC', 'StickerCategory.id ASC')
        ));
    }

    public function loadList($params = array())
    {
        $cond = array();
        if(isset($params['enabled']))
        {
            $cond['StickerCategory.enabled'] = $params['enabled'];
        }
        if(isset($params['keyword']))
        {
            $cond['I18n__name.content LIKE'] = '%'.trim($params['keyword']).'%';
        }
        return $this->find('list', array(
            'conditions' => $cond,
            'order' => array('StickerCategory.ordering ASC', 'StickerCategory.id ASC')
        ));
    }

    public function getDetail($id)
    {
        return $this->findById($id);
    }

}
