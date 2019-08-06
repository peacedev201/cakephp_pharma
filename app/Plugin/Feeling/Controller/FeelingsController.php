<?php 
class FeelingsController extends FeelingAppController{
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

        $this->url_categories = '/feelings';
        $this->set('url_categories', '/feelings');
    }

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkPermission(array('super_admin' => 1));
        $this->loadModel('Feeling.Feeling');
    }

    public function admin_ajax_translate($id) {

        if (!empty($id)) {
            $feeling = $this->Feeling->findById($id);
            $this->set('feeling', $feeling);
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
                $this->Feeling->id = $this->request->data['id'];
                foreach ($this->request->data['label'] as $lKey => $sContent) {
                    $this->Feeling->locale = $lKey;
                    if ($this->Feeling->saveField('label', $sContent)) {
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

    public function admin_index($category_id = null)
    {
        $category_id = intval($category_id);

        if(!empty($category_id)){
            $cond = array();
            $cond['Feeling.category_id'] = $category_id;

            //$viewer = MooCore::getInstance()->getViewer();
            /*$this->Paginator->settings = array(
                'limit' => Configure::read('Comic.comic_item_per_pages'),
                'order' => array('ComicChapter.id' => 'ASC')
            );*/
            $aFeelings = $this->paginate('Feeling', $cond);

            $this->loadModel('Feeling.FeelingCategory');
            $aCategory = $this->FeelingCategory->findById($category_id);
            $this->set('aFeelingCategory', $aCategory);
            $this->set('aFeelings', $aFeelings);
            $this->set('pCategoryId', $category_id);
            $this->set('title_for_layout', __d('feeling', 'Feeling Manager'));
        }else{
            $aFeelings = $this->paginate('Feeling');
            $this->set('aFeelings', $aFeelings);
            $this->set('pCategoryId', 0);
            $this->set('title_for_layout', __d('feeling', 'Feeling Manager'));
        }
    }
    public function index(){

    }

    public function admin_create($feeling_id = null, $category_id = null) {
        $bIsEdit = false;
        $this->_checkPermission(array('super_admin' => 1));
        $iFeelingId = intval($feeling_id);
        $iCategoryId = intval($category_id);

        $this->loadModel('Feeling.FeelingCategory');
        $categories = $this->FeelingCategory->getCategoriesList();

        if(!empty($iCategoryId)){
            $this->loadModel('Feeling.FeelingCategory');
            $aCategory = $this->FeelingCategory->findById($category_id);
            if(!empty($aCategory)){
                $this->set('pCategoryId', $iCategoryId);
                $this->set('aCategory', $aCategory);
                $categories = null;
            }else{
                $this->set('pCategoryId', 0);
                $this->set('aCategory', null);
            }
        }else{
            $this->set('pCategoryId', 0);
            $this->set('aCategory', null);
        }

        if(!empty($iFeelingId)){
            $bIsEdit = true;
            $aFeeling = $this->Feeling->findById($iFeelingId);
            $this->_checkExistence($aFeeling);
        }else{
            $aFeeling = $this->Feeling->initFields();
            $this->set('title_for_layout', __('Write New Entry'));
        }

        $this->set('bIsEdit', $bIsEdit);
        $this->set('aCategories', $categories);
        $this->set('aFeeling', $aFeeling);
        $this->set('category_id', $iCategoryId);
    }

    public function admin_save_validate() {
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $this->Feeling->set($this->request->data);
        $this->_validateData($this->Feeling);

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
            $this->Feeling->id = $this->request->data['id'];
        }

        $old = 0;
        if (!empty($this->request->data['id'])) {
            $old_Felling = $this->Feeling->findById($this->request->data['id']);
            $old = 1;
        }

        $parentCategoryId = 0;
        if( !empty($this->request->data['parentCategoryId']) ){
            $parentCategoryId = $this->request->data['parentCategoryId'];
            unset( $this->request->data['parentCategoryId'] );
        }

        $this->Feeling->set($this->request->data);
        if ($this->Feeling->save()) {

            if (!$bIsEdit) {
                $this->Feeling->saveField('order', $this->Feeling->id);
                $this->loadModel('Language');
                foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                    $this->Feeling->locale = $lKey;
                    $this->Feeling->saveField('label', $this->request->data['label']);
                }
            }

            if (isset($old_Felling) && $old_Felling['Feeling']['icon'] != $this->request->data['icon'])
            {
                $objectModel = MooCore::getInstance()->getModel("Storage.StorageAwsObjectMap");
                $objectModel->destroy($old_Felling['Feeling']['id'],'feelings');
            }

            Cache::clearGroup('feeling');

            $this->Feeling->clear();
            $this->Session->setFlash(__d('feeling', 'Feeling Status has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        } else {
            $this->Session->setFlash(__d('feeling', 'Feeling Status is not saved'), 'default', array('class' => 'Metronic-alerts alert alert-danger'), 'flash');
        }

        if(!empty($parentCategoryId)){
            $this->redirect(Router::url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_index', $parentCategoryId), true));
            exit();
        }
        $this->redirect(Router::url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_index'), true));
    }

    public function admin_delete($feeling_id = null, $category_id = null) {
        $iFeelingId = intval($feeling_id);
        $iCategoryId = intval($category_id);
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        $aFeeling = $this->Feeling->findById($iFeelingId);
        $this->_checkExistence($aFeeling);
        $this->Feeling->clear();
        $this->Feeling->delete($feeling_id);
        $this->Feeling->clear();

        Cache::clearGroup('feeling');

        $this->Session->setFlash(__d('feeling', 'Feeling Status has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

        if(!empty($iCategoryId)){
            $this->redirect(Router::url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_index', $iCategoryId), true));
            exit();
        }
        $this->redirect(Router::url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_index'), true));
    }

    public function admin_multi_delete($category_id = null) {
        $iCategoryId = intval($category_id);
        $this->autoRender = false;
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($_POST['feelingIds'])) {
            foreach ($_POST['feelingIds'] as $iReasonId) {
                $this->Feeling->clear();
                $this->Feeling->delete($iReasonId);
                $this->Feeling->clear();
            }

            Cache::clearGroup('feeling');

            $this->Session->setFlash(__d('feeling', 'Feelings Status has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        if(!empty($iCategoryId)){
            $this->redirect(Router::url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_index', $category_id), true));
            exit();
        }
        $this->redirect(Router::url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_index'), true));
    }

    public function admin_visiable() {
        $this->autoRender = false;
        $id = intval($this->request->data['id']);
        $value = intval($this->request->data['value']);
        $response['result'] = 0;
        if (!empty($id)) {
            $aFeeling = $this->Feeling->findById($id);
            $data = $aFeeling['Feeling'];
            $data['active'] = $value;
            $this->Feeling->id = $id;
            $this->Feeling->set($data);
            if($this->Feeling->save()){
                Cache::clearGroup('feeling');

                $this->Feeling->clear();
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
            //$this->Feeling->id = $id;
            //$this->Feeling->save(array('order' => $value));
            $aFeeling = $this->Feeling->findById($id);
            $data = $aFeeling['Feeling'];
            $data['order'] = $value;
            $this->Feeling->id = $id;
            $this->Feeling->set($data);
            $this->Feeling->save();
            $this->Feeling->clear();
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
        //$background_status_layouts = Configure::read('background_status_layouts');

        //$background_status = Cache::read('status_background.all', 'status_background');
        //if(!$background_status){
        //$background_status = $this->StatusBackground->getStatusBackground();
        //    Cache::write('status_background.all',$background_status, 'status_background');
        //}

        /*$index = 0;
        $background_status_layouts = array(
            array('index' => $index, 'id' => 0, 'width' => 563, 'height' => 407, 'maxHeightBg' => 223, 'type' => 'color', 'style' => 'background-color: #e9e9eb;', 'textColor' => '#000')
        );
        if(!empty($background_status)){
            $backgroundHelper = MooCore::getInstance()->getHelper('StatusBackground_StatusBackground');

            foreach ($background_status As $key => $background){
                $index++;

                $style =  $backgroundHelper->getStatusBackgroundStyle($background);

                $background_status_layouts[] = array(
                    'index' => $index,
                    'id' => $background['StatusBackground']['id'],
                    'width' => $background['StatusBackground']['width'],
                    'height' => $background['StatusBackground']['height'],
                    'maxHeightBg' => $background['StatusBackground']['max_height'],
                    'type' => $background['StatusBackground']['type'],
                    'style' => $style,
                    'textColor' => $background['StatusBackground']['text_color']
                );
            }
        }

        echo json_encode($background_status_layouts);*/

        echo json_encode(array(
            array(
                'id' => 1,
                'image' => $this->request->webroot.'feeling/img/emoji-1.png',
                'text' => 'Category 1',
                'items' => array(
                    array(
                        'id' => 1,
                        'image' => $this->request->webroot.'feeling/img/emoji-2.png',
                        'text' => 'Item 1.1',
                        'type' => 'icon',
                        'link' => ''
                    ),
                    array(
                        'id' => 2,
                        'image' => $this->request->webroot.'feeling/img/emoji-3.png',
                        'text' => 'Item 1.2',
                        'type' => 'icon',
                        'link' => ''
                    ),
                    array(
                        'id' => 3,
                        'image' => $this->request->webroot.'feeling/img/emoji-4.png',
                        'text' => 'Item 1.3',
                        'type' => 'icon',
                        'link' => ''
                    )
                )
            ),
            array(
                'id' => 2,
                'image' => $this->request->webroot.'feeling/img/emoji-5.png',
                'text' => 'Category 2',
                'items' => array(
                    array(
                        'id' => 4,
                        'image' => $this->request->webroot.'feeling/img/emoji-6.png',
                        'text' => 'Item 2.1',
                        'type' => 'link',
                        'link' => ''
                    ),
                    array(
                        'id' => 5,
                        'image' => $this->request->webroot.'feeling/img/emoji-7.png',
                        'text' => 'Item 2.2',
                        'type' => 'icon',
                        'link' => ''
                    ),
                    array(
                        'id' => 6,
                        'image' => $this->request->webroot.'feeling/img/emoji-8.png',
                        'text' => 'Item 2.3',
                        'type' => 'icon',
                        'link' => ''
                    )
                )
            ),
            array(
                'id' => 3,
                'image' => $this->request->webroot.'feeling/img/emoji-9.png',
                'text' => 'Category 3',
                'items' => array(
                    array(
                        'id' => 7,
                        'image' => $this->request->webroot.'feeling/img/emoji-10.png',
                        'text' => 'Item 3.1',
                        'type' => 'icon',
                        'link' => ''
                    ),
                    array(
                        'id' => 8,
                        'image' => $this->request->webroot.'feeling/img/emoji-11.png',
                        'text' => 'Item 3.2',
                        'type' => 'icon',
                        'link' => ''
                    ),
                    array(
                        'id' => 9,
                        'image' => $this->request->webroot.'feeling/img/emoji-12.png',
                        'text' => 'Item 3.3',
                        'type' => 'icon',
                        'link' => ''
                    )
                )
            )
        ));

        exit;
    }
}