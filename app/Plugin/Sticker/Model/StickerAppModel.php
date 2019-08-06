<?php 
App::uses('AppModel', 'Model');
class StickerAppModel extends AppModel{
    public $validationDomain = 'sticker';
    private $_default_locale = 'eng' ;
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        foreach($this->data[$this->alias] as $k => $v) 
        {
            $v = str_replace('<script>', '', $v);
            $v = str_replace('</script>', '', $v);
            $this->data[$this->alias][$k] = str_replace('<script>', '', $v);
        }
    }
    
    public function loadListTranslate($id)
    {
        $mI18n = MooCore::getInstance()->getModel('I18nModel');
        $data = $mI18n->find('all', array(
            'conditions' => array(
                'I18nModel.model' => get_class($this),
                'I18nModel.foreign_key' => $id
            )
        ));
        $result = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $item = $item['I18nModel'];
                $result[$item['locale']][$item['field']] = $item['content'];
            }
        }
        return $result;
    }
    
    protected function saveMultiLanguage($data, $field, $foreign_key)
    {
        if($data != null)
        {
            $mI18n = MooCore::getInstance()->getModel('I18nModel');
            foreach($data as $key => $value)
            {
                if(empty($value))
                {
                    $value = !empty($data[Configure::read('Config.language')]) ? $data[Configure::read('Config.language')] : $data[$this->_default_locale];
                }
                if(!$mI18n->hasAny(array(
                    'I18nModel.locale' => $key,
                    'I18nModel.model' => get_class($this),
                    'I18nModel.field' => $field,
                    'I18nModel.foreign_key' => $foreign_key
                )))
                {
                    $mI18n->create();
                    $mI18n->save(array(
                        'locale' => $key,
                        'model' => get_class($this),
                        'content' => $value,
                        'field' => $field,
                        'foreign_key' => $foreign_key
                    ));
                }
                else 
                {
                    $mI18n->create();
                    $mI18n->updateAll(array(
                        'I18nModel.content' => '"'.$value.'"',
                    ), array(
                        'I18nModel.locale' => $key,
                        'I18nModel.model' => get_class($this),
                        'I18nModel.field' => $field,
                        'I18nModel.foreign_key' => $foreign_key
                    ));
                }
            }
        }
    }
}