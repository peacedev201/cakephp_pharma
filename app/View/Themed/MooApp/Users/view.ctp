<?php

$this->MooApp->loading();
$this->Html->css(array('MooApp.main','MooApp.feed/activites','MooApp.feed/emoji','MooApp.feed/plugin'), array('block' => 'mooAppOptimizedCss','minify'=>false));
$this->addPhraseJs(array(
         'like' => __("%s like"),
        'likes' => __("%s likes"),
        'dislike' => __("%s dislike"),
        'dislikes' => __("%s dislikes"),
        'comment' => __("%s comment"),
        'comments' => __("%s comments"),
        'time' => __("Time"),
        'location' => __("Location"),
        'address' => __("Address"),
        'newFeedPlaceholder' => __("What's on your mind?"),
        'notAllow' => __("only shares some information with everyone"),
        'editProfile' => __("Edit Profile"),
        'message' => __("Message"),
        'cancelRequest' => __("Cancel request"),
        'respond' => __("Respond to request"),
        'accept' => __("Accept"),
        'delete' => __("Delete"),
        'add' => __("Add Friend"),
        'unFriend' => __("Unfriend"),
        'following' => __("Following"),
        'follow' => __("Follow"),
        'more' => __("More"),
        'gender' => __("Gender"),
        'born' => __("Born on"),
        'viewAll' => __("View all"),
        'feature' => __("Feature User"),
        'unFeature' => __("Unfeature User"),
        'block' => __("Block"),
        'unBlock' => __("Unblock"),
        'reportUser' => __("Report User"),
        'report' => __("Report Activity"),
        'deleteFeed' => __("Delete Activity"),
        'deleteComment' => __("Delete Comment"),
        )
);
?>
<?php 
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
$friendModel = MooCore::getInstance()->getModel('Friend');
$photoModel = MooCore::getInstance()->getModel('Photo_Photo');
$result = $menu = array();
$activityId = 0;

/* Profile Menu */
$menu['info']['text'] =  __('Info');
$menu['info']['url'] =  FULL_BASE_URL . $this->request->base . '/users/ajax_info/'. $user['User']['id'];
$menu['friend']['text'] =  __('Friends');
$menu['friend']['url'] = FULL_BASE_URL . $this->request->base . '/users/profile_user_friends/'. $user['User']['id'];
$menu['friend']['cnt'] =  $user['User']['friend_count'];

if($cuser && ($uid == $user['User']['id'] || $cuser['Role']['is_admin'])):
    $blockModel = MooCore::getInstance()->getModel("UserBlock");
    $menu['blocked']['text'] =  __('Blocked Members');
    $menu['blocked']['cnt'] =  $blockModel->find('count',array('conditions'=>array('UserBlock.user_id'=>$user['User']['id'])));
    $menu['blocked']['url'] = FULL_BASE_URL . $this->request->base . '/users/profile_user_blocks/'. $user['User']['id'];                        	
 endif;
 if (Configure::read("core.enable_follow") && $uid && $uid == $user['User']['id']):
     $followModel = MooCore::getInstance()->getModel("UserFollow");
    $menu['follow']['text'] =  __('Following');
    $menu['follow']['url'] =  FULL_BASE_URL .$this->request->base . '/follows/user_follows/'. $user['User']['id'];
    $menu['follow']['cnt'] = $followModel->find('count',array('conditions'=>array('UserFollow.user_id'=>$uid)));
endif;

if (Configure::read('Photo.photo_enabled')): 
    $menu['album']['text'] =  __('Albums');
    $menu['album']['url'] =  FULL_BASE_URL .$this->request->base . '/photos/profile_user_photo/'. $user['User']['id'];
    $menu['album']['cnt'] = $albums_count;
 endif;
if (Configure::read('Blog.blog_enabled')):
    $menu['blog']['text'] =  __('Blogs');
    $menu['blog']['url'] = FULL_BASE_URL . $this->request->base . '/blogs/profile_user_blog/'. $user['User']['id'];
    $menu['blog']['cnt'] = $user['User']['blog_count'];
endif;
if (Configure::read('Topic.topic_enabled')): 
    $menu['topic']['text'] =  __('Topics');
    $menu['topic']['url'] = FULL_BASE_URL . $this->request->base . '/topics/profile_user_topic/'. $user['User']['id'];
    $menu['topic']['cnt'] = $user['User']['topic_count'];
 endif;
 if (Configure::read('Video.video_enabled')): 
    $menu['video']['text'] =  __('Videos');
    $menu['video']['url'] = FULL_BASE_URL . $this->request->base . '/videos/profile_user_video/'. $user['User']['id'];
    $menu['video']['cnt'] = $user['User']['video_count'];
