<?php
App::uses('AppHelper', 'View/Helper');
class SlideHelper extends AppHelper {
    public $helpers = array('Storage.Storage');
    public function getImage($item, $options) {
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix'])) {
            $prefix = $options['prefix'] . '_';
        }
        /*$url = '';
        if ($item[key($item)]['image']) {
            $url = FULL_BASE_URL . $request->webroot . 'uploads/slide/image/' . $item[key($item)]['id'] . '/' . $prefix . $item[key($item)]['image'];
        } else {
            //$url = FULL_BASE_URL . $this->assetUrl('Recipe.noimage/recipe.png', $options + array('pathPrefix' => Configure::read('App.imageBaseUrl')));
        }

        return $url;*/

        return $this->Storage->getUrl($item[key($item)]['id'], $prefix, $item[key($item)]['image'], "sliders");
    }

}