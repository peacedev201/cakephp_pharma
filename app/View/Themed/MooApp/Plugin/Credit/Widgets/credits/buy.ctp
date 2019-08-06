<?php if ($uid): ?>
<?php
//    $creditHelper = MooCore::getInstance()->getHelper('Credit_Credit');
//    $creditHelper->doUpdateRankUser(7000,1);
if(empty($title)) $title = __d('credit', 'Buy Credits');
if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
?>

<div class="box2 buy_credit">
    <?php if($title_enable): ?>
        <h3><?php echo $title; ?></h3>
    <?php endif; ?>
    <div class="box_content ">
	   <div class="credit_buy_desc">
           <?php echo __d('credit', 'You can buy credits using your PayPal account, just click on Buy Credits button');?>
       </div>
       <div class="btt_buy_credit">
            <a class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="<?php echo $this->request->base?>/credits/ajax_sell" data-target="#themeModal" data-toggle="modal" class="" title="<?php echo __d('credit', 'Buy Credits')?>"><?php echo __d('credit', 'Buy Credits')?>! </a>
       </div>
	</div>
</div>
<?php endif;?>
