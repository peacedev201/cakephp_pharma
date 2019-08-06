<?php
echo $this->Html->css(array('jquery-ui', 'pickadate', 'footable.core.min', 'Business.business-admin.css'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'pickadate/picker', 'pickadate/picker.date', 'footable'), array('inline' => false));
$this->Html->addCrumb(__d('business', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('business', 'Transactions'), array('controller' => 'business_transactions', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Business'));
$this->end();
$helper = MooCore::getInstance()->getHelper('Business_Business');
$list_status = $helper->getListStatus('BusinessTransaction');
$this->Paginator->options(array('url' => $data_search));
$currency = Configure::read('Config.currency');
?>
<?php echo $this->Moo->renderMenu('Business', __d('business', 'Transactions')); ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
     if($('.datepicker').length > 0) {
            $('.datepicker').pickadate({
                    monthsFull: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    weekdaysFull: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    weekdaysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    today:"Today",
                    clear:"Clear",
                    close: "Close",
                    format: 'yyyy-mm-dd'
            });
    }
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
    $('.js_drop_down_link').click(function (){
        eleOffset = $(this).offset();
        $('#js_drop_down_cache_menu').remove();
        $('body').prepend('<div id="js_drop_down_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:9999;"><div class="link_menu" style="display:block;">' + $(this).parent().find('.link_menu:first').html() + '</div></div>');
        $('#js_drop_down_cache_menu .link_menu').hover(function ()
        {

        },
        function ()
        {
        $('#js_drop_down_cache_menu').remove();
        });
        return false;
    });
<?php $this->Html->scriptEnd(); ?>
<div id="center">
    <form method="post" action="<?php echo $this->base; ?>/admin/business/business_transactions" >
        <div style="padding-bottom: 15px;" class="dataTables_filter">
            <div class="" style="display:inline-block;width:30%;margin-bottom: 10px">
            <?php echo __d('business', 'Search'); ?>
            <input class="form-control input-small input-inline" value="<?php if (isset($name)) echo $name; ?>" type="text" name="name">
            </div>
            <div class="" style="display:inline-block;width:30%;margin-bottom: 10px">
                <?php echo __d('business', 'Packages'); ?>
            <select class="form-control input-small input-inline" name="business_package_id">
                <option></option>
                <?php foreach ($packages as $package): ?>
                    <option <?php if (isset($business_package_id) && $business_package_id == $package['BusinessPackage']['id']) echo 'selected="selected"'; ?> value="<?php echo $package['BusinessPackage']['id']; ?>"><?php echo $helper->getPackageDescription($package['BusinessPackage'], $currency['Currency']['currency_code']); ?></option>
                <?php endforeach; ?>
            </select>
            </div>
            <div class="" style="display:inline-block;width:30%;margin-bottom: 10px">
            <?php echo __d('business', 'Gateway'); ?>
            <select class="form-control input-small input-inline" name="gateway_id">
                <option></option>
                <?php foreach ($gateways as $gateway): ?>
                    <option <?php if (isset($gateway_id) && $gateway_id == $gateway['Gateway']['id']) echo 'selected="selected"'; ?> value="<?php echo $gateway['Gateway']['id']; ?>"><?php echo $gateway['Gateway']['name']; ?></option>
                <?php endforeach; ?>
            </select>
            </div>
            <div class="" style="display:inline-block;width:30%;margin-bottom: 10px">
            <?php echo __d('business', 'Status'); ?>
            <select class="form-control input-small input-inline" name="status">
                <option></option>
                <?php foreach ($list_status as $key => $name): ?>
                    <option <?php if (isset($status) && $status == $key) echo 'selected="selected"'; ?> value="<?php echo $key ?>"><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
            </div>
            <div class="" style="display:inline-block;width:30%;margin-bottom: 10px">
            <?php echo __d('business', 'Start date'); ?>
            <input class="datepicker form-control input-small input-inline" value="<?php if (isset($start_date)) echo $start_date; ?>" type="text" name="start_date">
            </div>
            <div class="" style="display:inline-block;width:30%;margin-bottom: 10px">
            <?php echo __d('business', 'End date'); ?>
            <input class="datepicker form-control input-small input-inline" value="<?php if (isset($end_date)) echo $end_date; ?>" type="text" name="end_date">
            </div>
            <button class="btn btn-gray" id="sample_editable_1_new" type="submit">
                <?php echo __d('business', 'Search'); ?>
            </button>
        </div>
    </form>	
    <table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?php echo __d('business', 'Payer'); ?></th>	
                <th><?php echo __d('business', 'Pay Type'); ?></th>	
                <th><?php echo __d('business', 'Package'); ?></th>
                <th><?php echo __d('business', 'Gateway'); ?></th>
                <th><?php echo __d('business', 'Amount'); ?></th>
                <th style="width: 15%"><?php echo __d('business', 'Transaction Id'); ?></th>
                <th style="width: 7%"><?php echo $this->Paginator->sort('BusinessTransaction.status', __d('business', 'Status')); ?></th>
                <th style="width: 12%"><?php echo $this->Paginator->sort('BusinessTransaction.created', __d('business', 'Created date')); ?></th>
               
            </tr>
        </thead>
        <tbody>
            <?php if (count($transactions)): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td>
                            <?php if($transaction['User']['id'] > 0):?>
                                <a href="<?php echo $transaction['User']['moo_href']; ?>"><?php echo $transaction['User']['name']; ?></a>
                            <?php else:?>
                                <i><?php echo __d('business', 'deleted user');?></i>
                            <?php endif;?>
                        </td>	
                        <td><?php echo $helper->getTextPayType($transaction); ?></td>
                        <td>
                            <?php if($transaction['BusinessTransaction']['business_package_id'] > 0): ?>
                            <p><?php echo $transaction['BusinessPackage']['name']; ?></p>
                            <p><?php echo $helper->getPackageDescription($transaction['BusinessPackage'], $transaction['BusinessPaid']['currency_code']); ?></p>						
                            <?php else: ?>
                                <?php echo $transaction['BusinessPaid']['feature_day'] ." ". __d('business', 'days')?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            echo $transaction['Gateway']['name'];
                            ?>
                        </td>
                        <td><?php echo $transaction['BusinessTransaction']['amount']; ?> <?php echo $transaction['BusinessTransaction']['currency']; ?></td>
                        <td>
                            <?php
                                $calback = json_decode($transaction['BusinessTransaction']['callback_params'], true);
                                echo isset($calback['transaction']['senderTransactionId']) ? $calback['transaction']['senderTransactionId'] : $transaction['BusinessTransaction']['txn'];
                            ?>
                        </td>
                        <td><?php echo $helper->getTextStatusTransaction($transaction); ?></td>
                        <td><?php echo $this->Time->format('m/d/Y', $transaction['BusinessTransaction']['created']); ?></td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">
                        <?php echo __d('business', 'No transaction found'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

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