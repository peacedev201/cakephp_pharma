<?php 
App::uses('AppModel', 'Model');
App::uses('Sanitize', 'Utility');
class StoreAppModel extends AppModel
{
	private $_default_locale = 'eng' ;
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        foreach($this->data[$this->alias] as $k => $v) 
        {
            $v = str_replace('<script', '', $v);
            $v = str_replace('</script>', '', $v);
            $this->data[$this->alias][$k] = $v;
        }
    }
    
    protected function parseRatingPercentage($rating)
    {
        return round($rating / PRODUCT_MAX_RATING * 100).'%';
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
                    $value = $data[$this->_default_locale];
                }
                if(!$mI18n->hasAny(array(
                    'I18nModel.locale' => $key,
                    'I18nModel.model' => get_class($this)
                )))
                {
                    $mI18n->create();
                    $mI18n->save(array(
                        'I18nModel.locale' => $key,
                        'I18nModel.model' => get_class($this),
                        'I18nModel.content' => $value,
                        'I18nModel.field' => $field,
                        'I18nModel.foreign_key' => $foreign_key
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