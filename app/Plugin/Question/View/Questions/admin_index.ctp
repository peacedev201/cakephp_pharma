<?php
echo $this->Html->script(array('admin/layout/scripts/compare.js?'.Configure::read('core.version')), array('inline' => false));
echo $this->Html->css(array('jquery-ui','Question.admin', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('question','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('question', 'Question'), '/admin/question/questions');
$this->Html->addCrumb(__d('question', 'Question Manager'), '/admin/question/questions');

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Question"));
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
<?php echo $this->Moo->renderMenu('Question', __d('question','Question Manager'));?>

<div id="center">
	<form style="padding: 14px;"  method="post" action="<?php echo $this->base.'/admin/question/questions';?>" class="form-inline">
	  <div class="form-group">
	    <label><?php echo __d('question','Title');?></label>
	    <input class="form-control input-medium input-inline" value="<?php if (isset($title)) echo $title;?>" type="text" name="title">
	  </div>
	  <div class="form-group">
	    <label><?php echo __d('question','Category');?></label>
	    <?php echo $this->Form->select('category',$categories,array('class'=>'form-control input-medium input-inline','name'=>'category_id','value'=>isset($category_id) ? $category_id : '')); ?>
	  </div>
	  <div class="form-group">
	    <label ><?php echo __d('question','Featured');?></label>
	    <?php $array_feature = array(1=>__d('question','yes'),0=>__d('question','no'))?>
		<select class="form-control input-medium input-inline" name="feature">
			<option></option>
			<?php foreach ($array_feature as $id => $feature_name):?>
				<option <?php if (isset($feature) && $feature ==  $id) echo 'selected="selected"';?> value="<?php echo $id;?>"><?php echo $feature_name;?></option>
			<?php endforeach;?>
		</select>
	  </div>
	  <div class="form-group">
	    <label ><?php echo __d('question','Visible');?></label>
	    <?php $array_feature = array(1=>__d('question','yes'),0=>__d('question','no'))?>
		<?php $array_visiable = array(1=>__d('question','yes'),0=>__d('question','no'))?>
		<select class="form-control input-medium input-inline" name="visable">
			<option></option>
			<?php foreach ($array_visiable as $id => $visiable_name):?>
				<option <?php if (isset($visable) && $visable ==  $id) echo 'selected="selected"';?> value="<?php echo $id;?>"><?php echo $visiable_name;?></option>
			<?php endforeach;?>
		</select>	
	  </div>
	  <button class="btn btn-gray" id="sample_editable_1_new" type="submit">
			<?php echo __d('question','Search');?>
      </button>
	</form>
	<table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
		<thead>
		<tr>
				<th><?php echo __d('question','Title'); ?></th>				
				<th><?php echo __d('question','Member'); ?></th>
				<th><?php echo __d('question','Created date'); ?></th>
				<th><?php echo __d('question','Category'); ?></th>
				<th><?php echo __d('question','Feature'); ?></th>		
				<th><?php echo __d('question','Visible'); ?></th>
				<th><?php echo __d('question','Approved'); ?></th>
				<th><?php echo __d('question','Action'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($questions)):?>
				<?php foreach ($questions as $question): ?>
				<tr>
					<td>
						<a href="<?php echo $question['Question']['moo_href'];?>"><?php echo $question['Question']['moo_title'];?></a>
					</td>					
					<td>
						<a href="<?php echo $question['User']['moo_href'];?>"><?php echo $question['User']['name'];?></a>
					</td>
					<td><?php echo $this->Moo->getTime($question['Question']['created']);?></td>
					<td><?php if (isset($categories[$question['Question']['category_id']])) echo $categories[$question['Question']['category_id']]?></td>
					<td>
						<?php 
							if ($question['Question']['feature'])
							{
								?>
									<span onclick="changeFeature(<?php echo $question['Question']['id'];?>,this);" class="feature" title="<?php echo __d('question','feature');?>">&nbsp</span>
								<?php 
							}
							else
							{
								?>
									<span onclick="changeFeature(<?php echo $question['Question']['id'];?>,this);" class="unfeature" title="<?php echo __d('question','unfeature');?>">&nbsp</span>
								<?php 
							}
						?>
					</td>
					<td>
						<?php 
							if ($question['Question']['visiable'])
							{
								?>
									<span onclick="changeVisiable(<?php echo $question['Question']['id'];?>,this);" class="question_yes" title="<?php echo __d('question','yes');?>">&nbsp</span>
								<?php 
							}
							else
							{
								?>
									<span onclick="changeVisiable(<?php echo $question['Question']['id'];?>,this);" class="question_no" title="<?php echo __d('question','no');?>">&nbsp</span>
								<?php 
							}
						?>						
					</td>
					<td><?php if ($question['Question']['approve']) echo __d('question','yes'); else echo __d('question','no');?></td>
					<td>	
						<?php if (!$question['Question']['approve']) :?>
							<a href="javascript:void(0)" class="tip" title="<?php echo __d('question','Approve');?>" onclick="mooConfirm('<?php echo __d('question','Are you sure you want to approve this question?');?>', '<?php echo $this->request->base;?>/admin/question/questions/approve/<?php echo $question["Question"]["id"]?>')"><i class="icon-file-text-alt icon-small"></i></a>	
						<?php endif;?>	
						<a href="javascript:void(0)" class="tip" title="<?php echo __d('question','Delete');?>" onclick="mooConfirm('<?php echo __d('question','Are you sure you want to delete this question?');?>', '<?php echo $this->request->base;?>/admin/question/questions/delete/<?php echo $question["Question"]["id"]?>')"><i class="icon-trash icon-small"></i></a>				
					</td>
				</tr>
				<?php endforeach ?>
			<?php else:?>
				<tr>
					<td colspan="8">
						<?php echo __d('question','No question found');?>
					</td>
				</tr>
			<?php endif;?>
		</tbody>
	</table>
	
	<div class="pagination">
        <?php echo $this->Paginator->first('First');?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('question','Prev')) : '';?>&nbsp;
		<?php echo $this->Paginator->numbers();?>&nbsp;
		<?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__d('question','Next')) : '';?>&nbsp;
		<?php echo $this->Paginator->last('Last');?>
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
			$(e).attr('title','<?php echo __d('question','feature');?>');
		}
		else
		{
			$(e).addClass('unfeature');
			$(e).attr('title','<?php echo __d('question','unfeature');?>');
		} 
		
		$.post("<?php echo $this->request->base?>/admin/question/questions/feature", {'id':id,'value':value}, function (data) {
            
        });
	}
	
	function changeVisiable(id,e)
	{
		var value = 0;
		if ($(e).hasClass('question_no'))
		{
			value = 1;
		}
		$(e).attr('class','');
		if (value)
		{
			$(e).addClass('question_yes');
			$(e).attr('title','<?php echo __d('question','yes');?>');
		}
		else
		{
			$(e).addClass('question_no');
			$(e).attr('title','<?php echo __d('question','no');?>');
		} 
		
		$.post("<?php echo $this->request->base?>/admin/question/questions/visiable", {'id':id,'value':value}, function (data) {
            
        });
	}
<?php echo $this->Html->scriptEnd(); ?>