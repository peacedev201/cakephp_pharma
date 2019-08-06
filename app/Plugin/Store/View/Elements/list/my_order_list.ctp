<?php if($orders != null):?>
    <?php 
    $last_key = array_keys($orders);
    $last_key = end($last_key);
    $i = 0;
    foreach($orders as $order ): 
        $store = $order['Store'];
        $order_details = $order['StoreOrderDetail'];
		$store_payment = $order['StorePayment'];
        $order = $order['StoreOrder'];
        $showMoneyType = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? STORE_SHOW_MONEY_TYPE_CREDIT : STORE_SHOW_MONEY_TYPE_NORMAL;
    ?> 
        <div class="div-detail-row <?php if ($i == $last_key): ?> add-border-bottom <?php endif;$i++; ?>">
            <div class="top-list-brb">
                <?php echo __d('store', "Order Listing");?>
            </div>
            <div class="col-xs-12 col-md-2">
                <div class="group-group  text-left">
                    <i class="visible-sm visible-xs icon-app material-icons">supervisor_account</i>
                    <i class="text-app">
                        <a href="<?php echo $this->request->base;?>/users/view/<?php echo $store['user_id'];?>">
                            <?php echo $store['name'];?>
                        </a>
                    </i>
                </div>
            </div>
            <div class="col-xs-12 col-md-2">
                <div class="group-group  text-left">
                    <i class="visible-sm visible-xs icon-app material-icons">business_center</i>
                    <i class="text-app"><?php echo $order['order_code']; ?></i>
                </div>
            </div>
             <div class="col-xs-12 col-md-3">
                <div class="group-group">
                    <i class="visible-sm visible-xs icon-app material-icons">date_range</i>
                    <i class="text-app"><?php echo date('M d Y H:i a', strtotime($order['created']));?></i>
                </div>
            </div>
            <div class="col-xs-12 col-md-1">
                <div class="group-group">
                    <i class="visible-sm visible-xs icon-app material-icons">payment</i>
                    <i class="text-app"><?php echo $store_payment['name'];?></i>
                </div>
            </div>
            <div class="col-xs-12 col-md-2">
                <div class="group-group hasselect">
                    <i class="visible-sm visible-xs icon-app material-icons">note</i>
                    <i class="text-app"><?php echo $this->Store->getOrderStatus($order['order_status']);?></i>
                </div>
            </div>
            <div class="col-xs-12 col-md-1">
                <div class="group-group text-right">
                    <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                    <i class="text-app">
                        <?php 
                            $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order['amount_credit'] : $order['amount'];
                            echo $this->Store->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                        ?>
                    </i>
                </div>
            </div>
            <div class="col-xs-12 iconnottext col-md-1">
                <a href="javascript:void(0)" data-id="<?php echo $order['id'];?>" class="view_order_detail">
                    <i class="visible-sm visible-xs material-icons">visibility</i>
                    <i class="text-full hidden-sm hidden-xs"><?php echo __d('store', 'View') ?></i>
                </a>
            </div>
        </div>
    <?php endforeach;?>
<?php else:?>
    <div style="padding: 5px;text-align: center;">
        <?php echo __d('store', 'No more results found');?>
    </div>
<?php endif;?>
<?php if($this->Paginator->hasPage(2)):?>
    <div class="toolbar tb-bottom">
        <p class="store_plugin-result-count">
            <?php echo $this->Paginator->counter(sprintf(__d('store', 'Showing %s–%s of %s results'), '{:start}', '{:end}', '{:count}'));?>
        </p>
        <nav class="store_plugin-pagination">
            <ul class="page-numbers">
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array(
                    'class' => 'page-numbers previous order_paging', 
                    'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array(
                    'class' => 'page-numbers previous order_paging', 
                    'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                <?php echo $this->Paginator->numbers(array(
                    'class' => 'page-numbers order_paging', 
                    'tag' => 'li', 
                    'separator' => '')); ?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array(
                    'class' => 'page-numbers next order_paging', 
                    'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array(
                    'class' => 'page-numbers next order_paging', 
                    'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            </ul>
        </nav>
        <div class="clearfix"></div>
    </div>
<?php endif;?>