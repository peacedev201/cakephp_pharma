<?php
    $amount = round($params['amount']*Configure::read('Credit.credit_currency_exchange'),1);
?>
<div class="bar-content  full_content p_m_10">
    <div class="content_center">
        <h1><?php echo __d('credit','Checkout')?></h1>
        <div>
            <div><?php echo $params['description'] ?> - <?php echo $params['amount'].' '.$params['currency'].' ('.$amount.' '.__d('credit','credits').')' ?></div>
            <div><?php echo __d('credit','Your current credit balance').': '.$credit?></div>
            <?php if ($credit >= $amount): ?>
            <form method="post">
                <br/>
                <div>
                    <input type="submit" class="btn btn-action btnGateway" value="<?php echo __d('credit','Pay Now') ?>" />
                </div>
            </form>
            <?php else: ?>
                <br />
                <div><?php echo __d('credit',"You don't have enough credit to finish payment process. Please earn more credit or select other payment method to continue");?></div>
                <br />
                <div><input type="button" onclick="window.history.back();" class="btn btn-action btnGateway" value="<?php echo __d('credit','Select other payment method') ?>" /></div>
            <?php endif; ?>
        </div>
    </div>
</div>