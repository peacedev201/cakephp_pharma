<?php

App::uses('Widget', 'Controller/Widgets');

class browseContestWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $uid = MooCore::getInstance()->getViewer(true);
        $contestModel = MooCore::getInstance()->getModel('Contest.Contest');
        $page = 1;
        
        $type = 'all';
        if(!$controller->isApp()){
            $type = 'active';
        }
        if (isset($controller->request->params['named']['page'])){
            $page = $controller->request->params['named']['page'];
        }
        if (isset($controller->request->params['named']['type']))
            $type = $controller->request->params['named']['type'];

        if (isset($controller->request->params['named']['category'])){
            $type = 'category/' . $controller->request->params['named']['category'];
        }
        $params = array_merge($controller->request->params['named'], array('user_id' => $uid));
        if(!isset($params['type'])) {
            $params['type'] = $type;
        }
        $contests = $contestModel->getContests($params);

        $total = $contestModel->getTotalContests($params);
        $limit = Configure::read('Contests.contest_item_per_pages');
        $is_view_more = (($page - 1) * $limit + count($contests)) < $total;

        $url_more = '/contests/browse/' . $type . '/page:2';
        if ($controller->request->is('androidApp') || $controller->request->is('iosApp')) {
            $controller->set('contests', $contests);
            $controller->set('url_more', $url_more);
            $controller->set('type', $type);
            $controller->set('is_view_more', $is_view_more);
            $controller->set('uid', $uid);
        } else {
            $this->setData('contests', $contests);
            $this->setData('url_more', $url_more);
            $this->setData('type', $type);
            $this->setData('is_view_more', $is_view_more);
            $this->setData('uid', $uid);
        }
    }

}
