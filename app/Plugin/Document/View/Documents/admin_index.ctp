<?php
echo $this->Html->script(array('admin/layout/scripts/compare.js?'.Configure::read('core.version')), array('inline' => false));
echo $this->Html->css(array('jquery-ui','Document.admin', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('document','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('document', 'Document'), '/admin/document/documents');
$this->Html->addCrumb(__d('document', 'Document Manager'), '/admin/document/documents');

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Document"));
$this->end();

$this->Paginator->options(array('url' => $passedArgs));
?>
<style>
<!--
.dataTables_filter span
{
	padding: 5px;
}
.dataTables_filter .form-control
{
	margin-top: 7px !important;
}
-->
</style>
<?php echo $this->Moo->renderMenu('Document', __d('document','Document Manager'));?>

<div id="center">
	<form style="padding: 14px;"  method="post" action="<?php echo $this->base.'/admin/document/documents';?>" class="form-inline">
	  <div class="form-group">
	    <label><?php echo __d('document','Title');?></label>
	    <input class="form-control input-medium input-inline" value="<?php if (isset($title)) echo $title;?>" type="text" name="title">
	  </div>
	  <div class="form-group">
	    <label><?php echo __d('document','Category');?></label>
	    <select class="form-control input-medium input-inline" name="category_id">
			<option></option>
			<?php foreach ($categories as $id => $category):?>
				<option <?php if (isset($category_id) && $category_id ==  $id) echo 'selected="selected"';?> value="<?php echo $id;?>"><?php echo $category;?></option>
			<?php endforeach;?>
		</select>
	  </div>
	  <div class="form-group">
	    <label ><?php echo __d('document','Featured');?></label>
	    <?php $array_feature = array(1=>__d('document','yes'),0=>__d('document','no'))?>
		<select class="form-control input-medium input-inline" name="feature">
			<option></option>
			<?php foreach ($array_feature as $id => $feature_name):?>
				<option <?php if (isset($feature) && $feature ==  $id) echo 'selected="selected"';?> value="<?php echo $id;?>"><?php echo $feature_name;?></option>
			<?php endforeach;?>
		</select>
	  </div>
	  <div class="form-group">
	    <label ><?php echo __d('document','Visible');?></label>
	    <?php $array_feature = array(1=>__d('document','yes'),0=>__d('document','no'))?>
		<?php $array_visiable = array(1=>__d('document','yes'),0=>__d('document','no'))?>
		<select class="form-control input-medium input-inline" name="visable">
			<option></option>
			<?php foreach ($array_visiable as $id => $visiable_name):?>
				<option <?php if (isset($visable) && $visable ==  $id) echo 'selected="selected"';?> value="<?php echo $id;?>"><?php echo $visiable_name;?></option>
			<?php endforeach;?>
		</select>	
	  </div>
	  <button class="btn btn-gray" id="sample_editable_1_new" type="submit">
			<?php echo __d('document','Search');?>
      </button>
	</form>
	<table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
		<thead>
		<tr>
				<th><?php echo __d('document','Title'); ?></th>				
				<th><?php echo __d('document','Member'); ?></th>
				<th><?php echo __d('document','Created date'); ?></th>
				<th><?php echo __d('document','Category'); ?></th>
				<th><?php echo __d('document','Feature'); ?></th>		
				<th><?php echo __d('document','Visible'); ?></th>
				<th><?php echo __d('document','Approved'); ?></th>
				<th><?php echo __d('document','Action'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($documents)):?>
				<?php foreach ($documents as $document): ?>
				<tr>
					<td>
						<a href="<?php echo $document['Document']['moo_href'];?>"><?php echo $document['Document']['moo_title'];?></a>
					</td>					
					<td>
						<a href="<?php echo $document['User']['moo_href'];?>"><?php echo $document['User']['name'];?></a>
					</td>
					<td><?php echo $this->Moo->getTime($document['Document']['created']);?></td>
					<td><?php if ($document['Category'] && isset($categories[$document['Document']['category_id']])) echo $categories[$document['Document']['category_id']]?></td>
					<td>
						<?php 
							if ($document['Document']['feature'])
							{
								?>
									<span onclick="changeFeature(<?php echo $document['Document']['id'];?>,this);" class="feature" title="<?php echo __d('document','feature');?>">&nbsp</span>
								<?php 
							}
							else
							{
								?>
									<span onclick="changeFeature(<?php echo $document['Document']['id'];?>,this);" class="unfeature" title="<?php echo __d('document','unfeature');?>">&nbsp</span>
								<?php 
							}
						?>
					</td>
					<td>
						<?php 
							if ($document['Document']['visiable'])
							{
								?>
									<span onclick="changeVisiable(<?php echo $document['Document']['id'];?>,this);" class="document_yes" title="<?php echo __d('document','yes');?>">&nbsp</span>
								<?php 
							}
							else
							{
								?>
									<span onclick="changeVisiable(<?php echo $document['Document']['id'];?>,this);" class="document_no" title="<?php echo __d('document','no');?>">&nbsp</span>
								<?php 
							}
						?>						
					</td>
					<td><?php if ($document['Document']['approve']) echo __d('document','yes'); else echo __d('document','no');?></td>
					<td>	
						<?php if (!$document['Document']['approve']) :?>
							<a href="javascript:void(0)" class="tip" title="<?php echo __d('document','Approve');?>" onclick="mooConfirm('<?php echo __d('document','Are you sure you want to approve this document?');?>', '<?php echo $this->request->base;?>/admin/document/documents/approve/<?php echo $document["Document"]["id"]?>')"><i class="icon-file-text-alt icon-small"></i></a>	
						<?php endif;?>	
						<a href="javascript:void(0)" class="tip" title="<?php echo __d('document','Delete');?>" onclick="mooConfirm('<?php echo __d('document','Are you sure you want to delete this document?');?>', '<?php echo $this->request->base;?>/admin/document/documents/delete/<?php echo $document["Document"]["id"]?>')"><i class="icon-trash icon-small"></i></a>				
					</td>
				</tr>
				<?php endforeach ?>
			<?php else:?>
				<tr>
					<td colspan="8">
						<?php echo __d('document','No document found');?>
					</td>
				</tr>
			<?php endif;?>
		</tbody>
	</table>
	
	<div class="pagination">
        <?php echo $this->Paginator->first(__d('document','First'));?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('document','Prev')) : '';?>&nbsp;
		<?php echo $this->Paginator->numbers();?>&nbsp;
		<?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__d('document','Next')) : '';?>&nbsp;
		<?php echo $this->Paginator->last(__d('document','Last'));?>
    </div>
</div>
<?php echo $this->Html->scriptStart(array('inline' => false)); ?>
	function changeFeature(id,e)
	{
		var value = 0;
		if ($(e).hasClass('unfeature'))
		{
			value = 1;
		}
		$(e).attr('class','');
		if (value)
		{
			$(e).addClass('feature');
			$(e).attr('title','<?php echo __d('document','feature');?>');
		}
		else
		{
			$(e).addClass('unfeature');
			$(e).attr('title','<?php echo __d('document','unfeature');?>');
		} 
		
		$.post("<?php echo $this->request->base?>/admin/document/documents/feature", {'id':id,'value':value}, function (data) {
            
        });
	}
	
	function changeVisiable(id,e)
	{
		var value = 0;
		if ($(e).hasClass('document_no'))
		{
			value = 1;
		}
		$(e).attr('class','');
		if (value)
		{
			$(e).addClass('document_yes');
			$(e).attr('title','<?php echo __d('document','yes');?>');
		}
		else
		{
			$(e).addClass('document_no');
			$(e).attr('title','<?php echo __d('document','no');?>');
		} 
		
		$.post("<?php echo $this->request->base?>/admin/document/documents/visiable", {'id':id,'value':value}, function (data) {
            
        });
	}
<?php echo $this->Html->scriptEnd(); ?>