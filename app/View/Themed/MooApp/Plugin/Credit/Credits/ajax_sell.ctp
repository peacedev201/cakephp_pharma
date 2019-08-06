<div class="bar-content">
    <div class="content_center">
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
                                    echo number_format($balance['CreditBalances']['current_credit'], 0);
                                    }
                                    else{ echo '0';}?>
                            </em>  
                        </p>
                    </div>
                </div>
                <div class="buy-credit-ask"><?php echo __d('credit', 'How many credits would you like to add to your account?') ?></div>
            <form id="buyCreditForm" action="<?php if(Configure::read('Credit.credit_test_mode')):?>https://www.sandbox.paypal.com/cgi-bin/webscr<?php else:?>https://www.paypal.com/cgi-bin/webscr<?php endif;?>" enctype="application/x-www-form-urlencoded">
            	<div>
            		<?php if(!empty($sells)):?>
            			<?php foreach ($sells as $sell): ?>
        						<div class="sell_item">
        							<input type="radio" name="sell_selected" value="<?php echo $sell['CreditSells']['id'].'_'.$sell['CreditSells']['price'].'_'.$sell['CreditSells']['credit'] ?>"> <?php echo number_format($sell['CreditSells']['credit'], 0) .' '. __d('credit', 'Credits for') . ' $'. number_format($sell['CreditSells']['price'], 2) ?>	
                                </div>
        				<?php endforeach; ?>	
            		<?php else:?>
            			<div class="clear text-center"> <?php echo __d('credit',  'No more credit package found') ?> </div>
            		<?php endif;?>
            	</div>
                <div class="buy-credit-btn">
            	<input type="submit" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" id="btnPaypal" value="<?php echo __d('credit',  'CONTINUE')?>" >
            	</div>
            	<input id="cmd" type="hidden" value="_xclick" name="cmd">
                <input id="business" type="hidden" value="<?php echo Configure::read('Credit.credit_paypal_email');?>" name="business">
                <input id="item_name" type="hidden" value="<?php echo __d('credit', 'Buy Credits')?>" name="item_name">
                <input id="currency_code" type="hidden" value="USD" name="currency_code">
                <input id="notify_url" type="hidden" value="<?php echo $siteUrl.$this->base ?>" name="notify_url">
                <input id="return" type="hidden" value="" name="return">
                <input id="amount" type="hidden" value="" name="amount">                   
            </form>

        </div>
    </div>
</div>

<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooCredit'), 'object' => array('mooCredit'))); ?>
    mooCredit.initBuyCreditPaypal('<?php echo $urlReturn;?>', '<?php echo $urlReturnPaypal;?>');
<?php $this->Html->scriptEnd(); ?>
