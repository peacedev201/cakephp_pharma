<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('chat','Chat Logs'), array('controller' => 'chat_logs', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Chat'));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Chat', __d('chat','Logs')); ?>

<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">

                </div>
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter">
                    <button class="btn btn-gray" id="btn_clear_message">
                        <?php echo __d('chat', 'Clear Messages');?>                    
                    </button>
                    <?php echo $this->Form->select('clear_week', array(
                        '24' => __d('chat', 'Before 24 weeks from now'),
                        '12' => __d('chat', 'Before 12 weeks from now'),
                        '6' => __d('chat', 'Before 4 weeks from now'),
                        '3' => __d('chat', 'Before 3 weeks from now'),
                        '1' => __d('chat', 'Before 1 week from now'),
                    ), array(
                        'class' => 'form-control input-medium input-inline',
                        'empty' => array('' => __d('chat', 'Select'))
                    ));?>
                    <label>
                        <form method="post" action="<?php echo $this->request->base ?>/admin/chat/chat_logs">
                            <?php echo $this->Form->text('keyword', array('class' => 'form-control input-medium input-inline', 'value' => $keyword, 'placeholder' => __d("chat",'Search by username or email'))); ?>
                            <?php echo $this->Form->submit('', array('style' => 'display:none')); ?>
                        </form>
                    </label></div>
            </div>
        </div>
    </div>
    <form method="post" action="<?php echo $this->request->base ?>/admin/chat/chat_logs/delete" id="deleteForm">
        <table class="table table-striped table-bordered table-hover" id="sample_1">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('id', __d("chat",'ID')); ?></th>
                <th><?php echo $this->Paginator->sort('reason', __d("chat",'Room')); ?></th>
                <th data-hide="phone"><?php echo $this->Paginator->sort('created', __d("chat",'Created')); ?></th>

            </tr>
            </thead>
            <tbody>

            <?php $count = 0;

            foreach ($data as $room): ?>
                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                    <td><?php echo $room['ChatRoom']['id'] ?></td>
                    <td>
                        <?php
                        $memberIds = Hash::extract($room['ChatRoomsMember'], '{n}.user_id');
                        $name = array();
                        foreach ($memberIds as $id) {
                        if(isset($users[$id]["name"])){
                            array_push($name, $users[$id]["name"]);
                        }else{
                            array_push($name,__d('chat',"Account Deleted"));
                        }

                        }
                        echo $this->Html->link(
                            implode(",", $name),
                            array(
                                'controller' => 'ChatLogs',
                                'action' => 'admin_messages',
                                'full_base' => true,
                                $room['ChatRoom']['id']
                            )
                        );
                        ?>
                    </td>
                    <td><?php echo $this->Time->niceShort($room['ChatRoom']['created']) ?></td>
                </tr>
            <?php endforeach ?>

            </tbody>
        </table>
    </form>
    <div class="pagination pull-right">
        <?php echo $this->Paginator->prev('« ' . __('Previous'), null, null, array('class' => 'disabled')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next(__('Next') . ' »', null, null, array('class' => 'disabled')); ?>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    jQuery(document).on('click', '#btn_clear_message', function(){
        var item = jQuery(this);
        mooConfirmBox('<?php echo __d('chat', 'Are you sure you want to clear old messages?');?>', function(){
            item.html('<?php echo __d('chat', 'Clearing');?>');
            disableButton('btn_clear_message');
            jQuery.post("<?php echo $this->request->base;?>/admin/chat/ChatLogs/clear_old_messages/" + jQuery('#clear_week').val(), function(data){
                enableButton('btn_clear_message');
                item.html('<?php echo __d('chat', 'Clear Messages');?>');
                mooAlert(data);
            });
        });
    })
<?php $this->Html->scriptEnd(); ?>
