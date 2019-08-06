<?php 
App::uses('AppModel', 'Model');
class BusinessAppModel extends AppModel{
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        foreach($this->data[$this->alias] as $k => $v) 
        {
            $v = str_replace('<script', '', $v);
            $v = str_replace('</script>', '', $v);
            $this->data[$this->alias][$k] = $v;
        }
    }
}