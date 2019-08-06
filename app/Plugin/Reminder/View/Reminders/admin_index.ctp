<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min','token-input'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable','jquery.tokeninput'), array('inline' => false));

$this->Html->addCrumb(__d('reminder','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('reminder','Logs'), array('controller' => 'reminders', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Reminder"));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Reminder', __d('reminder','Logs'));?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter">
                    <label>
                    <form method="get" action="<?php echo  $this->request->base ?>/admin/reminder/reminders">
                        <?php echo $this->Form->text('keyword', array('name'=>'keyword','value'=>isset($this->request->query['keyword']) ? $this->request->query['keyword'] : '','class' => 'form-control input-medium input-inline', 'placeholder' => __('Search by name or email'))); ?>
                        <?php echo $this->Form->submit('', array('style' => 'display:none')); ?>
                    </form>
                    </label>
                </div>
            </div>
        </div>
    </div>    
    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
        <tr>            
            <th>
				<?php echo __('ID')?>
            </th>
            <th>
				<?php echo __('Name')?>
            </th>
            <th>
                <?php echo __('Email')?>
            </th>
            <th>
                <?php echo __d('reminder','Email verification reminders');?>
            </th>
            <?php if(Configure::read('SmsVerify.sms_verify_enable')):?>
	            <th>
	                <?php echo __d('reminder','SMS verification reminders');?>
	            </th>
            <?php endif;?>
            <th>
                <?php echo __d('reminder','Login reminders');?>
            </th>
            <th>
                <?php echo __d('reminder','Interaction reminders');?>
            </th>
        </tr>
        </thead>
        <tbody>
            <?php $count = 0;
            foreach ($users as $user): ?>
                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">                    
                    <td><?php echo  $user['User']['id'] ?></td>
                    <td>
                        <a href="<?php echo  $this->request->base ?>/admin/users/edit/<?php echo  $user['User']['id'] ?>"><?php echo  h($user['User']['name']) ?></a>
                    </td>
                    <td><?php echo  $user['User']['email'] ?></td>
                    <td><?php echo $user['ReminderUser']['verify_time']?></td>
                    <?php if(Configure::read('SmsVerify.sms_verify_enable')):?>
                    	<td><?php echo $user['ReminderUser']['verify_sms_time']?></td>
                    <?php endif;?>
                    <td><?php echo $user['ReminderUser']['login_time']?></td>
                    <td><?php echo $user['ReminderUser']['share_time']?></td>
                </tr>
            <?php endforeach ?>

        </tbody>
    </table>
    <div class="pagination">
        <?php echo $this->Paginator->prev('« '.__('Previous'), null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__('Next').' »', null, null, array('class' => 'disabled')); ?>
    </div>
</div>