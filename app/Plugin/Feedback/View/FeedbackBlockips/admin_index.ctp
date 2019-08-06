<?php
    $this->Html->addCrumb(__d('feedback', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('feedback', 'Block IP Addresses'), array('controller' => 'feedbackblockips'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Feedback'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Feedback', __d('feedback', 'Block IP Addresses'));?>

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
                <a class="btn btn-gray pull-right" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base.$url_admin_feebback.$url_blockips.$url_ajax_create?>">
                    <?php echo __d('feedback', 'Add New');?>
                </a>            
            </div>
        </div>
    </div>
    <?php if($aIps): ?>
        <table class="table table-striped table-bordered" id="sample_1">
            <thead>
                <tr>
                    <th><?php echo  $this->paginator->sort('id', __d('feedback', 'IP Address'));?></th>
                    <th class="text-center" style="width: 10%"><?php  echo __d('feedback', 'Option') ?></th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($aIps as $key => $aIp):?>
                <tr>
                    <td><?php echo  $aIp['FeedbackBlockip']['blockip_address'] ?></td>
                    <td class="text-center">
                        <a href="<?php echo $this->request->base.$url_admin_feebback.$url_blockips.$url_ajax_create.'/'.$aIp['FeedbackBlockip']['id']?>" data-toggle="modal" data-target="#ajax" title="<?php echo __d('feedback', 'Edit') ?>">
                        <i class="fa fa-pencil"></i></a>
                        &nbsp;|
                        <a href="javascript:void(0)" class="tip" title="<?php echo __d('feedback', 'Delete') ?>" onclick="mooConfirm('<?php echo __d('feedback', 'Are you sure you want to delete this ip?') ?>', '<?php echo $this->request->base.$url_admin_feebback.$url_blockips.$url_delete.'/'.$aIp['FeedbackBlockip']['id']?>')">
                        <i class="icon-trash icon-small"></i></a>
                    </td>  
                </tr>
            <?php endforeach ?>

            </tbody>
        </table>
        <?php echo $this->Paginator->first('First');?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev('Prev') : '';?>&nbsp;
        <?php echo $this->Paginator->numbers();?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next('Next') : '';?>&nbsp;
        <?php echo $this->Paginator->last('Last');?>
    <?php else:?>
        <?php echo __d('feedback', 'No block ip addresses');?>
    <?php endif;?>
</div>
