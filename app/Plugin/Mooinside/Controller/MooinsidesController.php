<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class MooinsidesController extends MooinsideAppController {

    public $components = array('QuickSettings');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');
        $this->loadModel('Plugin');
        $this->loadModel('Menu.CoreMenuItem');
    }

    public function admin_index($id = null) {
        // clear cache menu
        Cache::clearGroup('menu', 'menu');

        $this->QuickSettings->run($this, array("Mooinside"), $id);

        $this->set('title_for_layout', __('Mooinside Setting'));
    }
    
    public function getTopicCategories(){
        if ($this->request->is('requested')) {
            $categories = null;
            $this->loadModel('Category');
            $categories = $this->Category->getCategories('Topic');
            return $categories;
        }
    }
    
    // get topics of the first category Topic_Topic
    public function getTopicsOfFirstCategories(){
        if ($this->request->is('requested')) {
            $categories = null;
            $topics = null;
            $this->loadModel('Category');
            $this->loadModel('Topic.Topic');
            $categories = $this->Category->getCategories('Topic');
            $listCatIds = array();
            
            if (!empty($categories)){
                foreach ($categories as $cat){
                    $topics = $this->Topic->getTopics('category', $cat['Category']['id'], 1);
                    foreach ($cat['children'] as $subcat){
                        $topics = array_merge($topics, $this->Topic->getTopics('category', $subcat['Category']['id'], 1));
                    }
                    
                    if(!empty($topics)){
                        break;
                    }
                }
            }
            return $topics;
            
        }
    }
    
    public function getPhotosOfPopularAlbum(){
        if ($this->request->is('requested')) {
            $albumModel = MooCore::getInstance()->getModel('Photo_Album');
            $photoModel = MooCore::getInstance()->getModel('Photo_Photo');
            $num_item_show = $this->request->named['num_item_show'];
            $album = $albumModel->getPopularAlbums($num_item_show, Configure::read('core.popular_interval'));
            if (!empty($album)){
                $album = $album[0];
                $album['Photos'] = $photoModel->getPhotos('Photo_Album', $album['Album']['id'], 1, 10);
            }
            return $album;
        }
    }

}
