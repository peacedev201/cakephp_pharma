<?php
echo $this->Html->css(array('jquery-ui','Question.jquery.miniColors','Question.admin'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui','Question.jquery.miniColors.min'), array('inline' => false));

$this->Html->addCrumb(__d('question','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('question', 'Question'), '/admin/question/questions');
$this->Html->addCrumb(__d('question','Question Badges'), array('controller' => 'question_badges', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Question'));
$this->end();

$helper = MooCore::getInstance()->getHelper("Question_Question");
$permissions = $helper->_permissions;
?>
<?php echo $this->Moo->renderMenu('Question', __d('question','Badges'));?>

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
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base?>/admin/question/question_badges/create/">
                        <?php echo __d('question','Add New');?>
                    </button>                  
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr class="tbl_head">
            <th width="50px"><?php echo __d('question','ID');?></th>
            <th><?php echo __d('question','Name');?></th>
            <th><?php echo __d('question','Color');?></th>
            <th><?php echo __d('question','Point');?></th>
            <th><?php echo __d('question','Permissions');?></th>
            <th width="50px"><?php echo __d('question','Actions');?></th>
        </tr>
        </thead>
        <tbody>

        <?php $count = 0;
        foreach ($badges as $badge): ?>
            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                <td><?php echo $badge['QuestionBadge']['id']?></td>
                <td><a href="<?php echo $this->request->base?>/admin/question/question_badges/create/<?php echo $badge['QuestionBadge']['id']?>" data-toggle="modal" data-target="#ajax" title="<?php echo $badge['QuestionBadge']['name']?>"><?php echo $badge['QuestionBadge']['name']?></a></td>
                <td>
                	<span style="color:<?php echo $badge['QuestionBadge']['text_color']?>;padding: 5px;background-color: <?php echo $badge['QuestionBadge']['background_color']?>"><?php echo $badge['QuestionBadge']['name']?></a></span>
                </td>                
                <td><?php echo $badge['QuestionBadge']['point']?></td>
                <td>
                	<?php 
                		$array = array();
                		if (trim($badge['QuestionBadge']['permission']))
                			$array = explode(',',$badge['QuestionBadge']['permission']);
                	?>
                	<?php foreach ($array as $key):?>
                		<p><?php echo $permissions[$key];?></p>
                	<?php endforeach;?>
                </td>                                                                      
                <td><a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('question','Are you sure to delete this badge');?>', '<?php echo $this->request->base?>/admin/question/question_badges/delete/<?php echo $badge['QuestionBadge']['id']?>')"><i class="icon-trash icon-small"></i></a></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
