
<div class="bar-content  full_content p_m_10">
    <div class="content_center">
        <h1><?php echo __d( 'forum','Select Payment Gateway')?></h1>
			<div>
        		<?php echo __d('forum','Pin topic for %s days',$time);?>: <?php echo $price?> <?php echo $currency['Currency']['currency_code']?>
        	</div> 
            <?php foreach($gateways as $gateway):
                $gateway = $gateway['Gateway'];
                $helper = MooCore::getInstance()->getHelper($gateway['plugin'].'_'.$gateway['plugin']);
                if ($helper->checkSupportCurrency($currency['Currency']['currency_code']) ):
            ?>
		            <form id="formGateway" method="post">
		            	<?php echo $this->Form->hidden('time', array('value'=>$time)); ?>
		            	<?php echo $this->Form->hidden('id', array('value'=>$id)); ?>
		            	
		            	<?php echo $this->Form->hidden('gateway_id', array('id' => 'gateway_id','value'=>$gateway['id'])); ?>		                
		                <p><?php echo $gateway['description'];?></p>
		                <input name="pay" type="submit" class="btn btn-action btnGateway" value="<?php echo __d('forum','Pay with').' '.$gateway['name'];?>" />
		                <br/><br/>
		            </form>
            	<?php endif;?>
            <?php endforeach;?>
        
        <div id="formPayment"></div>
    </div>
</div>