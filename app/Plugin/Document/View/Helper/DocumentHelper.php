<?php
App::uses('AppHelper', 'View/Helper');
class DocumentHelper extends AppHelper {
	public $support_extention = array('txt','doc','docx','xls','xlsx','ppt','pptx','pdf','ai','psd');
	public $helpers = array('Storage.Storage');
	
	public function getImage($item, $options = array()) {
		$prefix = '111_';
		if (isset($options['prefix'])) {
			if ($options['prefix'])
			{
				$prefix = $options['prefix'] . '_';
			}
			else
			{
				$prefix = '';
			}
		}
		
		return $this->Storage->getUrl($item['Document']['id'], $prefix, $item['Document']['thumbnail'], "documents");		
	}
	
	public function getDocument($item)
	{
		return $this->Storage->getUrl($item['Document']['id'], '', $item['Document']['download_url'], "documents_files");
	}
	
	public function checkSeeComment($document,$uid)
	{
		if ($document['Document']['privacy'] == PRIVACY_EVERYONE)
		{
			return true;
		}
		
		return $this->checkPostStatus($document,$uid);
	}
	
	public function checkPostStatus($document,$uid)
	{
		if (!$uid)
			return false;		
		$friendModel = MooCore::getInstance()->getModel('Friend');
		if ($uid == $document['Document']['user_id'])
			return true;
			
		if ($document['Document']['privacy'] == PRIVACY_EVERYONE)
		{
			return true;
		}
		
		if ($document['Document']['privacy'] == PRIVACY_FRIENDS)
		{
			$areFriends = $friendModel->areFriends( $uid, $document['Document']['user_id'] );
			if ($areFriends)
				return true;
		}
		
		
		return false;
	}
	
	public function renderLicense($document)
	{
		$result = '';
		if ($document['DocumentLicense'])
		{
			if ($document['DocumentLicense']['url'])
			{
				$result = 'This work is licensed under a Creative Commons <a href="'.$document['DocumentLicense']['url'].'">'.$document['DocumentLicense']['name'].'</a>';
			}
			else
			{
				if ($document['DocumentLicense']['name'])
				{
					$result = $document['DocumentLicense']['name'];
					$params['year'] = date('Y',strtotime($document['Document']['created']));
					$params['user'] = '<a href="'.$document['User']['moo_href'].'">'.$document['User']['moo_title'].'</a>';
					foreach ($params as $key=>$value)
						$result = str_replace('['.$key.']', $value, $result);
				}
			}
		}
		return $result;
	}
	
	public function checkSeeActivity($document,$uid)
	{
		return $this->checkPostStatus($document,$uid);
	}	
	
	public function canEdit($item,$viewer)
	{
		if (!$viewer)
			return false;
		
		if ($viewer['Role']['is_admin'] || $viewer['User']['id'] == $item['Document']['user_id'])
			return true;

		return false;
	}
	
	public function canDelete($item,$viewer)
	{
		return $this->canEdit($item, $viewer);
	}
	
	public function isIOS()
	{
	    if (stripos($_SERVER['HTTP_USER_AGENT'],"iPhone")  !== false) {
	        return true;
	    } elseif (stripos($_SERVER['HTTP_USER_AGENT'],"iPad") !== false) {
	        return true;
	    } elseif (stripos($_SERVER['HTTP_USER_AGENT'],"iPod") !== false) {
	        return true;
	    }
	    return false;
	}
	
	public function getEnable()
	{
		return Configure::read('Document.document_enabled');
	}
	
	public function getTagUnionsDocument($documentids)
	{
		return "SELECT i.id, i.title, i.description as body, i.like_count, i.created, 'Document_Document' as moo_type, i.privacy, i.user_id
						 FROM " . Configure::read('core.prefix') . "documents i
						 WHERE i.id IN (" . implode(',', $documentids) . ")";
	}
	
	public function getItemSitemMap($name,$limit,$offset)
	{
		if (!MooCore::getInstance()->checkPermission(null, 'document_view'))
			return null;
	
		$documentModel = MooCore::getInstance()->getModel("Document.Document");
		$documents = $documentModel->find('all',array(
				'conditions' => array('Document.privacy'=>PRIVACY_PUBLIC),
				'limit' => $limit,
				'offset' => $offset
		));
			
		$urls = array();
		foreach ($documents as $document)
		{
			$urls[] = FULL_BASE_URL.$document['Document']['moo_href'];
		}
			
		return $urls;
	}
}
