<?php

echo $this->Html->css(array(
        'Store.jquery-ui', 
        'footable.core.min',
        'Store.storeapp',), null, array('inline' => false));
    echo $this->Html->script(array(
        'Store.jquery-ui', 
        'footable',
        'Store.admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  'Orders Manager'), array(
        'controller' => 'store_orders', 
        'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
?>
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
    .title-modal {
        font-size: 16px;
        font-weight: bold;
        background: #F4F4F4;
        padding: 15px;
        border-radius: 4px 4px 0 0;
    }
    .modal-dialog{
        width: 1000px !important;
    }
    .print_order {
        cursor: pointer;
        color: #787884;
        font-size: 14px;
        position: absolute;
        right: 0;
    }
    .print_order i {
        display: inline-block;
        vertical-align: middle;
    }
</style>
<?php echo $this->Moo->renderMenu('Store', __d('store', 'Orders'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo __d('store', "Manage Orders");?>
            <div class="clear"></div>
        </div>
        <div class="panel-body">
            <form id="searchForm" method="get" action="<?php echo $admin_url;?>">
                <div class="form-group">
                    <div class="col-md-2">
                        <?php echo $this->Form->input("keyword", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'placeholder' => __d('store', 'Keyword'),
                            'name' => 'keyword',
                            'value' => !empty($search['keyword']) ? $search['keyword'] : ''
                        ));?>
                    </div>
                    <div class="col-md-2">
                        <?php echo $this->Form->input('search_type', array(
                            'options' => array(
                                '1' => __d('store',  "Billing name"),
                                '2' => __d('store',  "Shipping name"),
                                '3' => __d('store',  "Billing email"),
                                '4' => __d('store',  "Shipping email"),
                                '5' => __d('store',  "Order code"),
                                '6' => __d('store',  "Store name"),
                            ), 
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                            'value' => !empty($search['search_type']) ? $search['search_type'] : '',
                            'name' => 'search_type',
                        ));?>
                    </div>
                     <div class="col-md-2">
                        <?php echo $this->Form->select("order_status", $order_statuses, array(
                            'empty' => array('' => __d('store', 'All statuses')),
                            'class' => 'form-control',
                            'value' => !empty($search['order_status']) ? $search['order_status'] : '',
                            'name' => 'order_status',
                        ));?>
                    </div>
                    <div class="col-md-2">
                        <?php echo $this->Form->input("from", array(
                            'div' => false,
                            'label' => false,
                            'id' => 'datetime_start',
                            'class' => 'form-control datepicker',
                            'placeholder' => __d('store',  "from"),
                            'value' => !empty($search['search_from']) ? $search['search_from'] : '',
                            'name' => 'search_from',
                        ));?>
                    </div>
                    <div class="col-md-3">
                        <?php echo $this->Form->input("to", array(
                            'div' => false,
                            'label' => false,
                            'id' => 'datetime_end',
                            'class' => 'form-control datepicker',
                            'placeholder' => __d('store',  "to"),
                            'value' => !empty($search['search_to']) ? $search['search_to'] : '',
                            'name' => 'search_to',
                        ));?>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary btn-lg" type="submit"><?php echo __d('store', "Search");?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
            
            <?php echo __d('store', 'Total site profit').': ';?>
            <b><?php echo $this->Store->formatMoney($site_profit);?></b>
            <br/>
            <?php echo __d('store', 'Total site profit credit').': ';?>
            <b><?php echo $this->Store->formatMoney($site_profit_credit, null, null, STORE_SHOW_MONEY_TYPE_CREDIT)?></b>
            <br/><br/>
            
            <?php if(!empty($orders)):?>
            <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_url?>">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="sample_1">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo $this->Paginator->sort('Store.name', __d('store',  'Store')); ?>
                                </th>
                                <th style="width: 10%">
                                    <?php echo $this->Paginator->sort('name', __d('store',  'Code')); ?>
                                </th>
                                <th style="width: 10%">
                                    <?php echo $this->Paginator->sort('StoreProduct.created', __d('store',  'Date')); ?>
                                </th>
                                <th style="width: 10%">
                                    <?php echo $this->Paginator->sort('User.name', __d('store',  'Amount')); ?>
                                </th>
                                <th style="width: 10%">
                                    <?php echo $this->Paginator->sort('created', __d('store',  'Profit')); ?>
                                </th>
                                <th style="width: 10%">
                                    <?php echo $this->Paginator->sort('User.name', __d('store',  'Status')); ?>
                                </th>
                                <th style="width: 11%"></th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php 
                            $count = 0;
                            foreach ($orders as $order): 
                                $store = $order['Store'];
                                $store_payment = $order['StorePayment'];
                                $order = $order['StoreOrder'];
                                $showMoneyType = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? STORE_SHOW_MONEY_TYPE_CREDIT : STORE_SHOW_MONEY_TYPE_NORMAL;
                        ?>
                            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                <td>
                                    <a href="<?php echo $store['moo_href']?>">
                                        <?php echo $store['name']; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $order['order_code']; ?>
                                </td>
                                <td>
                                    <?php echo date('M d Y', strtotime($order['created'])); ?>
                                </td>
                                <td>
                                    <?php 
                                        $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order['amount_credit'] : $order['amount'];
                                        echo $this->Store->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order['site_profit_credit'] : $order['site_profit'];
                                        echo $order['site_profit'] > 0 ? $this->Store->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false) : 0;
                                    ?>
                                </td>
                                <td>
                                    <?php echo $this->Store->getOrderStatus($order['order_status']);?>
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" data-id="<?php echo $order['id'];?>" class="view_order_detail">
                                        <?php echo __d('store', 'View') ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>

                        </tbody>
                    </table>
                </div>
            </form>
            <div class="row">
                <div class="col-sm-3">
                        <?php echo $this->Paginator->counter(array(
                            'separator' => __d('store', ' of a total of ')
                        ));?>
                </div>
                <div class="col-sm-9">
                    <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                        <ul class="pagination">
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php else:?>
                <?php echo __d('store', "No Orders");?>
            <?php endif;?>
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    
    jQuery(document).ready(function(){
        var start_id = 'datetime_start';
        var end_id = 'datetime_end';
        jQuery("#" + start_id).datepicker({
            changeMonth: true,
            onClose: function( selectedDate ) {
                jQuery("#" + end_id).datepicker("option", "minDate", selectedDate);
            }
        });
        jQuery("#" + end_id).datepicker({
            changeMonth: true,
            onClose: function( selectedDate ) {
                jQuery("#" + start_id).datepicker("option", "maxDate", selectedDate);
            }
        }); 
    });
    
    //view detail
    jQuery(document).on('click', '.view_order_detail', function(){
        jQuery.post(mooConfig.url.base + "/stores/manager/orders/order_detail/" + jQuery(this).data('id'), '', function(data){
            jQuery('#storeModal .modal-content').empty().append(data); 
            jQuery('#storeModal').modal();
        });
    })
    
    //print detail
    jQuery(document).on('click', '.print_order', function(){
        window.open(mooConfig.url.base + "/stores/orders/print_order/" + jQuery(this).data('id'), '_blank', 'location=yes');
    })
    
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>