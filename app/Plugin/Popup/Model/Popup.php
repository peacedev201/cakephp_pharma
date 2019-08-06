<?php
/**
 * Created by PhpStorm.
 * User: Social
 * Date: 11/8/2017
 * Time: 3:15 PM
 */
App::uses('PopupAppModel', 'Popup.Model');
class Popup extends PopupAppModel
{
    public $mooFields = array('title','body','onetime','popup_option','role','enable');
    public $validate = array(
        'title' => array('rule'=>'notBlank'),
        'body' => array('rule'=>'notBlank'),
    );
    public $belongsTo = array(
        'Page' => array(
            'className' => 'Page',
            'foreignKey' => 'page_id',
        ),
    );
    public $recursive = 1;
    public $actsAs = array(
        'Translate' => array(
            'title' => 'titleTranslation',
            'body' => 'bodyTranslation',
        ),
        'MooUpload.Upload' => array(
            'thumbnail' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}popup{DS}{field}{DS}',
            ),
        ),
        'Storage.Storage' => array(
            'type'=>array('popups'=>'thumbnail'),
        ),
    );
    private $_default_locale = 'eng' ;
    public function setLanguage($locale) {
        $this->locale = $locale;
    }
    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }
}