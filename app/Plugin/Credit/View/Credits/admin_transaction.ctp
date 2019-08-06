<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__d('credit','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('credit', 'List members'), array('plugin' => 'credit', 'controller' => 'credits', 'action' => 'admin_index'));
$this->Html->addCrumb(__d('credit', 'Transaction'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Credit'));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Credit', 'List members');?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){
$('.footable').footable();
});
<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <h3>
        <?php echo __d('credit', 'Transactions of'); ?>
        <a class="title" href="<?php echo $this->request->base?>/<?php echo (!empty( $user['User']['username'] )) ? '-' . $user['User']['username'] : 'users/view/'.$user['User']['id']?>">
            <?php echo h($user['User']['moo_title']); ?>
        </a>
    </h3>
    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
        <tr>
            <th><?php echo __d('credit', 'Action Date'); ?></th>
            <th><?php echo __d('credit', 'Action Type'); ?></th>
            <th><?php echo __d('credit', 'Credits'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($items)):?>
        <?php $count = 0;
        foreach ($items as $item): ?>
            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                <td>
                    <?php echo  $this->Moo->getTime($item['CreditLogs']['creation_date'], Configure::read('core.date_format'), $utz) ?>
                </td>
                <td>
                    <?php
                    $item_object = MooCore::getInstance()->getItemByType($item['CreditLogs']['object_type'],$item['CreditLogs']['object_id']);
                    $options = array();
                    if ($item['CreditActiontypes']['plugin'])
                    {
                        $options = array('plugin' => $item['CreditActiontypes']['plugin']);
                    }

                    if($item['CreditLogs']['is_delete'])
                        echo $this->element('log_text/delete_' . $item['CreditActiontypes']['action_type'], array('item' => $item, 'item_object'=>$item_object),$options);
                    else
                        echo $this->element('log_text/' . $item['CreditActiontypes']['action_type'], array('item' => $item,'item_object'=>$item_object),$options);
                    ?>
                </td>
                <td><?php echo $item['CreditLogs']['credit']; ?></td>
            </tr>
        <?php endforeach ?>
        <?php else:?>
        <tr>
            <td colspan="3">
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
    <!--<div class="col-md-12">
        <a href="javascript:void(0)" onclick="window.history.go(-1); return false;" class="btn btn-circle btn-action"><?php echo __d('credit','Go back')?></a>
    </div>-->
</div>
