<?php
echo $this->Html->css(array('jquery-ui','pickadate', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'pickadate/picker', 'pickadate/picker.date','footable'), array('inline' => false));

$this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('spotlight','Transaction Manager'), array('controller' => 'spotlight_transactions', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Spotlight"));
$this->end();

$helper = MooCore::getInstance()->getHelper('Spotlight_Spotlight');
$list_status = $helper->getListStatus('SpotlightTransaction');
$this->Paginator->options(array('url' => $data_search));

$currency = Configure::read('Config.currency');
?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).on('loaded.bs.modal', function (e) {
        Metronic.init();
    });
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
<?php $this->Html->scriptEnd(); ?>
<?php echo $this->Moo->renderMenu('Spotlight', __d('spotlight','Manage Transactions'));?>
<div id="center">
	<form method="post" action="<?php echo $this->base.$url;?>" >
		<div style="padding-bottom: 15px;" class="dataTables_filter">
			<?php echo __d('spotlight','User');?>
			<input class="form-control input-small input-inline" value="<?php if (isset($name)) echo $name;?>" type="text" name="name">

			<?php echo __d('spotlight','Type');?>
			<select class="form-control input-small input-inline" name="type">
				<option value=""></option>
				<option value="pay" <?php if(isset($data_search['type']) && $data_search['type'] == 'pay'):?>selected<?php endif;?>><?php echo __d('spotlight', 'Paypal');?></option>
				<option value="credit" <?php if(isset($data_search['type']) && $data_search['type'] == 'credit'):?>selected<?php endif;?>><?php echo __d('spotlight', 'Credit');?></option>
			</select>
			<?php echo __d('spotlight','Date from');?>
			<input class="datepicker form-control input-small input-inline" value="<?php if (isset($start_date)) echo $start_date;?>" type="text" name="start_date">
			<?php echo __d('spotlight','Date to');?>
			<input class="datepicker form-control input-small input-inline" value="<?php if (isset($end_date)) echo $end_date;?>" type="text" name="end_date">
			
			<button class="btn btn-gray" id="sample_editable_1_new" type="submit">
				<?php echo __d('spotlight','Search');?>
            </button>
		</div>
	</form>	
	<table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
		<thead>
			<tr>		
				<th><?php echo $this->Paginator->sort('User.name', __d('spotlight','User')); ?></th>
				<th><?php echo __d('spotlight','Type'); ?></th>
				<th><?php echo $this->Paginator->sort('SpotlightTransaction.status', __d('spotlight','Status')); ?></th>
				<th><?php echo $this->Paginator->sort('SpotlightTransaction.created', __d('spotlight','Date')); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($transactions)):?>
				<?php foreach ($transactions as $transaction): ?>
				<tr>
					<td>
						<a href="<?php echo $transaction['User']['moo_href'];?>"><?php echo $transaction['User']['name'];?></a>
					</td>					
					<td>
						<p><?php
							if($transaction['SpotlightTransaction']['type'] == 'pay') {
								echo 'Paypal';
							} else {
								echo ucwords($transaction['SpotlightTransaction']['type']);
							}
							?></p>
					</td>
					<td><?php echo $helper->getTextStatusTransaction($transaction);?></td>
					<td><?php echo date('m/d/Y H:i:s', strtotime($transaction['SpotlightTransaction']['created']));?></td>
				</tr>
				<?php endforeach ?>
			<?php else:?>
				<tr>
					<td colspan="7">
						<?php echo __d('spotlight','No transaction found');?>
					</td>
				</tr>
			<?php endif;?>
		</tbody>
	</table>
	
	<div class="pagination">
        <?php echo $this->Paginator->first('First');?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__('Prev')) : '';?>&nbsp;
		<?php echo $this->Paginator->numbers();?>&nbsp;
		<?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__('Next')) : '';?>&nbsp;
		<?php echo $this->Paginator->last('Last');?>
    </div>
</div>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$('.datepicker').pickadate({
	format: 'yyyy-mm-dd'
});
<?php $this->Html->scriptEnd(); ?>