<?php
App::uses('SliderAppModel', 'Slider.Model');
class SlideText extends SliderAppModel {
    public $belongsTo = array( 'Slide' );

    public $validate = array(
        'text' => array(
            'rule' => 'notBlank',
            'message' => 'Text name is required'
        ),
        'slide_id' => array(
            'rule' => 'notBlank',
            'message' => 'Slide is required'
        ),
    );

    public function getSlideTextById($id) {
        $slidetext = $this->findById($id);
        return $slidetext;
    }

    public function findAllBySlide($id)
    {
        $items = $this->find('all', array('conditions' => array('slide_id' => $id)));
        return $items;
    }

}