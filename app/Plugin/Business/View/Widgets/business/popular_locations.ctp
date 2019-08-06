<?php if(!empty($business_locations)): ?>
<div class="box2 filter_block popular_location">
    <h3><?php echo __d('business', 'Locations');?></h3>
    <?php if($business_locations != null):?>
        <div class="box_content box_widget ">
            <?php foreach($business_locations as $location):
                $location = $location['BusinessLocation'];
            ?>
                <div class="col-xs-6 col-md-6"> 
                    <a href="<?php echo $location['moo_href'];?>">
                        <?php echo $location['name'];?>
                    </a>
                </div>
            <?php endforeach;?>
            <div class="clear"></div>
            <a class="show_more" href="<?php echo $this->request->base;?>/locations">
                <?php echo __d('business', 'Show more');?>
            </a>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>