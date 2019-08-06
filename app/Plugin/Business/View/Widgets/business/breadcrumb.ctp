<div class="box2">
    <ul class="breadcrumb">
        <?php
        if($controller_name == "business" && $action_name == "create"):?>
        <li>
            <a href="<?php echo $this->request->base.'/businesses';?>">
                    <?php echo __d('business', 'Business');?>
            </a>
        </li>
        <li>
            <a href="<?php echo $this->request->base.'/businesses/create';?>">
                    <?php echo __d('business', 'Add New Business');?>
            </a>
        </li>
        <?php elseif($controller_name == "business" && $action_name == "search"):?>
            <li>
                <a href="<?php echo $this->request->base . '/businesses';?>">
                        <?php echo __d('business', 'Home');?>
                </a>
            </li>
            <?php if(isset($breadcrumb['BusinessLocation'])):?>
                <li>
                    <a href="<?php echo $this->request->base . '/locations';?>">
                        <?php echo __d('business', 'Locations');?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $breadcrumb['BusinessLocation']['moo_href'];?>">
                        <?php echo $breadcrumb['BusinessLocation']['name'];?>
                    </a>
                </li>
            <?php elseif(isset($breadcrumb['BusinessCategory'])):?>
                <li>
                    <a href="<?php echo $this->request->base . '/categories';?>">
                        <?php echo __d('business', 'Categories');?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $breadcrumb['BusinessCategory']['moo_href'];?>">
                        <?php echo $breadcrumb['BusinessCategory']['name'];?>
                    </a>
                </li>
            <?php else:?>
                <li>
                    <a href="<?php echo $current_link;?>">
                        <?php echo __d('business', 'Search results');?>
                    </a>
                </li>
            <?php endif;?>
        <?php elseif(!empty($cat_paths) && !empty($business)):?>
        <li>
            <a href="<?php echo $this->request->base.'/businesses';?>">
                <?php echo __d('business', 'Home');?>
            </a>
        </li>
        <?php 
            $location_name = trim($business['BusinessLocation']['name']);
            if(!empty($location_name)):
        ?>
        <li>
            <a href="<?php echo $business['BusinessLocation']['moo_href'];?>">
                <?php echo $location_name;?>
            </a>
        </li>
        <?php endif;?>
        <li>
            <a href="<?php echo $this->request->base.'/categories';?>">
                <?php echo __d('business', 'Category');?>
            </a>
        </li>
        <?php foreach($cat_paths as $k => $category):?>
        <li>
            <a href="<?php echo $category['BusinessCategory']['moo_href'];?>">
                <?php echo $category['BusinessCategory']['name'];?>
            </a>
        </li>
        <?php endforeach;?>
        <?php endif;?>
    </ul>
</div>