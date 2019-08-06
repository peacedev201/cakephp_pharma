<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initManage();
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
                <a href="<?php echo $url;?>">
                    <?php echo __d('store', "Manage Transactions");?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo __d('store', "Manage Transactions");?>
                <div class="clear"></div>
            </div>
            <div class="panel-body">
                <form id="searchForm" method="get" action="<?php echo $url;?>">
                    <div class="form-group form-search-app">
                        <div class="col-md-2"></div>
                        <div class="col-md-4">
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
                            <?php echo $this->Form->input('status', array(
                                'options' => $this->Store->loadTransactionStatusList(), 
                                'empty' => array("" => __d('store', 'All statuses')),
                                'class' => 'form-control',
                                'div' => false,
                                'label' => false,
                                'selected' => !empty($search['status']) ? $search['status'] : '',
                                'name' => 'status',
                            ));?>
                        </div>
                        <div class="col-md-3">
                            <?php echo $this->Form->input('package', array(
                                'options' => $this->Store->loadStorePackageList(), 
                                'empty' => array("" => __d('store', 'All packages')),
                                'class' => 'form-control',
                                'div' => false,
                                'label' => false,
                                'selected' => !empty($search['package']) ? $search['package'] : '',
                                'name' => 'package',
                            ));?>
                        </div>
                        <div class="col-md-1">
                            <button class="sl-mng-btn btn btn-primary btn-lg" type="submit"><?php echo __d('store', "Search");?></button>
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
                <?php if($store_transactions != null):?>
                <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $url;?>">
                    <div class="div-detail-app manage-product">
                        <div class="div-full-breabcrum">
                            <div class="col-md-3 col-custom-3">
                                <div class="group-group text-left">
                                    <i class="text-app"><?php echo $this->Paginator->sort('name', __d('store', 'Name')); ?></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="group-group text-left">
                                    <i class="text-app"><?php echo $this->Paginator->sort('price', __d('store', 'Package')); ?></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="group-group text-left">
                                    <i class="text-app"><?php echo $this->Paginator->sort('price', __d('store', 'Gateway')); ?></i>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="group-group">
                                    <i class="text-app"><?php echo $this->Paginator->sort('price', __d('store', 'Price')); ?></i>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store',  'Period')." (".__d('store',  'Days').") / ".__d('store', 'Expiration date'); ?></i>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="group-group">
                                    <i class="text-app"><?php echo $this->Paginator->sort('price', __d('store', 'Status')); ?></i>
                                </div>
                            </div>
                        </div>
                        <?php foreach($store_transactions as $store_transaction):
                             $gateway = $store_transaction['Gateway'];
                             $store = $store_transaction['Store'];
                             $store_product = $store_transaction['StoreProduct'];
                             $store_package = $store_transaction['StorePackage'];
                             $store_transaction = $store_transaction['StoreTransaction'];
                         ?>
                        <div class="div-detail-row ">
                            <div class="top-list-brb">
                                <?php echo __d('store', "Transaction Listing");?>
                            </div>
                            <div class="col-xs-12 col-md-3 col-custom-3">
                                <div class="group-group group-group-name text-left">
                                    <i class="visible-sm visible-xs icon-app material-icons">title</i>
                                    <i class="text-app">
                                        <?php if($store_transaction['store_product_id'] > 0):?>
                                            <a href="<?php echo $store_product['moo_href']?>" target="_blank">
                                                <?php echo  $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $store_product != null ? $store_product['name'] : $store_transaction['item_name'])), 80, array('eclipse' => ''));?>
                                            </a>
                                        <?php else:?>
                                            <a href="<?php echo $store['moo_href']?>" target="_blank">
                                                <?php echo  $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $store != null ? $store['name'] : $store_transaction['item_name'])), 80, array('eclipse' => ''));?>
                                            </a>
                                        <?php endif;?>
                                    </i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <div class="group-group text-left">
                                    <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                                    <i class="text-app"><?php echo $store_package['name'];?></i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <div class="group-group group-group-name text-left">
                                    <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                                    <i class="text-app"><?php echo $gateway['name']; ?> <br/> <?php echo $store_transaction['transaction_id']; ?></i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-1">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                                    <i class="text-app"><?php echo $this->Store->formatMoney($store_transaction['amount'], null, $store_transaction['currency_symbol'], STORE_SHOW_MONEY_TYPE_NORMAL); ?></i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                                    <i class="text-app"><?php echo $store_transaction['period'].' / '.date("Y-m-d", strtotime($store_transaction['expiration_date']));?></i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-1">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                                    <i class="text-app"><?php echo $this->Store->loadTransactionStatusList($store_transaction['status']);?></i>
                                </div>
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
                    <?php echo __d('store', "No Transactions");?>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>