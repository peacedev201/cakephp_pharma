<?php
class Sticker extends StickerAppModel {
    public $useTable = 'sticker_stickers';
    public $actsAs = array(
        'Translate' => array('name' => 'nameTranslation')
    );
    public $validate = array(
        'icon' => array(
            'rule' => 'notBlank',
            'message' => 'Icon is required'
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
            $this->data['Sticker']['trans_' . $field] = $this->data['Sticker'][$field];
            $this->data['Sticker'][$field] = reset($this->data['Sticker'][$field]);
        }
    }

    public function afterSave($created, $options = array())
    {
        parent::afterSave($created, $options);

        //save multi language
        foreach ($this->actsAs['Translate'] as $field => $item)
        {
            $data = !empty($this->data['Sticker']['trans_' . $field]) ? $this->data['Sticker']['trans_' . $field] : null;
            $this->saveMultiLanguage($data, $field, $this->data['Sticker']['id']);
        }
    }
    
    public function isStickerExist($id, $enable = null)
    {
        $cond = array(
            'Sticker.id' => $id
        );
        if (is_bool($enable))
        {
            $cond['Sticker.enable'] = $enable;
        }
        return $this->hasAny($cond);
    }
    
    function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'Sticker.' . $task => $value
                ), array(
            'Sticker.id' => $id,
        ));
    }

    function saveOrdering($id, $value)
    {
        $this->create();
        $this->updateAll(array(
            'Sticker.ordering' => $value == null ? 0 : $value,
                ), array(
            'Sticker.id' => $id,
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
            'limit' => 10,
            'order' => array('Sticker.ordering' => 'DESC'),
        );
        try
        {
            return $obj->paginate('Sticker');
        }
        catch (Exception $ex)
        {
            return null;
        }
    }
    
    public function getDetail($id)
    {
        return $this->findById($id);
    }
    
    public function deleteSticker($id)
    {
        if($this->delete($id))
        {
            $mStickerImage = MooCore::getInstance()->getModel('Sticker.StickerImage');
            $mStickerImage->deleteAll(array(
                'StickerImage.sticker_sticker_id' => $id
            ));
        }
        return false;
    }
    
    public function loadSticker($params = array())
    {
        $cond = array();
        if(isset($params['enabled']))
        {
            $cond['Sticker.enabled'] = $params['enabled'];
        }
        $data = $this->find('all', array(
            'conditions' => $cond,
            'order' => array('Sticker.ordering ASC', 'Sticker.id ASC')
        ));
        return $data;
    }
    
    public function loadStickerLog($user_id)
    {
        $mStickerLog = MooCore::getInstance()->getModel('Sticker.StickerLog');
        return $mStickerLog->findByUserId($user_id);
    }

    public function saveLog($user_id, $sticker_id)
    {
        $mStickerLog = MooCore::getInstance()->getModel('Sticker.StickerLog');
        $log = $mStickerLog->findByUserId($user_id);
        $mStickerLog->create();
        if($log != null)
        {
            $mStickerLog->id = $log['StickerLog']['id'];
            $stickers = !empty($log['StickerLog']['stickers']) ? explode(',', $log['StickerLog']['stickers']) : array();
            if($stickers != null && ($key = array_search($sticker_id, $stickers)) !== false)
            {
                unset($stickers[$key]);
            }
            else if(count($stickers) >= STICKER_MAX_LOG_ITEM)
            {
                array_pop($stickers);
            }
            array_unshift($stickers, $sticker_id);
            $stickers = implode(',', $stickers);
        }
        else
        {
            $stickers = $sticker_id;
        }
        $mStickerLog->save(array(
            'user_id' => $user_id,
            'stickers' => $stickers
        ));
    }
}
