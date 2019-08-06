<?php

echo $this->Html->css(array(
        'jquery-ui', 
        'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array(
        'jquery-ui', 
        'footable',
        'Store.admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  "Manage Store Transactions"), array('plugin' => 'store', 'controller' => 'store_transactions', 'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Transactions'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo __d('store', "Manage Transactions");?>
        </div>
        <div class="panel-body">
            <form id="searchForm" method="get" action="<?php echo $admin_url;?>">
                <div class="form-group">
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
                        <button class="btn btn-primary btn-lg" type="submit"><?php echo __d('store', "Search");?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
            <?php if(!empty($store_transactions)):?>
            <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_url?>">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="sample_1">
                        <thead>
                            <tr>
                                <th style="width: 12%">
                                    <?php echo __d('store',  'Store') ?>
                                </th>
                                <th>
                                    <?php echo __d('store',  'Item name') ?>
                                </th>
                                <th style="width: 10%">
                                    <?php echo __d('store',  'Package') ?>
                                </th>
                                <th style="width: 12%">
                                    <?php echo __d('store',  'Gateway') ?>
                                </th>
                                <th style="width: 10%">
                                    <?php echo __d('store',  'Price') ?>
                                </th>
                                <th style="text-align: center;width: 10%">
                                    <?php echo __d('store',  'Period') ?> (<?php echo __d('store',  'Days') ?>)
                                </th>
                                <th style="text-align: center;width: 10%">
                                    <?php echo __d('store',  'Expiration date');?>
                                </th>
                                <th style="text-align: center;width: 10%">
                                    <?php echo __d('store',  'Status');?>
                                </th>
                                <th style="width: 5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count = 0;
                                foreach ($store_transactions as $store_transaction): 
                                    $gateway = $store_transaction['Gateway'];
                                    $store = $store_transaction['Store'];
                                    $store_product = $store_transaction['StoreProduct'];
                                    $store_package = $store_transaction['StorePackage'];
                                    $store_transaction = $store_transaction['StoreTransaction'];
                            ?>
                                <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                    <td>
                                        <div style="display: none">
                                            <input type="checkbox" value="<?php echo $store_transaction['id']?>" class="multi_cb" id="cb<?php echo $store_transaction['id']?>" name="data[cid][]">
                                        </div>
                                        <?php echo $store['name']; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $store_product['moo_href']?>" target="_blank">
                                            <?php echo $store_product != null ? $store_product['name'] : $store_transaction['item_name']; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo $store_package['name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $gateway['name']; ?> <br/> <?php echo $store_transaction['transaction_id']; ?>
                                    </td>
                                    <td>
                                        <?php echo $this->Store->formatMoney($store_transaction['amount'], null, $store_transaction['currency_symbol'], STORE_SHOW_MONEY_TYPE_NORMAL);; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $store_transaction['period']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo date("Y-m-d", strtotime($store_transaction['expiration_date'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $this->Store->loadTransactionStatusList($store_transaction['status']); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $store_transaction['id'];?>', 'delete')">
                                            <?php echo __d('store', "Delete");?>
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
                <?php echo __d('store', "No Transactions");?>
            <?php endif;?>
        </div>
    </div>
</div>
