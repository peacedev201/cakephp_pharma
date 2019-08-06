<?php 
class DocumentsController extends DocumentAppController{
	public $components = array('Paginator');
	
	public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Category');
    }
	
    public function admin_index()
    {
    	$this->set('title_for_layout', __d('document','Documents'));
    	$this->loadModel('Document.Document');
    	$this->Paginator->settings = array(
            'limit' => Configure::read('Document.document_item_per_pages'),
            'order' => array(
                'Document.id' => 'DESC'
            )
        );
        
        $cond = array();
        $passedArgs = array();
        $named = $this->request->params['named'];
        if ($named)
        {
        	foreach ($named as $key => $value)
        	{
        		$this->request->data[$key] = $value;
        	}
        }
        
    	if ( !empty( $this->request->data['category_id'] ) )
        {
        	$cond['Document.category_id'] = $this->request->data['category_id'];
        	$this->set('category_id',$this->request->data['category_id']);
        	$passedArgs['category_id'] = $this->request->data['category_id'];
        }
        
    	if ( !empty( $this->request->data['title'] ) )
        {
        	$cond['Document.title LIKE'] = '%'.$this->request->data['title'].'%';
        	$this->set('title',$this->request->data['title']);
        	$passedArgs['title'] = $this->request->data['title'];
        }
        
    	if ( isset( $this->request->data['feature'] ) && $this->request->data['feature'] != '' )
        {
        	$cond['Document.feature'] = $this->request->data['feature'];
        	$this->set('feature',$this->request->data['feature']);
        	$passedArgs['feature'] = $this->request->data['feature'];
        }
        
    	if ( isset( $this->request->data['visable'] ) && $this->request->data['visable'] !='' )
        {
        	$cond['Document.visiable'] = $this->request->data['visable'];
        	$this->set('visable',$this->request->data['visable']);
        	$passedArgs['visable'] = $this->request->data['visable'];
        }
        
        $this->loadModel('Category');
    	$categories = $this->Category->getCategoriesList('Document');
    	$this->set('categories',$categories);
    	
    	$documents = $this->Paginator->paginate('Document',$cond);
        $this->set('documents', $documents);
        $this->set('passedArgs',$passedArgs);
    }
    
    public function admin_approve($id = null)
    {
    	$this->loadModel('Document.Document'); 	
    	
   		if ($id)
    	{
    		$this->Document->id = $id;
    		$this->Document->save(array('approve'=>1));
    		
   			$this->loadModel('Activity');
   			$document = $this->Document->findById($id);
           	$privacy = $document['Document']['privacy']; 
           	$this->Activity->updateAll(array('privacy'=>$privacy,'share'=>1),array('action'=>'document_create','item_type'=>'Document_Document','item_id'=>$id));
    		
    		//Send mail to user
    		$document = $this->Document->findById($id);
    		$ssl_mode = Configure::read('core.ssl_mode');
        	$http = (!empty($ssl_mode)) ? 'https' :  'http';
    		$this->MooMail->send($document['User']['email'],'document_approve',
    			array(
    				'document_title' => $document['Document']['moo_title'],
    				'document_link' => $http.'://'.$_SERVER['SERVER_NAME'].$document['Document']['moo_href'],    				
    			)
    		);
    		
    		$this->Session->setFlash( __d('document','Document has been approved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
			$this->redirect( '/admin/document/documents' );
    	}
    }
    
    public function admin_delete($id = null)
    {
    	$this->loadModel('Document.Document');
    	if ($id)
    	{    		
    		$this->Document->delete($id);
    	}
    	
    	$this->Session->setFlash( __d('document','Document has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
		$this->redirect( '/admin/document/documents' );
    }
    
    public function admin_visiable()
    {
    	$this->loadModel('Document.Document');
    	$id = $this->request->data['id'];    	
    	$value = $this->request->data['value'];
    	if ($id)
    	{
    		$this->Document->id = $id;
    		$this->Document->save(array('visiable'=>$value));
    	}
    	die();
    }
	public function admin_feature()
    {
    	$this->loadModel('Document.Document');
    	$id = $this->request->data['id'];    	
    	$value = $this->request->data['value'];
    	if ($id)
    	{
    		$this->Document->id = $id;
    		$this->Document->save(array('feature'=>$value));
    	}
    	die();
    	
    }
    public function index()
    {
    	$this->set('title_for_layout', '');
    	$params = $this->request->params['named'];
    	if (isset($params['category']) && $params['category'])
    	{
    		$this->loadModel('Catagory');
    		$category = $this->Category->findById($params['category']);
    		if ($category && !$category['Category']['active'])
    		{
    			$this->redirect( '/documents' );
    		}
    	}
    	
    	if ($this->isApp()) 
    	{
    		App::uses('browseDocumentWidget', 'Document.Controller'.DS.'Widgets'.DS.'document');
    		$widget = new browseDocumentWidget(new ComponentCollection(),null);
    		$widget->beforeRender($this);
    	}
    }
    
    public function delete_file($file_name = null)
    {
    	$this->_checkPermission(array('confirm' => true));
    	if ($file_name)
    	{
    		$dir = APP.'webroot'.DIRECTORY_SEPARATOR.'uploads' . DIRECTORY_SEPARATOR . 'documents'.DIRECTORY_SEPARATOR.'files';    		
	   		@unlink($dir.DIRECTORY_SEPARATOR.$file_name);
    	}
    	die();
    }
    
	public function delete($id = null)
    {
    	$this->loadModel('Document.Document');
    	$document = $this->Document->findById($id);
    	$this->_checkExistence($document);
    	$this->_checkPermission(array('admins' => array($document['User']['id'])));
    	
    	$this->Document->delete($id);
    	$this->Session->setFlash( __d('document','Document has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in') );
    	if (!$this->isApp())
    	{
    		$this->redirect( '/documents' );
    	}
    }
    
    public function view($id = null)
    {   
		$uid = MooCore::getInstance()->getViewer(true); 	
		$viewer = MooCore::getInstance()->getViewer();
    	$this->loadModel('Document.Document');
    	$document = $this->Document->findById($id);
    	
    	$this->Document->recursive = 2;
    	$document= $this->Document->findById($id);
    	if ($document['Category']['id'])
    	{
    		foreach ($document['Category']['nameTranslation'] as $translate)
    		{
    			if ($translate['locale'] == Configure::read('Config.language'))
    			{
    				$document['Category']['name'] = $translate['content'];
    				break;
    			}
    		}
    	}
    	$this->Document->recursive = 0;
    	
    	$this->_checkExistence($document);
    	
    	$this->_checkPermission( array('user_block' => $document['Document']['user_id']) );
		
		//check visiable
		if (!$document['Document']['visiable']  && !($uid && $viewer['Role']['is_admin']))
    	{
    		$this->_checkExistence(null);
    	}
		//check approve
		if (!$document['Document']['approve'] && $uid != $document['Document']['user_id'] && !($uid && $viewer['Role']['is_admin']))
    	{
    		$this->_checkExistence(null);
    	}
    	
    	$this->_checkPermission( array('aco' => 'document_view'));    	
    	
    	MooCore::getInstance()->setSubject($document);
    	    	
    	$areFriends = false;
    	if ($uid)
    	{
    		$this->loadModel('Friend');
    		$areFriends = $this->Friend->areFriends($uid, $document['User']['id']);
    	}
    	$this->_checkPrivacy($document['Document']['privacy'], $document['User']['id'], $areFriends);
    	
    	if ($uid != $document['Document']['user_id'])
    	{
	    	$this->Document->id = $id;
	    	$view_count = $document['Document']['view_count'] + 1;
	    	$this->Document->save(array('view_count'=>$view_count));
    	}
    	
    	$this->set('title_for_layout', $document['Document']['title']);
    	$description = $this->getDescriptionForMeta($document['Document']['description']);
    	if ($description) {
    		$this->set('description_for_layout', $description);
    		$this->loadModel("Tag");
    		$tags = $this->Tag->getContentTags($document['Document']['id'],'Document_Document');
    		if (count($tags))
    		{
    			$tags = implode(",", $tags).' ';
    		}
    		else
    		{
    			$tags = '';
    		}
    		$this->set('mooPageKeyword', $this->getKeywordsForMeta($tags.$description));
    	}    	

        // set og:image
        if ($document['Document']['thumbnail']){
            $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
            $this->set('og_image', $mooHelper->getImageUrl($document));
        }
    	
    	$this->set('document',$document);
    }
    
    public function browse($type = null,$param = null)
    {
    	$this->loadModel('Document.Document');
    	$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
    	$url = ( !empty( $param ) )	? $type . '/' . $param : $type;
    	$uid = MooCore::getInstance()->getViewer(true);
    	$documents = array();
    	$total = 0;
		
    	switch ($type) {
    		case 'all':
    		case 'my':
    		case 'friend':
    		case 'home':
    			$documents = $this->Document->getDocuments(array('type'=>$type,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Document->getTotalDocuments(array('type'=>$type,'user_id'=>$uid,'page'=>$page));
    			break;
    		case 'profile':
    			$documents = $this->Document->getDocuments(array('owner_id'=>$param,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Document->getTotalDocuments(array('owner_id'=>$param,'user_id'=>$uid,'page'=>$page));
    			break;
    		case 'category':
    			$documents = $this->Document->getDocuments(array('category'=>$param,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Document->getTotalDocuments(array('category'=>$param,'user_id'=>$uid,'page'=>$page));
    			break;
    		case 'search':				
    			$documents = $this->Document->getDocuments(array('search'=>$param,'user_id'=>$uid,'page'=>$page));
    			$total = $this->Document->getTotalDocuments(array('search'=>$param,'user_id'=>$uid,'page'=>$page));
    			break;
    	}
    	
    	$limit = Configure::read('Document.document_item_per_pages');
		$is_view_more = (($page - 1) * $limit  + count($documents)) < $total;
    	
		$this->set('is_view_more',$is_view_more);
    	$this->set('documents', $documents);
    	$this->set('type',$type);
    	$this->set('page',$page);
    	$this->set('param',$param);
		$this->set('url_more', '/documents/browse/' . h($url) . '/page:' . ( $page + 1 ) ) ;
    }
    
    public function upload()
    {
    	$helper = MooCore::getInstance()->getHelper('Document_Document');
    	$this->_checkPermission(array('confirm' => true));   
    	$allowedExtensions = $helper->support_extention;

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = 'uploads' . DS . 'documents'.DS.'files';
	
        $original_filename = $this->request->query['qqfile'];
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            $result['document_file'] = $result['filename'];
            $result['original_filename'] = $original_filename;
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        die();
    }
    
    public function create($id = null)
    {    	
    	$this->_checkPermission(array('aco' => 'document_create'));
    	$this->_checkPermission(array('confirm' => true)); 
    	
    	$uid = MooCore::getInstance()->getViewer(true);    	
    	$this->loadModel('Tag');
    	$helper = MooCore::getInstance()->getHelper('Document_Document');
    	
    	if ($helper->isIOS())
    	{
    		$this->render('error_ios');
    		return;
    	}
    	
    	$this->loadModel('Category');
    	$role_id = $this->_getUserRoleId();
    	$categories = $this->Category->getCategoriesList('Document',$role_id);
    	$this->set('categories',$categories);
    	
    	$this->loadModel('Document.DocumentLicense');
    	$licenses = $this->DocumentLicense->find('all');
    	$tmp = array();
    	foreach ($licenses as $license)
    		$tmp[$license['DocumentLicense']['id']] = $license['DocumentLicense']['title'];
    	$licenses = $tmp;
    	$this->set('licenses',$licenses);
    	$is_edit = false;
    	if ($id)
    	{    		
    		$document = $this->Document->findById($id);
    		if ($document)
    		{
    			$this->_checkPermission(array('admins' => array( $document['User']['id'])));
    			
    			$this->Document->id = $id;
    			$is_edit = true;
    			$tags = $this->Tag->getContentTags($id, 'Document_Document');
    			$document['Document']['tags'] = $tags;
    			unset($this->Document->validate['file_upload']);
    		}
    	}
    	else
    	{
    		$document = $this->Document->initFields();
    		$document['Document']['tags'] = '';
    	}
    	
    	$this->set('document',$document);
    	$this->set('is_edit',$is_edit);
    	
    	if (!$this->request->is('post'))
    	{
    		return;
    	}    
    }
    
    public function upload_avatar()
    {
    	$uid = MooCore::getInstance()->getViewer(true);

        if (!$uid)
            return;

        // save this picture to album
        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';

        $this->_prepareDir($path);

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            
            $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
            $result['file'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        die();
    }
    
	public function _getExtension($filename = null) {
        $tmp = explode('.', $filename);
        $re = array_pop($tmp);
        return $re;
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }
    
    public function save()
    {
    	$this->loadModel('Document.Document');
    	$this->loadModel('Tag');
    	$helper = MooCore::getInstance()->getHelper('Document_Document');
    	$this->autoRender = false;
    	
    	$this->_checkPermission(array('aco' => 'document_create'));
    	$this->_checkPermission(array('confirm' => true));    	
    	
    	$uid = MooCore::getInstance()->getViewer(true);
    	$setting = Configure::read('Document');
    	$id = isset($this->request->data['id']) ? $this->request->data['id'] : '';
    	$is_edit = false;
    	if ($id)
    	{    		
    		$document = $this->Document->findById($id);
    		if ($document)
    		{
    			$this->_checkPermission(array('admins' => array( $document['User']['id'])));
    			
    			$this->Document->id = $id;
    			$is_edit = true;
    			unset($this->Document->validate['document_file']);
    		}
    	}
    	$this->Document->set($this->request->data);
        $this->_validateData($this->Document);
        $data = $this->request->data;
        
    	if (!$is_edit)
        {
	        $filename = $data['document_file'];
	    	
	    	$data['file_name'] = $data['original_filename'];
	    	$data['user_id'] = $uid;
	    	$data['download_url'] = $filename;    	
	    	$data['approve'] = Configure::read('Document.document_approval');
        }
        else 
        {
       		
        }
        
    	if ($this->Document->save($data))
    	{    		    		
            $this->Tag->saveTags($this->request->data['tags'], $this->Document->id, 'Document_Document');
            
            $this->loadModel('Activity');
            if (!$is_edit)
            {
	            if (Configure::read('Document.document_approval'))
	            	$privacy = $data['privacy']; 
	            else
	            	$privacy = PRIVACY_ME;
	            		           
	            $this->Activity->save(array('type' => 'user',
	                'action' => 'document_create',
					'user_id' => $uid,
					'item_type' => 'Document_Document',
					'item_id' => $this->Document->id,
					'query' => 1,
					'plugin' => 'Document',
	            	'params' => 'item',
	            	'share' => $privacy != PRIVACY_ME ? 1 : 0,
	            	'privacy' => $privacy
				));
            }
            else 
            {
            	if (Configure::read('Document.document_approval') || $document['Document']['approve'])
	            	$privacy = $data['privacy']; 
	            else
	            	$privacy = PRIVACY_ME;
	            	
            	$this->Activity->updateAll(array('privacy'=>$privacy,'share' => $privacy != PRIVACY_ME ? 1 : 0),array('action'=>'document_create','item_type'=>'Document_Document','item_id'=>$this->Document->id));
            }
            
            
            if (!$is_edit)
            {
            	if (Configure::read('Document.document_approval'))
    				$this->Session->setFlash(__d('document','Document has been successfully added'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
    			else
    				$this->Session->setFlash(__d('document',"Document has been successfully added and is pending for admin's approval."), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            }
    		else
    		{
    			$this->Session->setFlash(__d('document','Document has been successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
    		}
    		
    		$document = $this->Document->read();
    		$response['result'] = 1;
    		$response['href'] = $document['Document']['moo_href'];
			
			echo json_encode($response);
			exit;
    	}
    }
    
    public function preview($id = null)
    {
    	$this->download($id);
    }
    
    public function download($id = null)
    {
    	$this->loadModel('Document.Document');
    	$document = $this->Document->findById($id);
    	$this->_checkExistence($document);
    	
    	if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'http://drive.google.com') !== false)
    	{
    		//nothing
    	}
    	else
    	{
	    	$this->_checkPermission( array('aco' => 'document_view'));
	    	
	    	$uid = MooCore::getInstance()->getViewer(true);
	    	$areFriends = false;
	    	if ($uid)
	    	{
	    		$this->loadModel('Friend');
	    		$areFriends = $this->Friend->areFriends($uid, $document['User']['id']);
	    	}
	    	$this->_checkPrivacy($document['Document']['privacy'], $document['User']['id'], $areFriends);
	    	
	    	if ($uid != $document['Document']['user_id'])
	    	{
		    	$download_count = $document['Document']['download_count'] + 1;
		    	
		    	$this->Document->id = $id;
		    	$this->Document->save(array('download_count'=>$download_count));
	    	}
    	}
    	
    	$this->viewClass = 'Media';
    	$extension = end(explode('.', $document['Document']['file_name']));
    	$name = str_replace('.'.$extension, '', $document['Document']['file_name']);
    	
        // Download app/outside_webroot_dir/example.zip
        $params = array(
            'id'        => $document['Document']['download_url'],
            'name'      => $name,
            'download'  => true,
            'extension' => $extension,
            'path'      => APP . 'webroot' . DS .'uploads' . DS .'documents' . DS . 'files' . DS
        );
        $this->set($params);
    }
    
}