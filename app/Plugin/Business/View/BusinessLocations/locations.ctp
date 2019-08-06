<?php echo $this->Element('mobile_menu');?>
<div class="box2">
    <h3> 
        <?php echo __d('business', 'Business Locations');?>
    </h3>
    <div class="box_content">
        <div class="row cat_list_page cate_listing_column">
            <?php if($locations != null):?>
                <?php 
                    $item_per_column = round(count($locations) / 3);
                    foreach($locations as $k => $location):
                        $k = $k + 1;
                        $location_children = !empty($location['children']) ? $location['children'] : null;
                        $location = $location['BusinessLocation'];
                ?>
                    <?php if($k == 1 || $k % ($item_per_column + 1) == 0):?>
                    <!--<div class="col-md-6">-->
                    <?php endif;?>
                    <div class="category-clone col-md-6">
                        <a class="b sitemap-menu-item" href="<?php echo $location['moo_href'];?>">
                            <?php echo $location['name'];?>
                        </a>
                        <br>
                        <?php if($location_children != null):?>
                            <?php foreach($location_children as $location_child):
                            $location_child = $location_child['BusinessLocation'];
                            ?>
                               
                                <a class="cate-child sitemap-menu-item" href="<?php echo $location_child['moo_href'];?>">
                                    <?php echo $location_child['name'];?>
                                </a>
                                
                                <br>
                            <?php endforeach;?>
                        <?php endif;?>
                                </div>
                    <?php if(($k == count($locations) || $k % $item_per_column == 0)):?>
                    <!--</div>-->
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
        </div>
        </div>
</div>