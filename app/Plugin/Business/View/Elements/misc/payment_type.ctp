<?php if(!empty($payments)):?>
    <?php foreach($payments as $payment):?>

        <div class="col-md-12">
            <img src="<?php echo $this->Business->getImage('/business/images/'.$payment['icon']);?>" title="<?php echo $payment['name'];?>"/>
            <?php echo $payment['name'];?>
        </div>
    <?php endforeach;?>
    <div class="clear"></div>
<?php endif;?>