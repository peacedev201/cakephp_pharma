<?php

App::uses('CakeEventListener', 'Event');

class FaqListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'MooView.afterLoadMooCore' => 'afterLoadMooCore',
            'MooView.beforeRender' => 'beforeRender',
            'Controller.Comment.afterComment' => 'afterComment',
            'Controller.Search.search' => 'search',
            'Controller.Search.suggestion' => 'suggestion',
            'Controller.Home.adminIndex.Statistic' => 'statistic',
            'Plugin.View.Api.Search' => 'apiSearch',

            'StorageHelper.fa_category_icon.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.fa_category_icon.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.fa_category_icon.getFilePath' => 'storage_amazon_get_file_path',
            
            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',

            'ApiHelper.renderAFeed.faq_item_detail_share' => 'exportFaqItemDetailShare',
            'Plugin.Controller.Faq.afterDeleteFaq' => 'doAfterDelete'
        );
    }
    public function  exportFaqItemDetailShare($e){
        $data = $e->data['data'];
        $actorHtml = $e->data['actorHtml'];

        $faqModel = MooCore::getInstance()->getModel("Faq_Faq");
        $faq = $faqModel->findById($data['Activity']['parent_id']);
        $helper = MooCore::getInstance()->getHelper('Faq_Faq');

        $target = array();

        if (isset($data['Activity']['parent_id']) && $data['Activity']['parent_id'])
        {
            $title = $data['User']['name'] . ' ' . __d('faq',"shared a faq");
            $titleHtml = $actorHtml . ' ' . __d('faq',"shared a faq");
            $target = array(
                'url' => FULL_BASE_URL . $faq['User']['moo_href'],
                'id' => $faq['User']['id'],
                'name' => $faq['User']['name'],
                'type' => 'User',
            );
        }

        list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml,true);
        if(!empty($title_tmp)){
            $title .=  $title_tmp['title'];
            $titleHtml .= $title_tmp['titleHtml'];
        }

        $e->result['result'] = array(
            'type' => 'share',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                'type' => 'Faq_Faq',
                'id' => $faq['Faq']['id'],
                'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($faq['Faq']['moo_href'], 'UTF-8', 'UTF-8')),
                'description' => $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $faq['Faq']['body'])), 200, array('exact' => false)),1),
                'title' => h($faq['Faq']['moo_title'])
            ),
            'target' => $target,
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
        
        if ($e->data['thumb']) {
            $url = FULL_BASE_LOCAL_URL . $request->webroot . '/uploads/faqs/' . $prefix . $thumb;
        } else {
            //$url = FULL_BASE_LOCAL_URL . $v->assetUrl('Blog.noimage/blog.png', array('prefix' => rtrim($prefix, "_"), 'pathPrefix' => Configure::read('App.imageBaseUrl')));
            $url = '';
        }
        
        $e->result['url'] = $url;
    }
    
    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];
        
        $e->result['url'] = $v->getAwsURL($e->data['oid'], "fa_category_icon", $e->data['prefix'], $e->data['thumb']);
        
    }
    
    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb = $e->data['thumb'];
        $type = $e->data['type'];;
        $path = false;
        
        if (!empty($thumb)) {
            $path = WWW_ROOT . "uploads" . DS . "faqs" . DS  . $thumb;
        }
        
        $e->result['path'] = $path;
    }
    
    public function storage_task_transfer($e)
    {
        $v = $e->subject();
        $categoryModel = MooCore::getInstance()->getModel('Faq.FaqHelpCategorie');
        $items = $categoryModel->find('all', array(
                'conditions' => array("FaqHelpCategorie.id > " => $v->getMaxTransferredItemId("fa_category_icon")),
                'limit' => 10,
                'order' => array('FaqHelpCategorie.id'),
        )
                );
        
        if($items){
            foreach($items as $item){
                if (!empty($item["FaqHelpCategorie"]["icon"])) {
                    $v->transferObject($item["FaqHelpCategorie"]['id'],"fa_category_icon",'',$item["FaqHelpCategorie"]["icon"]);
                    }
            }           
        }
    }

    public function afterLoadMooCore($event) {
        if (Configure::read('Faq.faq_enabled')) {
            $e = $event->subject();
            $e->Helpers->Html->css(array(
                'Faq.faq'
                    ), array('block' => 'css')
            );
        }
    }

    public function statistic($event) {
        $request = Router::getRequest();
        $faqModel = MooCore::getInstance()->getModel("Faq.Faq");
        $event->result['statistics'][] = array(
            'item_count' => $faqModel->find('count'),
            'ordering' => 9999,
            'name' => __d('faq', 'Faqs'),
            'href' => $request->base . '/admin/faq/faqs',
            'icon' => '<i class="material-icons">live_help</i>'
        );
    }

    public function apiSearch($event) {
        $view = $event->subject();
        $items = &$event->data['items'];
        $type = $event->data['type'];
        $viewer = MooCore::getInstance()->getViewer();
        $utz = $viewer['User']['timezone'];

        if ($type == 'Faq' && isset($view->viewVars['faqs']) && count($view->viewVars['faqs'])) {
            $faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
            foreach ($view->viewVars['faqs'] as $item) {
                $items[] = array(
                    'id' => $item["Faq"]['id'],
                    'url' => FULL_BASE_URL . $item['Faq']['moo_href'],
                    'avatar' =>  $faqHelper->getImage($item),
                    'owner_id' => $item["Faq"]['user_id'],
                    'title_1' => $item["Faq"]['moo_title'],
                    'title_2' => __('Posted by') . ' ' . $view->Moo->getNameWithoutUrl($item['User'], false) . ' ' . $view->Moo->getTime($item["Faq"]['created'], Configure::read('core.date_format'), $utz),
                    'created' => $item["Faq"]['created'],
                    'type' => "Faq",
                    'type_title' => __d('faq', 'Faqs')
                );
            }
        }
    }

    public function beforeRender($event) {
        if (Configure::read('Faq.faq_enabled')) {
            $e = $event->subject();
            if (Configure::read('debug') != 0) {
                $e->Helpers->Html->css(array(
                    'Faq.faq'
                        ), array('block' => 'css')
                );
            } else {
                $e->Helpers->Minify->css(array(
                    'Faq.faq'
                ));
            }
            if (Configure::read('debug') == 0) {
                $min = "min.";
            } else {
                $min = "";
            }
            $e->Helpers->MooRequirejs->addPath(array("mooFaq" => $e->Helpers->MooRequirejs->assetUrlJS("Faq.js/main.{$min}js")));
        }
    }

    public function afterComment($event) {

        $data = $event->data['data'];
        $target_id = isset($data['target_id']) ? $data['target_id'] : null;
        $type = isset($data['type']) ? $data['type'] : '';
        if ($type == 'Faq_Faq' && !empty($target_id)) {
            $faqModel = MooCore::getInstance()->getModel('Faq.Faq');
            $faqModel->updateCounter($target_id);
        }
    }

    public function search($event) {
        if (Configure::read('Faq.faq_enabled')) {
            $e = $event->subject();
            $faqModel = MooCore::getInstance()->getModel('Faq.Faq');
            $role_id = ROLE_GUEST;
            $viewer = MooCore::getInstance()->getViewer();
            if (!empty($viewer)) {
                $role_id = $viewer['User']['role_id'];
            }
            $keyword = $e->keyword;
            $results = $faqModel->findFaqByTitle($keyword, $role_id);
            // debug($results);die;
            if (count($results) > Configure::read('Faq.faq_item_per_pages')) {
                $results = array_slice($results, 0, Configure::read('Faq.faq_item_per_pages'));
            }
            if (isset($e->plugin) && $e->plugin == 'Faq') {
                $e->set('faqs', $results);
                $e->render("Faq.Elements/lists/faqs_search_list");
            } else {
                $event->result['Faq']['header'] = __d('faq', 'Faqs');
                $event->result['Faq']['icon_class'] = 'live_help';
                $event->result['Faq']['view'] = "lists/faqs_search_list";
                if (!empty($results)) {
                    $event->result['Faq']['notEmpty'] = 1;
                }
                $e->set('faqs', $results);
            }
        }
    }

    public function suggestion($event) {
        if (Configure::read('Faq.faq_enabled')) {
            $role_id = ROLE_GUEST;
            $viewer = MooCore::getInstance()->getViewer();
            if (!empty($viewer)) {
                $role_id = $viewer['User']['role_id'];
            }
            $e = $event->subject();
            $faqModel = MooCore::getInstance()->getModel('Faq.Faq');
            $event->result['faq']['header'] = __d('faq', 'Faqs');
            $event->result['faq']['icon_class'] = 'live_help';
            $keyword = $event->data['searchVal'];
            if (isset($event->data['type']) && $event->data['type'] == 'faq') {
                $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
                $faqs = $faqModel->findFaqByTitle($keyword, $role_id);
                $e->set('faqs', $faqs);
                $e->set('result', 1);
                $e->set('is_more_url', 0);
                $e->set('more_url', '/search/suggestion/faq/' . $e->params['pass'][1] . '/page:' . ( $page + 1 ));
                $e->set('element_list_path', "Faq.lists/faqs_search_list");
            }
            if (isset($event->data['type']) && $event->data['type'] == 'all') {
                $event->result['faq'] = null;
                $faqs = $faqModel->findFaqByTitle($keyword, $role_id);
                if (count($faqs) > 2) {
                    $faqs = array_slice($faqs, 0, 2);
                }
                if (!empty($faqs)) {
                    $faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
                    foreach ($faqs as $index => &$detail) {
                        $event->result['faq'][$index]['img'] = $faqHelper->getImage($detail);
                        $event->result['faq'][$index]['id'] = $detail['Faq']['id'];
                        $event->result['faq'][$index]['title'] = h($detail['Faq']['title']);
                        $event->result['faq'][$index]['find_name'] = __d('faq', 'Find Faq');
                        $event->result['faq'][$index]['icon_class'] = 'live_help';
                        $event->result['faq'][$index]['view_link'] = 'faq/faqs/view/';
                    }
                }
            }
        }
    }

    public function doAfterDelete($event)
    {
        $item = $event->data['item'];

        $activityModel = MooCore::getInstance()->getModel('Activity');

        // delete shared feed
        if (!empty($item['Faq'])) {
            $activityModel->deleteAll(array('Activity.item_type' => 'Faq_Faq', 'Activity.parent_id' => $item['Faq']['id']));
        }
    } 

}
