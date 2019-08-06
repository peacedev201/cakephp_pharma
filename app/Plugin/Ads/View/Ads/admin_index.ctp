<?php
    __d('ads','Ads');
    $adsHelper = MooCore::getInstance()->getHelper('Ads_Ads');
    $this->Html->addCrumb(__d('ads','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('ads','Manage Ad Campaigns'), array('controller' => 'ads'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable', '/commercial/js/admin.js'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Ads'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Ads', __d('ads','Manage Ad Campaigns'));?>
<?php echo $this->Html->css(array('/commercial/css/commercial-admin.css' )); ?>
<style type="text/css">
    .pagination > li.current.paginate_button ,
    .pagination > li.disabled {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #428bca;
        text-decoration: none;
        background-color: #eee;
        border: 1px solid #ddd;
    }
</style>
<?php $item_status = array(
                        'pending' => __d('ads', 'Pending'),
                        'active' => __d('ads', 'Active'),
                        'disable' => __d('ads', 'Disable'),
); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <form id="searchForm" method="get" action="<?php echo $admin_url;?>">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <?php echo $this->Form->select('search_status', array(
                        'pending' => __d('ads', 'Pending'),
                        'active' => __d('ads', 'Active'),
                        'disable' => __d('ads', 'Disable'),
                    ), array(
                        'empty' => array('' => __d('ads', 'All')),
                        'class' => 'form-control',
                        'name' => 'search_status',
                        'value' => $search_status
                    ));?>
                </div>
                <div class="col-md-3">
                    <?php echo $this->Form->input("keyword", array(
                        'div' => false,
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __d('ads', 'Keyword'),
                        'name' => 'keyword',
                        'value' => $keyword
                    ));?>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-gray" type="submit"><?php echo __d('ads', "Search");?></button>
                </div>
                <div class="col-md-2">
                    <a class="btn btn-gray pull-right" href="<?php echo $admin_url;?>create"><?php echo __d('ads', "Create Campaign");?></a>
                </div>
                <div class="clear"></div>
            </div>
        </form>
    </div>
    <?php if(!empty($ads_campaigns)):?>
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
                            <?php echo $this->Paginator->sort('id', __d('ads',  'ID')); ?>
                        </th>
                        <th>
                            <?php echo $this->Paginator->sort('name', __d('ads',  'Name')); ?>
                        </th>
                        <th style="width: 10%">
                            <?php echo $this->Paginator->sort('AdsPlacement.name', __d('ads',  'Placement')); ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo $this->Paginator->sort('view_count', __d('ads',  'Views')); ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo $this->Paginator->sort('click_count', __d('ads',  'Clicks')); ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo __d('ads',  'Start Date'); ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo __d('ads',  'End Date'); ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo __d('ads',  'Timezone'); ?>
                        </th>
                        <th style="width: 10%">
                            <?php echo __d('ads',  'Payment Status'); ?>
                        </th>
                        <th style="width: 15%">
                            <?php echo __d('ads',  'Created by'); ?>
                        </th>
                        <th style="width: 6%">
                            <?php echo __d('ads',  'Status') ?>
                        </th>
                        <th style="width: 14%"></th>
                    </tr>
                </thead>
                <tbody>

                <?php 
                    $count = 0;
                    foreach ($ads_campaigns as $ads_campaign): 
                        $ads_placement = $ads_campaign['AdsPlacement'];
                        $ads_campaign = $ads_campaign['AdsCampaign'];
                ?>
                    <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                        <td style="text-align: center">
                            <?php if(array_key_exists('delete', $ads_campaign['action']) || $ads_campaign['is_expired']):?>
                                <input type="checkbox" value="<?php echo $ads_campaign['id']?>" class="check" id="cb<?php echo $ads_campaign['id']?>" name="data[cid][]">
                            <?php endif;?>
                        </td>
                        <td>
                            <?php echo $ads_campaign['id']?>
                        </td>
                        <td>
                            <a href="<?php echo $admin_url;?>create/<?php echo $ads_campaign['id'];?>">
                                <?php echo $this->Text->truncate(h($ads_campaign['name']), 100, array('eclipse' => '...')) ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo $this->request->base;?>/admin/ads/ads_placement/create/<?php echo $ads_placement['id'];?>">
                                <?php echo $this->Text->truncate(h($ads_placement['name']), 100, array('eclipse' => '...')) ?>
                            </a>
                        </td>
                        <td>
                            <?php echo h($ads_campaign['view_count'])?>
                        </td>
                        <td>
                            <?php echo h($ads_campaign['click_count'])?>
                        </td>
                        <td>
                            <?php if(!empty($ads_campaign['set_date'])):?>
                                <?php echo date('m-d-Y H:i', strtotime($ads_campaign['set_date']));?>
                            <?php elseif(!empty($ads_campaign['start_date'])):?>
                                <?php echo date('m-d-Y H:i', strtotime($ads_campaign['start_date']));?>
                            <?php endif;?>
                        </td>
                        <td>
                            <?php if(!empty($ads_campaign['set_end_date'])):?>
                                <?php echo date('m-d-Y H:i', strtotime($ads_campaign['set_end_date']));?>
                            <?php elseif(!empty($ads_campaign['end_date'])):?>
                                <?php echo date('m-d-Y H:i', strtotime($ads_campaign['end_date']));?>
                            <?php endif;?>
                        </td>
                        <td>
                            <?php echo $adsHelper->getGmtByTimezone($ads_campaign['timezone'])?>
                        </td>
                        <td>
                            <?php echo ($ads_campaign['payment_status'] == 1) ? __d('ads', 'Yes') : __d('ads', 'No');?>
                        </td>
                        <td>
                            <?php echo h($ads_campaign['email'])?>
                        </td>
                        <td>
                            <?php echo h($item_status[$ads_campaign['item_status']])?>
                        </td>
                        <td style="text-align: center">
                            <?php 
                            unset($ads_campaign['action']['delete']);
                            echo $this->Form->select('action', $ads_campaign['action'], array(
                                'empty' => array('' => __d('ads', 'Select')),
                                'class' => 'form-control',
                                'data-id' => $ads_campaign['id'],
                                'onchange' => 'jQuery.admin.changeAdsAction(this)'
                            ));?>
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
                        <button onclick="confirmSubmitForm('<?php echo __d('ads', 'Are you sure you want to delete?');?>', 'adminForm')" id="sample_editable_1_new" class="btn btn-gray">
                            <?php echo __d('ads',  'Delete'); ?>
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
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first('First', array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev('Previous', array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next('Next', array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last('Last', array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    </ul>
                </div>
            </div>
        </div>

    <?php else:?>
        <?php echo __d('ads', "No Campaigns");?>
    <?php endif;?>
</div>
