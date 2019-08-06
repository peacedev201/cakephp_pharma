<?php
App::uses('CakeEventListener', 'Event');

class SliderListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
            'MooView.beforeRender' => 'beforeRender',

            'StorageHelper.sliders.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.sliders.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.sliders.getFilePath' => 'storage_amazon_get_file_path',
            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
        );
    }

    public function beforeRender($event)
    {
        if(Configure::read('Slider.slider_enabled')){
            $e = $event->subject();

            if (Configure::read('debug') == 0){
                $min="min.";
            }else{
                $min="";
            }
            $e->Helpers->MooRequirejs->addPath(array(
                "mooSlider" => $e->Helpers->MooRequirejs->assetUrlJS( "Slider.js/main.{$min}js" ),
                "jquerydevramaslider" => $e->Helpers->MooRequirejs->assetUrlJS( "Slider.js/jquerydevramaslider.js" ),
            ));

            $e->Helpers->MooRequirejs->addShim(array(
                'jquerydevramaslider'=>array("deps" =>array('jquery')),
            ));
            $e->Helpers->MooPopup->register('themeModal');
        }
    }

    public function storage_geturl_local($e)
    {
        $v = $e->subject();
        $request = Router::getRequest();
        $oid = $e->data['oid'];
        $thumb = $e->data['thumb'];
        $prefix = $e->data['prefix'];
        $type = $e->data['type'];

        $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/slide/image/' . $oid . '/' . $prefix . $thumb;

        $e->result['url'] = $url;
    }

    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];
        $e->result['url'] = $v->getAwsURL($e->data['oid'], "sliders", $e->data['prefix'], $e->data['thumb']);
    }

    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb = $e->data['thumb'];
        $path = false;
        $type = $e->data['type'];
        if (!empty($thumb)) {
            $path = WWW_ROOT . "uploads" . DS . "slide" . DS . "image" . DS . $objectId . DS . $name . $thumb;

        }

        $e->result['path'] = $path;
    }

    public function storage_task_transfer($e)
    {
        $v = $e->subject();
        $slideModel = MooCore::getInstance()->getModel('Slider.Slide');
        $slides = $slideModel->find('all', array(
                'conditions' => array("Slide.id > " => $v->getMaxTransferredItemId("slide")),
                'limit' => 10,
                'fields'=>array('Slide.id','Slide.image'),
                'order' => array('Slide.id'),
            )
        );

        if($slides){
            foreach($slides as $slide){
                if (!empty($slide["Slide"]["image"])) {
                    $v->transferObject($slide["Slide"]['id'],"sliders",'',$slide["Slide"]["image"]);
                }
            }
        }
    }

}