endif;
if (Configure::read('Group.group_enabled')):
    $menu['group']['text'] =  __('Groups');
    $menu['group']['url'] = FULL_BASE_URL . $this->request->base . '/groups/profile_user_group/'. $user['User']['id'];
    $menu['group']['cnt'] = $user['User']['group_count'];
endif;
if (Configure::read('Event.event_enabled')):
    $menu['event']['text'] =  __('Events');
    $menu['event']['url'] = FULL_BASE_URL . $this->request->base . '/events/profile_user_event/'. $user['User']['id'];
    $menu['event']['cnt'] = $user['User']['event_count'];
endif;    
$event = new CakeEvent("profile.mooApp.afterRenderMenu" ,$this);
$this->getEventManager()->dispatch($event);
$result = $event->data['result'];
if ($result) $menu = array_merge($menu, $result);
$menu =  json_encode($menu);

// display profile fields
$profileFieldType = $profileFields = array();
$helper = MooCore::getInstance()->getHelper("Core_Moo");
if (Configure::read('core.enable_show_profile_type')):
    $profileFieldType[0]['name'] = __('Profile type');
    $profileFieldType[0]['value'] = $user['ProfileType']['name'];
endif;
foreach ($fields as $i => $field):
        if (!in_array($field['ProfileField']['type'],$helper->profile_fields_default)) {
            $options = array();
            if ($field['ProfileField']['plugin']){
                $options = array('plugin' => $field['ProfileField']['plugin']);
            }
                $userCountryModel = MooCore::getInstance()->getModel("UserCountry");
                $user_country = $userCountryModel->getUserCountryByUser($user['User']['id']);
                if ($user_country) {
                    $text = ($user_country['UserCountry']['address'] ? $user_country['UserCountry']['address'].", " : "");    
                    $text .= ($user_country['State'] && $user_country['State']['name'] ? $user_country['State']['name'].", " : "");
                    $text .= ($user_country['UserCountry']['zip'] ? $user_country['UserCountry']['zip'].", " : "");
                    $text .= ($user_country['Country'] ? $user_country['Country']['name']." " : "");
                    $profileFields[$i]['name'] = $field['ProfileField']['name'] ;
                    $profileFields[$i]['value'] = $text ;
                }
        }
        if ( !empty( $field['ProfileFieldValue']['value'] ) ) :
                $profileFields[$i]['name'] = $field['ProfileField']['name'] ;
                $profileFields[$i]['value'] = h($field['ProfileFieldValue']['value']) ;
        endif;
    endforeach;
$profileFields = array_merge($profileFieldType,$profileFields);
$profileFields =  json_encode($profileFields);


/* show profile album */ 
if($albums):
 foreach ($albums as $album):
        	 $covert = '';
		    	if ($album['Album']['type'] == 'newsfeed' &&  $role_id != ROLE_ADMIN && $uid != $album['Album']['user_id'] && (!$uid || $friendModel->areFriends($uid,$album['Album']['user_id'])))  
		    	{
			    	$photo = $photoModel->getPhotoCoverOfFeedAlbum($album['Album']['id']);
			    	if ($photo)
			    	{
			    		$covert = $photoHelper->getImage($photo, array('prefix' => '150_square'));
			    	}
			    	else
			    	{
			    		$covert = $photoHelper->getAlbumCover('', array('prefix' => '150_square'));
			    	}
		    	}
		    	else
		    	{ 
		    		$covert = $photoHelper->getAlbumCover($album['Album']['cover'], array('prefix' => '150_square'));
		    	}
                        $album['Album']['coverFull']= $covert;
                        $albumArray[] = $album; 
                        endforeach;
$albums = json_encode($albumArray);
endif;

if(isset($activity)) {
    $activityId = $activity['Activity']['id'];
}

$access = 0;
if ($canView) $access = 1 ;

/*HOOK FOR PLUGIN AT USER NAME*/
$profileNamePlugin ='';
$beforeRenderProfileName = new CakeEvent("View.Mooapp.users.view.beforeRenderProfileName", $this, array(
            'user' => $user
        ));
