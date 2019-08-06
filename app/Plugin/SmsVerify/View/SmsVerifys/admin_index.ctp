<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__d('sms_verify','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('sms_verify', 'Sms Verify'), '/admin/sms_verify/sms_verifys');
    $this->Html->addCrumb(__d('sms_verify', 'Users Manager'), '/admin/sms_verify/sms_verifys');
    
    $this->startIfEmpty('sidebar-menu');
    
    echo $this->element('admin/adminnav', array('cmenu' => 'Sms Verify'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('SmsVerify', __d('sms_verify','Users Manager'));?>

<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter">
                    <label>
                    <form method="post" action="<?php echo  $this->request->base ?>/admin/sms_verify/sms_verifys">
                        <?php echo $this->Form->text('keyword', array('value'=>isset($this->request->named['keyword']) ? $this->request->named['keyword'] : '','class' => 'form-control input-medium input-inline', 'placeholder' => __('Search by name or email'))); ?>
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
                <?php echo $this->Paginator->sort('id', __('ID')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('name', __('Name')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('email', __('Email')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('email', __d('sms_verify','Phone Number')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('sms_verify', __d('sms_verify','Sms verified')); ?>
            </th>            
            <th>
                <?php echo __d('sms_verify','Action'); ?>
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
                    <td><?php echo  $user['User']['mobile'] ?></td>
                    <?php
                    	$checked = false;
                    	
                    	if ($user['Role']['is_admin'])
                    	{
                    		$checked = true;
                    	}
                    	if (Configure::read("SmsVerify.sms_verify_pass_verify"))
                    	{
                    		if ($user['User']['sms_verify'])
                    		{
                    			$checked = true;
                    		}
                    	}
                    	else
                    	{
                    		if ($user['User']['sms_verify_checked'])
                    		{
                    			$checked = true;
                    		}
                    	}
                    ?>
                    <td><?php if ($checked) echo __('Yes'); else echo __('No'); ?></td>
                    <td>
                    	<?php if (!$user['Role']['is_admin']):?>
                    	<?php if ($checked): ?>
                    		<a onclick="mooConfirm('<?php echo __d('sms_verify','Are you sure you want to unverify this user?');?>', '<?php echo $this->request->base;?>/admin/sms_verify/sms_verifys/unverify/<?php echo $user['User']['id']?>')" href="javascript:void(0);"><?php echo __d('sms_verify','UnVerify');?></a>
                    	<?php else:?>
                    		<a onclick="mooConfirm('<?php echo __d('sms_verify','Are you sure you want to verify this user?');?>', '<?php echo $this->request->base;?>/admin/sms_verify/sms_verifys/verify/<?php echo $user['User']['id']?>')" href="javascript:void(0);"><?php echo __d('sms_verify','Verify');?></a>
                    	<?php endif;?>
                    	<?php endif;?>
                    </td>
                </tr>
            <?php endforeach ?>

        </tbody>
    </table>
    <div class="pagination">
        <?php echo $this->Paginator->prev('� '.__('Previous'), null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__('Next').' �', null, null, array('class' => 'disabled')); ?>
    </div>
</div>