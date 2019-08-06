<?php if(!empty($business_categories)): ?>
    <div class="box2 filter_block bus-cat">
        <h3><?php echo __d('business', 'Categories');?></h3>
        <div class="box_content box_widget">
            <?php if($business_categories != null):?>
                <ul>
                    <?php foreach($business_categories as $k => $business_category):
                        $business_category = $business_category['BusinessCategory'];
                    ?>
                        <li>
                            <a href="<?php echo $this->request->base.'/business_search/'.seoUrl($business_category['name']).'/'.$business_category['id'];?>">
                                <?php echo $business_category['name'];?>
                            </a>
                        </li>
                    <?php endforeach;?>
                </ul>
            <?php endif;?>
            <a class="show_more" href="<?php echo $this->request->base;?>/categories">
                <?php echo __d('business', 'Show more');?>
            </a>
        </div>
    </div>
<?php endif; ?>