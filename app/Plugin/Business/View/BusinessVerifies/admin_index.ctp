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
    echo $this->Html->css(array('Business.business-admin.css'), null, array('inline' => false));
    $this->Html->addCrumb(__d('business', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('business', 'Verification Requests'), array('controller' => 'business_verifies', 'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Business'));
    $this->end();
?>
<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<?php echo $this->Moo->renderMenu('Business', __d('business', 'Verification Requests')); ?>
<?php $mBusiness = MooCore::getInstance()->getModel('Business.Business'); ?>

<div class="portlet-body form">
    <div class="portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed ">
            <div class="table-toolbar">
                <form id="searchForm" method="get" action="">
                    <div class="">
                        <div class="col-md-6"></div>
                        <div class="col-md-2">
                            <?php echo $this->Form->select('status_filter', array('2' => __d('business', 'All'), '0' => __d('business', 'Pending'), '1' => __d('business', 'Verified')), array(
                                'empty' => false,
                                'class' => 'form-control',
                                'name' => 'status_filter',
                                'value' => $sStatusFilter
                            ));?>
                        </div>
                        <div class="col-md-3">
                            <?php echo $this->Form->input("keyword", array(
                                'div' => false,
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => __d('business', 'Search by name'),
                                'name' => 'keyword',
                                'value' => $keyword
                            ));?>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-gray" type="submit"><?php echo __d('business', "Search");?></button>
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr class="tbl_head">
                                            <th width="30"><?php echo $this->Paginator->sort('Business.id', __d('business', 'ID')); ?></th>
                                            <th><?php echo $this->Paginator->sort('Business.name', __d('business', 'Business Name'));?></th>
                                            <th><?php echo __d('business', 'Phone number');?></th>
                                            <th><?php echo __d('business', 'Document');?></th>
                                            <th><?php echo __d('business', 'Sent By');?></th>
                                            <th>
                                                <?php if($sStatusFilter === 1): ?>
                                                    <?php echo __d('business', 'Date'); ?>
                                                <?php else: ?>
                                                    <?php echo $this->Paginator->sort('BusinessVerify.modified', __d('business', 'Request Date'));?>
                                                <?php endif; ?>
                                            </th>
                                            <th><?php echo __d('business', 'Status'); ?></th>
                                            <th class="text-center"><?php echo __d('business', 'Verify'); ?></th>
                                            <th class="text-center"><?php echo __d('business', 'Action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 0; ?>
                                        <?php foreach ($aBusinesses as $aBusiness): ?>
                                        <tr class="gradeX <?php echo (++$count % 2 ? "odd" : "even") ?>">
                                            <td><?php echo $aBusiness['Business']['id']; ?></td>
                                            <td>
                                                <a href="<?php echo $aBusiness['Business']['moo_href']; ?>" target="_blank"><?php echo $this->Text->truncate(h($aBusiness['Business']['name']), 100, array('eclipse' => '...')); ?></a>
                                                <?php if(!empty($aBusiness['Business']['parent_id'])):?>
                                                <?php $aParent = $mBusiness->findById($aBusiness['Business']['parent_id']) ?>
                                                <div class="bus_branch_head">
                                                    <?php echo __d('business', 'Parent page');?>: 
                                                    <a href="<?php echo $aParent['Business']['moo_href'];?>" class="title">
                                                        <?php echo $aParent['Business']['name'];?>                
                                                    </a> 
                                                </div>
                                                <?php endif;?>
                                            </td>
                                            <td><?php echo (!empty($aBusiness['BusinessVerify'])) ? $aBusiness['BusinessVerify']['phone_number'] : ''; ?></td>
                                            <td>
                                                <?php if(!empty($aBusiness['BusinessVerify'])): ?>
                                                <a href="<?php echo $this->Business->getVerifyFile($aBusiness['BusinessVerify']); ?>"><?php echo $aBusiness['BusinessVerify']['document']; ?></a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo $aBusiness['User']['moo_href']; ?>" target="_blank"><?php echo $aBusiness['User']['moo_title']; ?></a>
                                            </td>
                                            <td>
                                                <?php if($sStatusFilter === 1): ?>
                                                    <?php echo date('M d Y', strtotime($aBusiness['Business']['created'])); ?>
                                                <?php else: ?>
                                                    <?php echo date('M d Y', strtotime($aBusiness['BusinessVerify']['modified'])); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($aBusiness['Business']['status'] == BUSINESS_STATUS_PENDING): ?>
                                                    <?php echo __d('business', 'Pending'); ?>
                                                <?php elseif($aBusiness['Business']['status'] == BUSINESS_STATUS_APPROVED): ?>
                                                    <?php echo __d('business', 'Approve'); ?>
                                                <?php else: ?>
                                                    <?php echo __d('business', 'Rejected'); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (empty($aBusiness['Business']['parent_id'])): ?>
                                                    <?php if ($aBusiness['Business']['verify']): ?>
                                                        <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to unverify this business?');?>', '<?php echo $this->request->base.'/admin/business/business/verify/' . $aBusiness['Business']['id']?>/0')">
                                                            <i class="fa fa-check-square-o " title="<?php echo __d('business', 'Unverify') ?>"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to verify this business?');?>', '<?php echo $this->request->base.'/admin/business/business/verify/' . $aBusiness['Business']['id']?>/1')">
                                                            <i class="fa fa-times-circle" title="<?php echo __d('business', 'Verify') ?>"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if(!empty($aBusiness['BusinessVerify']['id']) && $aBusiness['Business']['verify']): ?>
                                                <!--a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to reject and delete this request?');?>', '<?php echo $this->request->base.'/admin/business/business_verifies/delete/' . $aBusiness['BusinessVerify']['id']?>')">
                                                    <i class="icon-trash icon-small" title="<?php echo __d('business', 'Delete') ?>"></i>
                                                </a-->
                                                <?php elseif(!empty($aBusiness['BusinessVerify']['id'])): ?>
                                                <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to delete this request?');?>', '<?php echo $this->request->base.'/admin/business/business_verifies/delete/' . $aBusiness['BusinessVerify']['id']?>')">
                                                    <i class="icon-trash icon-small" title="<?php echo __d('business', 'Delete') ?>"></i>
                                                </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                </div>
                <div class="col-sm-9">
                    <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                        <ul class="pagination">
                            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('business', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('business', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                            <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('business', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('business', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>