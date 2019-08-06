<?php
$array_status = array(
	'0'=> __d('question','Unapproved'),
	'1'=> __d('question','Approved'),
	'2'=> __d('question','Denied')
);

$this->Html->addCrumb(__d('question','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('question', 'Question'), '/admin/question/questions');
$this->Html->addCrumb(__d('question','Question Tags'), array('controller' => 'question_tags', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Question'));
$this->end();
$this->Paginator->options(array('url' => $passedArgs));
?>
<?php echo $this->Moo->renderMenu('Question', __d('question','Tags'));?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('hidden.bs.modal', function (e) {
    $(e.target).removeData('bs.modal');
});
<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base?>/admin/question/question_tags/create/">
                        <?php echo __d('question','Add New');?>
                    </button>                  
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
    
    <form style="padding: 14px;"  method="post" action="<?php echo $this->base.'/admin/question/question_tags';?>" class="form-inline">
	  <div class="form-group">
	    <label><?php echo __d('question','Status');?></label>
	    <select class="form-control input-medium input-inline" name="status">
			<option></option>
			<?php foreach ($array_status as $id => $text):?>
				<option <?php if (isset($status) && $status ==  $id) echo 'selected="selected"';?> value="<?php echo $id;?>"><?php echo $text;?></option>
			<?php endforeach;?>
		</select>
	  </div>
	  <button class="btn btn-gray" id="sample_editable_1_new" type="submit">
			<?php echo __d('question','Search');?>
      </button>
	</form>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr class="tbl_head">
            <th width="50px"><?php echo __d('question','ID');?></th>
            <th><?php echo __d('question','Title');?></th>
            <th width="100px"><?php echo __d('question','Status');?></th>         
            <th width="200px"><?php echo __d('question','Actions');?></th>
        </tr>
        </thead>
        <tbody>

        <?php $count = 0;
        foreach ($tags as $tag): ?>
            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                <td><?php echo $tag['QuestionTag']['id']?></td>                
                <td><a href="<?php echo $this->request->base?>/admin/question/question_tags/create/<?php echo $tag['QuestionTag']['id']?>" data-toggle="modal" data-target="#ajax" title="<?php echo $tag['QuestionTag']['title']?>"><?php echo $tag['QuestionTag']['title']?></a></td>
                <td>
                	<?php echo $tag['QuestionTag']['moo_status']?>
                </td>
                <td>
                	<?php if ($tag['QuestionTag']['status'] == 0):?>
                		<a onclick="mooConfirm('<?php echo __d('question','Are you sure you want to approve this tag?');?>', '<?php echo $this->request->base?>/admin/question/question_tags/change_status/<?php echo $tag['QuestionTag']['id']?>/1')" href="javascript:void(0)"><?php echo __d('question','approve');?></a> | <a onclick="mooConfirm('<?php echo __d('question','Are you sure you want to deny this tag?');?>', '<?php echo $this->request->base?>/admin/question/question_tags/change_status/<?php echo $tag['QuestionTag']['id']?>/2')" href="javascript:void(0)"><?php echo __d('question','deny');?></a>
                	<?php endif;?>
                	<a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('question','Are you sure you want to delete this tag?');?>', '<?php echo $this->request->base?>/admin/question/question_tags/delete/<?php echo $tag['QuestionTag']['id']?>')"><i class="icon-trash icon-small"></i></a>
                </td>
            </tr>
        <?php endforeach ?>
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
