<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooCredit'), 'object' => array('$', 'mooCredit'))); ?>
mooCredit.initWithDrawRequest();
<?php $this->Html->scriptEnd(); ?>
<table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
    <tr>
        <th><?php echo __d('credit','Date'); ?></th>
        <th><?php echo __d('credit','Amount'); ?></th>
        <th><?php echo __d('credit','Method'); ?></th>
        <th><?php echo __d('credit','Status'); ?></th>
        <th><?php echo __d('credit','Action'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
        if($withdraw){
            foreach($withdraw as $item){
    ?>
    <tr>
        <td>
            <?php echo $item['CreditWithdraw']['created']?>
        </td>
        <td>
            <?php echo $item['CreditWithdraw']['amount'];?>
        </td>
        <td>
            <?php echo $item['CreditWithdraw']['payment'];?>
        </td>
        <td>
            <?php echo ($item['CreditWithdraw']['status'] == CREDIT_STATUS_COMPLETED) ? __d('credit','Completed') : __d('credit','Pending');?>
        </td>
        <td>
            <a data-id="<?php echo $item['CreditWithdraw']['id'] ?>" class="delete_withdraw_request" href="javascript:void(0)"><?php echo __d('credit','Delete')?></a>
        </td>
    </tr>
    <?php } } ?>
    </tbody>
</table>
<?php if($this->Paginator->counter() != '1 of 1'):?>
    <?php echo $this->Paginator->prev('« '.__d('credit', 'Previous'), null, null, array('class' => 'disabled')); ?>
    <?php echo $this->Paginator->numbers(); ?>
    <?php echo $this->Paginator->next(__d('credit', 'Next').' »', null, null, array('class' => 'disabled')); ?>
<?php endif;?>
