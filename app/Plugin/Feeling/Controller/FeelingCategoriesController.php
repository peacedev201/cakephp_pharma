<?php
/**
* 
*/
class FeelingCategoriesController extends FeelingAppController
{
    public $paginate = array(
        'limit' => RESULTS_LIMIT,
        'order' => array(
            'order' => 'ASC',
            'id' => 'ASC'
        )
    );

	public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);

        $this->url_categories = '/feeling_categories';
        $this->set('url_categories', '/feeling_categories');
    }

    public function beforeFilter()
	{
		parent::beforeFilter();
		//$this->_checkPermission(array('super_admin' => 1));
        $this->loadModel('Feeling.FeelingCategory');
	}

    public function admin_ajax_translate($id) {

        if (!empty($id)) {
            $category = $this->FeelingCategory->findById($id);
            $this->set('category', $category);
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
                $this->FeelingCategory->id = $this->request->data['id'];
                foreach ($this->request->data['label'] as $lKey => $sContent) {
                    $this->FeelingCategory->locale = $lKey;
                    if ($this->FeelingCategory->saveField('label', $sContent)) {
                        $response['result'] = 1;
                    } else {
                        $response['result'] = 0;
                    }
                }

                Cache::clearGroup('feeling');
            } else {
                $response['result'] = 0;
            }
        } else {
            $response['result'] = 0;
        }
        echo json_encode($response);
    }

	public function admin_index(){
		$aCategories = $this->paginate('FeelingCategory');
        $this->set('aFeelingCategories', $aCategories);
        $this->set('title_for_layout', __d('feeling','Feeling status Manager'));
    }

    public function admin_create($id = null) {
        $iCategoryId = intval($id);
        $this->_checkPermission(array('super_admin' => 1));
        $bIsEdit = false;

        if (!empty($iCategoryId)) {
            $bIsEdit = true;
            $aCategories = $this->FeelingCategory->findById($iCategoryId);
            $this->_checkExistence($aCategories);
        } else {
            $aCategories = $this->FeelingCategory->initFields();
        }
        $this->set('bIsEdit', $bIsEdit);
        $this->set('aFellingCategories', $aCategories);
    }

    public function admin_save_validate() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $this->FeelingCategory->set($this->request->data);
        $this->_validateData($this->FeelingCategory);

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_save() {
        //echo __METHOD__; die();
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));
        $bIsEdit = false;
        if (!empty($this->request->data['id'])) {
            $bIsEdit = true;
            $this->FeelingCategory->id = $this->request->data['id'];
        }

        $old = 0;
        if (!empty($this->request->data['id'])) {
            $old_FellingCategory = $this->FeelingCategory->findById($this->request->data['id']);
            $old = 1;
        }

        $this->FeelingCategory->set($this->request->data);
        if ($this->FeelingCategory->save()) {

            if (!$bIsEdit) {
                $this->FeelingCategory->saveField('order', $this->FeelingCategory->id);
                $this->loadModel('Language');
                foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                    $this->FeelingCategory->locale = $lKey;
                    $this->FeelingCategory->saveField('label', $this->request->data['label']);
                }
            }

            if (isset($old_FellingCategory) && $old_FellingCategory['FeelingCategory']['photo'] != $this->request->data['photo'])
            {
                $objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
                $objectModel->destroy($old_FellingCategory['FeelingCategory']['id'],'feeling_categories');
            }

            Cache::clearGroup('feeling');

            $this->FeelingCategory->clear();
            $this->Session->setFlash(__d('feeling', 'Category has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        } else {
            $this->Session->setFlash(__d('feeling', 'Category is not saved'), 'default', array('class' => 'Metronic-alerts alert alert-danger'), 'flash');
        }

        $this->redirect(Router::url(array('plugin' => 'feeling', 'controller' => 'feeling_categories', 'action' => 'admin_index'), true));
    }

    public function admin_delete($id) {
        $iCategoryId = intval($id);
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $aCategory = $this->FeelingCategory->findById($iCategoryId);
        $this->_checkExistence($aCategory);
        $this->FeelingCategory->clear();
        $this->FeelingCategory->delete($id);
        $this->FeelingCategory->clear();

        $this->loadModel('Feeling.Feeling');
        $this->Feeling->deleteAll(array('Feeling.category_id' => $id));

        Cache::clearGroup('feeling');

        $this->Session->setFlash(__d('feeling', 'Category has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect(Router::url(array('plugin' => 'feeling', 'controller' => 'feeling_categories', 'action' => 'admin_index'), true));
    }

    public function admin_multi_delete() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($_POST['categoriesIds'])) {
            $this->loadModel('Feeling.Feeling');

            $this->FeelingCategory->deleteAll(array('FeelingCategory.id IN' => $_POST['categoriesIds']));
            $this->Feeling->deleteAll(array('Feeling.category_id IN' => $_POST['categoriesIds']));

            Cache::clearGroup('feeling');

            $this->Session->setFlash(__d('feeling', 'Categories has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $this->redirect(Router::url(array('plugin' => 'feeling', 'controller' => 'feeling_categories', 'action' => 'admin_index'), true));
    }

    public function admin_visiable() {
        $this->autoRender = false;
        $id = intval($this->request->data['id']);
        $value = intval($this->request->data['value']);
        $response['result'] = 0;
        if (!empty($id)) {
            $aFeelingCategory = $this->FeelingCategory->findById($id);
            $data = $aFeelingCategory['FeelingCategory'];
            $data['active'] = $value;
            $this->FeelingCategory->id = $id;
            $this->FeelingCategory->set($data);
            if($this->FeelingCategory->save()){
                Cache::clearGroup('feeling');
                $this->FeelingCategory->clear();
                $response['result'] = 1;
            }
        }
        $response['id'] = $id;
        $response['value'] = $value;
        echo json_encode($response);
        die();
    }

    public function admin_save_order() {
        $this->autoRender = false;

        foreach ($this->request->data['cate'] as $id => $value) {
            //$this->FeelingCategory->id = $id;
            //$this->FeelingCategory->save(array('order' => $value));
            $aFeelingCategory = $this->FeelingCategory->findById($id);
            $data = $aFeelingCategory['FeelingCategory'];
            $data['order'] = $value;
            $this->FeelingCategory->id = $id;
            $this->FeelingCategory->set($data);
            $this->FeelingCategory->save();
            $this->FeelingCategory->clear();
        }

        Cache::clearGroup('feeling');
        $this->Session->setFlash(__d('feeling', 'Order saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        echo $this->referer();
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

    public function admin_upload_thumbnail($max_width = null, $max_height = null) {
        $this->autoRender = false;
        // save this picture to album

        //$path = 'uploads' . DS . 'feeling';
        //$url = 'uploads/feeling/';

        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';

        $this->_prepareDir($path);

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = MooCore::getInstance()->_getMaxFileSize();

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions, $maxFileSize);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            // resize image
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

            //$result['url'] = $url;
            //$result['path'] = $this->request->base . '/uploads/feeling/' . $result['filename'];
            //$result['file'] = $path . DS . $result['filename'];

            $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
            $result['file_path'] = $path . DS . $result['filename'];
            //$result['image'] = $path . DS .$result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }

    public function ajax_get_feelings(){
        $data = array();

        $current_locale = Configure::read('Config.language');
        $dataFeelings = Cache::read('feeling.data_feelings.'.$current_locale,'feeling');

        if(!$dataFeelings){
            $categories = $this->FeelingCategory->find('all', array('conditions' => array('FeelingCategory.active' => true), 'order' => 'FeelingCategory.order ASC, FeelingCategory.id ASC'));
            if(!empty($categories)){
                $feelingHelper = MooCore::getInstance()->getHelper('Feeling_Feeling');
                $this->loadModel('Feeling.Feeling');
                foreach ($categories As $key => $category){
                    $feelings = $this->Feeling->find('all', array('conditions' => array('Feeling.category_id' => $category['FeelingCategory']['id'],'Feeling.active' => true), 'order' => 'Feeling.order ASC, Feeling.id ASC'));
                    if(!empty($feelings)){
                        $dataItems = array();
                        foreach ($feelings As $feeling){
                            $dataItems[] = array(
                                'id' => $feeling['Feeling']['id'],
                                'image' => $feelingHelper->getFeelingImage($feeling, array('prefix' => '32_square')),
                                'text' => $feeling['Feeling']['label'],
                                'type' => $feeling['Feeling']['type'],
                                'link' => $feeling['Feeling']['link']
                            );
                        }
                        $data[] = array(
                            'id' => $category['FeelingCategory']['id'],
                            'image' => $feelingHelper->getCategoryImage($category, array('prefix' => '32_square')),
                            'text' => $category['FeelingCategory']['label'],
                            'items' => $dataItems
                        );
                    }
                }
                Cache::write('feeling.data_feelings.'.$current_locale, $data,'feeling');
            }
        }else{
            $data = $dataFeelings;
        }
        $this->autoRender = false;
        echo json_encode($data);
        exit;
    }
}