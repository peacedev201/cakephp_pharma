<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min', 'Chat.admin'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Chat'));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Chat', 'Logs'); ?>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-share font-red-sunglo"></i>
            <span class="caption-subject font-green-haze-sunglo bold uppercase">
                <?php
                $name = array();
                foreach ($memberIds as $memberId) {
                if(isset($users[$memberId]["name"])){
                    array_push($name, $users[$memberId]["name"]);
                }else{
                    array_push($name, __d('chat',"Account Deleted"));
                }

                }

                echo implode(",",$name );

                ?>
            </span>
        </div>
        <div class="actions">

                <a href="<?php echo $this->Html->url(
                array(
                'controller' => 'ChatLogs',
                'action' => 'admin_index',
                'full_base' => true,
                )
                ); ?>"><button class="btn btn-gray" id="sample_editable_1_new" ><?php echo __d("chat",'Back'); ?></button></a>

        </div>
    </div>
    <div class="portlet-body">

        <div class="clearfix">
            <ul class="media-list">
                <?php
                foreach ($data as $message){
                    ?>
                    <li class="media">
                        <?php if(isset($users[$message["ChatMessage"]["sender_id"]])):?>
                        <a class="pull-left" href="<?php echo  $this->request->base ?>/admin/users/edit/<?php echo  $users[$message["ChatMessage"]["sender_id"]]['id'] ?>">
                            <img class="media-object" src="<?php echo $this->Moo->getImageUrl(array('User'=>$users[$message["ChatMessage"]["sender_id"]]), array('prefix' => '50_square')); ?>">
                        </a>
                        <?php else: ?>
                            <a class="pull-left" href="#">
                                <img class="media-object" src="<?php echo $this->Moo->getImageUrl(array('User'=>null), array('prefix' => '50_square')); ?>">
                            </a>
                        <?php endif; ?>
                        <div class="media-body">
                            <h6 class="media-heading">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?php if(isset($users[$message["ChatMessage"]["sender_id"]])):?>
                                            <a href="<?php echo  $this->request->base ?>/admin/users/edit/<?php echo  $users[$message["ChatMessage"]["sender_id"]]['id'] ?>"><?php echo  h($users[$message["ChatMessage"]["sender_id"]]["name"]) ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="#"><?php echo __d('chat',"Account Deleted"); ?>
                                            </a>
                                        <?php endif; ?>
                                        </div>
                                    <div class="col-md-3"><?php echo $this->Time->niceShort($message["ChatMessage"]['created'])?></div>
                                </div>

                            </h6>
                            <p><?php   echo $this->Message->export($message["ChatMessage"],$users,true); ?></p>

                        </div>
                    </li>


                    <?php
                }
                ?>


            </ul>
        </div>

    </div>

</div>



    <div class="pagination pull-right">
        <?php echo $this->Paginator->prev('« ' . __('Previous'), null, null, array('class' => 'disabled')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next(__('Next') . ' »', null, null, array('class' => 'disabled')); ?>
    </div>

