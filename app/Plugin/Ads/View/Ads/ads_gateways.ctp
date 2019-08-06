<div class="bar-content">
    <div class="content_center">
        <div class="paypal_content">
            <div>
                <p><?php echo __d('ads','Placement name: %s', $adsPlacement['name']); ?></p>
                <p><?php echo __d('ads','Client name: %s', $adsCampaign['client_name']); ?></p>
                <p><?php echo __d('ads','Email: %s', $adsCampaign['email']); ?></p>
                <p><?php echo  __d('ads','Click limit: ').' '. ($adsPlacement['click_limit'] == 0?__d('ads','Unlimited'):$adsPlacement['click_limit']); ?></p>
                <p><?php echo __d('ads','View limit: ').' '. ($adsPlacement['view_limit'] == 0?__d('ads','Unlimited'):$adsPlacement['view_limit']); ?></p>
                <p><?php echo sprintf(__d('ads','Price: %s %s'),$adsPlacement['price'],$currency['Currency']['currency_code']);   ?></p>
            </div>
            <div style="margin-bottom: 10px;">
                <h2><?php echo __d('ads','Paypal'); ?></h2>
                <a class="btn btn-action" href="<?php echo Router::url('/', true) . 'ads/transaction/' . $transaction['verification_code']; ?>"><?php echo __d('ads','Pay with paypal'); ?></a>

            </div> 
            <div>   
            <?php foreach($gateways as $gateway):
                    $gateway = $gateway['Gateway'];
                    $helper = MooCore::getInstance()->getHelper($gateway['plugin'].'_'.$gateway['plugin']);
                    if ($helper->checkSupportCurrency($currency['Currency']['currency_code'])):
                            ?>
                <form id="formGateway" method="post" action="<?php echo $this->request->base;?>/ads/purchase_ads/<?php echo lcfirst($gateway['name']).'/'.$transaction['id'];?>">
                                    <?php echo $this->Form->hidden('gateway_id', array('id' => 'gateway_id','value'=>$gateway['id'])); ?>
                    <h2><?php echo $gateway['name'];?></h2>
                    <p><?php echo $gateway['description'];?></p>
                    <input type="submit" class="btn btn-action btnGateway" value="<?php echo __d('ads', 'Pay with %s', $gateway['name']);?>" />
                    <br/><br/>
                </form>
                    <?php endif;?>
            <?php endforeach;?>
            </div>
        </div>
    </div>
</div>