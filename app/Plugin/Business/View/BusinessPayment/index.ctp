<?php 
	$businessSubscription = MooCore::getInstance()->getHelper('Business_Business');
	$currency = Configure::read('Config.currency');
?>
<div class="bar-content  full_content p_m_10">
    <div class="content_center">
        <h1><?php echo __( 'Select Payment Gateway')?></h1>  
			<div>
        		<?php if ($pay_type == 'business_package'):?>
        			<?php echo __('Your selected plan');?>: <b><?php echo $package['BusinessPackage']['name'].' - '. $businessSubscription->getPackageDescription($package['BusinessPackage'],$currency['Currency']['currency_code']) ?></b>
        		<?php else:?>
        			<?php echo __d('business','Featured business for %s days',$featured_day);?>: <?php echo $price?><?php echo $currency['Currency']['currency_code']?>
        		<?php endif;?>
        	</div> 
            <?php foreach($gateways as $gateway):
                $gateway = $gateway['Gateway'];
                $helper = MooCore::getInstance()->getHelper($gateway['plugin'].'_'.$gateway['plugin']);
                if ($pay_type == 'business_package')
                {
                	$is_recurring = $businessSubscription->isRecurring($package);
					$is_trial = $businessSubscription->isTrial($package);
                }
                else
                {
                	$is_recurring = false;
                	$is_trial = false;
                }
                if ($helper->checkSupportCurrency($currency_code) && (!$is_trial || ($is_trial && $helper->supportTrial()) ) && (!$is_recurring || ($is_recurring && $helper->supportRecurring()) )):
            ?>
		            <form id="formGateway" method="post">
		            	<?php echo $this->Form->hidden('pay_type', array('value'=>$pay_type)); ?>
		            	<?php echo $this->Form->hidden('business_id', array('value'=>$business_id)); ?>
		            	<?php echo $this->Form->hidden('business_package_id', array('value'=>$package_id)); ?>
		            	
		        		<?php if ($pay_type != 'business_package'):?>    	
		            		<?php echo $this->Form->hidden('feature_day', array('value'=>$featured_day)); ?>
		            		<?php echo $this->Form->hidden('price', array('value'=>$price)); ?>
		            	<?php endif;?>
		            	
		            	<?php echo $this->Form->hidden('gateway_id', array('id' => 'gateway_id','value'=>$gateway['id'])); ?>		                
		                <p><?php echo $gateway['description'];?></p>
		                <input name="pay" type="submit" class="btn btn-action btnGateway" value="<?php echo __( 'Pay with').' '.$gateway['name'];?>" />
		                <br/><br/>
		            </form>
            	<?php endif;?>
            <?php endforeach;?>
        
        <div id="formPayment"></div>
    </div>
</div>