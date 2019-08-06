<?php
class SlideController extends SliderAppController{

    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModel('Slider.Slide');
    }

    public function admin_index($id = NULL)
    {
        if(!$id)
        {
            $this->redirect( '/pages/error' );
        }
        $cond = array();
        $sliderName = '';
        if (!empty($id))
        {
            $cond['Slide.slider_id'] = $id;
            $this->loadModel('Slider.Slider');
            $slider = $this->Slider->findById($id);
            if (!empty($slider))
            {
                $sliderName = $slider['Slider']['slider_name'];
            }
        }
        if (!empty($this->request->data['keyword']))
        {
            $cond['Slide.slide_name LIKE '] = '%'.$this->request->data['keyword'].'%';
        }
        $slides = $this->paginate('Slide', $cond);
        $this->set('slides', $slides);
        $this->set('sliderName', $sliderName);
        $this->set('id', $id);
        $this->set('title_for_layout', __d('slider','Slideshows Manager'));
    }

    public function admin_create($id = NULL, $slider_id)
    {
        if(!$slider_id)
        {
            $this->redirect( '/pages/error' );
        }
        $this->set('slider_id', $slider_id);
        $bIsEdit = false;
        if (!empty($id)) {
            $slide = $this->Slide->getSlideById($id);
            $bIsEdit = true;
        } else {
            $slide = $this->Slide->initFields();
        }

        if(isset($this->request->named['slider']))
        {
            $slide['Slide']['slider_id'] = $this->request->named['slider'];
        }

        $this->set('slide', $slide);
        $this->set('bIsEdit', $bIsEdit);
        $this->set('title_for_layout', __d('slider','Slideshows Manager'));
    }

    public function admin_save()
    {
        $bIsEdit = false;
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->Slide->id = $this->request->data['id'];
        }

        $values = $this->request->data;//debug($values);die();

        // check image size
        $this->loadModel('Slider.Slider');
        $slider = $this->Slider->findById( $values['slider_id'] );
        $request = Router::getRequest();
        $image_url = null;
        if( isset($values['id']) && $values['id'] ) {
            $slide = $this->Slide->getSlideById($values['id']);
            if( $slide['Slide']['image'] != $values['image'] ) {
                $image_url = FULL_BASE_URL . $request->webroot . $values['image'];
                list($width, $height) = getimagesize($image_url);
            }
        }
        else {
            $image_url = FULL_BASE_URL . $request->webroot . $values['image'];
            list($width, $height) = getimagesize($image_url);
        }
        if( $image_url && $width != $slider['Slider']['width'] && $height != $slider['Slider']['height'] ) {
            $errors = __d('slider', 'Please upload image size %s x %s', $slider['Slider']['width'], $slider['Slider']['height']);
            $response['result'] = 0;
            $response['message'] = $errors;
            echo json_encode($response);
            exit;
        }

        //die();
        $this->Slide->set($values);
        $this->_validateData($this->Slide);
        $this->Slide->save();

        if (!$bIsEdit) {
            $this->loadModel('Language');
            foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                $this->Slide->locale = $lKey;
                $this->Slide->saveField('slide_name', $values['slide_name']);
                $this->Slide->saveField('text', $values['text']);
            }
        }

        $this->Session->setFlash(__d('slider','Slide has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        echo json_encode($response);die();
    }

    public function admin_delete($id) {
        $this->autoRender = false;

        // delete text
        /*$this->loadModel('Slider.SlideText');
        $slidetext_arr = $this->SlideText->findAllBySlide($id);
        foreach($slidetext_arr as $item){
            $this->SlideText->delete($item['SlideText']['id']);
        }*/
        // delete slide
        $this->Slide->delete($id);

        $this->Session->setFlash(__d('slider','Slide has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }

    public function admin_review() {
        $data = $this->request->data;//debug($data);die();
        $image_url = NULL;
        $request = Router::getRequest();
        if( isset( $data['id'] ) && $data['id']) {
            $slide = $this->Slide->getSlideById($data['id']);
            if( $slide['Slide']['image'] != $data['image'] ) {
                $image_url = FULL_BASE_URL . $request->webroot.$data['image'];
            }
            else {
                $image_url = FULL_BASE_URL . $request->webroot .'uploads/slide/image/'.$data['id'].'/'.$data['image'];
            }
        }
        else {
            $image_url = FULL_BASE_URL . $request->webroot.$data['image'];
        }
        $this->loadModel('Slider.Slider');
        $slider = $this->Slider->findById( $data['slider_id'] );

        $this->set(array(
            'data' => $data,
            'slider' => $slider,
            'image_url' => $image_url
        ));
    }

    public function admin_ajax_translate($id) {
        if (!empty($id)) {
            $slide = $this->Slide->getSlideById($id);
            $this->set('slide', $slide);
            $this->loadModel('Language');
            $this->set('languages', $this->Language->getLanguages());
        } else {
            // error
        }
    }

    public function admin_ajax_translate_save() {

        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                // we are going to save the german version
                $this->Slide->id = $this->request->data['id'];
                foreach ($this->request->data['name'] as $lKey => $sContent) {
                    $this->Slide->locale = $lKey;
                    if ($this->Slide->saveField('slide_name', $sContent)) {
                        $response['result'] = 1;
                    } else {
                        $response['result'] = 0;
                    }
                }
                foreach ($this->request->data['text'] as $lKey => $sContent) {
                    $this->Slide->locale = $lKey;
                    if ($this->Slide->saveField('text', $sContent)) {
                        $response['result'] = 1;
                    } else {
                        $response['result'] = 0;
                    }
                }
            } else {
                $response['result'] = 0;
            }
        } else {
            $response['result'] = 0;
        }
        echo json_encode($response);
    }
}