
<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__('Groups Manager'), array('controller' => 'group_plugins', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Group'));
$this->end();
?>
<?php echo  $this->Moo->renderMenu('Group', __('Criteria')); ?>

<?php
$this->Paginator->options(array('url' => $this->passedArgs));
?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function() {
        $('.footable').footable();
    });
<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" id="sample_editable_1_new" onclick="confirmSubmitForm('Are you sure you want to delete these groups', 'deleteForm')">
                        <?php echo  __('Delete');?>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter"><label>
                <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/group/group_criteria/create">
                        <?php echo __('Create New group');?>
                    </button>
                    </label></div>
            </div>
        </div>
    </div>
    <form method="post" action="<?php echo  $this->request->base ?>/admin/group/group_criteria/delete" id="deleteForm">
        <?php echo  $this->Form->hidden('category'); ?>
        <table class="table table-striped table-bordered table-hover" id="sample_1">
            <thead>
                <tr>
                    <?php if ($cuser['Role']['is_super']): ?>
                        <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
                    <?php endif; ?>
                    <th><?php echo $this->Paginator->sort('id', __('ID')); ?></th>                    
                    <th data-hide="phone"><?php echo $this->Paginator->sort('User.name', __('Name')); ?></th>
                    <th><?php echo $this->Paginator->sort('name', __('Community_ID')); ?></th>
                    <th data-hide="phone"><?php echo $this->Paginator->sort('Category.name', __('Minimum')); ?></th>
                    <th data-hide="phone"><?php echo $this->Paginator->sort('Category.name', __('Active')); ?></th>
                    <th data-hide="phone"><?php echo $this->Paginator->sort('Category.name', __('Certificate')); ?></th>
                    <th data-hide="phone"><?php echo $this->Paginator->sort('created', __('List_upload')); ?></th>

                </tr>
            </thead>
            <tbody>

                <?php $count = 0;
                foreach ($groups as $group):
                    ?>
                    <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>">
                        <?php if ($cuser['Role']['is_super']): ?>
                            <td><input type="checkbox" name="groups[]" value="<?php echo  $group['GroupsDefinition']['id'] ?>" class="check"></td>
                        <?php endif; ?>
                        <td><?php echo  $group['GroupsDefinition']['id'] ?></td>
                        <td><a href=""><?php echo  h($group['GroupsDefinition']['name']) ?></a></td>
                        <td><a href=""><?php echo  h($group['GroupsDefinition']['community']) ?></a></td>
                        <td><a href=""><?php echo  h($group['GroupsDefinition']['minimum_no']) ?></a></td>
                        <td><a href=""><?php echo  h($group['GroupsDefinition']['min_ave_points']) ?></a></td>
                        <td><a href=""><?php echo  h($group['GroupsDefinition']['certificate']) ?></a></td>
                        <td><a href=""><?php echo  h($group['GroupsDefinition']['candidate_list']) ?></a></td>
                    </tr>
                <?php endforeach ?>

            </tbody>
        </table>
    </form>
    <div class="row">

        <div class="col-md-12">
            <div class="pagination pull-right">
				<?php echo $this->Paginator->prev('« '.__('Previous'), null, null, array('class' => 'disabled')); ?>
				<?php echo $this->Paginator->numbers(); ?>
				<?php echo $this->Paginator->next(__('Next').' »', null, null, array('class' => 'disabled')); ?>
			</div>
        </div>
    </div>


    
</div>