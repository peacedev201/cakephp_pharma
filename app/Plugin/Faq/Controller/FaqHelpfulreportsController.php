<?php

class FaqHelpfulreportsController extends FaqAppController {

    public $components = array('Paginator');

    public function admin_index() {
        $this->_checkPermission(array('super_admin' => true));
        $this->loadModel('Faq.FaqResult');

        $this->Paginator->settings = array(
            'limit' => Configure::read('Faq.faq_item_per_pages'),
            'order' => array(
                'FaqResult.id' => 'DESC'
            )
        );
        $cond = array();
        $data_search = array();
        //
        $this->request->data = array_merge($this->request->data, $this->request->params['named']);

        if (!empty($this->request->data['type'])) {
            $cond['vote'] = $this->request->data['type'];
            $data_search['vote'] = $this->request->data['type'];
        } else {
            $cond['vote'] = (isset($this->request->named['vote']) ? $this->request->named['vote'] : 0);
            $data_search['vote'] = (isset($this->request->named['vote']) ? $this->request->named['vote'] : 0);
        }

        $faqsResult = $this->Paginator->paginate('FaqResult', $cond);

        $this->set('title_for_layout', __d('faq', 'F.A.Q Reports'));
        $this->set('data_search', $data_search);
        $this->set('type', $data_search['vote']);
        $this->set('faqsResult', $faqsResult);
    }

    public function ajax_answerno() {
        $this->autoRender = FALSE;
        $uid = $this->Auth->user('id');
        $this->loadModel('Faq.FaqResult');
        $this->loadModel('Faq.Faq');
//        $faq_id = $this->request->data['faq_id'];
        if (empty($this->request->data['faqhelpful'])) {
            $response['result'] = 0;
            $response['message'] = __d('faq', 'You need to select reason !');
            echo json_encode($response);
            exit();
        }
        $data['faq_id'] = $this->request->data['faq_id'];
        $data['user_id'] = $uid;
        $data['vote'] = 0;
        $data['helpfull_id'] = $this->request->data['faqhelpful'];
        $faq_id = $this->request->data['faq_id'];
        $result = $this->FaqResult->getResults($faq_id, $uid);
        if ($result) {
            $this->FaqResult->delete($result[0]['FaqResult']['id']);
        }

        $this->FaqResult->save($data);
        $count_faq = $this->FaqResult->getTotalByFaqId($faq_id);
        $count_yes = $this->FaqResult->getTotalByFaqId($faq_id, TRUE, FALSE);
        $per = ($count_yes / $count_faq) * 100;
        $dt['id'] = $faq_id;
        $dt['per_usefull'] = $per;
        $dt['total_yes'] = $count_yes;
        $dt['total_no'] = $count_faq - $count_yes;
        $this->Faq->save($dt);
//        $this->Session->setFlash(__d('faq', 'Thanks for your feedback!'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $response['result'] = 1;
        $response['faqid'] = $faq_id;
        echo json_encode($response);
        exit();
    }

    public function ajax_answernobrowsepage() {
        $this->autoRender = FALSE;
        $uid = $this->Auth->user('id');
        $this->loadModel('Faq.FaqResult');
        $this->loadModel('Faq.Faq');
//        $faq_id = $this->request->data['faq_id'];
        $response = true;
        if (empty($this->request->data['faqhelpful'])) {
            $response = FALSE;
            $message = __d('faq', 'You need to select reason !');
        }
        $faq_id = $this->request->data['faq_id'];
        if ($response) {
            $data['faq_id'] = $this->request->data['faq_id'];
            $data['user_id'] = $uid;
            $data['vote'] = 0;
            $data['helpfull_id'] = $this->request->data['faqhelpful'];
            $result = $this->FaqResult->getResults($faq_id, $uid);
            if ($result) {
                $this->FaqResult->delete($result[0]['FaqResult']['id']);
            }

            $this->FaqResult->save($data);
            $count_faq = $this->FaqResult->getTotalByFaqId($faq_id);
            $count_yes = $this->FaqResult->getTotalByFaqId($faq_id, TRUE, FALSE);
            $per = ($count_yes / $count_faq) * 100;
            $dt['id'] = $faq_id;
            $dt['per_usefull'] = $per;
            $dt['total_yes'] = $count_yes;
            $dt['total_no'] = $count_faq - $count_yes;
            $this->Faq->save($dt);
            $message = __d('faq', 'Thanks for your feedback!');
        }
//        $this->Session->setFlash(__d('faq', 'Thanks for your feedback!'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $faq = $this->Faq->findById($faq_id);
        $last_update = $faq['Faq']['modified'];
        $last_result = $this->FaqResult->getLastUpdate($faq_id);
        if (!empty($last_result))
            $last_update = $last_result['FaqResult']['modified'];
        $choice = 2; //not choise
        $choice_id = 0; //not choise
        if (!empty($result)) {
            $choice = $result[0]['FaqResult']['vote'];
            $choice_id = $result[0]['FaqResult']['helpfull_id'];
        }

        $this->set('faq', $faq);
        $this->set('choice', $choice);
        $this->set('choice_id', $choice_id);
        $this->set('last_update', $last_update);
        $this->set('response', $response);
        $this->set('settings', true);
        $this->set('message', $message);
        $this->render('Faq.Elements/lists/faqs_answer');
    }

