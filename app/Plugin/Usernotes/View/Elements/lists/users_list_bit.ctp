<?php
if (count($users) > 0) {
    foreach ($users as $user):
        ?>
        <li <?php if (isset($type) && $type == 'home'): ?>id="friend_<?php echo $user['Friend']['friend_id'] ?>"<?php endif; ?>
                                                            <?php if (isset($group)): ?>id="member_<?php echo $user['GroupUser']['id'] ?>"<?php endif; ?>
                                                            class="user-list-index unote-item-<?php echo $user['User']['id']; ?>">
            <input type="hidden" class="unote-target-id" value="<?php echo $user['User']['id']; ?>">

            <div class="row" style="height:20px;">
                <div class="col-md-12 text-right">
                    <div class="list_option">
                        <div class="dropdown">
                            <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">edit</i>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel" >

                                <li class="unote-edit">  
                                    <a href="javascript:void(0);" ><?php echo __d('usernotes', 'Edit') ?></a>
                                </li>
                                <li class="seperate"></li>
                                <li class="unote-delete">  
                                    <a href="javascript:void(0);" ><?php echo __d('usernotes', 'Delete') ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="list-content">
                <div class="user-idx-item">
                    <a href="<?php echo $this->request->base ?>/<?php echo (!empty($user['User']['username'])) ? '-' . $user['User']['username'] : 'users/view/' . $user['User']['id'] ?>"><?php echo $this->Moo->getItemPhoto(array('User' => $user['User']), array('prefix' => '200_square')) ?></a>
                    <?php if (isset($friends_request) && in_array($user['User']['id'], $friends_request) && $user['User']['id'] != $uid): ?>
                        <a href="<?php echo $this->request->base ?>/friends/ajax_cancel/<?php echo $user['User']['id'] ?>" id="cancelFriend_<?php echo $user['User']['id'] ?>" class="add_people" title="<?php __d('usernotes', 'Cancel a friend request'); ?>">
                            <i class="material-icons">clear</i> <?php echo __d('usernotes', 'Cancel Request') ?>
                        </a>
                    <?php elseif (!empty($respond) && in_array($user['User']['id'], $respond) && $user['User']['id'] != $uid): ?>
                        <div class="dropdown" style="" >
                            <a href="#" id="respond" data-target="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false" class="add_people" title="<?php __d('usernotes', 'Respond to Friend Request'); ?>">
                                <i class="material-icons">person_add</i> <?php echo __d('usernotes', 'Respond to Friend Request') ?>
                            </a>

                            <ul class="dropdown-menu" role="menu" aria-labelledby="respond">
                                <li><a class="respondRequest" data-id="<?php echo $request_id[$user['User']['id']]; ?>" data-status="1" href="javascript:void(0)"><?php echo __d('usernotes', 'Accept'); ?></a></li>
                                <li><a class="respondRequest" data-id="<?php echo $request_id[$user['User']['id']]; ?>" data-status="0" href="javascript:void(0)"><?php echo __d('usernotes', 'Delete'); ?></a></li>
                            </ul>
                        </div>
         <?php endif; ?>

                    <?php
                    if (isset($user_block)) {
                        $this->MooPopup->tag(array(
                            'href' => $this->Html->url(array("controller" => "user_blocks",
                                "action" => "ajax_remove",
                                "plugin" => false,
                                $user['User']['id']
                            )),
                            'title' => __d('usernotes', 'Unblock'),
                            'innerHtml' => '<i class="material-icons icon-large delete-icon">clear</i> ' . __d('usernotes', 'Unblock'),
                            'id' => 'unblock_' . $user['User']['id'],
                            'class' => 'add_people unblock'
                        ));
                    }
                    ?>

                </div>

                <?php if (isset($type) && $type == 'home'): ?>
                    <?php
                    $this->MooPopup->tag(array(
                        'href' => $this->Html->url(array("controller" => "friends",
                            "action" => "ajax_remove",
                            "plugin" => false,
                            $user['User']['id']
                        )),
                        'title' => '',
                        'innerHtml' => '<i class="material-icons icon-large delete-icon">clear</i>',
                        'id' => 'removeFriend_' . $user['User']['id']
                    ));
                    ?>

                <?php endif; ?>

                <div class="user-list-info">
                    <div class="user-name-info">
                        <?php echo $this->Moo->getName($user['User']) ?>
                    </div>
                    <div class="">
                        <span class="date">
                         
                            <?php
                            if (isset($group) && isset($admins) && $user['User']['id'] != $uid && $group['User']['id'] != $user['User']['id'] &&
                                    (!empty($cuser['Role']['is_admin']) || in_array($uid, $admins) )):
                                ?>
                                <a href="javascript:void(0)" class="removeMember" data-id="<?php echo $user['GroupUser']['id'] ?>"><?php echo __d('usernotes', 'Remove Member') ?></a> .
                            <?php endif; ?>

                            <?php
                            if (isset($group) && isset($admins) && !in_array($user['User']['id'], $admins) &&
                                    (!empty($cuser['Role']['is_admin']) || $uid == $group['User']['id'] )):
                                ?>
                                <a href="javascript:void(0)" class="changeAdmin" data-id="<?php echo $user['GroupUser']['id'] ?>" data-type="make"><?php echo __d('usernotes', 'Make Admin') ?></a>
                            <?php endif; ?>

                            <?php
                            if (isset($group) && isset($admins) && in_array($user['User']['id'], $admins) && $user['User']['id'] != $group['User']['id'] &&
                                    (!empty($cuser['Role']['is_admin']) || $uid == $group['User']['id'] )):
                                ?>
                                <a href="javascript:void(0)" class="changeAdmin" data-id="<?php echo $user['GroupUser']['id'] ?>" data-type="remove"><?php echo __d('usernotes', 'Remove Admin') ?></a>
        <?php endif; ?>


                        </span>
                    </div>
                </div>

            </div>
            <div class="detail-note" >
                <textarea readonly="true" class="no-grow" style="display: none; width: 99%;overflow: auto;min-height: 150px !important;border: none;" rows="10"><?php echo h($this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $user['Usernote']['content'] ))); ?></textarea>
                <div class="note_content">
                    <?php echo $this->viewMore( $user['Usernote']['content'], 100 ); ?>
                </div>
            </div>
        <?php $this->Html->rating($user['User']['id'], 'users'); ?>
        </li>

        <?php
    endforeach;
} else
    echo '<div class="clear">' . __d('usernotes', 'No more results found') . '</div>';
?>

<style type="text/css" media="screen">
    .note_content{
        line-height: 17px;
    }
</style>
    