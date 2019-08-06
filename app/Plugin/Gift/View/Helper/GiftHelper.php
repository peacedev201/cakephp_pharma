<?php
App::uses('AppHelper', 'View/Helper');
class GiftHelper extends AppHelper {
    public $helpers = array('Storage.Storage');
	public function checkPostStatus($gift,$uid)
	{
		return true;
	}
	
	public function getEnable()
	{
		return Configure::read('Gift.gift_enabled');
	}
	
	public function checkSeeComment($gift,$uid)
	{
		return true;
	}
	
	public function getTagUnionsGift($giftids)
	{
		return "SELECT i.id, i.title, i.description, i.like_count, i.created, 'Gift_Gift' as moo_type, i.privacy, i.user_id
						 FROM " . Configure::read('core.prefix') . "gifts i
						 WHERE i.id IN (" . implode(',', $giftids) . ")";
	}
    
    public function getUserById($user_id)
    {
        $mUser = MooCore::getInstance()->getModel("User");
        return $mUser->findById($user_id);
    }

    public function getImage($item, $options = array()){
        $prefix = (isset($options['prefix']))?$options['prefix']:'';
        $thumb = !empty($item['thumb']) ? $item['thumb'] : $item['filename'];
        return $this->Storage->getUrl($item['id'], $prefix, $thumb,"gifts");
    }

    public function getFile($item)
    {
        return $this->Storage->getUrl($item['id'], '', $item['filename'], "gift_files");
    }
}
