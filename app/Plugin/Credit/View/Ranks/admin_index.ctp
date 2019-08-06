<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->Html->addCrumb(__d('credit','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('credit', 'Manage ranks'), array('plugin' => 'credit', 'controller' => 'ranks', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Credit'));
$this->end();
?>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooCredit'), 'object' => array('$', 'mooCredit'))); ?>
<?php
echo $this->Html->script(array('vendor/jquery.fileuploader'), array('inline' => false));
$creditHelper = MooCore::getInstance()->getHelper('Credit_Credit');
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('loaded.bs.modal', function (e) {
Metronic.init();
});
$(document).on('hidden.bs.modal', function (e) {
$(e.target).removeData('bs.modal');
});
<?php $this->Html->scriptEnd(); ?>
<?php echo $this->Moo->renderMenu('Credit', __d('credit','Manage ranks'));?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/credit/ranks/create">
                        <?php echo __d('credit','Add new rank');?>
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
            <th><?php echo __d('credit','Ranks');?></th>
            <th width="50px" data-hide="phone"><?php echo __d('credit','Actions');?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($ranks)):?>
        <?php $count = 0;
        foreach ($ranks as $rank): ?>
        <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $rank['CreditRanks']['id']?>">
            <td width="50px"><?php echo $rank['CreditRanks']['id']?></td>
            <td class="reorder">
                <div class="col-md-1">
                    <img width="100" src="<?php echo $creditHelper->getImageRank($rank, array('prefix' => '150_square'))?>" id="item-avatar" class="img_wrapper">
                </div>
                <div class="col-md-6" style="padding-left: 50px;">
                    <h4 style="margin-top:0px;">
                        <a data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/credit/ranks/create/<?php echo $rank['CreditRanks']['id'];?>">
                            <?php echo htmlspecialchars($rank['CreditRanks']['name']);?>
                        </a>
                    </h4>
                    <p><strong><?php echo __d('credit','Credit');?>: <?php echo round($rank['CreditRanks']['credit'],2);?></strong></p>
                    <p><?php echo __d('credit','Number of members at this rank');?>: <?php echo $creditHelper->memberOfRank($rank['CreditRanks']['credit']);?></p>
                    <p><?php echo __d('credit','Description');?>: <?php echo h($rank['CreditRanks']['description']);?></p>
                </div>
            </td>
            <td width="50px">
                <a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__d('credit','Are you sure you want to delete this rank?'));?>', '<?php echo $this->request->base?>/admin/credit/ranks/delete/<?php echo $rank['CreditRanks']['id']?>')"><i class="icon-trash icon-small"></i></a>
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
