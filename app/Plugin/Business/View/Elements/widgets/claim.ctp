<!-- Can change phrase again -->
<?php if($bClaim): ?>
<div class="box2 filter_block">
    <div class="box2 filter_block claim_bus_block">
        <h3><?php echo __d('business', 'Is this your business?');?></h3>
        <div class="box_content">
            <p><?php echo __d('business', "If this is your business, you can claim the listing and add extra information."); ?></p>
            <a href="<?php echo $this->request->base; ?>/businesses/claims/<?php echo $business['Business']['id']; ?><?php echo $is_app ? "?app_no_tab=1" : "";?>" class="button <?php if($uid == null):?>non_login<?php endif;?>">
                <?php echo __d('business', 'Claim it now') ?>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>