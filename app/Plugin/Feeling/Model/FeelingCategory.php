<?php
App::uses('FeelingAppModel', 'Feeling.Model');

class FeelingCategory extends FeelingAppModel
{
    //public $mooFields = array('image');
    private $_default_locale = 'eng' ;
    public $recursive = 2;

    public $actsAs = array(
        'Translate' => array('label' => 'labelTranslation'),
        'MooUpload.Upload' => array(
            'photo' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}feeling{DS}{field}{DS}',
                'thumbnailSizes' => array('size' => array('32_square'))
            ),
        ),
        'Storage.Storage' => array(
            'type'=>array('feeling_categories'=>'photo'),
        )
    );

	public $validate = array(   
        'label' =>   array(
            'rule'     => 'notBlank',
            'message'  => 'Group is required'
        )
        /*'photo' =>   array(
            'rule' => array(
                'extension',
                array('gif', 'jpeg', 'png', 'jpg')
            ),
            'message'  => 'Image is required'
        )*/
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }

    public function setLanguage($locale) {
        $this->locale = $locale;
    }

    public function beforeDelete($cascade = true){

        parent::beforeDelete($cascade);
    }

    public function isIdExist($id)
    {
        return $this->hasAny(array('id' => $id));
    }

    public function deleteCategory($id){

        $this->delete($id);

        App::import('Model', 'Feeling.Feeling');
        $Feeling = new Feeling();
        $aFeelings = $Feeling->find('all', array(
                'conditions' => array('category_id' => $id),
            ));
        
        foreach ($aFeelings as $aFeeling) {
            $Feeling->id = $aFeeling['Feeling']['id'];
            $Feeling->save(array('category_id' => null));
        }
    }

    public function getCategoriesList() {
        return $this->find('list', array('fields' => array('id', 'label')));
    }

}