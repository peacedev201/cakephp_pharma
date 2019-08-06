<?php 
class ForumCategoriesController extends ForumAppController{
    public function admin_index()
    {
    }
    public function index()
    {
    }
    public function admin_create($id = null, $language = null)
    {
        $this->_checkPermission( array( 'super_admin' => true ) );
        $bIsEdit = false;
        $forum_cat = null;
        if (!empty($id)) {
            $forum_cat = $this->ForumCategory->getForumCategoryById($id);
            $bIsEdit = true;
        }
        else {
            $forum_cat = $this->ForumCategory->initFields();
        }
        $this->set('forum_cat',$forum_cat);
        $this->set('bIsEdit',$bIsEdit);
    }
    public function admin_ajax_translate($id) {

        if (!empty($id)) {
            $forum_cat = $this->ForumCategory->getForumCategoryById($id);
            $this->set('forum_cat', $forum_cat);
            $this->set('languages', $this->Language->getLanguages());
        } else {
            // error
        }
    }
    public function admin_save()
    {
        $this->autoRender = false;
        $bIsEdit = false;
        $data['name'] = $this->request->data['name'];
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->ForumCategory->id = $this->request->data['id'];
            if(!empty($this->request->data['thumb']))
                $data['thumb'] = $this->request->data['thumb'];
        }
        else {
            $data['thumb'] = $this->request->data['thumb'];
        }
        $data['order'] = intval($this->request->data['order']);

        if(empty($data['name'])){
            $response['result'] = 0;
            $response['message'] = __d('forum','Forum name can not empty');
            echo json_encode($response);
            exit();
        }
        if($bIsEdit == false && empty($data['thumb'])){
            $response['result'] = 0;
            $response['message'] = __d('forum','Please select icon for forum');
            echo json_encode($response);
            exit();
        }
        $this->ForumCategory->set($data);
        $this->ForumCategory->save();
        if (!$bIsEdit) {
            foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                $this->ForumCategory->locale = $lKey;
                $this->ForumCategory->saveField('name', $this->request->data['name']);
            }
        }
        $this->Session->setFlash(__d('forum','Forum category has been successfully saved'),'default',
            array('class' => 'Metronic-alerts alert alert-success fade in' ));
        $response['result'] = 1;
        echo json_encode($response);
    }
    public function admin_ajax_translate_save() {

        $this->autoRender = false;
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                // we are going to save the german version
                $this->ForumCategory->id = $this->request->data['id'];
                foreach ($this->request->data['name'] as $lKey => $sContent) {
                    $this->ForumCategory->locale = $lKey;
                    if ($this->ForumCategory->saveField('name', $sContent)) {
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

    public function admin_delete_confirm()
    {
        $this->autoRender = false;
        $id = $this->request->data['id'];
        $forumModel = MooCore::getInstance()->getModel('Forum.Forum');
        $forums = $forumModel->find('count',array(
            'conditions' => array(
                'Forum.category_id' => $id
            ),
        ));
        if(empty($forums))
        {
            $response['result'] = 1;
            $response['id'] = $id;
            $response['message'] = __d('forum', 'Are you sure you want to delete? All of topics inside of this forum will also be deleted');
            echo json_encode($response);
            exit();
        }
        else
        {
            $response['result'] = 0;
            $response['message'] = __d('forum','Before deleting a forum category, please delete all forums in the forum category');
            echo json_encode($response);
            exit();
        }
    }

    public function admin_delete($id = 0)
    {
        $this->autoRender = false;
        if($id > 0)
        {
            $result = $this->ForumCategory->delete($id);
            if($result == true)
            {
                $this->Session->setFlash(__d('forum','Forum category has been deleted!'),'default',
                    array('class' => 'Metronic-alerts alert alert-success fade in' ));
                $response['result'] = 1;
                echo json_encode($response);
                exit();
            }
            else
            {
                $response['result'] = 0;
                $response['message'] = __d('forum','Cannot delete Forum Category');
                echo json_encode($response);
                exit();
            }
        }
        else
        {
            $response['result'] = 0;
            $response['message'] = __d('forum','Not available');
            echo json_encode($response);
            exit();
        }
    }
}