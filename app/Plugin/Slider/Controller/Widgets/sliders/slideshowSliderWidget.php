<?php
App::uses('Widget','Controller/Widgets');

class slideshowSliderWidget extends Widget {
    public function beforeRender(Controller $controller) {
        if( !Configure::read('Slider.slider_enabled') ){
            return;
        }
        if(!isset($this->params['item']))
        {
            return;
        }
        $item_id = $this->params['item'];
        $controller->loadModel('Slider.Slide');
        $slides = $controller->Slide->findAllBySlider($item_id);
        $language_set = Configure::read('Config.language');
        foreach($slides as $k=>$slide) {
            foreach($slide['nameTranslation'] as $translate) {
                if($translate['locale'] == $language_set) {
                    $slides[$k]['Slide']['slide_name'] = $translate['content'];
                }
            }
            foreach($slide['textTranslation'] as $translate) {
                if($translate['locale'] == $language_set) {
                    $slides[$k]['Slide']['text'] = $translate['content'];
                }
            }
        }//debug($slides);die();
        $this->setData('slides', $slides);
        App::import('Slider.Model', 'Slider');
        $slider = new Slider();
        $slider = $slider->getSliderById($item_id);
        $this->setData('slider', $slider);
        //debug($slider);die();

        $this->setData('title_enable', $this->params['title_enable']);
        $this->setData('title', $this->params['title']);
        $this->setData('key', rand());
    }
}