$this->getEventManager()->dispatch($beforeRenderProfileName);        
foreach ($beforeRenderProfileName->result['result'] as $pf):
        $profileNamePlugin .= $pf['profile'];
endforeach;
/*END HOOK FOR PLUGIN*/

/*CUSTOM FOR PROFILE COMPLETENESS*/
if (Configure::read('ProfileCompletion.profile_completion_enabled')) :
    $profileCompletion = array();
    $showProfileCompletion = false;
    $beforeRenderProfileCompletion = new CakeEvent("View.Mooapp.users.view.renderProfileCompletionForApp", $this, array());
    $this->getEventManager()->dispatch($beforeRenderProfileCompletion);
    $result = $beforeRenderProfileCompletion->result['result'];
    if(isset($result['profile_completion'])) {
        $profileCompletion = $result['profile_completion'];
        $profileCompletion =  json_encode($profileCompletion);
        $showProfileCompletion = true;
    }
    $this->addPhraseJs(array(
        'completionNext' => __d('profile_completion', 'Next: '),
        'completionUpdateProfile' => __d('profile_completion', 'Update Profile'),
            )
    );
    
endif;
/*END CUSTOM FOR PROFILE COMPLETENESS*/

/*HOOK FOR PLUGIN SHOW INFO*/
$moreProfileInfo = array();
$afterRenderProfileInfo = new CakeEvent("View.Mooapp.users.view.afterRenderProfileInfo", $this, array());
$this->getEventManager()->dispatch($afterRenderProfileInfo);
if (!empty($afterRenderProfileInfo->result['result'])) {
    $moreProfileInfo = array_merge($moreProfileInfo, $afterRenderProfileInfo->result['result']);
}
$moreProfileInfo =  json_encode($moreProfileInfo);
/*END HOOK FOR PLUGIN SHOW INFO*/
?>


<?php $this->start('mooAppOptimizedContent'); ?>
<script type="text/javascript">
    <?php if (!empty($moreProfileInfo)): ?>
    window.moreProfileInfo = <?php echo $moreProfileInfo; ?>;
    <?php endif; ?>
    <?php if (isset($showProfileCompletion) && $showProfileCompletion == true): ?>
    window.profileCompletion = '<?php echo $profileCompletion; ?>';
    <?php endif; ?>
    <?php if(Configure::read('MooApp.show_more_button_what_new_box')==1): ?>
    window.show_more_button = true;
    <?php else: ?>
    window.show_more_button = false;
    <?php endif; ?>
    window.userId = <?php echo $user['User']['id']; ?>;
    window.activityId = <?php echo $activityId ?>;
    window.menu = <?php echo $menu; ?>;
    <?php if ( !empty($profileNamePlugin)): ?>
    window.profileNamePlugin = '<?php echo $profileNamePlugin; ?>';
    <?php endif; ?>
    <?php if ( !empty($is_online)): ?>
    window.isOnline = true;
    <?php endif; ?>
    <?php if ( !empty($albums)): ?>
    window.albums = <?php echo $albums; ?>;
    <?php endif; ?>
    window.uid = <?php echo $uid; ?>;
    window.access = <?php echo $access; ?>;
    window.profileFields = <?php echo $profileFields; ?>;
   <?php if ( !empty($request_sent) ) { ?>
    window.requestSent = <?php echo $request_sent; ?>;
   <?php } ?>
   <?php if ( !empty($respond) ) { ?>
    window.respond = <?php echo $respond; ?>;
   <?php } ?>
   <?php if ( !empty($uid) && !$areFriends && empty($request_sent) && empty($respond) ){ ?>
    window.addFriend = true;
   <?php } ?>



   <?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && !$user['User']['featured'] ): ?>
    window.feature = true;
   <?php endif; ?>
   <?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && $user['User']['featured'] ): ?>
    window.unFeature = true;
   <?php endif; ?>
   <?php if ( !empty($uid) && $areFriends ): ?>
    window.unFriend = true;
   <?php endif; ?>

    <?php if ( !empty($uid) && ($uid != $user['User']['id'] ) && !$user['Role']['is_admin'] && !$user['Role']['is_super']){
       if(!$is_viewer_block){ ?>
    window.block = true;
      <?php } else { ?>
    window.unBlock = true;
    <?php } } ?>
</script>
<?php $this->end(); ?>
<?php
$this->MooGzip->script(array('zip'=>'users.view.bundle.js.gz','unzip'=>'MooApp.users.view.bundle'));
?>