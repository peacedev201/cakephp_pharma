<?php
echo $this->Html->script(array('admin/layout/scripts/compare.js?'.Configure::read('core.version')), array('inline' => false));
echo $this->Html->css(array('jquery-ui','Poll.admin', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('poll','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('poll', 'Poll'), '/admin/poll/polls');
$this->Html->addCrumb(__d('poll', 'Poll Manager'), '/admin/poll/polls');

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Poll"));
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
<?php echo $this->Moo->renderMenu('Poll', __d('poll','Poll Manager'));?>

<div id="center">
	<form style="padding: 14px;"  method="post" action="<?php echo $this->base.'/admin/poll/polls';?>" class="form-inline">
	  <div class="form-group">
	    <label><?php echo __d('poll','Title');?></label>
	    <input class="form-control input-medium input-inline" value="<?php if (isset($title)) echo $title;?>" type="text" name="title">
	  </div>
	  <div class="form-group">
	    <label><?php echo __d('poll','Category');?></label>
	    <select class="form-control input-medium input-inline" name="category_id">
			<option></option>
			<?php foreach ($categories as $id => $category):?>
				<option <?php if (isset($category_id) && $category_id ==  $id) echo 'selected="selected"';?> value="<?php echo $id;?>"><?php echo $category;?></option>
			<?php endforeach;?>
		</select>
	  </div>
	  <div class="form-group">
	    <label ><?php echo __d('poll','Featured');?></label>
	    <?php $array_feature = array(1=>__d('poll','yes'),0=>__d('poll','no'))?>
		<select class="form-control input-medium input-inline" name="feature">
			<option></option>
			<?php foreach ($array_feature as $id => $feature_name):?>
				<option <?php if (isset($feature) && $feature ==  $id) echo 'selected="selected"';?> value="<?php echo $id;?>"><?php echo $feature_name;?></option>
			<?php endforeach;?>
		</select>
	  </div>
	  <div class="form-group">
	    <label ><?php echo __d('poll','Visible');?></label>
	    <?php $array_feature = array(1=>__d('poll','yes'),0=>__d('poll','no'))?>
		<?php $array_visiable = array(1=>__d('poll','yes'),0=>__d('poll','no'))?>
		<select class="form-control input-medium input-inline" name="visable">
			<option></option>
			<?php foreach ($array_visiable as $id => $visiable_name):?>
				<option <?php if (isset($visable) && $visable ==  $id) echo 'selected="selected"';?> value="<?php echo $id;?>"><?php echo $visiable_name;?></option>
			<?php endforeach;?>
		</select>	
	  </div>
	  <button class="btn btn-gray" id="sample_editable_1_new" type="submit">
			<?php echo __d('poll','Search');?>
      </button>
	</form>
	<table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
		<thead>
		<tr>
				<th><?php echo __d('poll','Title'); ?></th>				
				<th><?php echo __d('poll','Member'); ?></th>
				<th><?php echo __d('poll','Created date'); ?></th>
				<th><?php echo __d('poll','Category'); ?></th>
				<th><?php echo __d('poll','Feature'); ?></th>
				<th><?php echo __d('poll','Visible'); ?></th>
				<th><?php echo __d('poll','Action'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($polls)):?>
				<?php foreach ($polls as $poll): ?>
				<tr>
					<td>
						<a href="<?php echo $poll['Poll']['moo_href'];?>"><?php echo $poll['Poll']['moo_title'];?></a>
					</td>					
					<td>
						<a href="<?php echo $poll['User']['moo_href'];?>"><?php echo $poll['User']['name'];?></a>
					</td>
					<td><?php echo $this->Moo->getTime($poll['Poll']['created']);?></td>
					<td><?php if ($poll['Category']) echo $poll['Category']['name']?></td>
					<td>
						<?php 
							if ($poll['Poll']['feature'])
							{
								?>
									<span onclick="changeFeature(<?php echo $poll['Poll']['id'];?>,this);" class="feature" title="<?php echo __d('poll','feature');?>">&nbsp</span>
								<?php 
							}
							else
							{
								?>
									<span onclick="changeFeature(<?php echo $poll['Poll']['id'];?>,this);" class="unfeature" title="<?php echo __d('poll','unfeature');?>">&nbsp</span>
								<?php 
							}
						?>
					</td>
					<td>
						<?php 
							if ($poll['Poll']['visiable'])
							{
								?>
									<span onclick="changeVisiable(<?php echo $poll['Poll']['id'];?>,this);" class="poll_yes" title="<?php echo __d('poll','yes');?>">&nbsp</span>
								<?php 
							}
							else
							{
								?>
									<span onclick="changeVisiable(<?php echo $poll['Poll']['id'];?>,this);" class="poll_no" title="<?php echo __d('poll','no');?>">&nbsp</span>
								<?php 
							}
						?>						
					</td>
					<td>	
						<a href="javascript:void(0)" class="tip" title="<?php echo __d('poll','Delete');?>" onclick="mooConfirm('<?php echo __d('poll','Are you sure you want to delete this poll?');?>', '<?php echo $this->request->base;?>/admin/poll/polls/delete/<?php echo $poll["Poll"]["id"]?>')"><i class="icon-trash icon-small"></i></a>				
					</td>
				</tr>
				<?php endforeach ?>
			<?php else:?>
				<tr>
					<td colspan="9">
						<?php echo __d('poll','No poll found');?>
					</td>
				</tr>
			<?php endif;?>
		</tbody>
	</table>
	
	<div class="pagination">
        <?php echo $this->Paginator->first(__d('poll','First'));?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('poll','Prev')) : '';?>&nbsp;
		<?php echo $this->Paginator->numbers();?>&nbsp;
		<?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__d('poll','Next')) : '';?>&nbsp;
		<?php echo $this->Paginator->last(__d('poll','Last'));?>
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
			$(e).attr('title','<?php echo __d('poll','feature');?>');
		}
		else
		{
			$(e).addClass('unfeature');
			$(e).attr('title','<?php echo __d('poll','unfeature');?>');
		} 
		
		$.post("<?php echo $this->request->base?>/admin/poll/polls/feature", {'id':id,'value':value}, function (data) {
            
        });
	}
	function changeVisiable(id,e)
	{
		var value = 0;
		if ($(e).hasClass('poll_no'))
		{
			value = 1;
		}
		$(e).attr('class','');
		if (value)
		{
			$(e).addClass('poll_yes');
			$(e).attr('title','<?php echo __d('poll','yes');?>');
		}
		else
		{
			$(e).addClass('poll_no');
			$(e).attr('title','<?php echo __d('poll','no');?>');
		} 
		
		$.post("<?php echo $this->request->base?>/admin/poll/polls/visiable", {'id':id,'value':value}, function (data) {
            
        });
	}
<?php echo $this->Html->scriptEnd(); ?>