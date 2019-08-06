<?php
App::uses('CakeEventListener', 'Event');

class DocumentListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
        	'Model.beforeDelete' => 'doAfterDelete',
        	'Controller.Search.search' => 'search',
            'Controller.Search.suggestion' => 'suggestion',
        	'MooView.beforeRender' => 'beforeRender',
        	'welcomeBox.afterRenderMenu' => 'welcomeBoxAfterRenderMenu',
        	'profile.afterRenderMenu'=> 'profileAfterRenderMenu',
        	'Controller.Widgets.tagCoreWidget' => 'hashtagEnable',
        	'Controller.Search.hashtags_filter' => 'hashtags_filter',
        	'Controller.Search.hashtags' => 'hashtags',
        	'View.Adm.Layout.adminGetContentInfo' => 'widgetTag',
        	'Controller.Share.afterShare' => 'afterShare',
        	'Controller.Comment.afterComment' => 'afterComment',
        	'Plugin.View.Api.Search' => 'apiSearch',
        	'Controller.Home.adminIndex.Statistic' => 'statistic',
        		
        	'StorageHelper.documents.getUrl.local' => 'storage_geturl_local',
        	'StorageHelper.documents.getUrl.amazon' => 'storage_geturl_amazon',
        	'StorageAmazon.documents.getFilePath' => 'storage_amazon_get_file_path',
        		
        	'StorageHelper.documents_files.getUrl.local' => 'storage_geturl_local',
        	'StorageHelper.documents_files.getUrl.amazon' => 'storage_geturl_amazon',
        	'StorageAmazon.documents_files.getFilePath' => 'storage_amazon_get_file_path',
        		
        	'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
        	'StorageAmazon.documents_files.putObject.success' => 'storage_amazon_documents_files_put_success_callback',
        		
        	'ApiHelper.renderAFeed.document_create' => 'exportDocumentCreate',
        	'ApiHelper.renderAFeed.document_item_detail_share' => 'exportDocumentItemDetailShare',
        	
        	'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu'
        );
    }
    
    public function apiAfterRenderMenu($e)
    {
    	$subject = MooCore::getInstance()->getSubject();
    	$e->data['result']['document'] = array(
    			'text' => __d('document','Documents'),
    			'url' => FULL_BASE_URL . $e->subject()->request->base . '/documents/browse/profile/'. $subject['User']['id'],
    			'cnt' => 0
    	);
    }
    
    public function exportDocumentCreate($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$documentModel = MooCore::getInstance()->getModel("Document_Document");
    	$document = $documentModel->findById($data['Activity']['item_id']);
    	$helper = MooCore::getInstance()->getHelper('Document_Document');
    	
    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml);
    	if(!empty($title_tmp)){
    		$title =  $title_tmp['title'];
    		$titleHtml = $title_tmp['titleHtml'];
    	}else{
    		$title = __d('document','created a new document');
    		$titleHtml = $actorHtml . ' ' . __d('document','created a new document');
    	}
    	$e->result['result'] = array(
    			'type' => 'create',
    			'title' => $title,
    			'titleHtml' => $titleHtml,
    			'objects' => array(
    					'type' => 'Document_Document',
    					'id' => $document['Document']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($document['Document']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $document['Document']['description'])), 200, array('eclipse' => '')), Configure::read('Document.document_hashtag_enabled')),
    					'title' => $document['Document']['moo_title'],
    					'images' => array('850'=>$helper->getImage($document,array('prefix'=>''))),
    			),
    			'target' => $target,
    	);
    }
    
    public function exportDocumentItemDetailShare($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$documentModel = MooCore::getInstance()->getModel("Document_Document");
    	$document = $documentModel->findById($data['Activity']['parent_id']);
    	$helper = MooCore::getInstance()->getHelper('Document_Document');
    	
    	$target = array();
    	
    	if (isset($data['Activity']['parent_id']) && $data['Activity']['parent_id'])
    	{    	
    		$title = $data['User']['name'] . ' ' . __d('document',"shared %s's document", $document['User']['name']);
    		$titleHtml = $actorHtml . ' ' . __d('document',"shared %s's document", $e->subject()->Html->link($document['User']['name'], FULL_BASE_URL . $document['User']['moo_href']));
	    	$target = array(
	    			'url' => FULL_BASE_URL . $document['User']['moo_href'],
	    			'id' => $document['User']['id'],
	    			'name' => $document['User']['name'],
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
    					'type' => 'Document_Document',
    					'id' => $document['Document']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($document['Document']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $document['Document']['description'])), 200, array('eclipse' => '')), Configure::read('Document.document_hashtag_enabled')),
    					'title' => $document['Document']['moo_title'],
    					'images' => array('850'=>$helper->getImage($document,array('prefix'=>''))),
    			),
    			'target' => $target,
    	);
    }
    
    public function storage_amazon_documents_files_put_success_callback($e)
    {
    	$path = $e->data['path'];
    	if (Configure::read('Storage.storage_amazon_delete_image_after_adding') == "1")
    	{
	    	if ($path)
	    	{
	    		$file = new File($path);
	    		$file->delete();
	    		$file->close();
	    	}
    	}
    }
    
    public function storage_geturl_local($e)
    {
    	$v = $e->subject();
    	$request = Router::getRequest();
    	$oid = $e->data['oid'];
    	$type = $e->data['type'];
    	$thumb = $e->data['thumb'];
    	$prefix = $e->data['prefix'];
    	
    	if ($type == 'documents')
    	{
	    	if ($e->data['thumb']) {
	    		$url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/documents/thumbnail/' . $oid . '/' . $prefix . $thumb;
	    	} else {
	    		//$url = FULL_BASE_LOCAL_URL . $v->assetUrl('Blog.noimage/blog.png', array('prefix' => rtrim($prefix, "_"), 'pathPrefix' => Configure::read('App.imageBaseUrl')));
	    		$url = $v->getImage("document/img/noimage/document.png");
	    	}
    	}
    	else
    	{
    		$url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/documents/files/'. $thumb;
    	}
    	$e->result['url'] = $url;
    }
    
    public function storage_geturl_amazon($e)
    {
    	$v = $e->subject();
    	$type = $e->data['type'];
    	if ($type =='documents')
    	{
    		$e->result['url'] = $v->getAwsURL($e->data['oid'], "documents", $e->data['prefix'], $e->data['thumb']);
    	}
    	else 
    	{
    		$e->result['url'] = $v->getAwsURL($e->data['oid'], "documents_files", $e->data['prefix'], $e->data['thumb']);
    	}
    }
    
    public function storage_amazon_get_file_path($e)
    {
    	$objectId = $e->data['oid'];
    	$name = $e->data['name'];
    	$thumb = $e->data['thumb'];
    	$type = $e->data['type'];;
    	$path = false;
    	if ($type == 'documents')
    	{
	    	if (!empty($thumb)) {
	    		$path = WWW_ROOT . "uploads" . DS . "documents" . DS . "thumbnail" . DS . $objectId . DS . $name . $thumb;
	    	}
    	}
    	else
    	{
    		$path = WWW_ROOT . "uploads" . DS . "documents" . DS . "files" . DS . $thumb;
    	}
    	
    	$e->result['path'] = $path;
    }
    
    public function storage_task_transfer($e)
    {
    	$v = $e->subject();
    	$documentModel = MooCore::getInstance()->getModel('Document.Document');
    	$documents = $documentModel->find('all', array(
    			'conditions' => array("Document.id > " => $v->getMaxTransferredItemId("documents")),
    			'limit' => 10,
    			'order' => array('Document.id'),
    	)
    			);
    
    	if($documents){
    		foreach($documents as $document){
    			if (!empty($document["Document"]["thumbnail"])) {
    				$v->transferObject($document["Document"]['id'],"documents",'111_',$document["Document"]["thumbnail"]);
    				$v->transferObject($document["Document"]['id'],"documents",'',$document["Document"]["thumbnail"]);    				
    			}
				$v->transferObject($document["Document"]['id'],"documents_files",'',$document["Document"]["download_url"]);
    		}
    	}
    }
    
    public function statistic($event)
    {
    	$request = Router::getRequest();
    	$documentModel = MooCore::getInstance()->getModel("Document.Document");
    	$event->result['statistics'][] = array(
    		'item_count' => $documentModel->find('count'),
    		'ordering' => 9999,
    		'name' => __d('document','Documents'),
    		'href' => $request->base.'/admin/document/documents',
    		'icon' => '<i class="fa fa-book"></i>'
    	);
    }
    
    public function apiSearch($event)
    {
    	$view = $event->subject();
    	$items = &$event->data['items'];
    	$type = $event->data['type'];
    	$viewer = MooCore::getInstance()->getViewer();
    	$utz = $viewer['User']['timezone'];
    	if ($type == 'Document' && isset($view->viewVars['documents']) && count($view->viewVars['documents']))
    	{
    		$helper = MooCore::getInstance()->getHelper('Document_Document');
    		foreach ($view->viewVars['documents'] as $item){
    			$items[] = array(
    					'id' => $item["Document"]['id'],
    					'url' => FULL_BASE_URL.$item['Document']['moo_href'],
    					'avatar' =>  $helper->getImage($item),
    					'owner_id' => $item["Document"]['user_id'],
    					'title_1' => $item["Document"]['moo_title'],
    					'title_2' => __( 'Posted by') . ' ' . $view->Moo->getNameWithoutUrl($item['User'], false) . ' ' .$view->Moo->getTime( $item["Document"]['created'], Configure::read('core.date_format'), $utz ),
    					'created' => $item["Document"]['created'],
    					'type' => "Document",
    					'type_title' => __d('document',"Document")
    			);
    		}
    	}
    }
    
    public function afterShare($event){
    	$data = $event->data['data'];
    	if (isset($data['item_type']) && $data['item_type'] == 'Document_Document'){
    		$document_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
    		$documentModel = MooCore::getInstance()->getModel('Document.Document');
    		$documentModel->updateAll(array('Document.share_count' => 'Document.share_count + 1'), array('Document.id' => $document_id));
    	}
    }
    
    public function afterComment($event){
    	$data = $event->data['data'];
    	$target_id = isset($data['target_id']) ? $data['target_id'] : null;
    	$type = isset($data['type']) ? $data['type'] : '';
    	if ($type == 'Document_Document' && !empty($target_id)){
    		$documentModel = MooCore::getInstance()->getModel('Document.Document');
    		$documentModel->updateCounter($target_id);
    	}
    }
    
    public function widgetTag($event)
    {
    	$event->result['tag']['type']['Document_Document'] = 'Document';
    }

	public function hashtagEnable($event)
    {
    	if(Configure::read('Document.document_enabled')){
        	$enable = Configure::read('Document.document_hashtag_enabled');
        	$event->result['documents']['enable'] = $enable;
    	}
    }
    
    public function hashtags($event)
    {
    	if(Configure::read('Document.document_enabled')){
	    	$enable = Configure::read('Document.document_hashtag_enabled');
	        $documents = array();
	        $e = $event->subject();	       
	        $tagModel = MooCore::getInstance()->getModel('Tag');
	        $documentModel = MooCore::getInstance()->getModel('Document.Document');
	        $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
	
	        $uid = MooCore::getInstance()->getViewer(true);
	        if($enable)
	        {
	            if(isset($event->data['type']) && $event->data['type'] == 'documents')
	            {
	            	$documents = $documentModel->getDocuments(array('user_id'=>$uid,'page'=>$page,'ids'=>$event->data['item_ids']));
	            }
	            $table_name = $documentModel->table;	            
	            if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
	            {
	            	$documents = $documentModel->getDocuments(array('user_id'=>$uid,'limit'=>5,'ids'=>$event->data['item_groups'][$table_name]));	                
	            }
	        }
	
	        // get tagged item
	        $tag = h(urldecode($event->data['search_keyword']));
	        $tags = $tagModel->find('all', array('conditions' => array(
	            'Tag.type' => 'Document_Document',
	            'Tag.tag' => $tag
	        )));
	        $document_ids = Hash::combine($tags,'{n}.Tag.id', '{n}.Tag.target_id');
			$items = $documentModel->getDocuments(array('user_id'=>$uid,'page'=>$page,'ids'=>$document_ids));
	        
	        $documents = array_merge($documents, $items);
	
	        //only display 5 items on All Search Result page
	        if(isset($event->data['type']) && $event->data['type'] == 'all')
	        {
	            $documents = array_slice($documents,0,5);
	        }
	        $documents = array_map("unserialize", array_unique(array_map("serialize", $documents)));	        
	        if(!empty($documents))
	        {
	            $event->result['documents']['header'] = __d('document','Documents');
	            $event->result['documents']['icon_class'] = 'book';
	            $event->result['documents']['view'] = "Document.lists/documents";
	            if(isset($event->data['type']) && $event->data['type'] == 'documents')
	            {
	                $e->set('result',1);
	                $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/documents/page:' . ( $page + 1 ));
	                $e->set('element_list_path',"Document.lists/documents");
	            }
	            $e->set('documents', $documents);
	        }
    	}
    }
    
    public function hashtags_filter($event)
    {
    	if(Configure::read('Document.document_enabled')){
	   		$e = $event->subject();
	   		$documentModel = MooCore::getInstance()->getModel('Document_Document');
	   		$uid = MooCore::getInstance()->getViewer(true);
	
	        if(isset($event->data['type']) && $event->data['type'] == 'documents')
	        {
	            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
	            $documents = $documentModel->getDocuments(array('user_id'=>$uid,'page'=>$page,'ids'=>$event->data['item_ids']));
	            $e->set('documents', $documents);
	            $e->set('result',1);
	            $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/documents/page:' . ( $page + 1 ));
	            $e->set('element_list_path',"Document.lists/documents");
	        }
	        $table_name = $documentModel->table;
	        if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
	        {
	            $event->result['documents'] = null;

	            $documents = $documentModel->getDocuments(array('user_id'=>$uid,'limit'=>5,'ids'=>$event->data['item_groups'][$table_name]));
	
	            if(!empty($documents))
	            {
	                $event->result['documents']['header'] = __d('document','Documents');
	                $event->result['documents']['icon_class'] = 'book';
	                $event->result['documents']['view'] = "Document.lists/documents";
	                $e->set('documents', $documents);
	            }
	        }
    	}
    }
    
	public function profileAfterRenderMenu($event)
    {
    	$view = $event->subject();
    	$uid = MooCore::getInstance()->getViewer(true);
    	if(Configure::read('Document.document_enabled')){
    		$documentModel = MooCore::getInstance()->getModel('Document_Document');
    		$subject = MooCore::getInstance()->getSubject();
    		$total = $documentModel->getTotalDocuments(array('owner_id'=>$subject['User']['id'],'user_id'=>$uid));
    		echo $view->element('menu_profile',array('count'=>$total),array('plugin'=>'Document'));
    	}
    }
    
    public function welcomeBoxAfterRenderMenu($event)
    {
    	$view = $event->subject();
    	$uid = MooCore::getInstance()->getViewer(true);
    	if(Configure::read('Document.document_enabled') && $uid){
    		$documentModel = MooCore::getInstance()->getModel('Document_Document');
    		$total = $documentModel->getTotalDocuments(array('type'=>'my','user_id'=>$uid));
    		echo $view->element('menu_welcome',array('count'=>$total),array('plugin'=>'Document'));
    	}
    }
    
	public function beforeRender($event)
    {    	
    	if(Configure::read('Document.document_enabled')){
    		$e = $event->subject();
    		$e->Helpers->Html->css( array(
					'Document.main'
				),
				array('block' => 'css')
			);
    		
    		if (Configure::read('debug') == 0){
    			$min="min.";
    		}else{
    			$min="";
    		}
    		$e->Helpers->MooRequirejs->addPath(array(
    			"mooDocument"=>$e->Helpers->MooRequirejs->assetUrlJS("Document.js/main.{$min}js"),
				"mooDocumentSlippry"=>$e->Helpers->MooRequirejs->assetUrlJS("Document.js/slippry.min.js")
    		));

			$e->Helpers->MooRequirejs->addShim(array(
				'mooDocumentSlippry'=>array("deps" =>array('jquery')),
			));
    		
    		$e->addPhraseJs(array(
    			'delete_document_confirm' => __d('document','Are you sure you want to delete this document?')
    		));
    	}
    }
    
    public function doAfterDelete($event)
    {
    	$model = $event->subject();
    	$type = ($model->plugin) ? $model->plugin.'_' : ''.get_class($model);
    	if ($type == 'User')
    	{    		    	
    		$documentModel = MooCore::getInstance()->getModel('Document_Document');
    		$documentModel->deleteAll(array('Document.user_id' => $model->id)); 
    	}
    }    
    
	public function search($event)
    {
    	if(Configure::read('Document.document_enabled')){
	        $e = $event->subject();
	        $uid = MooCore::getInstance()->getViewer(true);
	       	$documentModel = MooCore::getInstance()->getModel('Document_Document');
	        $results = $documentModel->getDocuments(array('search'=>$e->keyword,'user_id'=>$uid,'type' => 'all','limit'=>4));
	                
	        if(isset($e->plugin) && $e->plugin == 'Document')
	        {
	            $e->set('documents', $results);
	            $e->render("Document.Elements/lists/documents");
				$e->set('no_list_id',true);
	        }
	        else 
	        {
	            $event->result['Document']['header'] = __d('document',"Document");
	            $event->result['Document']['icon_class'] = "book";
	            $event->result['Document']['view'] = "lists/documents";
				$e->set('no_list_id',true);
	            if(!empty($results))
	                $event->result['Document']['notEmpty'] = 1;
	            $e->set('documents', $results);
	        }
    	}
    }

    public function suggestion($event)
    {
    	if(Configure::read('Document.document_enabled')){
	        $e = $event->subject();
	        $documentModel = MooCore::getInstance()->getModel('Document_Document');
	       	$uid = MooCore::getInstance()->getViewer(true);
	       	 
	        $event->result['document']['header'] = __d('document',"Document");
	        $event->result['document']['icon_class'] = 'book';
	
	        if(isset($event->data['type']) && $event->data['type'] == 'document')
	        {
	            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
	            $documents = $documentModel->getDocuments(array('page'=>$page,'user_id'=>$uid,'search'=>$event->data['searchVal']));
	            $documents_next = $documentModel->getDocuments(array('page'=>$page + 1,'user_id'=>$uid,'search'=>$event->data['searchVal']));
	
	            $e->set('documents', $documents);
	            $e->set('result',1);
				$e->set('no_list_id',true);
				
				if ($documents_next && count($documents_next))
					$e->set('is_view_more',true);
				
	            $e->set('url_more','/search/suggestion/document/'.$e->params['pass'][1]. '/page:' . ( $page + 1 ));
	            $e->set('element_list_path',"Document.lists/documents");
	        }
	        if(isset($event->data['type']) && $event->data['type'] == 'all')
	        {
	            $event->result['document'] = null;
	            $documents = $documentModel->getDocuments(array('page'=>1,'limit'=>2,'user_id'=>$uid,'search'=>$event->data['searchVal']));
	            $helper = MooCore::getInstance()->getHelper('Document_Document');
	            $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
	            
	            if(!empty($documents)){
	            	$event->result['document'] = array(__d('document','Document'));
	                foreach($documents as $index=>$detail){
						$index++;
	                    $event->result['document'][$index]['id'] = $detail['Document']['id'];
	                    $event->result['document'][$index]['img'] = $helper->getImage($detail);
	                   
	    
	                    $event->result['document'][$index]['title'] = $detail['Document']['title'];
	                    $event->result['document'][$index]['find_name'] = __d('document','Find Document');
	                    $event->result['document'][$index]['icon_class'] = 'book';
	                    $event->result['document'][$index]['view_link'] = 'documents/view/';
	                    
	                    $event->result['document'][$index]['more_info'] = __d('document','Posted by') . ' ' . $mooHelper->getNameWithoutUrl($detail['User'], false) . ' ' . $mooHelper->getTime( $detail['Document']['created'], Configure::read('core.date_format'), $e->viewVars['utz'] );
	                }
	            }
	        }
    	}
    }
}