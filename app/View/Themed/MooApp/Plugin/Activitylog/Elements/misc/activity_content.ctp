<?php
switch ( $activity_log['Activitylog']['type'] )
{
  case 'activity':
          switch($object['Activity']['action']){
             case 'checkin_location':
             case 'wall_post_background':
                $object['Activity']['action'] = 'wall_post';
                break;
             case 'wall_post_background_share':
                 $object['Activity']['action'] = 'wall_post_share';
                 break;
        }

        if($this->elementExists('activity/content/'.$object['Activity']['action']) && !in_array($activity_log['Activitylog']['action'], array('post_photos','post_group_photos', 'post_event_photos'))){
            echo $this->element('activity/content/'.$object['Activity']['action'], array('activity' => $object,'object'=>array()),array());
        }else if($this->elementExists($object['Activity']['plugin'].'.activity/content/'.$object['Activity']['action'])){
            if($object['Activity']['item_id'] && $object['Activity']['item_type']){
                $target_object = MooCore::getInstance()->getItemByType($object['Activity']['item_type'],$object['Activity']['item_id']);
            }elseif($object['Activity']['items'] && $object['Activity']['plugin'] == 'Photo'){
                $items = explode(',', $object['Activity']['items']);
                if(count($items) == 1){
                    $target_object = MooCore::getInstance()->getItemByType($object['Activity']['item_type'],$items[0]);
                }
            }
            if(!empty($target_object)){
                if($object['Activity']['action'] == 'friend_add'){
                    echo $this->element('activity/content/friend_add', array('activity' => $object,'object'=>$target_object),array('plugin' => 'Activitylog'));
                }else {
                    echo $this->element('activity/content/' . $object['Activity']['action'], array('activity' => $object, 'object' => $target_object), array('plugin' => $object['Activity']['plugin']));
                }
            }elseif($object['Activity']['params'] && $object['Activity']['plugin'] == 'Video'){
                echo $this->element('video/content/'.$object['Activity']['action'], array('activity' => $object),array());
            }elseif($object['Activity']['plugin'] == 'Business'){
                echo $this->element('Business.activity/content/'.$object['Activity']['action'], array('activity' => $object),array());
            }elseif($object['Activity']['plugin'] == 'FeedList'){
                echo $this->element('activity/content/'.$object['Activity']['action'], array('activity' => $object),array('plugin' => $object['Activity']['plugin']));
            }
        }
		break;
	case 'comment':
        echo '<div>'. $this->viewMore(h($object[$name]['message']),null,null,null,true,array('no_replace_ssl'=>1)).'</div>';
        if (!empty($object[$name]['thumbnail'])){
            echo $this->Moo->getImage($object,array('prefix'=>'200'));
        }
		break;

	case 'core_activity_comment':
		echo '<div>'.$this->viewMore(h($object[$name]['comment']),null,null,null,true,array('no_replace_ssl'=>1)).'</div>';
        if (!empty($object[$name]['thumbnail'])){
            echo $this->Moo->getImage(array('ActivityComment'=>$object[$name]),array('prefix'=>'200'));
        }
		break;
    case 'Photo_Photo':
        $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
        echo '<img src="'.$photoHelper->getImage($object, array('prefix' => '300_square')).'" />';
        break;
    case 'Photo_Album':
        $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
        echo '<img src="'.$photoHelper->getAlbumCover($object['Album']['cover'], array('prefix' => '300_square')).'" />';
        break;
    case 'Topic_Topic':
        echo $this->element('activity/content/topic_create', array('object' => $object),array('plugin' => 'Topic'));
        break;
    case 'Blog_Blog':
        echo $this->element('activity/content/blog_create', array('object' => $object),array('plugin' => 'Blog'));
        break;
    case 'Event_Event':
        echo $this->element('activity/content/event_create', array('object' => $object),array('plugin' => 'Event'));
        break;
    case 'Group_Group':
        echo $this->element('activity/content/group_create', array('object' => $object),array('plugin' => 'Group'));
        break;
    case 'Video_Video':
        echo $this->element('activity/content/video_create', array('object' => $object),array('plugin' => 'Video'));
        break;
	default :
        break;
}
?>