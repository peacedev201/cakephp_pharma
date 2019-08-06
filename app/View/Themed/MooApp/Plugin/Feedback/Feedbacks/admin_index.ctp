<?php
    $this->Html->addCrumb(__d('feedback', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('feedback', 'Feedback'), array('controller' => 'feedbacks'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min', 'Feedback.feedback'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable', 'Feedback.feedback'), array('inline' => false));

    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Feedback'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Feedback', __d('feedback', 'Feedback'));?>

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
        <form id="searchForm" method="get" action="<?php echo $this->request->base?>/admin/feedback/feedbacks">
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
    <?php if($aFeedbacks): ?>
        <form method="post" action="<?php echo $this->request->base.$url_admin_feebback.$url_feedbacks.$url_delete?>" id="deleteForm">
            <table class="table table-striped table-bordered" id="sample_1">
                <thead>
                    <tr>
                        <?php if ( $cuser['Role']['is_super'] ): ?>
                            <th width="30"><input type="checkbox" onclick="toggleCheckboxes(this)"></th>
                        <?php endif; ?>
                        <th width="50" class="text-center"><?php echo $this->paginator->sort('id', __d('feedback', 'ID'));?></th>
                        <th ><?php echo $this->paginator->sort('title', __d('feedback', 'Title'));?></th>
                        <th class="text-center"><?php echo $this->paginator->sort('User.name', __d('feedback', 'Creator'));?></th>
                        <th class="text-center"><?php echo $this->paginator->sort('FeedbackCategory.name', __d('feedback', 'Category'));?></th>
                        <th class="text-center"><?php echo $this->paginator->sort('FeedbackSeverity.name', __d('feedback', 'Severity'));?></th>                        
                        <th class="text-center"><?php echo $this->paginator->sort('FeedbackStatus.name', __d('feedback', 'Status'));?></th>
                        <th class="text-center"><?php echo $this->paginator->sort('total_votes', __d('feedback', 'Votes'));?></th>
                        <?php if($permission_approve_feedback):?>
                        <th class="text-center"><?php echo $this->paginator->sort('privacy', __d('feedback', 'Published'));?></th>
                        <?php endif;?>
                        <th class="text-center"><?php echo $this->paginator->sort('featured', __d('feedback', 'Featured'));?></th>
                        <th ><?php echo $this->paginator->sort('created', __d('feedback', 'Date'));?></th>
                        <!-- <th width="150"><?php echo $this->paginator->sort('is_active', __d('feedback', 'Active'));?></th> -->
                        <th class="text-center" width="50"><?php echo __d('feedback', 'Options') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($aFeedbacks as $key => $aFeedback):?>
                    <tr>
                        <?php if ( $cuser['Role']['is_super'] ): ?>
                        <td><input type="checkbox" name="feedbacks[]" value="<?php echo $aFeedback['Feedback']['id']?>" class="check"></td>
                        <?php endif; ?>
                        <td class="text-center"><?php echo  $aFeedback['Feedback']['id'] ?></td>
                        <td><a href="<?php echo $this->request->base.'/feedback/feedbacks'?>/view/<?php echo $aFeedback['Feedback']['id']?>/<?php echo seoUrl($aFeedback['Feedback']['title'])?>"><?php echo  $aFeedback['Feedback']['title'] ?></a></td>
                        <td class="text-center">
                        <?php if($aFeedback['User']['id']): ?>
                            <?php echo  $this->Moo->getName($aFeedback['User']) ?>
                        <?php else: ?>
                            <?php echo  $aFeedback['Feedback']['email']?><br/>
                            <?php echo  $aFeedback['Feedback']['ip_address']?>
                        <?php endif ?>                            
                        </td>             

                        <td class="text-center">
                        <?php if($aFeedback['FeedbackCategory']['id']): ?>
                            <?php echo  $aFeedback['FeedbackCategory']['name'] ?>
                        <?php else: ?>
                            <?php echo __d('feedback', 'No')?>
                        <?php endif ?>                        
                        </td>

                        <td class="text-center">
                        <?php if($aFeedback['FeedbackSeverity']['id']): ?>
                            <?php echo  $aFeedback['FeedbackSeverity']['name'] ?>
                        <?php else: ?>
                            <?php echo __d('feedback', 'No')?>
                        <?php endif ?>                        
                        </td>

                        <td class="text-center">
                        <?php if($aFeedback['FeedbackStatus']['id']): ?>                            
                            <a href="<?php echo $this->request->base.'/admin/feedback/feedbacks'.$url_ajax_add_status.'/'.$aFeedback['Feedback']['id']?>" data-toggle="modal" data-target="#ajax"><?php echo  $aFeedback['FeedbackStatus']['name'] ?></a>
                        <?php else: ?>
                            <a href="<?php echo $this->request->base.'/admin/feedback/feedbacks'.$url_ajax_add_status.'/'.$aFeedback['Feedback']['id']?>" data-toggle="modal" data-target="#ajax"><?php echo __d('feedback', 'Add Status')?></a>
                        <?php endif ?>                        
                        </td>

                        <td class="text-center"><?php echo  $aFeedback['Feedback']['total_votes'] ?></td>
                        <?php if($permission_approve_feedback):?>
                            <td class="text-center">
                            <?php if ( $aFeedback['Feedback']['approved'] == 1 ): ?>
                                <a href="<?php echo $this->request->base.'/admin/feedback/feedbacks/do_active/'.$aFeedback['Feedback']['id']?>/approved/0"><i class="fa fa-check-square-o " title="<?php echo __d('feedback', 'Disable') ?>"></i></a>&nbsp;
                            <?php else: ?>
                                <a href="<?php echo $this->request->base.'/admin/feedback/feedbacks/do_active/'.$aFeedback['Feedback']['id']?>/approved/1"><i class="fa fa-times-circle" title="<?php echo __d('feedback', 'Enable') ?>"></i></a>&nbsp;
                            <?php endif; ?>

                            </td>
                        <?php endif; ?>
                        <td class="text-center">
                        <?php if ( $aFeedback['Feedback']['featured'] == 1 ): ?>
                            <a href="<?php echo $this->request->base.'/admin/feedback/feedbacks/do_active/'.$aFeedback['Feedback']['id']?>/featured/0"><i class="fa fa-check-square-o " title="<?php echo __d('feedback', 'Disable') ?>"></i></a>&nbsp;
                        <?php else: ?>
                            <a href="<?php echo $this->request->base.'/admin/feedback/feedbacks/do_active/'.$aFeedback['Feedback']['id']?>/featured/1"><i class="fa fa-times-circle" title="<?php echo __d('feedback', 'Enable') ?>"></i></a>&nbsp;
                        <?php endif; ?>

                        </td>
                        <td><?php echo  $aFeedback['Feedback']['created'] ?></td>

                        <td class="text-center">
                            <a href="<?php echo $this->request->base.'/admin/feedback/feedbacks/ajax_edit/'.$aFeedback['Feedback']['id']?>" data-toggle="modal" data-target="#ajax" title="<?php echo __d('feedback', 'Edit') ?>">
                            <i class="fa fa-pencil"></i></a>
                            &nbsp;|
                            <a href="javascript:void(0)" class="tip" title="<?php echo __d('feedback', 'Delete') ?>" onclick="mooConfirm('<?php echo __d('feedback', 'Are you sure you want to delete this feedback?') ?>', '<?php echo $this->request->base.'/admin/feedback/feedbacks'.$url_delete.'/'.$aFeedback['Feedback']['id']?>')">
                            <i class="icon-trash icon-small"></i></a>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </form>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-control" onchange="doFeedbackAction(this.value, 'feedback/feedbacks')">
                                <option value=""><?php echo __d('feedback','With selected...') ?></option>
                                <option value="delete"><?php echo __d('feedback','Delete') ?></option>
                            </select>
                            <input type="hidden" id="delete_feedback" value="<?php echo __d('feedback', 'Are you sure you want to delete these feedbacks?') ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <div class="pagination">
                    <?php echo $this->Paginator->first('First');?>&nbsp;
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev('Prev') : '';?>&nbsp;
                    <?php echo $this->Paginator->numbers();?>&nbsp;
                    <?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next('Next') : '';?>&nbsp;
                    <?php echo $this->Paginator->last('Last');?>
                </div>
            </div>
        </div>
    <?php else:?>
        <?php echo __d('feedback', 'No feedbacks');?>
    <?php endif;?>
</div>