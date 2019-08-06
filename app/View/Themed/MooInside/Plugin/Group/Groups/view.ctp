<?php
$groupHelper = MooCore::getInstance()->getHelper('Group_Group');
$topic_id = !empty( $this->request->named['topic_id'] ) ? $this->request->named['topic_id'] : 0;
$video_id = !empty( $this->request->named['video_id'] ) ? $this->request->named['video_id'] : 0;
$tab = !empty( $tab ) ? $tab : '';
?>

<?php if($this->request->is('ajax')): ?>
<script>
    require(["jquery","mooGroup", "hideshare"], function($,mooGroup) {
        mooGroup.initOnView();
        $(".sharethis").hideshare({media: '<?php echo $groupHelper->getImage($group,array('prefix' => '300_square'))?>', linkedin: false});
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery','mooGroup', 'hideshare'),'object'=>array('$','mooGroup'))); ?>
    mooGroup.initOnView();
    $(".sharethis").hideshare({media: '<?php echo $groupHelper->getImage($group,array('prefix' => '300_square'))?>', linkedin: false});
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

    <div class="mb-group-head">
        <div class="mb-group-img" style="background-image:url('<?php echo $groupHelper->getImage($group, array())?>')">
           
            <div class="gradient_bg"></div>
            <h3 class="group-detail-name"><?php echo h($group['Group']['name'])?></h3>
        </div>  
        
            <div class="mb-menu-top menu-profile-section">
                <div class="mb-active-detail">
                    <div class="group_button_top">
                        <?php if (!empty($uid) && (($group['Group']['type'] != PRIVACY_PRIVATE && empty($my_status['GroupUser']['status'])) || ($group['Group']['type'] == PRIVACY_PRIVATE && empty($my_status['GroupUser']['status'])))): ?>

                            <a href="<?php echo  $this->request->base ?>/groups/do_request/<?php echo  $group['Group']['id'] ?>" class="btn btn-default"><?php echo  __('Join') ?></a>

                        <?php endif; ?>
                        <?php if (!empty($request_count)): ?>
                            
                                <a class="btn btn-default"  id="join-request" data-request="<?php echo  $request_count; ?>" href="<?php echo  $this->request->base ?>/groups/ajax_requests/<?php echo  $group['Group']['id'] ?>" data-target="#portlet-config" data-toggle="modal" title="<?php echo  __('Join Requests') ?>">
                                    <?php echo  $request_count ?> <?php echo  __n('join request', 'join requests', $request_count) ?>
                                </a>
                           
                        <?php endif; ?>

                        
                            <?php if ($uid): ?>
                           <?php if ($uid): ?>

            <div class="list_option">
                <div class="dropdown">
                    <a class="btn btn-default" id="dropdown-edit" data-target="#" data-toggle="dropdown">
                        <i class="material-icons">more_vert</i>
                    </a>

                    <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit">

                        <?php if ( ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_MEMBER  && $group['Group']['type'] != PRIVACY_PRIVATE) ||
                                !empty($cuser['Role']['is_admin'] ) ||
                                ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_ADMIN)
                                ): ?>
                        <li>
                            <?php
                                $this->MooPopup->tag(array(
                                       'href'=>$this->Html->url(array("controller" => "groups",
                                                                      "action" => "ajax_invite",
                                                                      "plugin" => 'group',
                                                                      $group['Group']['id'],

                                                                  )),
                                       'title' => __( 'Invite Friends'),
                                       'innerHtml'=> __( 'Invite Friends'),
                               ));
                            ?>
                        </li>
                        <?php endif; ?>

                        <?php if ( ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_ADMIN && $group['Group']['user_id'] == $uid ) || !empty($cuser['Role']['is_admin'] ) ): ?>
                        <li><a href="<?php echo $this->request->base?>/groups/create/<?php echo $group['Group']['id']?>"><?php echo __( 'Edit Group')?></a></li>
                        <li><a href="javascript:void(0)" data-id="<?php echo  $group['Group']['id'] ?>" class="deleteGroup"><?php echo __( 'Delete Group')?></a></li>
                        <?php endif; ?>

                        <li>
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "reports",
                                            "action" => "ajax_create",
                                            "plugin" => false,
                                            'group_group',
                                            $group['Group']['id'],
                                        )),
             'title' => __( 'Report Group'),
          'data-dismiss' => 'modal',
             'innerHtml'=> __( 'Report Group'),
     ));
 ?>
                           </li>
                        <li class="seperate"></li>
                        <?php if ( !empty($my_status) && ( $my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) && ( $uid != $group['Group']['user_id'] ) ): ?>
			<li><a href="javascript:void(0)" class="leaveGroup" data-id="<?php echo $group['Group']['id']?>"><?php echo __('Leave Group')?></a></li>
			<?php endif; ?>
                        <?php if (isset($my_status['GroupUser']['status'])):?>
                            <?php
                                $settingModel = MooCore::getInstance()->getModel("Group.GroupNotificationSetting");
                                $checkStatus = $settingModel->getStatus($group['Group']['id'],$uid);
                            ?>
                            <li><a href="<?php echo $this->request->base?>/groups/stop_notification/<?php echo $group['Group']['id']?>"><?php if ($checkStatus) echo __( 'Turn Off Notification'); else echo __('Turn On Notification');?></a></li>
                        <?php endif;?>
                        <?php // do not add "Do Feature" for private group ?>
                        <?php if ( ( !empty($cuser) && $cuser['Role']['is_admin'] && $group['Group']['type'] != PRIVACY_PRIVATE ) ): ?>
                        <?php if ( !$group['Group']['featured'] ): ?>
                        <li><a href="<?php echo $this->request->base?>/groups/do_feature/<?php echo $group['Group']['id']?>"><?php echo __( 'Feature Group')?></a></li>
                        <?php else: ?>
                        <li><a href="<?php echo $this->request->base?>/groups/do_unfeature/<?php echo $group['Group']['id']?>"><?php echo __( 'Unfeature Group')?></a></li>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($group['Group']['type'] != PRIVACY_PRIVATE && $group['Group']['type'] != PRIVACY_RESTRICTED): ?>
                        <li>
                            <a href="javascript:void(0);" share-url="<?php echo $this->Html->url(array(
                                    'plugin' => false,
                                    'controller' => 'share',
                                    'action' => 'ajax_share',
                                    'Group_Group',
                                    'id' => $group['Group']['id'],
                                    'type' => 'group_item_detail'
                                ), true); ?>" class="shareFeedBtn"><?php echo __('Share'); ?></a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
                            <?php endif; ?>
                        
                    </div>
                </div>
                <ul id="browse" >
                    <li class="current">
                            <a class="no-ajax" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>">
                                 <?php echo __( 'Details')?>
                            </a>
                        </li>		
                       <li><a data-url="<?php echo $this->request->base?>/groups/members/<?php echo $group['Group']['id']?>" rel="profile-content" id="teams" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/tab:teams">
                                <?php echo __( 'Members')?> </a>
                        </li>
                        <li><a data-url="<?php echo $this->request->base?>/photos/ajax_browse/group_group/<?php echo $group['Group']['id']?>" rel="profile-content" id="photos" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/tab:photos">
                            <?php echo __('Photos')?> </a>
                        </li>
                        <li class='dropdown visible-xs visible-sm'>
                            <span data-toggle="dropdown"><?php echo __('More') ?></span>
                            <ul class="dropdown-menu">
                                <?php foreach ($group_menu as $item): ?>
                                <li><a data-url="<?php echo $item['dataUrl']?>" rel="profile-content" id="<?php echo $item['id']?>" href="<?php echo $item['href']?>">
                                    <?php echo $item['name']?> </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                         
                        <?php foreach ($group_menu as $item): ?>
                        <li class='hidden-xs hidden-sm'><a data-url="<?php echo $item['dataUrl']?>" rel="profile-content" id="<?php echo $item['id']?>" href="<?php echo $item['href']?>">
                            <?php echo $item['name']?> </a>
                        </li>
                        <?php endforeach; ?>
                </ul>
                
            </div>
    </div>
    


	<div id="profile-content" class="group-detail">
            <div class="groupId" data-id="<?php echo $group['Group']['id']; ?>"></div>
            <div class="topicId" data-id="<?php echo $topic_id; ?>"></div>
            <div class="videoId" data-id="<?php echo $video_id; ?>"></div>
            <div class="tab" data-id="<?php echo $tab; ?>"></div>
        <?php if ( empty( $tab ) ): ?>
		<?php 
		if ( !empty( $this->request->named['topic_id'] ) || !empty( $this->request->named['video_id'] ) )
			echo __( 'Loading...');
		else
			echo $this->element('ajax/group_detail');
		?>
	    <?php else: ?>
            <?php echo __( 'Loading...')?>
        <?php endif; ?>
    </div>
