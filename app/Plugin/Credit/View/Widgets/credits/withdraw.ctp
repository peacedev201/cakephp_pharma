<?php if($uid) {
    if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
?>
<div class="box2 send_credit">
    <?php if($title_enable): ?>
        <h3><?php echo $title; ?></h3>
    <?php endif; ?>
    <div class="box_content ">
        <?php echo __d('credit','You can send money withdrawal request to site admin by clicking on the below button');?>
        <div class="clear"></div>
        <div class="btt_buy_credit">
        <a href="<?php echo $this->request->base ?>/credits/index/withdraw_request" class="btn btn-action"><?php echo __d('credit','Withdrawal Request')?></a>
        </div>
    </div>
</div>
<?php } ?>
