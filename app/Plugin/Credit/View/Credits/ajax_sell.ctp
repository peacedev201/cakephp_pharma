<div class="title-modal">
    <?php echo __d('credit', 'Buy Credits')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<?php $urlReturn = $siteUrl.$this->base."/credit/gateway/purchased/".$viewerId;
$urlReturnPaypal = $siteUrl.$this->base."/credit/gateway/returnPaypal/".$viewerId;
$currency = Configure::read('Config.currency');
?>
<div class="modal-body">
    <div class="error_buy"></div>
    <div class="rank-info buy-creadit">
        <div>
            <i class="current-balance-large"></i>
            <p>  <?php echo __d('credit', 'Current Balance')?>
                <em>
                    <?php if(!empty($balance)){
                        echo round($balance['CreditBalances']['current_credit'], 2);
                    }
                    else{ echo '0';}?>
                </em>
            </p>
        </div>
    </div>
    <div class="buy-credit-ask"><?php echo __d('credit', 'How many credits would you like to add to your account?') ?></div>
    <!--<form id="buyCreditForm" action="<?php if(Configure::read('Credit.credit_test_mode')):?>https://www.sandbox.paypal.com/cgi-bin/webscr<?php else:?>https://www.paypal.com/cgi-bin/webscr<?php endif;?>" enctype="application/x-www-form-urlencoded">
    	<div>
    		<?php if(!empty($sells)):?>
    			<?php foreach ($sells as $sell): ?>
						<div class="sell_item">
							<input type="radio" name="sell_selected" value="<?php echo $sell['CreditSells']['id'].'_'.$sell['CreditSells']['price'].'_'.$sell['CreditSells']['credit'] ?>"> <?php echo number_format($sell['CreditSells']['credit'], 0) .' '. __d('credit', 'Credits for') . ' '.$currency['Currency']['symbol']. number_format($sell['CreditSells']['price'], 2) ?>
                        </div>
				<?php endforeach; ?>
    		<?php else:?>
    			<div class="clear text-center"> <?php echo __d('credit',  'No more credit package found') ?> </div>
    		<?php endif;?>
    	</div>
        <div class="buy-credit-btn">
    	<input type="submit" class="btn btn-action" id="btnPaypal" value="<?php echo __d('credit',  'CONTINUE')?>" >
    		or <a data-dismiss="modal" href="#" class=""><?php echo __d('credit', 'Cancel');?></a>
    	</div>
    	<input id="cmd" type="hidden" value="_xclick" name="cmd">
        <input id="business" type="hidden" value="<?php echo Configure::read('Credit.credit_paypal_email');?>" name="business">
        <input id="item_name" type="hidden" value="<?php echo __d('credit', 'Buy Credits')?>" name="item_name">
        <input id="currency_code" type="hidden" value="<?php echo $currency['Currency']['currency_code']; ?>" name="currency_code">
        <input id="notify_url" type="hidden" value="<?php echo $siteUrl.$this->base ?>" name="notify_url">
        <input id="return" type="hidden" value="" name="return">
        <input id="amount" type="hidden" value="" name="amount">
    </form>-->

    <div class="paypal_content" id="buyCreditForm">
        <?php if(!empty($sells)):?>
            <?php foreach ($sells as $sell): ?>
                <div class="sell_item">
                    <input type="radio" name="sell_selected" value="<?php echo $sell['CreditSells']['id'].'_'.$sell['CreditSells']['price'].'_'.$sell['CreditSells']['credit'] ?>"> <?php echo round($sell['CreditSells']['credit'], 2) .' '. __d('credit', 'Credits for') . ' '.$currency['Currency']['symbol']. number_format($sell['CreditSells']['price'], 2) ?>
                </div>
            <?php endforeach; ?>
        <?php else:?>
            <div class="clear text-center"> <?php echo __d('credit',  'No more credit package found') ?> </div>
        <?php endif;?>
    </div>

    <?php if(Configure::read('Credit.credit_paypal_email')):?>
        <div>
            <form action="<?php if(Configure::read('Credit.credit_test_mode')):?>https://www.sandbox.paypal.com/cgi-bin/webscr<?php else:?>https://www.paypal.com/cgi-bin/webscr<?php endif;?>" enctype="application/x-www-form-urlencoded">
                <div>
                    <h2><?php echo __d('credit','PayPal');?></h2>
                </div>
                <div class="clear"></div>
                <div>
                    <input type="submit" class="btn btn-action" id="btnPaypal" value="<?php echo __d('credit',  'Pay with PayPal')?>" >
                </div>
                <input type="hidden" name="cmd" value="_cart">
                <input type="hidden" name="business" value="<?php echo Configure::read('Credit.credit_paypal_email');?>">
                <input type="hidden" name="currency_code" value="<?php echo $currency['Currency']['currency_code']; ?>">
                <input type="hidden" name="return" id="return" value="">
                <input type="hidden" name="cancel_return" value="">
                <input type="hidden" name="notify_url" id="notify_url" value="">
                <input type="hidden" name="custom" id="sell_id" value="">
                <input type="hidden" name="charset" value="utf-8">
                <input type="hidden" name="rm" value="2">
                <input type="hidden" name="upload" value="1">
                <input type="hidden" name="tax_cart" value="0">
                <input type="hidden" name="item_name_1" value="<?php echo __d('credit', 'Buy Credits')?>">
                <input type="hidden" name="quantity_1" value="1">
                <input type="hidden" name="amount_1" id="amount" value="">
                <br/><br/>
            </form>
        </div>
    <?php endif;?>
    <div>
        <?php foreach($gateways as $gateway):
            $gateway = $gateway['Gateway'];
            $helper = MooCore::getInstance()->getHelper($gateway['plugin'].'_'.$gateway['plugin']);
            if ($helper->checkSupportCurrency($currency['Currency']['currency_code'])):
                ?>
                <form id="formGateway" method="post" action="<?php echo $this->request->base;?>/credits/purchase_credit/<?php echo lcfirst($gateway['name']);?>">
                    <?php echo $this->Form->hidden('sell_id', array('class'=>'sell_id')); ?>
                    <?php echo $this->Form->hidden('gateway_id', array('id' => 'gateway_id','value'=>$gateway['id'])); ?>
                    <h2><?php echo $gateway['name'];?></h2>
                    <p><?php echo $gateway['description'];?></p>
                    <input type="submit" class="btn btn-action btnGateway" value="<?php echo __d('spotlight', 'Pay with %s', $gateway['name']);?>" />
                    <br/><br/>
                </form>
            <?php endif;?>
        <?php endforeach;?>
    </div>

    <?php if(!Configure::read('Credit.credit_paypal_email') && count($gateways) ==  0):?>
        <div class="paypal_content">
            <p><?php echo __d('credit',"Can't make payment now, please contact admin for more details.");?></p>
        </div>
    <?php endif;?>

</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooCredit"], function($, mooCredit) {
        mooCredit.initBuyCreditPaypal('<?php echo $urlReturn;?>', '<?php echo $urlReturnPaypal;?>');
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooCredit'), 'object' => array('$', 'mooCredit'))); ?>
        mooCredit.initBuyCreditPaypal('<?php echo $urlReturn;?>', '<?php echo $urlReturnPaypal;?>');
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>