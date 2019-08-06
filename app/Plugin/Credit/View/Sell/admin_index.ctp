<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__d('credit','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('credit', 'Credit Packages'), array('plugin' => 'credit', 'controller' => 'sell', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Credit'));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Credit', __d('credit','Credit Packages'));?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('loaded.bs.modal', function (e) {
Metronic.init();
});
$(document).on('hidden.bs.modal', function (e) {
$(e.target).removeData('bs.modal');
});
<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/credit/sell/create">
                        <?php echo __d('credit','Add new package');?>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
        <tr class="tbl_head">
            <th width="50px"><?php echo __d('credit','Id');?></th>
            <th><?php echo __d('credit','Caption');?></th>
            <th width="50px" data-hide="phone"><?php echo __d('credit','Actions');?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($sells)):?>
        <?php $count = 0;
        foreach ($sells as $sell): ?>
            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $sell['CreditSells']['id']?>">
                <td width="50px"><?php echo $sell['CreditSells']['id']?></td>
                <td class="reorder">
                    <?php
                        $currency = Configure::read('Config.currency');
                    ?>
                    <?php echo $sell['CreditSells']['credit'].' '.__d('credit','Credits for').' '.$currency['Currency']['symbol'].round($sell['CreditSells']['price'],2);?>
                </td>
                <td width="200px">
                    <a data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/credit/sell/create/<?php echo $sell['CreditSells']['id']?>"><i class="icon-edit icon-small"></i></a>
                    &nbsp;&nbsp;
                    <a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__d('credit','Are you sure you want to delete this package?'));?>', '<?php echo $this->request->base?>/admin/credit/sell/delete/<?php echo $sell['CreditSells']['id']?>')"><i class="icon-trash icon-small"></i></a>
                </td>
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
    <div class="pagination">
        <?php echo $this->Paginator->first(__d('credit','First'));?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('credit','Prev')) : '';?>&nbsp;
        <?php echo $this->Paginator->numbers();?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__d('credit','Next')) : '';?>&nbsp;
        <?php echo $this->Paginator->last(__d('credit','Last'));?>
    </div>
</div>
