<?php
App::uses('FeelingAppModel', 'Feeling.Model');

class Feeling extends FeelingAppModel {

    //public $mooFields = array('icon');
    private $_default_locale = 'eng' ;
    public $recursive = 2;

    public $actsAs = array(
        'Translate' => array('label' => 'labelTranslation'),
        'MooUpload.Upload' => array(
            'icon' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}feeling{DS}{field}{DS}',
                'thumbnailSizes' => array('size' => array('32_square'))
            ),
        ),
        'Storage.Storage' => array(
            'type'=>array('feelings'=>'icon'),
        )
    );

    /*public $belongsTo = array(
        'FeelingCategory' => array(
            'className'     => 'FeelingCategory',
            'foreignKey'    => 'category_id'),
    );*/

    public $validate = array(
        'label' =>   array(
            'rule'     => 'notBlank',
            'message'  => 'Status is required'
        ),
        /*'icon' =>   array(
            'rule' => array(
                'extension',
                array('gif', 'jpeg', 'png', 'jpg')
            ),
            'message'  => 'Image is required'
        ),*/
        'category_id' =>   array(
            'rule'     => 'notBlank',
            'message'  => 'Category is required'
        )
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }

    public function setLanguage($locale) {
        $this->locale = $locale;
    }

    /*public function clearCache($row)
    {
        $target_id= $row['StatusBackground']['id'];
        $key = '_'.$target_id;
        Cache::clearGroup($key, 'status_background');
    }*/

    public function beforeDelete($cascade = true){
//        $background = $this->findById($this->id);
        //$this->clearCache($photo);

//        if (!empty($background['Feeling']['icon']) && file_exists(WWW_ROOT . 'uploads' . DS . 'feeling' . DS . $background['Felling']['icon']))
//            unlink(WWW_ROOT . 'uploads' . DS . 'feeling' . DS . $background['Feeling']['image']);

        //$this->clearCache($background);
        parent::beforeDelete($cascade);
    }

    public function afterDelete() {
        Cache::clearGroup('feeling', 'feeling');
    }

    public function getFelling(){
        $order = array('Feeling.order ASC', 'Feeling.id ASC');
        return $this->find('all', array(
            'conditions' => array(
                'active' => true
            ),
            //'limit' => $limit,
            //'page' => $page,
            'order' => $order
        ));
    }
}