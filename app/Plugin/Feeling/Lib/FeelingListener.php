<?php

App::uses('CakeEventListener', 'FeelingListener');

class FeelingListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'MooView.beforeRender' => 'beforeRender',
            'ActivitesController.afterShare' => 'afterShare',
            'Controller.Activity.afterDeleteActivity' => 'afterDeleteActivity',

            'element.activities.renderFeelingFeed' => 'renderFeelingFeed',
			'ApiHelper.afterRenderActorHtml' => 'renderFeelingFeedApp',

            'StorageHelper.feelings.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.feelings.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.feelings.getFilePath' => 'storage_amazon_get_file_path',

            'StorageHelper.feeling_categories.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.feeling_categories.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.feeling_categories.getFilePath' => 'storage_amazon_get_file_path',

            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer'

            //'MooView.beforeMooConfigJSRender' => 'mooView_beforeMooConfigJSRender'
        );
    }

    public function storage_geturl_local($e)
    {
        $v = $e->subject();
        $request = Router::getRequest();
        $oid = $e->data['oid'];
        $type = $e->data['type'];
        $thumb = $e->data['thumb'];
        $prefix = $e->data['prefix'];
        $extra = $e->data['extra'];

        $url = '';
        if($type == 'feelings'){
            if ($e->data['thumb']) {
                $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/feeling/icon/' . $oid . '/' . $extra['Feeling']['id'] . $prefix . $thumb;
            } else {
                //$url = FULL_BASE_LOCAL_URL . $v->assetUrl('Blog.noimage/blog.png', array('prefix' => rtrim($prefix, "_"), 'pathPrefix' => Configure::read('App.imageBaseUrl')));
                //$url = $v->getImage("business/images/".$prefix."no-image.png");
                $url = '';

            }
        }else if($type == 'feeling_categories'){

            if ($e->data['thumb']) {
                $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/feeling/photo/'. $oid . '/' . $extra['FeelingCategory']['id'] . $prefix . $thumb;
            } else {
                //$url = FULL_BASE_LOCAL_URL . $v->assetUrl('Blog.noimage/blog.png', array('prefix' => rtrim($prefix, "_"), 'pathPrefix' => Configure::read('App.imageBaseUrl')));
                //$url = $v->getImage("business/images/".$prefix."no-image.png");
                $url = '';
            }
        }

        $e->result['url'] = $url;
    }

    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];

        if($type == 'feelings'){
            $e->result['url'] = $v->getAwsURL($e->data['oid'], "feelings", $e->data['prefix'], $e->data['thumb']);
        }else if($type == 'feeling_categories'){
            $e->result['url'] = $v->getAwsURL($e->data['oid'], "feeling_categories", $e->data['prefix'], $e->data['thumb']);
        }
    }

    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb = $e->data['thumb'];
        $type = $e->data['type'];;
        $path = false;

        if($type == 'feelings'){
            if (!empty($thumb)) {
                $path = WWW_ROOT . "uploads" . DS . "feeling" . DS . "icon" . DS . $objectId . DS . $name . $thumb;
            }
        }else if($type == 'feeling_categories'){
            if (!empty($thumb)) {
                //$path = WWW_ROOT . "uploads" . DS . "feeling_category" . DS . "photo" . DS . $objectId . DS . $name . $thumb;
                $path = WWW_ROOT . "uploads" . DS . "feeling" . DS . "photo" . DS . $objectId . DS . $name . $thumb;
            }
        }

        $e->result['path'] = $path;
    }

    public function storage_task_transfer($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];

        if($type == 'feelings'){
            $feelingModel = MooCore::getInstance()->getModel('Felling.Feeling');
            $feelings = $feelingModel->find('all', array(
                    'conditions' => array("Feeling.id > " => $v->getMaxTransferredItemId("feelings")),
                    'limit' => 10,
                    'order' => array('Feeling.id'),
                )
            );
            if($feelings != null) {
                foreach($feelings as $feeling){
                    $feelingItem = $feeling['Feeling'];
                    if (!empty($feelingItem["icon"])) {
                        //$v->transferObject($feelingItem['id'], 'feelings', 32, $feelingItem["icon"]);
                        $v->transferObject($feelingItem['id'], 'feelings', '', $feelingItem["icon"]);
                    }
                }
            }
        }else if($type == 'feeling_categories'){
            $feelingCategoryModel = MooCore::getInstance()->getModel('Felling.FeelingCategory');
            $feelingCategories = $feelingCategoryModel->find('all', array(
                    'conditions' => array("FeelingCategory.id > " => $v->getMaxTransferredItemId("feeling_categories")),
                    'limit' => 10,
                    'order' => array('FeelingCategory.id'),
                )
            );
            if($feelingCategories != null) {
                foreach($feelingCategories as $feelingCategory){
                    $category = $feelingCategory['FeelingCategory'];
                    if (!empty($category["photo"])) {
                        //$v->transferObject($category['id'], 'feeling_categories', 32, $category["photo"]);
                        $v->transferObject($category['id'], 'feeling_categories', '', $category["photo"]);
                    }
                }
            }
        }

    }

    public function renderFeelingFeed($oEvent) {
        if(Configure::read('Feeling.feeling_enabled')) {
            //$aUser = $oEvent->data['user'];
            if (isset($oEvent->data['activity'])) {
                $aActivity = $oEvent->data['activity'];
        if (!isset($aActivity['feeling_id'])) {
                $feelingActivityModel = MooCore::getInstance()->getModel('Feeling.FeelingActivity');
                $feeling = $feelingActivityModel->get_felling($aActivity);
                if(!empty($feeling)){
                    $categoryModel = MooCore::getInstance()->getModel('Feeling.FeelingCategory');
                    $category = $categoryModel->findById($feeling['Feeling']['category_id']);
                    if (!empty($category)) {
                        $feelingHelper = MooCore::getInstance()->getHelper('Feeling_Feeling');
                        if ($feeling['Feeling']['type'] == 'link') {
                            echo '<div class="feed-feeling">—&nbsp;<span class="feeling-icon" style="background-image: url(' . $feelingHelper->getFeelingImage($feeling, array('prefix' => '32_square')) . ');"></span>&nbsp;<span class="feeling-lvl1">' . $category['FeelingCategory']['label'] . '</span>&nbsp;<a href="' . $feeling['Feeling']['link'] . '"><span class="feeling-lvl2">' . $feeling['Feeling']['label'] . '</span></a></div>';
                        } else {
                            echo '<div class="feed-feeling">—&nbsp;<span class="feeling-icon" style="background-image: url(' . $feelingHelper->getFeelingImage($feeling, array('prefix' => '32_square')) . ');"></span>&nbsp;<span class="feeling-lvl1">' . $category['FeelingCategory']['label'] . '</span>&nbsp;<span class="feeling-lvl2">' . $feeling['Feeling']['label'] . '</span></div>';
                        }
                    }
                }
        }
            }
        }
    }
	
	public function renderFeelingFeedApp($oEvent) {
        if(Configure::read('Feeling.feeling_enabled')) {
            //$aUser = $oEvent->data['user'];
            if (isset($oEvent->data['activity'])) {
                $aActivity = $oEvent->data['activity'];
        if (!isset($aActivity['feeling_id'])) {
                $feelingActivityModel = MooCore::getInstance()->getModel('Feeling.FeelingActivity');
                $feeling = $feelingActivityModel->get_felling($aActivity);
                if (!empty($feeling)) {
                    $categoryModel = MooCore::getInstance()->getModel('Feeling.FeelingCategory');
                    $category = $categoryModel->findById($feeling['Feeling']['category_id']);
                    if (!empty($category)) {
                        $feelingHelper = MooCore::getInstance()->getHelper('Feeling_Feeling');
                        if ($feeling['Feeling']['type'] == 'link') {
                            $titleHtml = '<div class="feed-feeling">—&nbsp;<span class="feeling-icon" style="background-image: url(' . $feelingHelper->getFeelingImage($feeling, array('prefix' => '32_square')) . ');"></span>&nbsp;<span class="feeling-lvl1">' . $category['FeelingCategory']['label'] . '</span>&nbsp;<a href="' . $feeling['Feeling']['link'] . '"><span class="feeling-lvl2">' . $feeling['Feeling']['label'] . '</span></a></div>';
                            $oEvent->result['result'][] = array(
                                'titleHtml' => $titleHtml,
                            );
                        } else {
                            $titleHtml = '<div class="feed-feeling">—&nbsp;<span class="feeling-icon" style="background-image: url(' . $feelingHelper->getFeelingImage($feeling, array('prefix' => '32_square')) . ');"></span>&nbsp;<span class="feeling-lvl1">' . $category['FeelingCategory']['label'] . '</span>&nbsp;<span class="feeling-lvl2">' . $feeling['Feeling']['label'] . '</span></div>';
                            $oEvent->result['result'][] = array(
                                'titleHtml' => $titleHtml,
                            );
                        }
                    }
                }
        }
            }
        }
    }

    public function beforeRender($event)
    {
        $e = $event->subject();


        if(Configure::read('Feeling.feeling_enabled')){
            if(!empty($e->viewVars['site_rtl'])){
                $css_direction = 'Feeling.feeling-rtl';
            }else{
                $css_direction = 'Feeling.feeling-ltr';
            }

            $e->Helpers->Html->css( array(
                'Feeling.feeling', $css_direction
            ),
                array('block' => 'css')
            );

            /*if (Configure::read('debug') != 0){
                $e->Helpers->Html->css( array(
                    'About.main'
                ),
                    array('block' => 'css')
                );
            }
            else
            {
                $e->Helpers->Minify->css(array(
                    'About.main'
                ));
            }*/

            if (Configure::read('debug') == 0){
                $min="min.";
            }else{
                $min="";
            }

            $e->Helpers->MooRequirejs->addPath(array(
                "mooPostFeeling" => $e->Helpers->MooRequirejs->assetUrlJS("feeling/js/post-feeling.{$min}js"),
            ));
            $e->Helpers->MooRequirejs->addShim(array(
                'mooPostFeeling'=>array("deps" =>array('jquery'))
            ));

            $e->addPhraseJs(array(
                "feeling_text_button_wall" => __d('feeling', 'Feelings'),
                "feeling_text_choose_feeling" => __d('feeling', 'Choose a feelings...'),
                "feeling_text_choose_item" => __d('feeling', 'Choose a item')
            ));

            if ($e->theme != 'adm') {
                $request = Router::getRequest();
                //$url_ajax_load_layout = $request->base . 'feelings/ajax_get_feelings';
                $url_ajax_load_layout = Router::url(array('plugin' => 'feeling', 'controller' => 'feeling_categories', 'action' => 'ajax_get_feelings'));

                $e->Helpers->Html->scriptBlock(
                    "require(['jquery','mooPostFeeling'], function($,mooPostFeeling) {\$(document).ready(function(){ mooPostFeeling.init('".$url_ajax_load_layout."'); });});",
                    array(
                        'inline' => false,
                    )
                );
            }else{
                $e->Helpers->Html->css( array(
                    'Feeling.admin_feeling'
                ),
                    array('block' => 'css')
                );
            }

            //$e->Helpers->MooPopup->register('themeModal');
        }
    }

    public function afterShare($event){
        if(Configure::read('Feeling.feeling_enabled')){
            $controller = $event->subject();
            if(isset($controller->request->data['feelingCategoryId']) && isset($controller->request->data['feelingId'])){
                $feelingCategoryId = intval($controller->request->data['feelingCategoryId']);
                $feelingId = intval($controller->request->data['feelingId']);

                if( $feelingCategoryId > 0 && $feelingId > 0 ){
                    $activityData = $event->data['activity']['Activity'];

                    $feelingActivityModel = MooCore::getInstance()->getModel('Feeling.FeelingActivity');

                    $feelingActivityModel->set(array(
                        'activity_id' => $activityData['id'],
                        'feeling_id' => $feelingId
                    ));
                    $feelingActivityModel->save();
                }
            }
        }
    }

    public function afterDeleteActivity($event){
        if(Configure::read('Feeling.feeling_enabled')){
            $activityData = $event->data['activity']['Activity'];
            $feelingActivityModel = MooCore::getInstance()->getModel('Feeling.FeelingActivity');

            $feeling_activity = $feelingActivityModel->find('first', array(
                'conditions' => array('activity_id' => $activityData['id'])
            ));

            if(!empty($feeling_activity)){
                $feelingActivityModel->delete($feeling_activity['FeelingActivity']['id']);
            }
        }
    }

    /*public function mooView_beforeMooConfigJSRender($event)
    {
        if(Configure::read('Feeling.feeling_enabled')){
            $e = $event->subject();
            $feedConfig = $this->getPluginConfigXML();

            if(isset($event->data['mooConfig'])){
                $config = $event->data['mooConfig'];
            }else{
                $config = $e->mooConfig;
            }
            $event->result['mooConfig'] = $config + array('FeelingFeedConfig' => $feedConfig);
            $e->mooConfig = $event->result['mooConfig'];
        }
    }

    private function getPluginConfigXML(){
        $result = array();
        if(file_exists(FEELING_CONFIG_PATH)) {
            $content = file_get_contents(FEELING_CONFIG_PATH);
            $xml = new SimpleXMLElement($content);
            $plugins = json_decode(json_encode($xml), true);
            if( !empty($plugins['plugin']) ){
                foreach ($plugins['plugin'] as $key => $plugin){
                    if(!empty($plugin['name'])){
                        $result[$plugin['name']] = !empty($plugin['disable']['item']) ? $plugin['disable']['item'] : array();
                    }
                }
            }
        }
        return $result;
    }*/
}
