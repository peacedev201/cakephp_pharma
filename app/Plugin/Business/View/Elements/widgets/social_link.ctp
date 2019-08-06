<?php $business = $business['Business'];?>
<?php if(!empty($business['facebook']) || !empty($business['twitter']) || !empty($business['linkedin']) || !empty($business['youtube']) || !empty($business['instagram'])):?>
<div class="box2 filter_block bus_social">
    <h3><?php echo __d('business', 'Find us on');?></h3>
    <div class="box_content">
        <?php if(!empty($business['facebook'])):?>
            <a href="<?php echo $this->Business->getFullUrl($business['facebook']);?>" target="_blank">
                <i class="bus_fb"></i>
            </a>
        <?php endif;?>
        <?php if(!empty($business['twitter'])):?>
            <a href="<?php echo $this->Business->getFullUrl($business['twitter']);?>" target="_blank">
                <i class="bus_twitter"></i>
            </a>
        <?php endif;?>
        <?php if(!empty($business['linkedin'])):?>
            <a href="<?php echo $this->Business->getFullUrl($business['linkedin']);?>" target="_blank">
                <i class="bus_linkedin"></i>
            </a>
        <?php endif;?>
        <?php if(!empty($business['youtube'])):?>
            <a href="<?php echo $this->Business->getFullUrl($business['youtube']);?>" target="_blank">
                <i class="bus_utube"></i>
            </a>
        <?php endif;?>
        <?php if(!empty($business['instagram'])):?>
            <a href="<?php echo $this->Business->getFullUrl($business['instagram']);?>" target="_blank">
                <i class="bus_instagram"></i>
            </a>
        <?php endif;?>
    </div>
</div>
<?php endif;?>