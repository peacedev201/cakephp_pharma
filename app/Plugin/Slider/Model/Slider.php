<?php
App::uses('SliderAppModel', 'Slider.Model');
class Slider extends SliderAppModel {

    public $validationDomain = 'slider';

    public $validate = array(
        'slider_name' => array(
            'rule' => 'notBlank',
            'message' => 'Slideshow name is required'
        ),
        'duration' => array(
            'rule' => 'numeric',
            'message' => 'Duration is numbers'
        ),
        'width' => array(
            'rule' => 'numeric',
            'message' => 'Width is numbers'
        ),
        'height' => array(
            'rule' => 'numeric',
            'message' => 'Height is numbers'
        ),
        'transition_speed' => array(
            'rule' => 'numeric',
            'message' => 'Transition speed is numbers'
        )
    );

    public function getThumb($row){
        return 'thumbnail';
    }

    public function getSliderById($id) {
        $slider = $this->findById($id);
        return $slider;
    }

    public function getAllSlider()
    {
        $sliders = $this->find('all', array());
        return $sliders;
    }
}