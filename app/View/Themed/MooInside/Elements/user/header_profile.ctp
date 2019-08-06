<?php

if($this->request->is('ajax')): ?>
<script>
    require(["jquery", "mooUser"], function ($, mooUser) {
        mooUser.initRespondRequest();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery','mooUser'),'object'=>array('$','mooUser'))); ?>
mooUser.initRespondRequest();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php
if(empty($title)) $title = "Featured Members";
if(empty($num_item_show)) $num_item_show = 10;

$friends = $this->requestAction(
    "users/friends/num_item_show:$num_item_show/user_id:$uid"
);
?>

<div class="profile-header">


    <div id="cover" style='background-image:url(<?php if ( !empty( $user['User']['cover'] ) ): ?><?php echo $this->request->webroot?>uploads/covers/<?php echo $user['User']['cover']?><?php else: ?><?php echo $this->request->webroot?>theme/default/img/cover.jpg <?php endif; ?>)'>
        <?php if ( !empty( $user['User']['cover'] ) ): ?>
        <img id="cover_img_display" width="100%" src="<?php echo $this->request->webroot?>uploads/covers/<?php echo $user['User']['cover']?>" />
        <?php else: ?>
        <img id="cover_img_display" width="100%" src="<?php echo $this->request->webroot?>img/cover.jpg" />
        <?php endif; ?>
        <?php if ( !empty( $cover_album_id ) ): ?>
        <a href="<?php echo $this->request->base?>/albums/view/<?php echo $cover_album_id?>"></a>
        <?php endif; ?>

        <?php if ( $uid == $user['User']['id'] ): ?>
        <div id="cover_upload">
                <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "users",
                                            "action" => "ajax_cover",
                                            "plugin" => false,
                                           
                                        )),
             'title' => __('Edit Cover Picture'),
             'innerHtml'=> __('<i class="material-icons">photo_camera</i>'),
          'data-backdrop' => 'static',
     ));
 ?>

        </div>
        <?php endif; ?>
        <div class="gradient_bg"></div>
    </div>
    <div class="user-profile-main">
        <div id="avatar">
            <?php if ( !empty( $profile_album_id ) ): ?>
            <a href="<?php echo $this->request->base?>/albums/view/<?php echo $profile_album_id?>">
                    <?php echo $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '200_square'), array("id" => "av-img"))?>
            </a>
            <?php else: ?>
                <?php echo $this->Moo->getItemPhoto(array('User' => $user['User']), array("id" => "av-img", 'prefix' => '200_square'))?>
            <?php endif; ?>

            <?php if ( $uid == $user['User']['id'] ): ?>
            <div id="avatar_upload" >
                    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "users",
                                            "action" => "ajax_avatar",
                                            "plugin" => false,
                                        )),
             'title' => __('Edit Profile Picture'),
             'innerHtml'=> __('<i class="material-icons">photo_camera</i>'),
             'data-backdrop' => 'static'
     ));
 ?>

            </div>
            <?php endif; ?>

        </div>
        <div class="profile-info">
            <div class="profile-info-left">
                <div class="profile-info-section">
                    <h1><?php echo h($this->Text->truncate($user['User']['name'], 30, array('exact' => false)))?>
                     <?php if ( !empty($is_online)): ?>
                        <span class="online-stt">
                        </span>
                    <?php endif; ?>
                    </h1>
                </div>
             <?php if ( $canView ): ?>
                <ul class="list3 profile_info">
                    <?php if ( !empty( $user['User']['gender'] ) ): ?>
                    <li style="background:none;padding:0"><span class="date"><?php echo __('Gender')?>:</span> <?php $this->Moo->getGenderTxt($user['User']['gender']); ?></li>
                    <?php endif; ?>
                <?php if ( !empty( $user['User']['birthday'] ) && $user['User']['birthday'] != '0000-00-00'): ?>
                    <li><span class="date"><?php echo __('Born on')?>:</span> <?php echo $this->Time->event_format($user['User']['birthday'], '%B %d')?></li>
                    <?php endif; ?>
                    <?php 
                	//add profile type
	                ?>
	                <?php if ($user['ProfileType']['id']):?>
	                	<?php if (Configure::read('core.enable_show_profile_type')):?>
	                	<li>
	                		<span class="date"><?php echo __('Profile type');?>: </span>
	                		<a href="<?php echo $this->request->base;?>/users/index/profile_type:<?php echo $user['ProfileType']['id'];?>"><?php echo $user['ProfileType']['name'];?></a>
	                	</li>	
	                	<?php endif;?>
                    <?php $helper = MooCore::getInstance()->getHelper("Core_Moo");?>
                     <?php foreach ($fields as $field):
                         if (!in_array($field['ProfileField']['type'],$helper->profile_fields_default))
                         {
                             $options = array();
                             if ($field['ProfileField']['plugin'])
                             {
                                 $options = array('plugin' => $field['ProfileField']['plugin']);
                             }

                             echo $this->element('profile_field/' . $field['ProfileField']['type'].'_profile', array('field' => $field,'user'=>$user),$options);
                             continue;
                         }
                           if ( !empty( $field['ProfileFieldValue']['value'] ) && $field['ProfileField']['type'] != 'heading' ) :
                        ?>
                    <li><span class="date"><?php echo $field['ProfileField']['name']?>: </span>
                                        <?php echo $this->element( 'misc/custom_field_value', array( 'field' => $field ) ); ?>
                    </li>
                        <?php endif; 
                    endforeach; 
                        ?>
			 <?php endif; ?>
                </ul>
            <?php endif; ?>
            </div>
            <div class="section-menu">
                <div class="profile-action">
                <?php if ($user['User']['id'] != $uid && !empty($uid)): ?>



                    <a data-target="#themeModal" data-toggle="modal" class="topButton button button-action hidden-xs hidden-sm" href="<?php echo $this->request->base?>/conversations/ajax_send/<?php echo $user['User']['id']?>" title="<?php echo __('Send New Message')?>">
                    <?php echo __('Send Message')?>
                    </a>

                <?php if ( !empty($request_sent) ): ?>
                    <a id="userCancelFriend" href="<?php echo $this->request->base?>/friends/ajax_cancel/<?php echo $user['User']['id']?>" class="topButton button button-action" title="<?php __('Cancel a friend request');?>">
                    <?php echo __('Cancel Request')?>
                    </a>
                <?php endif; ?>

                <?php if ( !empty($respond) ): ?>
                    <div class="dropdown" style="float:right" >
                        <a id="respond" data-target="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false" class=" button button-action" title="<?php __('Respond to Friend Request');?>">
                        <?php echo __('Respond to Friend Request')?>
                        </a>

                        <ul class="dropdown-menu" role="menu" aria-labelledby="respond">
                            <li><a onclick="respondRequest(<?php echo  $request_id; ?>, 1)" href="javascript:void(0)"><?php echo  __('Accept'); ?></a></li>
                            <li><a onclick="respondRequest(<?php echo  $request_id; ?>, 0)" href="javascript:void(0)"><?php echo  __('Delete'); ?></a></li>
                        </ul>
                    </div>

                <?php endif; ?>

                <?php if ( !empty($uid) && !$areFriends && empty($request_sent) && empty($respond) ): ?>
                    <a id="userAddFriend" href="<?php echo $this->request->base?>/friends/ajax_add/<?php echo $user['User']['id']?>" id="addFriend_<?php echo $user['User']['id']?>" data-target="#themeModal" data-toggle="modal" class="topButton button button-action" title="<?php printf( __('Send %s a friend request'), h($user['User']['name']) )?>">
                    <?php echo __('Add as Friend')?>
                    </a>
                <?php endif; ?>

            <?php endif;?>
            <?php if ($user['User']['id'] == $uid): ?>
                    <a href="<?php echo $this->request->base?>/users/profile" class="btn btn-default" >

                    <?php echo __('Edit Profile')?></a>
            <?php endif; ?>
	    <?php if ($uid && Configure::read("core.enable_follow") && $uid != $user['User']['id']): ?>
            <?php
            $followModel = MooCore::getInstance()->getModel("UserFollow");
            $follow = $followModel->checkFollow($uid,$user['User']['id']);
            ?>
            <?php if (!$follow): ?>
                    <a href="javascript:void(0);" class="button button-action user_action_follow" data-uid="<?php echo $user['User']['id']; ?>" data-follow="0" >
                        <i class="visible-xs visible-sm material-icons">rss_feed</i><i class="hidden-xs hidden-sm">
                        <?php echo __('Follow')?></i></a>
            <?php else : ?>
                    <a href="javascript:void(0);" class="button button-action user_action_follow" data-uid="<?php echo $user['User']['id']; ?>" data-follow="1" >
                        <i class="visible-xs visible-sm material-icons"">check</i><i class="hidden-xs hidden-sm">
                        <?php echo __('Unfollow')?></i></a>
            <?php endif; ?>
        <?php endif; ?>
                    <div class="dropdown">
                        <a href="#" data-toggle="dropdown" class="btn btn-default">
                            <i class="material-icons">expand_more</i>
                        </a>
                        <ul class="dropdown-menu">
                        <?php if ($user['User']['id'] != $uid && !empty($uid)): ?> 
                            <li class='visible-xs visible-sm'>
                                <a data-target="#themeModal" data-toggle="modal" class="topButton button button-action" href="<?php echo $this->request->base?>/conversations/ajax_send/<?php echo $user['User']['id']?>" title="<?php echo __('Send New Message')?>">
                            <?php echo __('Send Message')?>
                                </a>
                            </li>
                         <?php endif; ?>
                        <?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && !$user['User']['featured'] ): ?>
                            <li><a href="<?php echo $this->request->base?>/admin/users/feature/<?php echo $user['User']['id']?>"><?php echo __('Feature User')?></a></li>
                        <?php endif; ?>
                        <?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && $user['User']['featured'] ): ?>
                            <li><a href="<?php echo $this->request->base?>/admin/users/unfeature/<?php echo $user['User']['id']?>"><?php echo __('Unfeature User')?></a></li>
                        <?php endif; ?>
                        <?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && !$user['Role']['is_admin'] ): ?>
                            <li><a href="<?php echo $this->request->base?>/admin/users/edit/<?php echo $user['User']['id']?>"><?php echo __('Edit User')?></a></li>
                        <?php endif; ?>
                            <li><a href="<?php echo $this->request->base?>/reports/ajax_create/user/<?php echo $user['User']['id']?>" data-target="#themeModal" data-toggle="modal" class="" title="<?php echo __('Report User')?>"><?php echo __('Report User')?></a></li>
                        <?php if ( !empty($uid) && $areFriends ): ?>
                            <li><a href="<?php echo  $this->request->base; ?>/friends/ajax_remove/<?php echo $user['User']['id'] ?>" data-target="#portlet-config" data-toggle="modal"><?php echo __('Unfriend')?></a></li> <?php //<a onclick="return removeFriend(<?php echo $user['User']['id'] )" href="javascript:void(0)">test</a>?>
                        <?php endif; ?>	
			<?php if ( !empty($uid) && ($uid != $user['User']['id'] ) && $user['User']['role_id'] != 1): ?>
                            <li><?php
                            if(!$is_viewer_block){
                                $this->MooPopup->tag(array(
                                    'href'=>$this->Html->url(array("controller" => "user_blocks",
                                                        "action" => "ajax_add",
                                                        "plugin" => false,
                                                         $user['User']['id']

                                                    )),
                                        'title' => __('Block'),
                                        'innerHtml'=> __('Block'),
                                     ));
                            }else{
                                $this->MooPopup->tag(array(
                                    'href'=>$this->Html->url(array("controller" => "user_blocks",
                                                        "action" => "ajax_remove",
                                                        "plugin" => false,
                                                        $user['User']['id']

                                                    )),
                                        'title' => __('Unblock'),
                                        'innerHtml'=> __('Unblock'),
                                     ));
                            }
                        ?></li>
                       <?php endif; ?>			
                        </ul>
                    </div>
                </div>


            </div>

        </div>
    </div>
    <div id="browse" class="profile-menu-section">
        <ul class="profile-menu">
            <li class="current">
                <a data-url="<?php echo $this->request->base?>/users/ajax_profile/<?php echo $user['User']['id']?>" rel="profile-content" href="<?php echo $this->Moo->getProfileUrl( $user['User'] )?>"><?php echo __('Wall')?></a>
            </li>
            <li>
                <a data-url="<?php echo $this->request->base?>/users/ajax_info/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Info')?></a>
            </li>
            <li>
                <a data-url="<?php echo $this->request->base?>/users/profile_user_friends/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Friends')?>
                </a>
            </li>
            <li class="show-in-mobile">
                <span href="#" data-toggle="dropdown">
                        <?php echo __('More') ?>
                </span>
                <ul class="dropdown-menu" aria-labelledby="dLabel">
		    	<?php if (Configure::read("core.enable_follow") && $user['User']['id'] == $uid): ?>

                    <li>
                        <a id="profile_follow" data-url="<?php echo $this->request->base?>/follows/user_follows" rel="profile-content" href="#">  <?php echo __('Following')?>
                        </a>
                    </li>
		          <?php endif; ?>
                        <?php if($cuser && ($uid == $user['User']['id'] || $cuser['Role']['is_admin'])): ?>
                    <li>
                        <a data-url="<?php echo $this->request->base?>/users/profile_user_blocks/<?php echo $user['User']['id']?>" rel="profile-content" href="#"> <?php echo __('Blocked Members')?>
                        </a>
                    </li>
                        <?php endif; ?>
                        <?php if (Configure::read('Photo.photo_enabled')): ?>
                    <li>
                        <a data-url="<?php echo $this->request->base?>/photos/profile_user_photo/<?php echo $user['User']['id']?>" rel="profile-content" id="user_photos" href="#"><?php echo __('Albums')?>
                        </a>
                    </li>		
                        <?php endif; ?>
                        <?php if (Configure::read('Blog.blog_enabled')): ?>
                    <li>
                        <a data-url="<?php echo $this->request->base?>/blogs/profile_user_blog/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Blog')?>
                        </a>
                    </li>
                        <?php endif; ?>
                        <?php if (Configure::read('Topic.topic_enabled')): ?>
                    <li>
                        <a data-url="<?php echo $this->request->base?>/topics/profile_user_topic/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Topics')?>
                        </a>
                    </li>		
                        <?php endif; ?>
                        <?php if (Configure::read('Video.video_enabled')): ?>
                    <li><a data-url="<?php echo $this->request->base?>/videos/profile_user_video/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Videos')?>
                        </a>
                    </li>	
                        <?php endif; ?>

                        <?php if (Configure::read('Group.group_enabled')): ?>
                    <li><a data-url="<?php echo $this->request->base?>/groups/profile_user_group/<?php echo $user['User']['id']?>" rel="profile-content" href="#"> <?php echo __('Groups')?>
                        </a>
                    </li>	
			<?php endif; ?>

                        <?php if (Configure::read('Event.event_enabled')): ?>
                            <?php
                            $this->Html->script(
                                array('https://maps.google.com/maps/api/js?sensor=false'), array('block' => 'mooScript')
                            );
                            ?>
                    <li><a data-url="<?php echo $this->request->base?>/events/profile_user_event/<?php echo $user['User']['id']?>" rel="profile-content" href="#"> <?php echo __('Events')?></a>
                    </li>	
			<?php endif; ?>
                    <?php
			$this->getEventManager()->dispatch(new CakeEvent('profile.afterRenderMenu', $this)); 
                    ?>
                <?php
    if ( $this->elementExists('menu/user') )
        echo $this->element('menu/user');
    ?>
                </ul>
            </li>
		<?php if (Configure::read("core.enable_follow") && $user['User']['id'] == $uid): ?>

            <li class="hidden-xs hidden-sm">
                <a id="profile_follow" data-url="<?php echo $this->request->base?>/follows/user_follows" rel="profile-content" href="#">  <?php echo __('Following')?>
                </a>
            </li>
		          <?php endif; ?>
                        <?php if($cuser && ($uid == $user['User']['id'] || $cuser['Role']['is_admin'])): ?>
                                <?php
				$blockModel = MooCore::getInstance()->getModel("UserBlock");
				?>
            <li class="hidden-xs hidden-sm">
                <a data-url="<?php echo $this->request->base?>/users/profile_user_blocks/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Blocked Members')?>
                </a>
            </li>
                        <?php endif; ?>
                <?php if (Configure::read('Photo.photo_enabled')): ?>
            <li class="hidden-xs hidden-sm">
                <a data-url="<?php echo $this->request->base?>/photos/profile_user_photo/<?php echo $user['User']['id']?>" rel="profile-content" id="user_photos" href="#"><?php echo __('Albums')?>
                </a>
            </li>		
                <?php endif; ?>
                <?php if (Configure::read('Blog.blog_enabled')): ?>
            <li class="hidden-xs hidden-sm">
                <a data-url="<?php echo $this->request->base?>/blogs/profile_user_blog/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Blog')?>
                </a>
            </li>
                <?php endif; ?>
                <?php if (Configure::read('Topic.topic_enabled')): ?>
            <li class="hidden-xs hidden-sm">
                <a data-url="<?php echo $this->request->base?>/topics/profile_user_topic/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Topics')?>
                </a>
            </li>		
                <?php endif; ?>
                <?php if (Configure::read('Video.video_enabled')): ?>
            <li class="hidden-xs hidden-sm"><a data-url="<?php echo $this->request->base?>/videos/profile_user_video/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Videos')?>
                </a>
            </li>	
                <?php endif; ?>
                <?php if (Configure::read('Group.group_enabled')): ?>
            <li class="hidden-xs hidden-sm"><a data-url="<?php echo $this->request->base?>/groups/profile_user_group/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><?php echo __('Groups')?>
                </a>
            </li>	
                <?php endif; ?>

                <?php if (Configure::read('Event.event_enabled')): ?>
                    <?php
                        $this->Html->script(
                           array('https://maps.google.com/maps/api/js?sensor=false'), array('block' => 'mooScript')
                        );
                    ?>
            <li class="hidden-xs hidden-sm"><a data-url="<?php echo $this->request->base?>/events/profile_user_event/<?php echo $user['User']['id']?>" rel="profile-content" href="#"> <?php echo __('Events')?>
                </a>
            </li>	
		<?php endif; ?>
                    <?php
			$this->getEventManager()->dispatch(new CakeEvent('profile.afterRenderMenu', $this)); 
                    ?>
                <?php
    if ( $this->elementExists('menu/user') )
        echo $this->element('menu/user');
    ?>
        </ul>

    </div>

</div>