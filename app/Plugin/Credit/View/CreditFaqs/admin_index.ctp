<?php
$this->Html->addCrumb(__d('credit', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('credit', 'FAQ Manager'), array('controller' => 'credit_faqs', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Credit"));
$this->end();
$this->Paginator->options(array('url' => $data_search));
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).on('loaded.bs.modal', function (e) {
        Metronic.init();
    });
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
<?php $this->Html->scriptEnd(); ?>
<?php echo $this->Moo->renderMenu('Credit', __d('credit', 'Manage FAQ'));?>
<div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <a class="btn btn-gray"  href="<?php echo  $this->request->base ?>/admin/credit/credit_faqs/create">
                        <?php echo __d('credit','Add New FAQ');?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
<div id="center">
	<form method="post" action="<?php echo $this->base.$url;?>" >
		<div style="padding-bottom: 15px;" class="dataTables_filter">
			<input class="form-control input-inline" value="<?php if (isset($name)) echo $name;?>" type="text" name="name">
			<button class="btn btn-gray" id="sample_editable_1_new" type="submit">
				<?php echo __d('credit', 'Search');?>
            </button>
            
		</div>
	</form>	

	<table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
		<thead>
			<tr>		
				<th><?php echo $this->Paginator->sort('User.name', __d('credit', 'User')); ?></th>		
				<th><?php echo $this->Paginator->sort('CreditFaq.question', __d('credit', 'Question')); ?></th>
				<th><?php echo $this->Paginator->sort('CreditFaq.answer', __d('credit', 'Answer')); ?></th>
				<th><?php echo $this->Paginator->sort('CreditFaq.created', __d('credit', 'Date')); ?></th>
				<th width="50px" data-hide="phone"><?php echo $this->Paginator->sort('CreditFaq.active', __d('credit', 'Active')); ?></th>
				<th width="100px" data-hide="phone"><?php echo __d('credit', 'Actions');?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($faqs)):?>
				<?php foreach ($faqs as $faq): ?>
				<tr>
					<td>
						<a href="<?php echo $faq['User']['moo_href'];?>"><?php echo $faq['User']['name'];?></a>
					</td>					
					<td>
						<?php echo h($faq['CreditFaq']['question']);?>
					</td>
					<td>
						<?php echo $faq['CreditFaq']['answer'];?>
					</td>
					<td><?php echo date('m/d/Y H:i:s', strtotime($faq['CreditFaq']['created']));?></td>
					<td width="50px" class="reorder"><?php echo ($faq['CreditFaq']['active']) ? __d('credit','Yes') : __d('credit','No');?></td>
					<td>
                    	<a  href="<?php echo  $this->request->base ?>/admin/credit/credit_faqs/create/<?php echo $faq['CreditFaq']['id']?>"><i class="icon-edit icon-small"></i></a>
                    	&nbsp;&nbsp;
                    	<a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__d('credit','Are you sure you want to delete this question?'));?>', '<?php echo $this->request->base?>/admin/credit/credit_faqs/delete/<?php echo $faq['CreditFaq']['id']?>')"><i class="icon-trash icon-small"></i></a>
                	</td>
				</tr>
				<?php endforeach ?>
			<?php else:?>
				<tr>
					<td colspan="7">
						<?php echo __d('credit', 'No faq found');?>
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
