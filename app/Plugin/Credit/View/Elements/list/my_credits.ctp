<table class="table table-striped">
    <thead>
    <tr class="tbl_head">
        <th width="30%"><?php echo __d('credit','Action Date');?></th>
        <th><?php echo __d('credit','Action Type');?></th>
        <th width="50px" data-hide="phone"><?php echo __d('credit','Credits');?></th>
    </tr>
    </thead>
    <tbody id="list-content">
<?php
$creditHelper = MooCore::getInstance()->getHelper('Credit_Credit');
foreach ($items as $item): ?>
    <tr>
        <td>
            <?php echo  $this->Moo->getTime($item['CreditLogs']['creation_date'], Configure::read('core.date_format'), $utz) ?>
        </td>
        <td>
            <?php
            $item_object = MooCore::getInstance()->getItemByType($item['CreditLogs']['object_type'],$item['CreditLogs']['object_id']);
            $options = array();
            if ($item['CreditActiontypes']['plugin'])
            {
                $options = array('plugin' => $item['CreditActiontypes']['plugin']);
            }

            if($item['CreditLogs']['is_delete'])
                echo $this->element('log_text/delete_' . $item['CreditActiontypes']['action_type'], array('item' => $item, 'item_object'=>$item_object),$options);
            else
                echo $this->element('log_text/' . $item['CreditActiontypes']['action_type'], array('item' => $item,'item_object'=>$item_object),$options);
            ?>
        </td>
        <td><?php echo round($item['CreditLogs']['credit'],2); ?></td>
    </tr>
<?php endforeach ?>
    </tbody>
</table>
<?php if($this->Paginator->counter() != '1 of 1'):?>
    <?php $this->Paginator->options(array('url' => array('my_credits', 'app_no_tab' => 1)));?>
    <?php echo $this->Paginator->prev('« '.__d('credit', 'Previous'), null, null, array('class' => 'disabled')); ?>
    <?php echo $this->Paginator->numbers(); ?>
    <?php echo $this->Paginator->next(__d('credit', 'Next').' »', null, null, array('class' => 'disabled')); ?>
<?php endif;?>
