<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1><?php echo __d('friend_inviter', "Your contacts"); ?></h1>
        </div>
        <?php
        $counter = 0;
        if (!isset($errormessage)) {

            if (isset($addtofriend) && !empty($addtofriend)) {
                ?>
                <div id='show_sitefriend' style="display:block;">
                    <?php } else { ?>
                    <div id='show_sitefriend' style="display:none;">
                        <?php
                    }

                    $total = count($addtofriend);
                    if ($total > 0) {
                        ?>
                        <div class="header">	
                            <div class="title">
        <?php echo __d('friend_inviter', "The following users are already a member of our community") ?>
                            </div>                  		
                        </div>

                        <?php
                    }

                    if (!empty($addtofriend)) {
                        ?>
                        <div class="user_contacts">
                            <div class="user_contacts_list">
                                <table width="100%" cellpadding="0" cellspacing="0" id="tbl_addtofriend">
                                    <?php
                                    $total_contacts = 0;
                                    foreach ($addtofriend as $values) {
                                        $total_contacts++;
                                        ?>
                                        <tr id="addFriend_<?php echo $values['id']; ?>">
                                            <td width="10%">
                                                <?php
                                                $user = array(
                                                    'User' => $values
                                                );
                                                echo $this->Moo->getItemPhoto($user, array('prefix' => '50_square'), array('class' => 'tip user_avatar_small'));
                                                ?>
                                            </td>
                                            <td width="60%">
                                                <b><?php echo $this->Moo->getName($values) ?></b>
                                            </td>
                                            <td>
            <?php if (isset($friends_request) && in_array($user['User']['id'], $friends_request) && $user['User']['id'] != $uid): ?>

                                                    <a href="<?php echo $this->request->base ?>/friends/ajax_cancel/<?php echo $user['User']['id'] ?>" id="cancelFriend_<?php echo $user['User']['id'] ?>" title="<?php __('Cancel a friend request'); ?>">
                                                        <i class="icon-pending"></i> <?php echo __('Cancel Request') ?>
                                                    </a>
            <?php elseif (!empty($respond) && in_array($user['User']['id'], $respond) && $user['User']['id'] != $uid): ?>
                                                    <div class="dropdown" style="" >
                                                        <a href="#" id="respond" data-target="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false" title="<?php __('Respond to Friend Request'); ?>">
                                                            <i class="icon-user-add"></i> <?php echo __('Respond to Friend Request') ?>
                                                        </a>

                                                        <ul class="dropdown-menu" role="menu" aria-labelledby="respond">
                                                            <li><a class="respondRequest" data-id="<?php echo $request_id[$user['User']['id']]; ?>" data-status="1" href="javascript:void(0)"><?php echo __('Accept'); ?></a></li>
                                                            <li><a class="respondRequest" data-id="<?php echo $request_id[$user['User']['id']]; ?>" data-status="0" href="javascript:void(0)"><?php echo __('Delete'); ?></a></li>
                                                        </ul>
                                                    </div>

                                                <?php elseif (isset($friends) && in_array($user['User']['id'], $friends) && $user['User']['id'] != $uid): ?>
                                                    <?php
                                                    $this->MooPopup->tag(array(
                                                        'href' => $this->Html->url(array("controller" => "friends",
                                                            "action" => "ajax_remove",
                                                            "plugin" => false,
                                                            $user['User']['id']
                                                        )),
                                                        'title' => __('Remove'),
                                                        'innerHtml' => '<i class="icon-delete"></i> ' . __('Remove'),
                                                        'id' => 'removeFriend_' . $user['User']['id'],
                                                        'class' => ''
                                                    ));
                                                    ?>

                                                <?php elseif (isset($friends) && isset($friends_request) && !in_array($user['User']['id'], $friends) && !in_array($user['User']['id'], $friends_request) && $user['User']['id'] != $uid): ?>
                                                    <?php
                                                    $this->MooPopup->tag(array(
                                                        'href' => $this->Html->url(array("controller" => "friends",
                                                            "action" => "ajax_add",
                                                            "plugin" => false,
                                                            $user['User']['id']
                                                        )),
                                                        'title' => sprintf(__('Send %s a friend request'), h($user['User']['name'])),
                                                        'innerHtml' => '<i class="icon-user-add"></i>&nbsp;' . __('Add'),
                                                        'id' => 'addFriend_' . $user['User']['id'],
                                                        'class' => ''
                                                    ));
                                                    ?>

            <?php endif; ?>

                                            </td>
                                        </tr>
        <?php }
        ?>
                                </table>
                            </div>		  
                        </div>

                        <input type="hidden" name="total_contacts"  id="total_contacts" value="<?php echo $total_contacts; ?>" >
                        <?php
                    }
                    ?>
                </div>

                    <?php if (!empty($user_contacts)) { ?>
                    <div id='show_nonsitefriends' style="display:block;">

                        <?php } else {
                            ?>
                        <div id='show_nonsitefriends' style="display:none;">
                            <?php
                        }
                        $total = count($user_contacts);
                        if ($total > 0) {
                            ?>
                            <div class="header">	

                                <div class="title">	
        <?php echo __d('friend_inviter', 'The following people are not joined yet, please select to send invitations.') ?>
                                </div>


                            </div>
                            <?php
                        }
                        ?>

                        <form method="post" action="<?php echo $this->Html->url(array('plugin' => 'friend_inviter', 'controller' => 'layout', 'action' => 'admin_editpageinfo', 'pageId', 'ext' => 'json')); ?>" class="global_form" name='openinviter' enctype="application/x-www-form-urlencoded" >
                            <div class="contactimporter_contactlist">
    <?php if (count($user_contacts) > 0): ?>
                                    <table class='' align='left' cellspacing='0' cellpadding='5px' style="width: 100%;border-left:2px solid #EDEDED;border-right:2px solid #EDEDED;">
                                        <tr style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#EDEDED none repeat scroll 0 50%;border-bottom:1px solid #C0C0C0;margin:0px auto 0;font-weight:bold;clear:both;width:80%'>
                                            <td style="width: 2.5%">&nbsp;</td>
                                            <td style="width: 9%"><input id='checkallBox' type='checkbox' name='toggle_all' title='Select/Deselect all' checked></td>
                                            <td style="width: 50%"><?php echo __d('friend_inviter', 'Name') ?></td>
                                            <?php if ($plugType == 'email'): ?>
                                                <td><?php echo __d('friend_inviter', 'E-mail') ?></td>
                                            <?php else: ?>
                                                <td></td>
                                    <?php endif; ?>
                                        </tr>
                                    </table>
    <?php endif; ?>
                                <div id = 'page_1'>
                                    <div style="max-height: 560px; overflow-x: auto; overflow-y: auto; float: left; width: 100%;margin-bottom: 10px">
                                        <table class='thTable ' align='left' cellspacing='0' cellpadding='5px' style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#FFFFFF none repeat scroll 0 50%;border:1px solid #C0C0C0;overflow:auto;width:100%;'>
                                            <?php
                                            $contents = "";
                                            $contacts = array();
                                            $temps = $user_contacts;

                                            $contacts = $temps;

                                            $total_contacts = count($contacts);
                                            $contact_per_page = 30;
                                            $total_pages = ceil(count($contacts) / $contact_per_page);
                                            $page = 1;

                                            if ($total_contacts == 0) {
                                                $contents .= "<tr class='thTableOddRow'><td align='center' style='padding:20px;' colspan='" . ($plugType == 'email' ? "3" : "2") . "'>'You do not have any contacts in your address book.')</td></tr>";
                                            } else {
                                                uasort($contacts, 'compareOrder');

                                                $check_first_cha = "";

                                                foreach ($contacts as $email => $data) {

                                                    $counter++;
                                                    if (is_array($data)) {

                                                        $pic = "<img height='30px' src='{$data['picture']}'>";

                                                        $name = trim($data['name']);

                                                        if (isset($data['email'])) {
                                                            $email = $data['email'];
                                                        }
                                                    } else {
                                                        $name = trim($data);
                                                        if ($name == "")
                                                            $name = $email;
                                                        $pic = '';
                                                    }
                                                    //check and add new page
                                                    if ($counter > $page * $contact_per_page) {
                                                        $from = ($page - 1) * $contact_per_page + 1;
                                                        $to = $page * $contact_per_page > $total_contacts ? $total_contacts : $page * $contact_per_page;

                                                        $contents .= "</table></div><span class='contactimporter_total_page'>"
                                                                . "$from-$to " . __d('friend_inviter', "of ") . $total_contacts . ' ' . __d('friend_inviter', "Contacts")
                                                                . "</span></div>";
                                                        $page ++;
                                                        $contents .= "<div id = 'page_" . $page . "'>";
                                                        $contents .= "<div style='max-height: 560px; overflow-x: auto; overflow-y: auto; float: left; width: 100%;margin-bottom: 10px'>";
                                                        $contents .= "<table class='thTable' align='left' cellspacing='0' cellpadding='5px' style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#FFFFFF none repeat scroll 0 50%;border:1px solid #C0C0C0;overflow:auto;width:100%; padding-left:5px;'>";
                                                    }

                                                    if (ucfirst(mb_substr($name, 0, 1, 'UTF-8')) != $check_first_cha) {
                                                        $contents .= '<tr class="letter">
												<td>&nbsp;</td>
	                    						<td colspan="3">
	                    						<div style="padding-left:2px;">' . ucfirst(mb_substr($name, 0, 1, 'UTF-8')) . '</div></td>
	                			</tr>';
                                                        $check_first_cha = ucfirst(mb_substr($name, 0, 1, 'UTF-8'));
                                                    }
                                                    if ($counter <= $max_invitation)
                                                        $class = 'thTableSelectRow';
                                                    elseif ($counter % 2)
                                                        $class = ' thTableOddRow';
                                                    else
                                                        $class = 'thTableEvenRow';

                                                    $contents .= "<tr class='{$class}'  id='row_{$counter}'  ><td style = 'width: 2.5%'>&nbsp;</td><td style = 'width: 9%'><input id='check_{$counter}' name='check_{$counter}' class='check_item' rel='{$counter}' value='" . ($plugType == 'email' ? $data["email"] : $data["id"] . '#' . $data["name"]) . "' type='checkbox' class='thCheckbox'";
                                                    if ($counter <= $max_invitation)
                                                        $contents .= " checked ";
                                                    $contents .= "><input type='hidden' name='email_{$counter}' value='{$email}'><input type='hidden' name='name_{$counter}' value='{$name}'></td><td style = 'width: 50%' class='check_item' rel='{$counter}'>{$name}</td>" . ($plugType == 'email' ? "<td class='check_item' rel='{$counter}'>{$email}</td>" : "<td class = 'contactimporter_contact_image'>" . $pic . "</td>") . "</tr>";
                                                }
                                            }
                                            if ($counter == 0)
                                                $contents = "<tr class='thTableOddRow'><td align='center' style='padding:20px;' colspan='" . ($plugType == 'email' ? "3" : "2") . "'>" . "You do not have any contacts in your address book." . "</td></tr>";

                                            echo $contents;
                                            ?>
                                        </table>
                                    </div>

                                    <?php if ($total_contacts > 0): ?>
                                        <?php
                                        $from = ($page - 1) * $contact_per_page + 1;
                                        $to = $page * $contact_per_page > $total_contacts ? $total_contacts : $page * $contact_per_page;
                                        ?>

                                        <span class='contactimporter_total_page'>
                                        <?php echo "$from-$to " . __d('friend_inviter', "of ") . $total_contacts . __d('friend_inviter', "Contacts") ?>
                                        </span>
    <?php endif; ?>
                                </div>
                            </div>

    <?php if ($total_contacts > 0): ?>
                                <div id="pagination">
                                    <div class="pages" style="float:right; margin-top: -20px;">
        <?php if ($total_pages > 1): ?>
                                            <ul class="paginationControl" id="contactimporter_page_list">
                                                <li style="display:none">
                                                    <a href="javascript:;" id="0" rel="page_0" >
                                                        &#171; <?php echo __d('friend_inviter', 'Previous'); ?>
                                                    </a>
                                                </li>
                                                <li class="selected">
                                                    <a href="javascript:;" id="1" rel="page_1">
                                                        1
                                                    </a>
                                                </li>
            <?php for ($i = 2; $i <= $total_pages; $i ++): ?>
                                                    <li <?php if ($i > 10) echo "style = 'display: none'" ?>>
                                                        <a href="javascript:;" id="<?php echo $i ?>" rel="page_<?php echo $i ?>" >
                <?php echo $i ?>
                                                        </a>
                                                    </li>
            <?php endfor; ?>
                                                <li>
                                                    <a href="javascript:;" id="<?php echo $total_pages + 1 ?>" rel="page_<?php echo $total_pages + 1; ?>">
            <?php echo __d('friend_inviter', 'Next'); ?> &#187;
                                                    </a>
                                                </li>
                                            </ul>
        <?php endif; ?>
                                    </div>
                                </div>
    <?php endif; ?>   

                            <br /><br />
                            <div class="form-wrapper" id="message-wrapper"><div class="form-label" id="message-label" style="width: 120px;text-align: left;"><label class="optional" for="message"><?php echo __d('friend_inviter', "Custom Message"); ?></label></div>
                                <div class="form-element" id="message-element">
                                    <textarea rows="6" cols="45" id="custom_message" name="custom_message"></textarea>

                                    <table><tr><td>
                                                <input type="hidden" value="do_add" name="task" />
                                                <input type="hidden" value="<?php echo $plugType ?>" name="plugType" />
                                                <input type='hidden' id='nonsitetotal_contacts' name='nonsitetotal_contacts' value='<?php echo $total_contacts; ?>'>
                                                <button class="btn btn-action" type='button' id='send_invite' name='send_invite'><?php echo __d('friend_inviter', 'Send Selected'); ?> (<span id = "count_contacts"><?php echo ($total_contacts < $max_invitation) ? $total_contacts : $max_invitation ?></span>)</button> 

                                            </td>
                                            <td>                                     
                                                </form>
                                                &nbsp;&nbsp;<button class="btn btn-action" type='button' name='skip_action' id="skip_action"><?php echo __d('friend_inviter', 'Skip'); ?> &gt;&gt;</button> 
                                            </td></tr></table>
                                    <input type="hidden" value="<?php echo $plugType ?>" name="plugType" />
                                    <input   type="hidden" value="skip" name="task" />

                                </div>
                                <?php
                            }
                            else {
                                echo "<div>" . __d('friend_inviter', "All your imported contacts are already members of") . ' ' . Configure::read('core.site_name') . ".</div>";
                            }
                            ?>

                            <?php

                            function compareOrder($a, $b) {
                                if ($a['name']) {
                                    return ucfirst(trim($b['name'])) < ucfirst(trim($a['name']));
                                }
                            }

                            function compare($a, $b) {
                                return ucfirst(trim($a)) > ucfirst(trim($b));
                            }
                            ?>
                            <?php
                            if ($is232):
                                $this->Html->css(array('FriendInviter.fi'), array('block' => 'css'));
                                $this->MooRequirejs->addPath(array(
                                    "mooFriendinviter" => $this->MooRequirejs->assetUrlJS("FriendInviter.js/friendinviter.js", array('plugin' => true)),
                                    "mooFriendinviterTabContent" => $this->MooRequirejs->assetUrlJS("FriendInviter.js/TabContent.js", array('plugin' => true))
                                ));

                                $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooFriendinviter'), 'object' => array('$', 'mooFriendinviter')));
                                ?>
                                mooFriendinviter.initOnGetcontacts('<?php echo $provide_id ?>',<?php echo count($user_contacts); ?>,<?php echo $max_invitation; ?>);
                                <?php $this->Html->scriptEnd(); ?>

                            <?php else: ?>
                                <input type="hidden" id="provide_id" value="<?php echo $provide_id ?>">
                                <input type="hidden" id="total_contacts" value="<?php echo count($user_contacts); ?>">
                                <input type="hidden" id="total_allow_select" value="<?php echo $max_invitation ?>">
                                <?php
                                echo $this->Html->css(array('FriendInviter.fi'), array('plugin' => true), array('inline' => false));
                                echo $this->Html->script(array('jquery.mp.min', 'mooPhrase', 'mooAjax', 'FriendInviter.prev/TabContent', 'FriendInviter.prev/friendinviter'), array('inline' => false));
                                ?>

<?php endif; ?>
                        </div>
                </div>
            </div>
        </div>
 </div>
</div>
