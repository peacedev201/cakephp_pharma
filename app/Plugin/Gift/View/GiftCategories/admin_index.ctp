<?php
    $this->Html->addCrumb(__d('gift', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('gift', 'Categories'), array('controller' => 'gift_categories'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Gift'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Gift', __d('gift','Categories'));?>

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
            <div class="col-md-10">
            </div>
            <div class="col-md-2">
                <a class="btn btn-gray pull-right" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base.$admin_url.'ajax_create/'?>">
                    <?php echo __d('gift', 'Add New');?>
                </a>            
            </div>
        </div>
    </div>
    <?php if($aCategories): ?>
        <table class="table table-striped table-bordered" id="sample_1">
            <thead>
                <tr>
                    <th width="50"><?php echo $this->paginator->sort('id', __d('gift', 'ID'));?></th>
                    <th width="250"><?php echo $this->paginator->sort('name', __d('gift', 'Name'));?></th>
                    <th width="450"><?php echo __d('gift',  'Description') ?></th>
                    <th style="width: 10%" class="text-center"><?php echo $this->paginator->sort('enable', __d('gift', 'Enable'));?></th>
                    <th class="text-center" style="width: 10%"><?php echo __d('gift', 'Options');?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($aCategories as $key => $aCategory):?>
                <tr>
                    <td><?php echo  $aCategory['GiftCategory']['id'] ?></td>
                    <td><?php echo  h($aCategory['GiftCategory']['name']); ?></td>
                    <td><?php echo  h($aCategory['GiftCategory']['description']); ?></td>
                    <td class="text-center">                        
                        <?php if ( $aCategory['GiftCategory']['enable'] ): ?>
                            <a href="<?php echo $this->request->base.$admin_url.'do_active/'.$aCategory['GiftCategory']['id']?>"><i class="fa fa-check-square-o " title="<?php echo __d('gift', 'Disable');?>"></i></a>&nbsp;
                        <?php else: ?>
                            <a href="<?php echo $this->request->base.$admin_url.'do_active/'.$aCategory['GiftCategory']['id']?>/1"><i class="fa fa-times-circle" title="<?php echo __d('gift', 'Enable');?>"></i></a>&nbsp;
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="<?php echo $this->request->base.$admin_url.'ajax_create/'.$aCategory['GiftCategory']['id']?>" data-toggle="modal" data-target="#ajax" title="<?php echo __d('gift', 'Edit');?>">
                        <i class="fa fa-pencil"></i></a>
                        &nbsp;|
                        <a href="javascript:void(0)" class="tip" title="<?php echo __d('gift', 'Delete');?>" onclick="mooConfirm('<?php echo __d('gift', 'Are you sure you want to delete this category?');?>', '<?php echo $this->request->base.$admin_url.'delete/'.$aCategory['GiftCategory']['id']?>')">
                        <i class="icon-trash icon-small"></i></a>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php echo $this->Paginator->first(__d('gift','First'));?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('gift','Prev')) : '';?>&nbsp;
        <?php echo $this->Paginator->numbers();?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__d('gift','Next')) : '';?>&nbsp;
        <?php echo $this->Paginator->last(__d('gift','Last'));?>
    <?php else:?>
        <?php echo __d('gift', 'No categories');?>
    <?php endif;?>
</div>
