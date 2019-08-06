<?php
$this->Html->addCrumb(__d('credit', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('credit', 'Transactions Manager'), array('controller' => 'transactions', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Credit"));
$this->end();
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
<?php echo $this->Moo->renderMenu('Credit', __d('credit', 'Manage Transactions'));?>

<div id="center">
	<form method="post" action="<?php echo $this->base.$url;?>" >
		<div style="padding-bottom: 15px;" class="dataTables_filter">
			<input class="form-control input-small input-inline" value="<?php if (isset($name)) echo $name;?>" type="text" name="name">
			<button class="btn btn-gray" id="sample_editable_1_new" type="submit">
				<?php echo __d('credit', 'Search');?>
            </button>
		</div>
	</form>	
	<table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
		<thead>
			<tr>		
				<th><?php echo $this->Paginator->sort('User.name', __d('credit', 'User')); ?></th>		
				<th><?php echo $this->Paginator->sort('CreditOrder.transation_id', __d('credit', 'Transaction ID')); ?></th>
				<th><?php echo $this->Paginator->sort('CreditOrder.creation_date', __d('credit', 'Date')); ?></th>
				<th><?php echo $this->Paginator->sort('CreditOrder.price', __d('credit', 'Price')); ?></th>
				<th><?php echo $this->Paginator->sort('CreditOrder.credit', __d('credit', 'Credit')); ?></th>
				<th><?php echo $this->Paginator->sort('CreditOrder.status', __d('credit', 'Status')); ?></th>
				<th><?php echo $this->Paginator->sort('CreditOrder.type', __d('credit', 'Gateways')); ?></th>
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
						<?php echo $transaction['CreditOrder']['transation_id'];?>				
					</td>
					<td>
						<?php echo $this->Moo->getTime( $transaction['CreditOrder']['creation_date'], Configure::read('core.date_format'), $utz ) ?>
					</td>
					<td>
						<?php echo $currency['Currency']['symbol'].$transaction['CreditOrder']['price'];?>				
					</td>
					<td>
						<?php echo $transaction['CreditOrder']['credit'];?>
					</td>
					<td>
						<?php echo $transaction['CreditOrder']['status'];?>
					</td>
					<td>
						<?php echo ucwords($transaction['CreditOrder']['type']);?>
					</td>
				</tr>
				<?php endforeach ?>
			<?php else:?>
				<tr>
					<td colspan="7">
						<?php echo __d('credit', 'No transaction found');?>
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
