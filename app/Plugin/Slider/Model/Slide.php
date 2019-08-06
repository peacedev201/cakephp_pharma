<?php
App::uses('SliderAppModel', 'Slider.Model');
class Slide extends SliderAppModel {

    public $belongsTo = array( 'Slider' );

    public $validationDomain = 'slider';

    public $validate = array(
        'slide_name' => array(
            'rule' => 'notBlank',
            'message' => 'Slide name is required'
        ),
    );

    public $actsAs = array(
        'MooUpload.Upload' => array(
            'image' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}slide{DS}{field}{DS}',
            )
        ),
        'Translate' => array('slide_name' => 'nameTranslation', 'text' => 'textTranslation'),
        'Storage.Storage' => array(
            'type'=>array('sliders'=>'image'),
        ),
    );
    public $recursive = 2;
    private $_default_locale = 'eng' ;
    public function setLanguage($locale) {
        $this->locale = $locale;
    }
    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }

    public function getSlideById($id) {
        $slide = $this->findById($id);
        return $slide;
    }

    public function getAllSlide()
    {
        $slides = $this->find('all', array());
        return $slides;
    }

    public function findAllBySlider($id)
    {
        $slides = $this->find('all', array('conditions' => array('slider_id' => $id)));
        return $slides;
    }

}