<?php
__d('sms_verify','Enable Sms Verify');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('sms_verify','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('sms_verify', 'Sms Verify'), '/admin/sms_verify/sms_verifys');
$this->Html->addCrumb(__d('sms_verify','Sms Verify Gateways'), array('controller' => 'sms_verify_gateways', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Sms Verify"));
$this->end();
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('hidden.bs.modal', function (e) {
    $(e.target).removeData('bs.modal');
});

<?php $this->Html->scriptEnd(); ?>
<div id="center">		
	<?php echo $this->Moo->renderMenu('SmsVerify', __d('sms_verify','Gateways'));?>
	<table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th width="70%"><?php echo __d('sms_verify','Name'); ?></th>				
				<th><?php echo __d('sms_verify','Action'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($gateways)):?>
				<?php foreach ($gateways as $gateway): ?>
				<tr>
					<td>
						<?php echo $gateway['SmsVerifyGateway']['name'];?>
					</td>									
					<td>
						<a data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base;?>/admin/sms_verify/sms_verify_gateways/edit/<?php echo $gateway['SmsVerifyGateway']["id"];?>"><?php echo __d('sms_verify','Edit');?></a> |
						<?php if ($gateway['SmsVerifyGateway']['enable']):?>
							<span><?php echo __d('sms_verify','Activated');?></span>
						<?php else:?>
							<a href="javascript:void(0)" class="tip" title="<?php echo __d('sms_verify','Activate as Default');?>" onclick="mooConfirm('<?php echo __d('sms_verify','Are you sure you want to active this gateway?');?>', '<?php echo $this->request->base;?>/admin/sms_verify/sms_verify_gateways/active/<?php echo $gateway['SmsVerifyGateway']["id"]?>')"><?php echo __d('sms_verify','Activate as Default');?></a>
						<?php endif;?>				
					</td>
				</tr>
				<?php endforeach ?>
			<?php else:?>
				<tr>
					<td colspan="8">
						<?php echo __d('sms_verify','No gateway found');?>
					</td>
				</tr>
			<?php endif;?>
		</tbody>
	</table>
</div>