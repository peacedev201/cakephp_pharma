<?php 
class SlidersController extends SliderAppController{

    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModel('Slider.Slider');
    }

    public function admin_index()
    {
        $cond = array();
        if (!empty($this->request->data['keyword']))
        {
            $cond['Slider.slider_name LIKE '] = '%'.$this->request->data['keyword'].'%';
        }
        $sliders = $this->paginate('Slider', $cond);
        $this->set('sliders', $sliders);
        $this->set('title_for_layout', __d('slider','Slideshows Manager'));
    }

    public function admin_create($id = null)
    {
        $bIsEdit = false;
        if (!empty($id)) {
            $slider = $this->Slider->getSliderById($id);
            $bIsEdit = true;
        } else {
            $slider = $this->Slider->initFields();
        }

        $position_navigation_array = array(
            'out-center-bottom' => __d('slider','out-center-bottom'),
            'out-left-bottom'=> __d('slider','out-left-bottom'),
            'out-right-bottom'=> __d('slider','out-right-bottom'),
            'out-center-top'=> __d('slider','out-center-top'),
            'out-left-top'=> __d('slider','out-left-top'),
            'out-right-top'=> __d('slider','out-right-top'),
            'in-center-bottom'=> __d('slider','in-center-bottom'),
            'in-right-bottom'=> __d('slider','in-right-bottom'),
            'in-center-top'=> __d('slider','in-center-top'),
            'in-left-top'=> __d('slider','in-left-top'),
            'in-right-top'=> __d('slider','in-left-middle'),
            'in-right-middle'=> __d('slider','in-right-middle')
        );
        $position_control_array = array(
            'left-right' => __d('slider','left-right'),
            'top-center' => __d('slider','top-center'),
            'bottom-center' => __d('slider','bottom-center'),
            'top-left' => __d('slider','top-left'),
            'top-right' => __d('slider','top-right'),
            'bottom-left' => __d('slider','bottom-left'),
            'bottom-right' => __d('slider','bottom-right'),
        );
        $navigation_type = array(
            'number' => __d('slider','number'),
            'circle' => __d('slider','circle'),
            'square' => __d('slider','square'),
        );
        $transition = array(
            'random' => __d('slider','random'),
            'slide-left' => __d('slider','slide-left'),
            'slide-right' => __d('slider','slide-right'),
            'slide-top' => __d('slider','slide-top'),
            'slide-bottom' => __d('slider','slide-bottom'),
            'fade' => __d('slider','fade'),
            'split' => __d('slider','split'),
            'split3d' => __d('slider','split3d'),
            'door' => __d('slider','door'),
            'wave-left' => __d('slider','wave-left'),
            'wave-right' => __d('slider','wave-right'),
            'wave-top' => __d('slider','wave-top'),
            'wave-bottom' => __d('slider','wave-bottom'),

        );

        $this->set('position_navigation_array', $position_navigation_array);
        $this->set('position_control_array', $position_control_array);
        $this->set('navigation_type', $navigation_type);
        $this->set('transition', $transition);

        $this->set('slider', $slider);
        $this->set('title_for_layout', __d('slider','Slideshows Manager'));
    }

    public function admin_save()
    {
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->Slider->id = $this->request->data['id'];
        }
        $values = $this->request->data;
        $this->Slider->set($values);
        $this->_validateData($this->Slider);
        $this->Slider->save();

        $sliders = $this->Slider->getAllSlider();
        $slider_arr = array();
        foreach($sliders as $slider)
        {
            $slider_arr[$slider['Slider']['id']] = $slider['Slider']['slider_name'];
        }
        $this->loadModel('CoreBlock');
        $block = $this->CoreBlock->find('all',
            array('conditions' => array('CoreBlock.path_view' => 'sliders.slideshow'))
        );
        $this->CoreBlock->id = $block[0]['CoreBlock']['id'];
        $params = array("0" => array("label"=>"Title","input"=>"text","value"=>"Slideshow","name"=>"title"),
            "1" => array("label"=>"Item Slider","input"=>"select","value"=>$slider_arr,"name"=>'item'),
            "2" => array("label"=>"Title","input"=>"checkbox","value"=>"Enable Title","name"=>"title_enable"),
            "3" => array("label"=>"plugin","input"=>"hidden","value"=>"Slider","name"=>"plugin")
                         );
        $block[0]['CoreBlock']['params'] = json_encode($params);
        $this->CoreBlock->set($block[0]['CoreBlock']);
        $this->CoreBlock->save();

        $this->Session->setFlash(__d('slider','Slider has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        echo json_encode($response);die();
    }

    public function admin_delete($id) {
        $this->autoRender = false;
        $slider = $this->Slider->findById($id);

        // delete slide
        $this->loadModel('Slider.Slide');
        /*$slides = $this->Slide->findAllBySlider($id);
        foreach ($slides as $slide){
            // delete text
            $this->loadModel('Slider.SlideText');
            $slidetext_arr = $this->SlideText->findAllBySlide($slide['Slide']['id']);
            foreach($slidetext_arr as $item){
                $this->SlideText->delete($item['SlideText']['id']);
            }
            $this->Slide->delete($slide['Slide']['id']);
        }*/
        $this->Slider->delete($id);

        // delete block content
        $this->loadModel('CoreContent');
        $contents = $this->CoreContent->getCoreContentByPageName('sliders.slideshow');
        foreach($contents as $content){
            $params = json_decode($content['CoreContent']['params']);
            if($params->item == $id)
            {
                $this->CoreContent->delete($content['CoreContent']['id']);
            }
        }
        // add params again of block
        $sliders = $this->Slider->getAllSlider();
        $slider_arr = array();
        foreach($sliders as $slider)
        {
            $slider_arr[$slider['Slider']['id']] = $slider['Slider']['slider_name'];
        }
        $this->loadModel('CoreBlock');
        $block = $this->CoreBlock->find('all',
            array('conditions' => array('CoreBlock.path_view' => 'sliders.slideshow'))
        );
        $this->CoreBlock->id = $block[0]['CoreBlock']['id'];
        $params = array("0" => array("label"=>"Title","input"=>"text","value"=>"Slideshow","name"=>"title"),
            "1" => array("label"=>"Item Slider","input"=>"select","value"=>$slider_arr,"name"=>'item'),
            "2" => array("label"=>"Title","input"=>"checkbox","value"=>"Enable Title","name"=>"title_enable"),
            "3" => array("label"=>"plugin","input"=>"hidden","value"=>"Slider","name"=>"plugin")
        );
        $block[0]['CoreBlock']['params'] = json_encode($params);
        $this->CoreBlock->set($block[0]['CoreBlock']);
        $this->CoreBlock->save();

        $this->Session->setFlash(__d('slider','Slider has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }

	public function admin_review($id)
	{
		App::import('Slider.Model', 'Slide');
        $slide = new Slide();
        $slides = $slide->findAllBySlider($id);
        $this->set('slides', $slides);
        App::import('Slider.Model', 'Slider');
        $slider = new Slider();
        $slider = $slider->getSliderById($id);
        $this->set('slider', $slider);
	}
	
    public function index()
    {
    }
}