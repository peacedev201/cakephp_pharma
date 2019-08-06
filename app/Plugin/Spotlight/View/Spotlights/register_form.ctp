<?php if($this->request->is('ajax')) $this->setCurrentStyle(4);?>

<div class="title-modal">
    <?php echo __d('spotlight','Spotlight Register Form')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
   
</div>
<div class="modal-body">
    <div>
    	<p></p><?php echo __d('spotlight',"Your profile display in Spotlight %s days.", $period);?></p>

    </div>
	<?php if($price > 0 && !Configure::read('Spotlight.spotlight_email') && count($gateways) ==  0):?>
	<div>
		<p><?php echo __d('spotlight',"Can't make payment now, please contact admin for more details.");?></p>
	</div>
	<?php else:?>
		<?php if($price > 0):?>
		<?php if(Configure::read('Spotlight.spotlight_email')):?>
    	<div <?php if(count($gateways)):?>class="paypal_content"<?php endif;?>>
			<form id="buyForm" action="<?php if(Configure::read('Spotlight.spotlight_test_mode')):?>https://www.sandbox.paypal.com/cgi-bin/webscr<?php else:?>https://www.paypal.com/cgi-bin/webscr<?php endif;?>" enctype="application/x-www-form-urlencoded">
				<div>
					<h2><?php echo __d('spotlight','PayPal');?></h2>
					<p><?php echo __d('spotlight','Payment spotlight - %s %s', number_format($price,2), $currency['Currency']['currency_code']);?></p>
				</div>
				<div class="clear"></div>
				<div>
					<input type="submit" class="btn btn-action" id="btnPaypal" value="<?php echo __d('spotlight',  'Pay with PayPal')?>" >
				</div>
				<input type="hidden" name="cmd" value="_cart">
				<input type="hidden" name="business" value="<?php echo Configure::read('Spotlight.spotlight_email');?>">
				<input type="hidden" name="currency_code" value="<?php echo $currency['Currency']['currency_code']; ?>">
				<input type="hidden" name="return" value="<?php echo $siteUrl.$this->base ?>/spotlights/purchased/<?php echo $user_id;?>">
				<input type="hidden" name="cancel_return" value="<?php echo $siteUrl.$this->base ?>">
				<input type="hidden" name="notify_url" value="<?php echo $siteUrl.$this->base ?>/spotlights/returnPaypal/<?php echo $user_id;?>">
				<input type="hidden" name="custom" value="<?php echo $user_id;?>">
				<input type="hidden" name="charset" value="utf-8">
				<input type="hidden" name="rm" value="2">
				<input type="hidden" name="upload" value="1">
				<input type="hidden" name="tax_cart" value="0">
				<input type="hidden" name="item_name_1" value="<?php echo __d('spotlight', 'Join Spotlight')?>">
				<input type="hidden" name="quantity_1" value="1">
				<input type="hidden" name="amount_1" value="<?php echo number_format($price,2);?>">
			</form>
		</div>
		<?php endif;?>
		<div>
			<?php foreach($gateways as $gateway):
				$gateway = $gateway['Gateway'];
				$helper = MooCore::getInstance()->getHelper($gateway['plugin'].'_'.$gateway['plugin']);
				if ($helper->checkSupportCurrency($currency['Currency']['currency_code'])):
					?>
					<form id="formGateway" method="post" action="<?php echo $this->request->base;?>/spotlights/purchase_spotlight/<?php echo lcfirst($gateway['name']);?>">
						<?php echo $this->Form->hidden('gateway_id', array('id' => 'gateway_id','value'=>$gateway['id'])); ?>
						<h2><?php echo $gateway['name'];?></h2>
						<p><?php echo $gateway['description'];?></p>
						<input type="submit" class="btn btn-action btnGateway" value="<?php echo __d('spotlight', 'Pay with %s', $gateway['name']);?>" />
						<br/><br/>
					</form>
				<?php endif;?>
			<?php endforeach;?>
		</div>
		<?php else:?>
		<div>
			<form id="formGateway" method="post" action="<?php echo $this->request->base;?>/spotlights/purchase_spotlight/paypal">
				<input type="submit" class="btn btn-action btnGateway" value="<?php echo __d('spotlight', 'Join Free');?>" />
				<br/><br/>
			</form>
		</div>
		<?php endif;?>
	<?php endif;?>
</div>

