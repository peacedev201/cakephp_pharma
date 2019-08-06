<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->Html->addCrumb(__d('credit','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('credit', 'List members'), array('plugin' => 'credit', 'controller' => 'credits', 'action' => 'admin_index'));

    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Credit'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Credit', __d('credit','List members'));?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).ready(function(){
    $('.footable').footable();
    });
<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-2">
                <div id="sample_1_filter" class="dataTables_filter"><label>
                        <form method="post" action="<?php echo $this->request->base?>/admin/credit/credits">
                            <?php echo $this->Form->text('keyword', array('class' => 'form-control input-medium input-inline', 'placeholder' => __d('credit','Search by name')));?>
                            <?php echo $this->Form->submit('', array( 'style' => 'display:none' ));?>
                        </form>
                    </label></div>
            </div>
        </div>
    </div>
        <table class="table table-striped table-bordered table-hover" id="sample_1">
            <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('id', __d('credit', 'ID')); ?></th>
                <th><?php echo $this->Paginator->sort('User.name', __d('credit', 'Name')); ?></th>
                <th><?php echo $this->Paginator->sort('current_credit', __d('credit', 'Credit Balance')); ?></th>
                <th><?php echo $this->Paginator->sort('earned_credit', __d('credit', 'Earned Credits')); ?></th>
                <th><?php echo $this->Paginator->sort('spent_credit', __d('credit', 'Spent Credits')); ?></th>
                <th><?php echo __d('credit', 'Options'); ?></th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($items)):?>
            <?php $count = 0;
            foreach ($items as $user): ?>
                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                    <td>
                        <?php echo $user['User']['id'];?>
                    </td>
                    <td>
                        <a class="title" href="<?php echo $this->request->base?>/<?php echo (!empty( $user['User']['username'] )) ? '-' . $user['User']['username'] : 'users/view/'.$user['User']['id']?>">
                            <?php echo h($user['User']['moo_title']); ?>
                        </a>
                    </td>
                    <td>
                        <?php echo $user['CreditBalances']['current_credit'];?>
                    </td>
                    <td>
                        <?php echo $user['CreditBalances']['earned_credit'];?>
                    </td>
                    <td>
                        <?php echo $user['CreditBalances']['spent_credit'];?>
                    </td>
                    <td>
                        <a href="<?php echo  $this->request->base ?>/admin/credit/credits/transaction/<?php echo  $user['User']['id'] ?>"><?php echo __d('credit','Transactions');?></a>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php else:?>
            <tr>
                <td colspan="6">
                    <?php echo __d('credit', 'No item found');?>
                </td>
            </tr>
            <?php endif;?>
            </tbody>
        </table>

    <div class="pagination pull-right">
        <?php echo $this->Paginator->prev('« '.__('Previous'), null, null, array('class' => 'disabled')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next(__('Next').' »', null, null, array('class' => 'disabled')); ?>
    </div>
</div>
