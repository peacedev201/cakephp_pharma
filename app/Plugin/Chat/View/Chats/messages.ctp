<?php

if(empty($viewerId)) return ;
if (Configure::read('Chat.chat_disable')) return;
?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('webChat'), 'object' => array( 'webChat'))); ?>
webChat.initOnMessagesPage();
<?php $this->Html->scriptEnd(); ?>



<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
<div id="rooms-list" class="bar-content">
    <h1><?php echo __d("chat","All chat history") ?></h1>
    <div id="filters" style="margin-top:5px">
        <input name="data[keyword]" placeholder="<?php echo __d("chat","Enter username to search");?>"  type="text" >                    		</div>
    <div class="box2" style="overflow-y: auto; max-height:600px;">

        <?php
        $bloodHound = array();
        //foreach ($rooms as $room){
        foreach ($order as $i){
            $room = $rooms[$i];
            $members = Hash::extract($room,"ChatRoomsMember.{n}[user_id!=$viewerId].user_id");

            $name = array();
            $imgs = array();
            $limit = 0;
            foreach ($members as $member) {
                if(isset($users[$member]["name"])){
                    array_push($name, $users[$member]["name"]);
                    if($limit < 4){
                        array_push($imgs,'<img class="moochat_userscontentavatarimage" src="'.$this->Moo->getImageUrl(array("User"=>$users[$member]), array('prefix' => '50_square')).'" prefix="50_square" alt="root"> ');
                    }
                }else{
                    array_push($name, __d('chat',"Account Deleted"));
                }
                $limit++;


            }
            array_push($bloodHound, array(
                'id'=>$room['ChatRoom']['id'],
                'name'=> implode(" ",$name )
            ));
            ?>
        <div class="rooms-item room-id-<?php echo $room['ChatRoom']['id']; ?>" style="cursor: pointer;" data-url="<?php echo $this->Html->url(
                array(
                    'plugin'=>'Chat',
                    'controller' => 'chats',
                    'action' => 'messages',
                    'full_base' => true,
                    $room['ChatRoom']['id'],
                    '#app_no_tab'
                )
            );?>" >
            <div <?php if(count($imgs) > 1): ?>class="mooGroup"<?php endif; ?>  >
                <span class="moochat_userscontentavatar <?php if(count($imgs) == 2): ?>two_member<?php elseif(count($imgs) == 3): ?>three_member<?php endif; ?>">
            <?php
            foreach($imgs as $img){
                echo $img;
            }
            ?>
                </span>
            </div>
            <div class="room-name">
                <b ><?php echo implode(",",$name );  ?></b>
            </div>
        </div>
            <?php
        }
        ?>
    </div>

</div>




<?php $this->end(); ?>

