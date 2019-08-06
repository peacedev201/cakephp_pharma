<?php
	if(isset($object[$name]['moo_title'])){
		$title = $object[$name]['moo_title'];
	}else if(isset($object[$name]['title'])){
		$title = $object[$name]['title'];
	}else{
		$title = '';
	}

	if(isset($object[$name]['moo_plugin'])){
		$type = strtolower($object[$name]['moo_plugin']);
	}else{
		$type = '';
	}

switch ( $activity_log['Activitylog']['action'] )
{
	case 'like_post':
		if($activity_log['Activitylog']['params'] && $enable_reaction){
		 	$this->getEventManager()->dispatch(new CakeEvent('element.reaction.render', $this,array('reaction' => $activity_log['Activitylog']['params']) ));
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','reacted to %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'post').'</a>');
		}else{
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','liked %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'post').'</a>');
		}
		break;
	case 'like_activity_comment':
	case 'like_comment':
		if($activity_log['Activitylog']['params'] && $enable_reaction){
			$this->getEventManager()->dispatch(new CakeEvent('element.reaction.render', $this,array('reaction' => $activity_log['Activitylog']['params']) ));
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','reacted to %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'comment').'</a>');
		}else{
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','liked %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'comment').'</a>');
		}
		break;
	case 'like_item':
		if($activity_log['Activitylog']['params'] && $enable_reaction){
			$this->getEventManager()->dispatch(new CakeEvent('element.reaction.render', $this,array('reaction' => $activity_log['Activitylog']['params']) ));
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','reacted to %s ', '<a href="'.$href.'">'.$title.'</a>');
		}else{
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','liked %s ', '<a href="'.$href.'">'.$title.'</a>');
		}
		break;
	case 'like_photo':
		if($activity_log['Activitylog']['params'] && $enable_reaction){
			$this->getEventManager()->dispatch(new CakeEvent('element.reaction.render', $this,array('reaction' => $activity_log['Activitylog']['params']) ));
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','reacted to %1$s %2$s', $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'photo').'</a>');
		}else{
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','liked %1$s %2$s', $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'photo').'</a>');
		}
	break;
	case 'like_album':
		if($activity_log['Activitylog']['params'] && $enable_reaction){
			$this->getEventManager()->dispatch(new CakeEvent('element.reaction.render', $this,array('reaction' => $activity_log['Activitylog']['params']) ));
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','reacted to %1$s %2$s', $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'album').'</a>');
		}else{
			echo $this->Moo->getName($activity_log['User']).'&nbsp;';
			echo __d('activitylog','liked %1$s %2$s', $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'album').'</a>');
		}
		break;

	case 'dislike_post':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','disliked %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'post').'</a>');
		break;
	case 'dislike_activity_comment':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','disliked %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'comment').'</a>');
		break;
	case 'dislike_comment':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','disliked %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'comment').'</a>');
		break;
	case 'dislike_item':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','disliked %s ', '<a href="'.$href.'">'.$title.'</a>');
		break;
	case 'dislike_photo':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','disliked %1$s %2$s', $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true),'<a href="'.$href.'">'.__d('activitylog', 'photo').'</a>');
		break;
	case 'dislike_album':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','disliked %1$s %2$s', $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'album').'</a>');
		break;

	case 'comment_activity':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','commented on %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'post').'</a>');
		break;
	case 'comment_photo':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','commented on %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true),'<a href="'.$href.'">'.__d('activitylog', 'photo').'</a>');
		break;
	case 'comment_album':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','commented on %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'album').'</a>');
		break;
	case 'comment_item':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','commented on %s ', '<a href="'.$href.'">'.$title.'</a>');
		break;

	case 'share_post':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','shared %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'post').'</a>');
		echo $add_text;
		break;
	case 'share_album':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','shared %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'album').'</a>');
		echo $add_text;
		break;
	case 'share_photo':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','shared %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'photo').'</a>');
		echo $add_text;
		break;
	case 'share_item':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','shared %s ', '<a href="'.$href.'">'.$title.'</a>');
		echo $add_text;
		break;

	case 'post_status':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		if($activity_log['User']['id'] == $activity_log['Owner']['id']){
			echo __d('activitylog','updated %1$s %2$s',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true), '<a href="'.$href.'">'.__d('activitylog', 'status').'</a>');
		}else{
			echo __d('activitylog','wrote on %s timeline',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true));
		}
		break;
	case 'post_photo':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		if($activity_log['User']['id'] == $activity_log['Owner']['id']){
			echo __d('activitylog','added a new %s ', '<a href="'.$href.'">'.__d('activitylog', 'photo').'</a>');
		}else{
			echo __d('activitylog','added a new %1$s to %2$s timeline', '<a href="'.$href.'">'.__d('activitylog', 'photo').'</a>',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true));
		}
		break;
	case 'post_photos':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		if($activity_log['User']['id'] == $activity_log['Owner']['id']){
			echo __d('activitylog','added new %s ', '<a href="'.$href.'">'.__d('activitylog', 'photos').'</a>');
		}else{
			echo __d('activitylog','added new %1$s to %2$s timeline', '<a href="'.$href.'">'.__d('activitylog', 'photos').'</a>', $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true));
		}
		break;
	case 'post_group':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		$group = MooCore::getInstance()->getItemByType('Group_Group',$object[$name]['target_id']);
		echo __d('activitylog','posted a %s into group', '<a href="'.$href.'">'.__d('activitylog', 'status').'</a>');
		if(!empty($group)){
			echo ' <a href="'.$group['Group']['moo_href'].'">'.$group['Group']['moo_title'].'</a>';
		}
		break;
	case 'post_group_photo':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		$group = MooCore::getInstance()->getItemByType('Group_Group',$object[$name]['target_id']);
		echo __d('activitylog','added a new %s into group', '<a href="'.$href.'">'.__d('activitylog', 'photo').'</a>');
		if(!empty($group)){
			echo ' <a href="'.$group['Group']['moo_href'].'">'.$group['Group']['moo_title'].'</a>';
		}
		break;
	case 'post_group_photos':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		$group = MooCore::getInstance()->getItemByType('Group_Group',$object[$name]['target_id']);
		echo __d('activitylog','added new %s into group', '<a href="'.$href.'">'.__d('activitylog', 'photos').'</a>');
		if(!empty($group)){
			echo ' <a href="'.$group['Group']['moo_href'].'">'.$group['Group']['moo_title'].'</a>';
		}
		break;
	case 'post_event':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		$event = MooCore::getInstance()->getItemByType('Event_Event',$object[$name]['target_id']);
		echo __d('activitylog','posted a %s into event', '<a href="'.$href.'">'.__d('activitylog', 'status').'</a>');
		if(!empty($event)){
			echo ' <a href="'.$event['Event']['moo_href'].'">'.$event['Event']['moo_title'].'</a>';
		}
		break;
	case 'post_event_photo':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		$event = MooCore::getInstance()->getItemByType('Event_Event',$object[$name]['target_id']);
		echo __d('activitylog','added a new %s into event', '<a href="'.$href.'">'.__d('activitylog', 'photo').'</a>');
		if(!empty($event)){
			echo ' <a href="'.$event['Event']['moo_href'].'">'.$event['Event']['moo_title'].'</a>';
		}
		break;
	case 'post_event_photos':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		$event = MooCore::getInstance()->getItemByType('Event_Event',$object[$name]['target_id']);
		echo __d('activitylog','added new %s into event', '<a href="'.$href.'">'.__d('activitylog', 'photos').'</a>');
		if(!empty($event)){
			echo ' <a href="'.$event['Event']['moo_href'].'">'.$event['Event']['moo_title'].'</a>';
		}
		break;
	case 'tagged':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','was tagged in a %s ', '<a href="'.$href.'">'.__d('activitylog', 'post').'</a>');
		break;

	case 'create_album':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','created new %s ', '<a href="'.$href.'">'.__d('activitylog', 'album').'</a>');
		break;
	case 'add_video':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		if($object[$name]['group_id']){
			$group = MooCore::getInstance()->getItemByType('Group_Group',$object[$name]['group_id']);
		}
		if(!empty($group)){
			echo __d('activitylog','added new video %s into group', '<a href="'.$href.'">'.$title.'</a>');
			echo ' <a href="'.$group['Group']['moo_href'].'">'.$group['Group']['moo_title'].'</a>';
		}else{
			echo __d('activitylog','added new video %s', '<a href="'.$href.'">'.$title.'</a>');
		}
		break;
	case 'add_photo':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		$number = count(explode(',', $object[$name]['items']));
		$album = MooCore::getInstance()->getItemByType($object[$name]['item_type'],$object[$name]['item_id']);
        if ($number > 1) {
			echo __d('activitylog', 'added %1$s new %2$s to album', $number, '<a href="'.$href.'">'. __d('activitylog','photos').'</a>');
		} else {
			echo __d('activitylog', 'added %1$s new %2$s to album', $number, '<a href="'.$href.'">'. __d('activitylog','photo').'</a>');
		}
		if(!empty($album['Album'])){
			echo ' '.$album['Album']['moo_title'];
		}
		break;
	case 'create_item':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','created new %s ', __d('activitylog', $type). ' <a href="'.$href.'">'.$title.'</a>');
		if(!empty($object[$name]['group_id'])){
			$group = MooCore::getInstance()->getItemByType('Group_Group',$object[$name]['group_id']);
			echo __d('activitylog', ' into group %s', '<a href="'.$group['Group']['moo_href'].'">'.$group['Group']['moo_title'].'</a>');
		}
		break;

	case 'follow':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','followed %s ', $this->Moo->getName($activity_log['Owner']));
		break;
	case 'add_friend':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','became friends with %s ', $this->Moo->getName($activity_log['Owner']));
		break;
	case 'user_avatar':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','changed %s profile picture',  $activitylogHelper->possession( $activity_log['User'], $activity_log['Owner'], true));
		break;
	case 'tag_photo':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','was tagged in a %s ', '<a href="'.$href.'">'. __d('activitylog','photo').'</a>' );
		break;
	case 'join_group':
		echo $this->Moo->getName($activity_log['User']).'&nbsp;';
		echo __d('activitylog','joined group %s', '<a href="'.$href.'">'. $object[$name]['moo_title'].'</a>' );
		break;
	default :
        break;
}
?>