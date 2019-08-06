<?php
$this->Html->addCrumb(__d('credit', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('credit', 'Withdraw request'), array('controller' => 'withdraw', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Credit"));
$this->end();
$this->Paginator->options(array('url' => $data_search));
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('loaded.bs.modal', function (e) {
Metronic.init();
});
$(document).on('hidden.bs.modal', function (e) {
$(e.target).removeData('bs.modal');
});
<?php $this->Html->scriptEnd(); ?>
<?php echo $this->Moo->renderMenu('Credit', __d('credit', 'Withdraw request')); ?>
<div id="center">
    <form method="post" action="<?php echo $this->base . $url; ?>">
        <div style="padding-bottom: 15px;" class="dataTables_filter">
            <div style="padding: 0px;" class="col-md-3">
                <input class="form-control input-inline" value="<?php if (isset($name)) echo $name; ?>" type="text"
                       name="name">
            </div>
            <div class="col-md-4">
                <?php
                echo $this->Form->input('status', array(
                    'class' => "form-control",
                    'options' => array(
                        CREDIT_STATUS_PENDING => __d('credit', 'Pending request'),
                        CREDIT_STATUS_COMPLETED => __d('credit', 'Completed request')
                    ),
                    'label' => false,
                    'empty' => __d('credit', 'Choose status'),
                    'selected' => (isset($status)) ? $status : ""
                )); ?>
            </div>
            <button class="btn btn-gray" id="sample_editable_1_new" type="submit">
                <?php echo __d('credit', 'Search'); ?>
            </button>

        </div>
    </form>

    <table class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th><?php echo __d('credit', 'Requester name'); ?></th>
            <th><?php echo __d('credit', 'Method'); ?></th>
            <th><?php echo __d('credit', 'Amount'); ?></th>
            <th><?php echo __d('credit', 'Date'); ?></th>
            <th><?php echo __d('credit', 'Actions'); ?></th>
            <th><?php echo __d('credit', 'Completed date'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($withdraws)): ?>

            <?php foreach ($withdraws as $item):
                if ($item['CreditWithdraw']['status'] == CREDIT_STATUS_COMPLETED) {
                    $options = array(
                        CREDIT_STATUS_COMPLETED => __d('credit', 'Completed request'),
                        CREDIT_STATUS_DELETE => __d('credit', 'Delete request'));
                } else {
                    $options = array(
                        CREDIT_STATUS_PENDING => __d('credit', 'Pending request'),
                        CREDIT_STATUS_COMPLETED => __d('credit', 'Completed request'),
                        CREDIT_STATUS_DELETE => __d('credit', 'Delete request'));
                }
                ?>
                <tr>
                    <td>
                        <a target="_blank"
                           href="<?php echo $item['User']['moo_href']; ?>"><?php echo $item['User']['name']; ?></a>
                    </td>
                    <td>
                        <?php echo $item['CreditWithdraw']['payment']; ?><br/>
                        ( <b> <?php echo $item['CreditWithdraw']['payment_info']; ?> </b> )
                    </td>
                    <td>
                        <?php echo $item['CreditWithdraw']['amount']; ?>
                    </td>
                    <td><?php echo date('m/d/Y H:i:s', strtotime($item['CreditWithdraw']['created'])); ?></td>
                    <td>
                        <?php
                        echo $this->Form->input('action', array(
                            'class' => "form-control select_" . $item['CreditWithdraw']['id'] . "",
                            'options' => $options,
                            'label' => false,
                            'selected' => $item['CreditWithdraw']['status'],
                            'onchange' => "changeWithdrawStatus(" . $item['CreditWithdraw']['id'] . ")",
                            'data-id' => $item['CreditWithdraw']['id']
                        )); ?>
                        <input type="hidden" value="<?php echo $item['CreditWithdraw']['status']; ?>"
                               id="status_<?php echo $item['CreditWithdraw']['id']; ?>"/>
                    </td>
                    <td>
                        <?php
                        if ($item['CreditWithdraw']['completed_date'] != "0000-00-00 00:00:00") {
                            echo date('m/d/Y H:i:s', strtotime($item['CreditWithdraw']['completed_date'])); ?><br/>
                            ( <b> <?php echo $item['CreditWithdraw']['transaction_id']; ?> </b> )
                        <?php } ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td colspan="7">
                    <?php echo __d('credit', 'No withdraw request found'); ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php echo $this->Paginator->first('First'); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__('Prev')) : ''; ?>&nbsp;
        <?php echo $this->Paginator->numbers(); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__('Next')) : ''; ?>&nbsp;
        <?php echo $this->Paginator->last('Last'); ?>
    </div>
</div>
<?php
echo $this->Html->scriptStart(array('inline' => false));
?>

function changeWithdrawStatus(id){
var value = $(".select_" + id + " option:selected").val(),
status = $("#status_" + id).val();

if((value != <?php echo CREDIT_STATUS_COMPLETED; ?> && value != <?php echo CREDIT_STATUS_DELETE ?>) || (value == <?php echo CREDIT_STATUS_COMPLETED ?> && status == <?php echo CREDIT_STATUS_COMPLETED ?>))
return false;

$('#themeModal .modal-content').load("<?php echo $this->base . $url ?>" + "ajax_change_status_withdraw/" + id + "/" + value);
$("#themeModal").modal('show');
}

<?php
echo $this->Html->scriptEnd();
?>
