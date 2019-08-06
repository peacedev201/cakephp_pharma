<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('quiz', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('quiz', 'Quizzes Manager'), array('controller' => 'quiz_plugins', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Quiz"));
$this->end();
?>

<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('Quiz', __d('quiz', 'General')); ?>
            <div style="margin-top: 10px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-fit-height btn-gray dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                <?php echo __d('quiz', $sStatus); ?>
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="<?php echo $this->Html->url(array('plugin' => 'quiz', 'controller' => 'quiz_plugins', 'action' => 'admin_index', 'filter' => 'pending')); ?>"><?php echo __d('quiz', 'Pending'); ?></a></li>
                                <li><a href="<?php echo $this->Html->url(array('plugin' => 'quiz', 'controller' => 'quiz_plugins', 'action' => 'admin_index', 'filter' => 'approved')); ?>"><?php echo __d('quiz', 'Approved'); ?></a></li>
                                <li><a href="<?php echo $this->Html->url(array('plugin' => 'quiz', 'controller' => 'quiz_plugins', 'action' => 'admin_index')); ?>"><?php echo __d('quiz', 'All'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="btn-group">
                            <button class="btn btn-gray" onclick="confirmSubmitForm('<?php echo __d('quiz', 'Are you sure you want to delete these quizzes'); ?>', 'deleteForm')">
                                <?php echo __d('quiz', 'Delete'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="sample_1_filter" class="dataTables_filter">
                            <label>
                                <form method="post" action="">
                                    <?php echo $this->Form->text('keyword', array('class' => 'form-control input-medium input-inline', 'placeholder' => __d('quiz', 'Search by title'))); ?>
                                    <?php echo $this->Form->submit('submit', array('style' => 'display:none')); ?>
                                </form>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding-top: 10px; min-height: 99px">
                <div class="row">
                    <div class="col-md-12">
                        <div class="tab-content">
                            <div class="tab-pane active">
                                <form method="post" action="<?php echo $this->Html->url(array("plugin" => "quiz", "controller" => "quiz_plugins", "action" => "admin_delete")); ?>" id="deleteForm">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr class="tbl_head">
                                                <?php if ($cuser['Role']['is_super']): ?>
                                                <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
                                                <?php endif; ?>
                                                <th width="77" class="text-center"><?php echo $this->Paginator->sort('id', __d('quiz', 'ID')); ?></th>
                                                <th><?php echo $this->Paginator->sort('title', __d('quiz', 'Title'));?></th>
                                                <th data-hide="phone"><?php echo $this->Paginator->sort('User.name', __d('quiz', 'Author')); ?></th>
                                                <th data-hide="phone"><?php echo $this->Paginator->sort('Category.name', __d('quiz', 'Category')); ?></th>
                                                <th data-hide="phone"><?php echo $this->Paginator->sort('created', __d('quiz', 'Date')); ?></th>
                                                <th width="99" class="text-center"><?php echo $this->Paginator->sort('approved', __d('quiz', 'Approved')); ?></th>
                                                <th width="99" class="text-center"><?php echo $this->Paginator->sort('published', __d('quiz', 'Published')); ?></th>
                                                <th width="99"><?php echo __d('quiz', 'Action'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $iCount = 0; ?>
                                            <?php foreach ($aQuizzes as $aQuizz): ?>
                                            <tr class="gradeX <?php echo (++$iCount % 2 ? "odd" : "even"); ?>">
                                                <?php if ($cuser['Role']['is_super']): ?>
                                                <td><input type="checkbox" name="data[quizzes][]" value="<?php echo $aQuizz['Quiz']['id']; ?>" class="check"></td>
                                                <?php endif; ?>
                                                <td class="text-center"><?php echo $aQuizz['Quiz']['id']; ?></td>
                                                <td><a href="<?php echo $this->request->base . '/quizzes/create/' . $aQuizz['Quiz']['id']; ?>" target="_blank"><?php echo $this->Text->truncate(h($aQuizz['Quiz']['title']), 100, array('eclipse' => '...')); ?></a></td>
                                                <td><a href="<?php echo $this->request->base . '/admin/users/edit/' . $aQuizz['User']['id']; ?>" target="_blank"><?php echo h($aQuizz['User']['name']); ?></a></td>
                                                <td><?php echo $aQuizz['Category']['name']; ?></td>
                                                <td><?php echo $this->Time->niceShort($aQuizz['Quiz']['created']); ?></td>
                                                <td class="text-center"><?php echo $aQuizz['Quiz']['approved'] ? __d('quiz', 'Yes') : __d('quiz', 'No'); ?></td>
                                                <td class="text-center"><?php echo $aQuizz['Quiz']['published'] ? __d('quiz', 'Yes') : __d('quiz', 'No'); ?></td>
                                                <td>
                                                    <a href="javascript:void(0)" title="<?php echo __d('quiz','Delete'); ?>" onclick="mooConfirm('<?php echo __d('quiz','Are you sure you want to delete this quiz?'); ?>', '<?php echo $this->Html->url(array("plugin" => "quiz", "controller" => "quiz_plugins", "action" => "admin_delete", $aQuizz['Quiz']['id'])); ?>')"><i class="icon-trash icon-small"></i></a>&nbsp;
                                                    <?php if (empty($aQuizz['Quiz']['approved']) && $aQuizz['Quiz']['published']): ?>
                                                    <a href="javascript:void(0)" title="<?php echo __d('quiz', 'Approve'); ?>" onclick="mooConfirm('<?php echo __d('quiz', 'Are you sure you want to approve this quiz?'); ?>', '<?php echo $this->Html->url(array("plugin" => "quiz", "controller" => "quiz_plugins", "action" => "admin_approve", $aQuizz['Quiz']['id'], 1)); ?>')"><i class="fa fa-check-square-o"></i></a>&nbsp;
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pagination pull-right">
		<?php echo $this->Paginator->prev('« '.__d('quiz', 'Previous'), null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__d('quiz', 'Next').' »', null, null, array('class' => 'disabled')); ?>
            </div>
        </div>
    </div>
</div>