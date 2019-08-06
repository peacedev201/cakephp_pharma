<?php
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $this->Html->addCrumb(__d('business', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('business', 'Manage Businesses'), array('plugin' => 'business'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min', 'Business.business-admin.css'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable', 'Business.admin'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Business'));
    $this->end();
    
    echo $this->addPhraseJs(array(
        'tconfirm' => __d('business', 'Are you sure?'),
    ));
?>
<?php echo $this->Moo->renderMenu('Business', __d('business', 'Manage Businesses'));?>
<div class="portlet-body">
    <div class="table-toolbar">
        <form id="searchForm" method="get" action="<?php echo $admin_url;?>">
            <div class="">
                <div class="col-md-6"></div>
                <div class="col-md-2">
                    <?php echo $this->Form->select('status_filter', $status, array(
                        'empty' => array('' => __d('business', 'All')),
                        'class' => 'form-control',
                        'name' => 'status',
                        'value' => $status_filter,
                        'onchange' => 'window.location = "'.$admin_url.'?status=" + jQuery("#status_filter").val()'
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
    <?php if(!empty($businesses)):?>
        <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_url?>delete">
            <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="sample_1">
                <thead>
                    <tr>
                        <th style="width: 7px">
                            <?php echo $this->Form->checkbox('', array(
                                'hiddenField' => false,
                                'div' => false,
                                'label' => false,
                                'onclick' => 'toggleCheckboxes2(this)'
                            ));?>
                        </th>
                        <th style="width: 4%">
                            <?php echo $this->Paginator->sort('id', __d('business',  'ID')); ?>
                        </th>
                        <th>
                            <?php echo $this->Paginator->sort('title', __d('business',  'Business Name')); ?>
                        </th>
                        <th style="width: 10%">
                            <?php echo $this->Paginator->sort('type', __d('business',  'Owner')); ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo __d('business',  'Package') ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo __d('business',  'Date') ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo __d('business',  'Status') ?>
                        </th>
                        <th style="width: 10%" class="text-center">
                            <?php echo __d('business',  'Featured') ?>
                        </th>
                        <th style="width: 10%" class="text-center">
                            <?php echo __d('business',  'Verify') ?>
                        </th>
                        <th style="width: 10%" class="text-center">
                            <?php echo __d('business',  'Options') ?>
                        </th>
                    </tr>
                </thead>
                <tbody>

                <?php 
                    $count = 0;
                    foreach ($businesses as $business): 
                        $user = $business['User'];
                        $package = $business['BusinessPackage'];
                        $business = $business['Business'];
                ?>
                    <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                        <td style="text-align: center">
                            <input type="checkbox" value="<?php echo $business['id']?>" class="check" id="cb<?php echo $business['id']?>" name="data[cid][]">
                        </td>
                        <td>
                            <?php echo $business['id']?>
                        </td>
                        <td>
                            <a href="<?php echo $business['moo_href'];?>" target="_blank">
                                <?php echo $this->Text->truncate(h($business['name']), 100, array('eclipse' => '...')) ?>
                            </a>
                            <?php if(!empty($business['moo_parent'])):?>
                            <div class="bus_branch_head">
                                <?php echo __d('business', 'Parent page');?>: 
                                <a href="<?php echo $business['moo_parent']['moo_href'];?>" class="title">
                                    <?php echo $business['moo_parent']['name'];?>                
                                </a> 
                            </div>
                            <?php endif;?>
                        </td>
                        <td>
                            <?php echo $user['name']?>
                        </td>
                        <td>
                            <?php echo $package['name']?>
                        </td>
                        <td>
                            <?php echo date('M d Y', strtotime($business['created']));?>
                        </td>
                        <td>
                            <?php 
                            $item_status = $status;
                            if($business['status'] != BUSINESS_STATUS_PENDING)
                            {
                                unset($item_status[BUSINESS_STATUS_PENDING]);
                            }
                            echo $this->Form->select('status', $item_status, array(
                                'empty' => false,
                                'class' => 'form-control',
                                'value' => $business['status'],
                                'onchange' => 'jQuery.admin.changeBusinessStatus(this, '.$business['id'].', \''.$business['status'].'\')'
                            ));?>
                        </td>
                        <td style="text-align: center">
                            <?php if ( $business['featured'] ): ?>
                                <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to un-featured this business?');?>', '<?php echo $admin_url.'unfeatured/'.$business['id']?>')">
                                    <i class="fa fa-check-square-o " title="<?php echo __d('business',  'Un-featured') ?>"></i>
                                </a>&nbsp;
                            <?php else: ?>
                                <a data-target="#ajax" data-toggle="modal" href="<?php echo $admin_url.'featured_dialog/'.$business['id']?>">
                                    <i class="fa fa-times-circle" title="<?php echo __d('business',  'Featured') ?>"></i>
                                </a>&nbsp;
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center">
                            <?php if($business['parent_id'] == 0):?>
                                <?php if ( $business['verify'] ): ?>
                                    <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to unverify this business?');?>', '<?php echo $admin_url.'verify/'.$business['id']?>/0')">
                                        <i class="fa fa-check-square-o " title="<?php echo __d('business',  'Unverify') ?>"></i>
                                    </a>&nbsp;
                                <?php else: ?>
                                    <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('business', 'Are you sure you want to verify this business?');?>', '<?php echo $admin_url.'verify/'.$business['id']?>/1')">
                                        <i class="fa fa-times-circle" title="<?php echo __d('business',  'Verify') ?>"></i>
                                    </a>&nbsp;
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center">
                            <a href="javascript:void(0)" class="tip" title="<?php echo __d('business',  'Delete') ?>" onclick="mooConfirm('<?php echo __d('business',  'Are you sure you want to delete this business?') ?>', '<?php echo $admin_url.'delete/'.$business['id']?>')">
                                <i class="icon-trash icon-small"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>

                </tbody>
            </table>
            </div>
        </form>
        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <button onclick="confirmSubmitForm('<?php echo __d('business', 'Are you sure you want to delete?');?>', 'adminForm')" id="sample_editable_1_new" class="btn btn-gray">
                            <?php echo __d('business',  'Delete'); ?>
                        </button>
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

    <?php else:?>
        <?php echo __d('business', "No Businesses");?>
    <?php endif;?>
</div>
