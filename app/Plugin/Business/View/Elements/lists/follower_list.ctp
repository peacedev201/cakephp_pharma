<?php if($followers != null):?>
    <?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooBehavior", "mooBusiness", "mooUser"], function($, mooBehavior, mooBusiness, mooUser) {
            mooBehavior.initMoreResults();
            // init action addfriend
            mooUser.initRespondRequest();
        });
    </script>
    <?php endif?>
    <?php foreach($followers as $follower):
        $business_follower = $follower['BusinessFollow'];
        $follower = $follower['User'];
    ?>
        <div class="bus_follow_list">
            <?php
                echo $this->Moo->getItemPhoto(array(
                    'User' => $follower), 
                array( 
                    'prefix' => '50_square'
                ), array(
                    'class' => 'img_wrapper2'
                ));
            ?>
            <div class="bus_review_info">
                <div class="user_review">
                    <?php echo $this->Moo->getName($follower)?>
                </div>
                <div class="bus_user_info">
                    <?php echo $follower['friend_count'];?> <?php echo $follower['friend_count'] > 1 ? __d('business', 'friends') : __d('business', 'friend');?>
                    -
                    <?php echo $follower['photo_count'];?> <?php echo $follower['photo_count'] > 1 ? __d('business', 'photos') : __d('business', 'photo');?>
                </div>
                <div class="follow_list_add_friend">
                    <?php if($permission_can_ban && !in_array($follower['id'], $admin_list)):?>
                        <?php if($business_follower['is_banned']):?>
                            <a class="button" id="btn_unban_follower" href="javascript:void(0)" data-business="<?php echo $business_follower['business_id'];?>" data-user="<?php echo $follower['id'];?>">
                                <?php echo __d('business', 'Un-ban');?>
                            </a>
                        <?php else:?>
                            <a class="button" id="btn_ban_follower" href="javascript:void(0)" data-business="<?php echo $business_follower['business_id'];?>" data-user="<?php echo $follower['id'];?>">
                                <?php echo __d('business', 'Ban');?>
                            </a>
                        <?php endif;?>
                    <?php endif;?>
                    <?php if ($this->Business->existRequest($uid, $follower['id'])): ?>
                        <a id="cancelFriend_<?php echo $follower['id'];?>" href="javascript:void(0)" class="button cancel_friend_request" data-id="<?php echo $follower['id'];?>">
                            <?php echo __d('business', 'Cancel Request')?>
                        </a>
                    <?php elseif ( !empty($respond) && in_array($follower['id'], $respond ) && $follower['id'] != $uid): ?>
                            <a href="javascript:void(0)" id="respond" data-target="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false" class="button" title="<?php __d('business', 'Respond to Friend Request');?>">
                                <?php echo __d('business', 'Respond Request')?>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="respond">
                                <li><a data-id="<?php echo  $request_id[$follower['id']]; ?>" data-status="1" class="respondRequest" href="javascript:void(0)"><?php echo  __d('business', 'Accept'); ?></a></li>
                                <li><a data-id="<?php echo  $request_id[$follower['id']]; ?>" data-status="0" class="respondRequest" href="javascript:void(0)"><?php echo  __d('business', 'Delete'); ?></a></li>
                            </ul>
                    <?php elseif ( !empty($uid) && $uid != $follower['id'] && !$this->Business->areFriends($uid, $follower['id'])): ?>
                        <?php
                            $this->MooPopup->tag(array(
                                'href'=>$this->Html->url(array(
                                    "controller" => "friends",
                                    "action" => "ajax_add",
                                    "plugin" => false,
                                    $follower['id']
                                )),
                                'class' => 'button',
                                'title' => sprintf( __d('business', 'Send %s a friend request'), h($follower['name']) ),
                                'innerHtml'=> __d('business', 'Add as Friend'),
                                'id' => 'addFriend_' . $follower['id'],
                                'data-target' => '#businessModal',
								'target' => 'businessModal'
                           ));
                        ?>
                    <?php elseif ( !empty($uid) && $uid != $follower['id'] && $this->Business->areFriends($uid, $follower['id'])): ?>
                        <?php
                            $this->MooPopup->tag(array(
                                'href'=>$this->Html->url(array(
                                    "controller" => "friends",
                                    "action" => "ajax_remove",
                                    "plugin" => false,
                                    $follower['id']
                                )),
                                'title' => __d('business', 'Remove'),
                                'innerHtml'=> __d('business', 'Remove'),
                                'id' => 'removeFriend_'.$follower['id'],
                                'class' => 'button',
                                'data-target' => '#businessModal',
								'target' => 'businessModal'
                           ));
                       ?>
                    <?php endif;?>
                </div>
            </div>
        </div>
    <?php endforeach;?>
    <?php if(count($followers) == Configure::read('Business.business_follower_item_per_page')):?>
        <?php $this->Html->viewMore($more_url, 'follower-content') ?>
    <?php endif;?>
<?php else:?>
	<?php echo '<div class="clear text-center">' . __d('business', 'No more results found') . '</div>';?>
<?php endif;?>