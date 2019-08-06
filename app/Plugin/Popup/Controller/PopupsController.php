<?php
ob_start();
class PopupsController extends PopupAppController{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->loadModel('Popup.Popup');
        $this->loadModel('Popup.PopupSave');

    }
    public function admin_index()
    {
        $this->_checkPermission(array('super_admin' => true));
        $keyword = "";
        if ( !empty( $this->request->data['keyword'] ) )
            $keyword = $this->request->data['keyword'];
        $popups = $this->paginate('Popup'," Popup.title like '%{$keyword}%' ");
        $this->set('popups',$popups);
        $this->set('title_for_layout', __d('popup','Popups Manager'));
    }
    public function admin_create($id = null, $language = null)
    {
        $this->_checkPermission(array('super_admin' => true));
        if ( !empty( $id ) )
        {
            $langs = $this->Language->find('all');

            if (!$language)
            {
                foreach ($langs as $lang)
                {
                    $language = $lang['Language']['key'];
                    break;
                }
            }

            $tmp = array();
            foreach ($langs as $lang)
            {
                $tmp[$lang['Language']['key']] = $lang['Language']['name'];
            }

            $this->set('languages', $tmp);
            $this->set('language', $language);
            $this->Popup->locale = $language;

            $popup = $this->Popup->findById($id);
            $this->_checkExistence($popup);
            //$conditions = array('conditions'=>array('Page.id !=' => $id));
            //$params = unserialize($page['Page']['params']);

            $this->set('title_for_layout', __d('popup','Edit Popup'));
        }
        else {
            $popup = $this->Popup->initFields();
            //$params = array('comments' => 1);

            $this->set('title_for_layout', __d('popup','Create New Popup' ));
        }
        $all_pages = $this->Page->find('all', array(
            'conditions' => array('NOT' => array('uri' => 'site.header'), 'uri not like'=>'site.footer'),
        ));
        $tmp = array('0' => 'All Page');
        foreach($all_pages as &$single_page){
            $tmp[$single_page['Page']['id']] = $single_page['Page']['title'] ;
        }
        $all_pages = $tmp;
        // get all roles
        $this->loadModel('Role');
        $roles = $this->Role->find('all');

        $this->set('popup', $popup);
        $this->set('roles', $roles);
        $this->set('all_pages', $all_pages);
    }
    public function admin_save()
    {
        $this->_checkPermission(array('super_admin' => true));
        $this->loadModel('CoreContent');
        $uid = $this->Auth->user('id');
        $this->autoRender = false;
        $old = 0;
        if ( !empty( $this->request->data['id'] ) ){
            $this->Popup->id = $this->request->data['id'];
            $old = 1;//to check if page existed or not
        }


        $this->request->data['permission'] = (empty( $this->request->data['everyone'] )) ? implode(',', $_POST['permissions']) : '';
        if($this->request->data['onetime'] == 1)
            $this->request->data['popup_option'] = 0;
        $data = $this->request->data;
        unset($data['title']);
        unset($data['body']);
        $this->Popup->set( $data );
        echo $this->_validateData($this->Popup);

        $popup_enable = null;
        if($this->request->data['enable'] == 1){
            $this->loadModel('Page.Page');
            $_conditions = null;
            if($old == 1){
                $_conditions = array('Popup.id' => $this->request->data['id']);
            }
            $popup_normal = $this->Popup->find('all', array(
                'conditions' => array('enable' => '1', 'NOT' => array($_conditions,'page_id' => 0)),
            ));
            $popup_allpage = $this->Popup->find('all', array(
                'conditions' => array('page_id' => 0, 'enable' => '1','NOT' => array($_conditions)),
            ));
            //all page > 0
            if(count($popup_allpage) > 0){
                $response['result'] = 0;
                $response['message'] = __d('popup',"You have popup for all page. Please disable this popup to continue");
                unset($popup_enable);
                echo json_encode($response);
                exit();
            }

            //check double popup on 1 page
            if(count($popup_normal) > 0){
                //var_dump($popup_normal);
                $count = 0;
                foreach ($popup_normal as $p)
                {
                    if($p['Popup']['page_id'] == $this->request->data['page_id'])
                        $count++;
                }
                if($count > 0){
                    $response['result'] = 0;
                    $response['message'] = __d('popup',"Can't add more than two active popups into the selected page each time. Please disable one of them to continue.");
                    unset($popup_enable);
                    echo json_encode($response);
                    exit();
                }
            }

            //check insert update all page
            if($this->request->data['page_id'] == 0){
                if($t = (count($popup_normal)+count($popup_allpage)) > 0){
                    $response['result'] = 2;
                    $response['message'] = __d('popup',"You are having %s popup enable. Do you want disable all popup?",h($t));
                    unset($popup_enable);
                    echo json_encode($response);
                    exit();
                }
            }
        }
        /**
         * insert or update data
         */
        $newPopupId = 0;
        // To do
        if($this->Popup->save())
        {
            $newPopupId = $this->Popup->id;
            if (!$old) {
                foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                    $this->Popup->locale = $lKey;
                    $this->Popup->saveField('title', $this->request->data['title']);
                    $this->Popup->saveField('body', $this->request->data['body']);
                }
            }
            else
            {
                $this->Popup->locale = $this->request->data['language'];
                $this->Popup->saveField('title', $this->request->data['title']);
                $this->Popup->saveField('body', $this->request->data['body']);
            }
            //Save image
            if (!empty($this->request->data['popup_photo_ids'])) {
                $photos = explode(',', $this->request->data['popup_photo_ids']);
                if (count($photos))
                {
                    $this->loadModel('Photo.Photo');
                    // Hacking for cdn
                    $result = $this->Photo->find("all",array(
                        'recursive'=>1,
                        'conditions' =>array(
                            'Photo.type' => 'Popup',
                            'Photo.user_id' => $uid,
                            'Photo.id' => $photos
                        )));
                    if($result){
                        $view = new View($this);
                        $mooHelper = $view->loadHelper('Moo');
                        foreach ($result as $iPhoto){
                            $iPhoto["Photo"]['moo_thumb'] = 'thumbnail';
                            $mooHelper->getImageUrl($iPhoto, array('prefix' => '450'));
                            $mooHelper->getImageUrl($iPhoto, array('prefix' => '1500'));
                        }
                        // End hacking
                        $this->Photo->updateAll(array('Photo.target_id' => $this->Popup->id), array(
                            'Photo.type' => 'Popup',
                            'Photo.user_id' => $uid,
                            'Photo.id' => $photos
                        ));
                    }

                }
            }
        }

        $this->Session->setFlash(__d('popup','Popup has been successfully saved'),'default',
            array('class' => 'Metronic-alerts alert alert-success fade in' ));
        $response['result'] = 1;
        $response['popup_id'] = $newPopupId;
        $this->PopupSave->deleteAll(['popup_id' => $newPopupId]);
        echo json_encode($response);
        /**
         * end insert or update data
         */

    }
    public function admin_delete()
    {
        $this->_checkPermission(array('super_admin' => true));
        if ( !empty( $_POST['popups'] ) )
        {
            $popups = $this->Popup->findAllById($_POST['popups']);
            foreach ($popups as $popup){
                $this->Popup->delete($popup['Popup']['id']);
            }
            $this->PopupSave->deleteAll(['popup_id' => $_POST['popups']]);
            $this->Session->setFlash( __d('popup','Popups have been deleted') , 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
        }
        $this->redirect( array(
            'plugin' => 'popup',
            'controller' => 'popups',
            'action' => 'admin_index'
        ) );
    }
    public function admin_disable($id = null)
    {
        $this->_checkPermission(array('super_admin' => true));
        $this->autoRender = false;
        if($id == null){
            $this->Popup->updateAll(array('enable' => 0));
        }
        else{
            $data = array('id' => $id, 'enable' => 0);
            $this->Popup->save($data);
        }
        $response['message'] = __d('popup',"Disable Successfully! Click \"Save\" button again to save the new popup!");
        echo json_encode($response);
    }
    public function write_session_popup()
    {
        $this->write_session('popup_option');
    }
    public function write_session_onetime()
    {
        $this->write_session('one_time');
    }
    public function write_session($key = 'popup')
    {
        $this->autoRender = false;
        if($this->request->is('post')) {
            $one_time['id'] = $this->request->data['idpopup'];
            $one_time['value'] = $this->request->data['valuepopup'];
            isset($_COOKIE[$key])? $data = json_decode($_COOKIE[$key],true): $data = null;
            $flag_double = false;
            if (!empty($data)) {
                foreach ($data as &$item) {
                    if ($item['id'] === $one_time['id']) {
                        $item['value'] = $one_time['value'];
                        $flag_double = true;
                    }
                }
            }

            if (!$flag_double) {
                $data[] = $one_time;
            }

            setcookie($key, json_encode($data),time() + (86400 * 30), "/");
            $uid = MooCore::getInstance()->getViewer(true);
            if($uid)
            {
                if($one_time['value'] == 0)
                {
                    $dt['user_id'] = $uid;
                    $dt['popup_id'] = $this->request->data['idpopup'];
                    $this->PopupSave->save($dt);
                }
                else
                {
                    $this->PopupSave->deleteAll([
                        'user_id'=>$uid,
                        'popup_id' => $one_time['id']
                    ]);
                }

            }
            //return json_decode($one_time);
        }
    }

}