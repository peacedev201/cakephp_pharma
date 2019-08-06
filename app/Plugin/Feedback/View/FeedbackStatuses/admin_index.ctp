<?php
    $this->Html->addCrumb(__d('feedback', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('feedback', 'Statuses'), array('controller' => 'feedback_statuses'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min', 'Feedback.colorpicker/colorpicker'), null, array('inline' => false));
    echo $this->Html->script(array(
        'jquery-ui', 
        'footable',
        'Feedback.colorpicker/colorpicker',
        'Feedback.colorpicker/eye',
        'Feedback.colorpicker/utils'
    ), array('inline' => false));

    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Feedback'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Feedback', __d('feedback', 'Statuses'));?>

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
                <a class="btn btn-gray pull-right" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base.$url_admin_feebback.$url_statuses.$url_ajax_create?>">
                    <?php echo __d('feedback', 'Add New');?>
                </a>            
            </div>
        </div>
    </div>
    <?php if($aStatuses): ?>
        <table class="table table-striped table-bordered" id="sample_1">
            <thead>
                <tr>
                    <th width="50"><?php echo $this->paginator->sort('id', __d('feedback', 'ID'));?></th>
                    <th width="250"><?php echo $this->paginator->sort('name', __d('feedback', 'Name'));?></th>
                    <th width="450"><?php echo __d('feedback', 'Default Comment');?></th>
                    <th width="150"><?php echo $this->paginator->sort('is_active', __d('feedback', 'Active'));?></th>
                    <th class="text-center" width="50"><?php echo __d('feedback', 'Options') ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($aStatuses as $key => $aStatus): ?>
                <tr>
                    <td><?php echo  $aStatus['FeedbackStatus']['id'] ?></td>
                    <td style="color:<?php echo  $aStatus['FeedbackStatus']['color']?>"><?php echo  $aStatus['FeedbackStatus']['name'] ?></td>
                    <td><?php echo  $aStatus['FeedbackStatus']['default_comment'] ?></td>
                    <td>                        
                        <?php if ( $aStatus['FeedbackStatus']['is_active'] ): ?>
                            <a href="<?php echo $this->request->base.$url_admin_feebback.$url_statuses.'/do_active/'.$aStatus['FeedbackStatus']['id']?>"><i class="fa fa-check-square-o " title="<?php echo __d('feedback', 'Disable') ?>"></i></a>&nbsp;
                        <?php else: ?>
                            <a href="<?php echo $this->request->base.$url_admin_feebback.$url_statuses.'/do_active/'.$aStatus['FeedbackStatus']['id']?>/1"><i class="fa fa-times-circle" title="<?php echo __d('feedback', 'Enable') ?>"></i></a>&nbsp;
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="<?php echo $this->request->base.$url_admin_feebback.$url_statuses.$url_ajax_create.'/'.$aStatus['FeedbackStatus']['id']?>" data-toggle="modal" data-target="#ajax" title="<?php echo __d('feedback', 'Edit') ?>">
                        <i class="fa fa-pencil"></i></a>
                        &nbsp;|
                        <a href="javascript:void(0)" class="tip" title="<?php echo __d('feedback', 'Delete') ?>" onclick="mooConfirm('<?php echo __d('feedback', 'Are you sure you want to delete this status?') ?>', '<?php echo $this->request->base.$url_admin_feebback.$url_statuses.$url_delete.'/'.$aStatus['FeedbackStatus']['id']?>')">
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
        <?php echo __d('feedback', 'No statuses');?>
    <?php endif;?>
</div>
