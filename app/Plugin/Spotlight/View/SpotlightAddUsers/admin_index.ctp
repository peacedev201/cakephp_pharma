<?php
    $this->Html->addCrumb(__('Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('spotlight','Spotlight Add Users'), array('controller' => 'spotlight_add_users', 'action' => 'admin_index'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Spotlight'));
    $this->end();
    $this->Paginator->options(array('url' => $this->passedArgs));
?>

<?php echo  $this->Moo->renderMenu('Spotlight', __d('spotlight','Spotlight Add Users')); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter">
                    <label>
                    <form method="post" action="<?php echo  $this->request->base ?>/admin/spotlight/spotlight_add_users">
                        <?php echo $this->Form->text('keyword', array('class' => 'form-control input-medium input-inline', 'placeholder' => __d('spotlight','Search by name or email'))); ?>
                        <?php echo $this->Form->submit('', array('style' => 'display:none')); ?>
                    </form>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
        <tr>
            <th>
                <?php echo $this->Paginator->sort('id', __d('spotlight','ID')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('name', __d('spotlight','Name')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('email', __d('spotlight','Email')); ?>
            </th>
            <th>
                <?php echo $this->Paginator->sort('Role.name', __d('spotlight','Role')); ?>
            </th>
            <th class="text-center" width="80">
                <?php echo $this->Paginator->sort('active', __d('spotlight','Active')); ?>
            </th>
            <th class="text-center" width="100">
                <?php echo __d('spotlight','Action')?>
            </th>
        </tr>
        </thead>
        <tbody>
            <?php $count = 0;
            foreach ($users as $user): ?>
                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                    <td><?php echo  $user['User']['id'] ?></td>
                    <td>
                        <a href="<?php echo  $this->request->base ?>/admin/users/edit/<?php echo  $user['User']['id'] ?>"><?php echo  h($user['User']['name']) ?></a>
                    </td>
                    <td><?php echo  $user['User']['email'] ?></td>
                    <td><?php echo  $user['Role']['name'] ?></td>
                    <td class="reorder text-center"><?php if ($user['User']['active']) echo __d('spotlight','Yes'); else echo __d('spotlight','No'); ?></td>
                    <td class="reorder text-center"><a class="btn btn-circle btn-action" href="<?php echo $this->request->base?>/admin/spotlight/spotlight_add_users/add/<?php echo $user['User']['id']?>" onclick="return confirm('<?php echo __d('spotlight','You are sure you want to add user %s to Spotlight?', $user['User']['name'])?>')"><?php echo __d('spotlight','Add') ?></a></td>
                </tr>
            <?php endforeach ?>

        </tbody>
    </table>
    <div class="pagination">
        <?php echo $this->Paginator->prev('« '.__('Previous'), null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__('Next').' »', null, null, array('class' => 'disabled')); ?>
    </div>
</div>
