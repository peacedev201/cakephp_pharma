<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooCredit'), 'object' => array('$', 'mooCredit'))); ?>
mooCredit.initWithDrawRequest();
<?php $this->Html->scriptEnd(); ?>
<?php
$currency = Configure::read('Config.currency');
?>
<form id="createFormWithDraw" method="POST" action="">
    <div class="box3">
        <div class="full_content p_m_10">
            <?php
                if($this->Session->read('errors')){
                    $errors = $this->Session->consume('errors');
            ?>
            <div class="Metronic-alerts alert alert-danger">
                <ul>
                <?php
                    foreach($errors as $item){
                        foreach($item as $item2) {
                            echo "<li>" . $item2 . "</li>";
                        }
                    }
                ?>
                </ul>
            </div>
            <?php } ?>
            <div class="form_content">
                <label><?php echo __d('credit','Note: You are allowed to withdraw %s time(s) in a Month',Configure::read('Credit.num_withdrawal') - $user_num_withdrawal); ?></label>
                <ul class="list6 list6sm2">
                    <li>
                        <div class="col-md-12"><?php echo __d('credit', 'Minimum withdrawal amount'); ?></div>
                        <div class="col-md-12">
                            <?php echo $this->Form->input('', array('class' => 'form-control', 'value' => $currency['Currency']['symbol'] . " " .$minimum_withdrawal_amount,'disabled','id' => 'minimum')); ?>
                        </div>
                    </li>
                    <li>
                        <div class="col-md-12"><?php echo __d('credit', 'Maximum withdrawal amount'); ?></div>
                        <div class="col-md-12">
                            <?php echo $this->Form->input('', array('class' => 'form-control', 'value' => $currency['Currency']['symbol'] . " " .$maximum_withdrawal_amount,'disabled','id' => 'maximum')); ?>
                        </div>
                    </li>
                    <li>
                        <div class="col-md-12"><?php echo __d('credit', 'Payment method *'); ?></div>
                        <div class="col-md-12">
                            <?php echo $this->Form->input('payment', array('class' => 'form-control', 'value' => '','options' => array('' => __d('credit','Select'),'paypal' => 'Paypal','payeer' => 'Payeer'),'label' => false)); ?>
                        </div>

                    </li>
                    <li id="li_payment_info" style="display: none;">
                        <div class="col-md-12"><?php echo __d('credit', 'Payment info *'); ?></div>
                        <div class="col-md-12">
                            <?php echo $this->Form->input('payment_info',array('class' => 'form-control','label' => false));?>
                        </div>
                    </li>
                    <li>
                        <div class="col-md-12">
                            <?php echo __d('credit','Enter withdraw amount');?>
                        </div>
                        <div class="col-md-12">
                            <?php echo $this->Form->text('amount', array('type' => 'number','class' => 'form-control', 'value' => '','id' => 'amount')); ?>
                        </div>
                    </li>
                    <li>
                        <div class="col-md-12">
                            <?php echo __d('credit','Credit-conversion formula');?> : <?php echo $currency['Currency']['symbol'] . " " . $formula_credit . "/" . $formula_money;?>
                        </div>
                    </li>
                    <li>
                        <div class="col-md-4">
                            <b><?php echo __d('credit','Your balance Earned Credits');?></b>
                        </div>
                        <div class="col-md-8">
                            <label style="width: 20px;"><?php echo $currency['Currency']['symbol'];?></label><span id="wd_money"> 0</span>
                        </div>
                    </li>
                    <li>
                        <button type="submit" id="btnWithDraw" class='btn btn-action'><?php echo __d('credit' ,'Submit')?></button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</form>
