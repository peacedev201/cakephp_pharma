
<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__('Groups Manager'), array('controller' => 'group_plugins', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Group'));
$this->end();
?>
<?php echo  $this->Moo->renderMenu('Group', __('Manage')); ?>

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
                        <form method="post" action="<?php echo  $this->request->base ?>/admin/group/group_plugins">
                            <?php echo $this->Form->text('keyword', array('class' => 'form-control input-medium input-inline', 'placeholder' => __('Search by title')) ); ?>
                            <?php echo $this->Form->submit('', array('style' => 'display:none')); ?>
                        </form>
                    </label></div>
            </div>
        </div>
    </div>
    <form method="post" action="<?php echo  $this->request->base ?>/admin/group/group_plugins/delete" id="deleteForm">
        <?php echo  $this->Form->hidden('category'); ?>
        <table class="table table-striped table-bordered table-hover" id="sample_1">
            <thead>
                <tr>
                    <?php if ($cuser['Role']['is_super']): ?>

                        <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
                    <?php endif; ?>
                    <th><?php echo $this->Paginator->sort('id', __('ID')); ?></th>
                    <th><?php echo $this->Paginator->sort('name', __('Title')); ?></th>
                    <th data-hide="phone"><?php echo $this->Paginator->sort('User.name', __('Author')); ?></th>
                    <th><?php echo $this->Paginator->sort('type', __('Level')); ?></th>
                    <th><?php echo $this->Paginator->sort('name', __('Upgrade')); ?></th>                    
                    <th data-hide="phone"><?php echo $this->Paginator->sort('created', __('Date')); ?></th>
                    <th><?php echo $this->Paginator->sort('group_user_count', __('Members')); ?></th>
                    <th><?php echo $this->Paginator->sort('name', __('AVER_POINT')); ?></th>
                    <th><?php echo $this->Paginator->sort('name', __('Certificate')); ?></th>
                    <th><?php echo $this->Paginator->sort('name', __('Candidate_list')); ?></th>
                    <th><?php echo $this->Paginator->sort('name', __('Status')); ?></th>
                </tr>
            </thead>
            <tbody>

                <?php $count = 0;
                foreach ($groups as $group):
                    $community = __('Group');
                    $upgrade = __('Group');
                    switch ($group['Group']['community']) {
                        case 1:
                            $community = __('Community');
                            break;
                        case 2:
                            $community = __('Alumni Association');
                            break;
                        case 3:
                            $community = __('Local KPA');
                            break;
                    }
                    switch ($group['Group']['group_status']) {
                        case 2:
                            $upgrade = __('Community');
                            break;
                        case 3:
                            $upgrade = __('Alumni Association');
                            break;
                        case 4:
                            $upgrade = __('Local KPA');
                            break;
                    }
                        
                    ?>
                    <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>">
                        <?php if ($cuser['Role']['is_super']): ?>
                            <td><input type="checkbox" name="groups[]" value="<?php echo  $group['Group']['id'] ?>" class="check"></td>
                        <?php endif; ?>
                        <td><?php echo  $group['Group']['id'] ?></td>
                        <td><a href="" target="_blank"><?php echo  h($group['Group']['name']) ?></a></td>
                        <td><a href=""><?php echo  h($group['User']['name']) ?></a></td>
                        <td><?php echo  $community ?></td>
                        <td><?php echo  $upgrade ?></td>
                        <td><?php echo  $this->Time->niceShort($group['Group']['created']) ?></td>
                        <td><?php echo  $group['Group']['group_user_count'] ?></td>
                        <td><?php echo  $group['Group']['credit_3month_mem_aver'] ?></td>
                        <td><?php echo  $group['Group']['certificate'] ?></td>
                        <td><?php echo  $group['Group']['candidate_list'] ?></td>
                        <?php
                        switch ($group['Group']['group_status']) {
                            case 0:
                                echo'<td>';
                                    echo'<select class="select_status" data-group-id="'.$group['Group']['id'].'" data-url="'.  $this->request->base.'" data-status="'.$group['Group']['group_status'].'">' ;
                                        echo'<option value=0>default</option>';
                                        echo'<option value=1>pending</option>';
                                        echo'<option value=2>approve</option>';
                                        echo'<option value=3>rejected</option>';
                                    echo '</select>';
                                echo '</td>';
                                break;
                            default :
                                echo'<td>';
                                    echo'<select class="select_status" data-group-id="'.$group['Group']['id'].'" data-url="'.  $this->request->base.'" data-status="'.$group['Group']['group_status'].'">';
                                        echo'<option value=0>default</option>';
                                        echo'<option value=1 selected>pending</option>';
                                        echo'<option value=2>approve</option>';
                                        echo'<option value=3>rejected</option>';
                                    echo '</select>';
                                echo '</td>';
                                
                        }
                        ?>
                        
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