<div class="bar-content full_content p_m_10 history_app">
    <div class="content_center">
        <div class="post_body">
            <div class="mo_breadcrumb">
                <h1><?php echo __d("chat","Chat history with") ?>
                    <?php

                    $members = Hash::extract($rooms,"{n}.ChatRoomsMember.{n}[room_id=$roomId][user_id!=$viewerId].user_id");

                    $name = array();
                    foreach ($members as $member) {
                        if(isset($users[$member]["name"])){
                            array_push($name, $users[$member]["name"]);
                        }

                    }
                    echo implode(",",$name );

                    ?></h1>
                <!-- Custom display user list on app -->
                <?php //if($this->request->is('iosApp') || $this->request->is('androidApp')): ?>
                    <button type="button" class="btn openChatListApp" data-toggle="modal" data-target="#exampleModal">
                        <?php echo __d("chat","All chat history") ?>
                    </button>
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel"><?php echo __d("chat","All chat history") ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="rooms-list" class="bar-content" style="text-align:left;">
                                        <div id="filters" style="margin-top:5px">
                                            <input name="data[keyword]" placeholder="<?php echo __d("chat","Enter username to search");?>"  type="text" >                    		</div>
                                        <div class="box2" style="overflow-y: auto; max-height:600px;">

                                <?php
                                $bloodHound = array();
                                //foreach ($rooms as $room){
                                foreach ($order as $i){
                                    $room = $rooms[$i];
                                    $members = Hash::extract($room,"ChatRoomsMember.{n}[user_id!=$viewerId].user_id");

                                    $name = array();
                                    $imgs = array();
                                    $limit = 0;
                                    foreach ($members as $member) {
                                        if(isset($users[$member]["name"])){
                                            array_push($name, $users[$member]["name"]);
                                            if($limit < 4){
                                                array_push($imgs,'<img class="moochat_userscontentavatarimage" src="'.$this->Moo->getImageUrl(array("User"=>$users[$member]), array('prefix' => '50_square')).'" prefix="50_square" alt="root"> ');
                                            }
                                        }else{
                                            array_push($name, __d('chat',"Account Deleted"));
                                        }
                                        $limit++;


                                    }
                                    array_push($bloodHound, array(
                                        'id'=>$room['ChatRoom']['id'],
                                        'name'=> implode(" ",$name )
                                    ));
                                    ?>
                                                                <div class="rooms-item room-id-<?php echo $room['ChatRoom']['id']; ?>" style="cursor: pointer;" data-url="<?php echo $this->Html->url(
                                        array(
                                            'plugin'=>'Chat',
                                            'controller' => 'chats',
                                            'action' => 'messages',
                                            'full_base' => true,
                                            $room['ChatRoom']['id'],
                                            '#app_no_tab'
                                        )
                                    );?>" >
                                                                    <div <?php if(count($imgs) > 1): ?>class="mooGroup"<?php endif; ?>  >
                                                                        <span class="moochat_userscontentavatar <?php if(count($imgs) == 2): ?>two_member<?php elseif(count($imgs) == 3): ?>three_member<?php endif; ?>">
                                    <?php
                                    foreach($imgs as $img){
                                        echo $img;
                                    }
                                    ?>
                                                                        </span>
                                                                    </div>
                                                                    <div class="room-name">
                                                                        <b ><?php echo implode(",",$name );  ?></b>
                                                                    </div>
                                                                </div>
                                    <?php
                                }
                                ?>
                                                            </div>

                                                        </div>
                                                    </div>
                            </div>
                        </div>
                    </div>
                <?php //endif; ?>
                
                <?php if(!$isHideSendMesseageButton):?>
                <a href="#app_no_tab" onclick='require(["mooChat"], function (chat) {
                            chat.openChatRoom(<?php echo $roomId; ?>)
                        });' class="openChatRoom topButton button button-action"  style=""><i class="visible-xs visible-sm material-icons">chat</i><i class="hidden-xs hidden-sm"><?php echo __('Send Message');?></i></a>
                <?php endif; ?>
            </div>

            <div class="post_content">
                <ul class="list6" id="list-content">
                    <?php
                    if(!empty($data)) {
                        foreach ($data as $message) {
                            if(isset($users[$message["ChatMessage"]["sender_id"]])){
                                $user = array('User' => $users[$message["ChatMessage"]["sender_id"]]);
                                ?>
                    <ul class="chat-content-list">
                        <li class="full_content p_m_10">
                            <a href="<?php echo $this->request->base ?>/<?php echo (!empty($user['User']['username'])) ? '-' . $user['User']['username'] : 'users/view/' . $user['User']['id'] ?>">
                                <img
                                    src="<?php echo $this->Moo->getImageUrl($user, array('prefix' => '50_square')); ?>"
                                    class="chat-thumb">
                            </a>
                            <div class="chat-info">
                                <a class="title"
                                   href="<?php echo $this->request->base ?>/<?php echo (!empty($user['User']['username'])) ? '-' . $user['User']['username'] : 'users/view/' . $user['User']['id'] ?>"><?php echo $users[$message["ChatMessage"]["sender_id"]]["name"]; ?></a>


                                <div
                                    class="list-item-description"><?php echo $this->Message->export($message["ChatMessage"], $users, true); ?></div>

                            </div>

                            <div class="list_option">
                                            <?php echo $this->Time->niceShort($message["ChatMessage"]['created']) ?>
                            </div>
                        </li>


                    </ul>
                                <?php
                            }else{
                                // For deleting user
                                ?>
                    <ul class="chat-content-list">
                        <li class="full_content p_m_10">
                            <a href="#">
                                <img src="<?php echo $this->request->base ?>/user/img/noimage/Male-user.png" class="chat-thumb">
                            </a>
                            <div class="chat-info">
                                <a class="title" href="#"><?php echo __d('chat',"Account Deleted"); ?></a>


                                <div class="list-item-description"><?php echo $this->Message->export($message["ChatMessage"], $users, true); ?></div>

                            </div>

                            <div class="list_option">
                                            <?php echo $this->Time->niceShort($message["ChatMessage"]['created']) ?>
                            </div>
                        </li>


                    </ul>
                                <?php
                            }
                        }
                    }else{?>
                    <div align="center"><?php echo __('No result found')?></div>
                    <?php } ?>
                </ul>
            </div>


        </div>
    </div>
</div>

<div class="pagination pull-right chat-app-paging">
    <?php echo $this->Paginator->prev('« ' . __('Previous'), null, null, array('class' => 'disabled')); ?>
    <?php echo $this->Paginator->numbers(); ?>
    <?php echo $this->Paginator->next(__('Next') . ' »', null, null, array('class' => 'disabled')); ?>
</div>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery'), 'object' => array('$'))); ?>
    $('.chat-app-paging a').each(function () {
        var href = $(this).attr("href") + "/#app_no_tab/";
        $(this).attr("href",href);
    });
    <?php if ($this->request->is('androidApp')): ?>
	$('#exampleModal').on('shown.bs.modal', function (e) {
            console.log("Android.disableRefresh");
            Android.disableRefresh();
        });
        $('#exampleModal').on('hidden.bs.modal', function (e) {
            console.log("Android.enableRefresh");
            Android.enableRefresh();
        });
    <?php endif; ?>
    
    
    
<?php $this->Html->scriptEnd(); ?>
    
<script type="text/javascript">
    var bloodhoundRawData = <?php echo json_encode($bloodHound); ?>
</script>