    public function answer($faq_id = null) {
        $this->autoRender = FALSE;

        $this->loadModel('Faq.FaqResult');
        $this->loadModel('Faq.Faq');
        $uid = $this->Auth->user('id');

        $data['faq_id'] = $faq_id;
        $data['user_id'] = $uid;
        $data['vote'] = 1;
        $data['helpfull_id'] = 0;
        $result = $this->FaqResult->getResults($faq_id, $uid);
        if ($result) {
            $this->FaqResult->delete($result[0]['FaqResult']['id']);
        }
        $this->FaqResult->save($data);
        $count_faq = $this->FaqResult->getTotalByFaqId($faq_id);
        $count_yes = $this->FaqResult->getTotalByFaqId($faq_id, TRUE, FALSE);
        $per = ($count_yes / $count_faq) * 100;
        $dt['id'] = $faq_id;
        $dt['per_usefull'] = $per;
        $dt['total_yes'] = $count_yes;
        $dt['total_no'] = $count_faq - $count_yes;
        $this->Faq->save($dt);
//        $this->Session->setFlash(__d('faq', 'Thanks for your feedback!'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        return $this->redirect('/faqs/view/'.$faq_id.'/name/1');
    }

    public function answeryes($faq_id = null) {
        $this->autoRender = FALSE;
        $this->loadModel('Faq.FaqResult');
        $this->loadModel('Faq.Faq');
        $uid = $this->Auth->user('id');

        $data['faq_id'] = $faq_id;
        $data['user_id'] = $uid;
        $data['vote'] = 1;
        $data['helpfull_id'] = 0;
        $result = $this->FaqResult->getResults($faq_id, $uid);
        if ($result) {
            $this->FaqResult->delete($result[0]['FaqResult']['id']);
        }
        $this->FaqResult->save($data);
        $count_faq = $this->FaqResult->getTotalByFaqId($faq_id);
        $count_yes = $this->FaqResult->getTotalByFaqId($faq_id, TRUE, FALSE);
        $per = ($count_yes / $count_faq) * 100;
        $dt['id'] = $faq_id;
        $dt['per_usefull'] = $per;
        $dt['total_yes'] = $count_yes;
        $dt['total_no'] = $count_faq - $count_yes;
        $this->Faq->save($dt);
        $faq = $this->Faq->findById($faq_id);
        $last_update = $faq['Faq']['modified'];
        $last_result = $this->FaqResult->getLastUpdate($faq_id);
        if (!empty($last_result))
            $last_update = $last_result['FaqResult']['modified'];
        $choice = 2; //not choise
        $choice_id = 0; //not choise
        if (!empty($result)) {
            $choice = $result[0]['FaqResult']['vote'];
            $choice_id = $result[0]['FaqResult']['helpfull_id'];
        }
        $this->set('faq', $faq);
        $this->set('choice', $choice);
        $this->set('choice_id', $choice_id);
        $this->set('last_update', $last_update);
        $this->set('settings', true);
        $this->set('response', true);
        $this->set('message', __d('faq', 'Thanks for your feedback!'));
        $this->render('Faq.Elements/lists/faqs_answer');
    }

}

