<?php
    $this->Html->addCrumb(__d('feedback', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('feedback', 'Block Users'), array('controller' => 'feedbackblockusers'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Feedback'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Feedback', __d('feedback', 'Block Users'));?>

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
        <form id="searchForm" method="get" action="<?php echo $this->request->base?>/admin/feedback/feedback_blockusers">
            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-3">
                    <?php echo $this->Form->input("keyword", array(
                        'div' => false,
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __d('feedback', 'Search by title'),
                        'name' => 'keyword',
                        'value' => $keyword
                    ));?>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-gray" type="submit"><?php echo __d('feedback', "Search");?></button>
                </div>
                <div class="clear"></div>
            </div>
        </form>
    </div>
    <?php if($aUsers): ?>
        <table class="table table-striped table-bordered" id="sample_1">
            <thead>
                <tr>
                    <th><?php echo  $this->paginator->sort('id', __d('feedback', 'ID'));?></th>
                    <th><?php echo  $this->paginator->sort('name', __d('feedback', 'User Name'));?></th>
                    <th class="text-center"><?php echo __d('feedback', 'Feedback Posting');?></th>
                    <th><?php echo __d('feedback', 'Email');?></th>
                    <th><?php echo __d('feedback', 'Roles');?></th>
                    <th><?php echo __d('feedback', 'Create');?></th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($aUsers as $key => $aUser):?>
                <?php 
                    $user = $aUser['User'];
                    $role = $aUser['Role'];
                ?>
                <tr>
                    <td><?php echo  $user['id'] ?></td>
                    <td><?php echo  $this->Moo->getName($user) ?></td>
                    <td class="text-center">                        
                        <?php if ( !$user['block_feedback'] ): ?>
                            <a href="<?php echo $this->request->base.$url_admin_feebback.$url_blockusers.'/do_active/feedback/'.$user['id']?>/1"><i class="fa fa-check-square-o " title="<?php echo __d('feedback', 'Disable') ?>"></i></a>&nbsp;
                        <?php else: ?>
                            <a href="<?php echo $this->request->base.$url_admin_feebback.$url_blockusers.'/do_active/feedback/'.$user['id']?>"><i class="fa fa-times-circle" title="<?php echo __d('feedback', 'Enable') ?>"></i></a>&nbsp;
                        <?php endif; ?>
                    </td>                   
                    <td><?php echo  $user['email']?></td>
                    <td><?php echo  $role['name'] ?></td>
                    <td><?php echo  $user['created']?></td>
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
        <?php echo __d('feedback', 'No block users');?>
    <?php endif;?>
</div>
