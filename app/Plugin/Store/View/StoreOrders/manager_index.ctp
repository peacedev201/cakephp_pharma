<?php
echo $this->Html->css(array(
    'fineuploader', 
    'Store.jquery-ui',
    'pickadate',
    'fineuploader'), array('block' => 'css', 'minify'=>false));
?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager', 'store_jquery_ui'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initManage();
    store_manager.initManageOrder();
<?php $this->Html->scriptEnd(); ?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php echo $this->Element('manager_menu'); ?>
<?php $this->end(); ?>
<div class="bar-content">
    <?php echo $this->Element('Store.mobile/mobile_manager_menu'); ?>
    <div class="content_center">
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo STORE_MANAGER_URL;?>">
                    <i class="material-icons">home</i>
                </a>
                <span class="divider"></span>
            </li>
            <li>
                <a href="<?php echo STORE_MANAGER_URL.'orders/';?>">
                    <?php echo __d('store', "Manage Orders");?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo __d('store', "Manage Orders");?>
                <div class="pull-right">
                    <div class="btn-group">
                        <button aria-expanded="false" type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                            <?php echo __d('store', "Actions");?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li>
                                <a title="<?php echo __d('store', "Add New");?>" href="<?php echo STORE_MANAGER_URL?>orders/create">
                                    <?php echo __d('store', "Add New");?>
                                </a>
                            </li>                        
                            <li>
                                <a title="<?php echo __d('store', "Delete");?>" href="javascript:void(0)" id="delete_all">
                                    <?php echo __d('store', "Delete");?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="panel-body">
                <form id="searchForm" method="get" action="<?php echo STORE_MANAGER_URL.'orders/index';?>">
                    <div class="form-group form-search-app">                        
                        <div class="col-md-3">
                            <?php echo $this->Form->input("keyword", array(
                                'div' => false,
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => __d('store',  "Keyword"),
                                'value' => !empty($search['keyword']) ? $search['keyword'] : '',
                                'name' => 'keyword',
                            ));?>
                        </div>
                        <div class="col-md-2">
                            <?php echo $this->Form->input('search_type', array(
                                'options' => array(
                                    '1' => __d('store',  "Billing name"),
                                    '2' => __d('store',  "Shipping name"),
                                    '3' => __d('store',  "Billing email"),
                                    '4' => __d('store',  "Shipping email"),
                                    '5' => __d('store',  "Order code")
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
                        <div class="col-md-2 col-xs-6 col-sm-6">
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
                        <div class="col-md-2 col-xs-6 col-sm-6">
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
                        <div class="col-md-1 col-xs-12">
                            <button class="btn-big-height btn btn-primary btn-lg" type="submit"><?php echo __d('store', "Search");?></button>
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
                <?php if(!empty($orders)):?>
                <form class="form-horizontal store-manager-order" id="adminForm" method="post" action="<?php echo STORE_MANAGER_URL.'orders/';?>">
                    <div class=" div-detail-app">
                        <div class="div-full-breabcrum">
                            <div class="col-md-1">
                                <?php echo $this->Form->checkbox('', array(
                                    'hiddenField' => false,
                                    'class' => 'group_checkbox'
                                ));?>
                            </div>
                            <div class="col-md-3">
                                <div class="group-group text-left">
                                    <i class="text-app"><?php echo __d('store', 'Code');?></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Date');?></i>
                                </div>
                            </div>
                            <div class=" col-md-1 ">
                                <div class="group-group text-right">
                                    <i class="text-app"><?php echo __d('store', 'Amount');?></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Status');?></i>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Fee');?></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Action');?></i>
                                </div>
                            </div>
                        </div>
                        <?php foreach ($orders as $order):
                                $orderDetail = $order['StoreOrderDetail'];
                                $store_payment = $order['StorePayment'];
                                $order = $order['StoreOrder'];
                                $showMoneyType = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? STORE_SHOW_MONEY_TYPE_CREDIT : STORE_SHOW_MONEY_TYPE_NORMAL;
                        ?>
                        <div class="div-detail-row">
                            <div class="top-list-brb">
                                <?php echo __d('store', "Order Listing");?>
                            </div>
                            <div class="col-xs-12 col-md-1 col-custom-1">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">check_circle</i>
                                    <i class="text-app">
                                        <?php echo $this->Form->checkbox('cid.', array(
                                            'hiddenField' => false,
                                            'id' => 'cb'.$order['id'],
                                            'class' => 'multi_cb',
                                            'value' => $order['id']
                                        ));?>
                                    </i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="group-group  text-left">
                                    <i class="visible-sm visible-xs icon-app material-icons">business_center</i>
                                    <i class="text-app"><?php echo $order['order_code']; ?></i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">date_range</i>
                                    <i class="text-app"><?php echo date('M d Y', strtotime($order['created'])); ?></i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-1">
                                <div class="group-group text-right" <?php if($store_payment['key_name'] == ORDER_GATEWAY_CREDIT):?>style="line-height:20px"<?php endif;?>>
                                    <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                                    <i class="text-app">
                                        <?php 
                                            $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order['amount_credit'] : $order['amount'];
                                            echo $this->Store->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                                        ?>
                                    </i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <div class="group-group hasselect">
                                    <?php echo $this->Form->select('order_status'.$order['id'], $order_statuses,array(
                                        'value' => $order['order_status'],
                                        'empty' => false,
                                        'class' => 'change_order_status',
                                        'data-id' => $order['id']
                                    ))?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-1">
								<div class="group-group text-center" <?php if($store_payment['key_name'] == ORDER_GATEWAY_CREDIT && $order['site_profit'] > 0):?>style="line-height:20px"<?php endif;?>>
                                    <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
									<i class="text-app">
                                        <?php 
                                            $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order['site_profit_credit'] : $order['site_profit'];
                                            echo $order['site_profit'] > 0 ? $this->Store->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false) : 0;
                                        ?>
                                    </i>
								</div>
                            </div>
                            <div class="hidden-xs hidden-sm col-md-2 no-border-right">
								<a href="javascript:void(0)" data-id="<?php echo $order['id'];?>" class="view_order_detail">
                                    <i class="text-full hidden-sm hidden-xs"><?php echo __d('store', 'View') ?></i>
                                </a>
                                <a href="<?php echo STORE_MANAGER_URL ?>orders/create/<?php echo $order['id'] ?>">
                                    <i class="text-full hidden-sm hidden-xs"> <?php echo __d('store', 'Edit');?></i>
                                </a>
                                <a class="action_delete" data-id="<?php echo $order['id']?>" href="javascript:void(0)">
                                    <i class="text-full hidden-sm hidden-xs"><?php echo __d('store', 'Delete') ?></i>                                
                                </a>
                            </div>
							<div class="visible-xs visible-sm col-xs-4 iconnottext">
                                <a href="javascript:void(0)" data-id="<?php echo $order['id'];?>" class="view_order_detail">
                                    <i class="material-icons">visibility</i>
                                </a>
                            </div>
                            <div class="visible-xs visible-sm col-xs-4 iconnottext iscenter">
                                <a href="<?php echo STORE_MANAGER_URL ?>orders/create/<?php echo $order['id'] ?>">
                                    <i class="material-icons">create</i>
                                </a>
                            </div>
                            <div class="visible-xs visible-sm col-xs-4 iconnottext">
                                <a class="action_delete" data-id="<?php echo $order['id']?>" href="javascript:void(0)">
                                    <i class="material-icons">delete_sweep</i>                                  
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
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
</div>