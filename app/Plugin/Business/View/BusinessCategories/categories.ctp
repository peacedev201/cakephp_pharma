<?php echo $this->Element('mobile_menu');?>
<div class="box2 box-bu-category">
    <h3> 
        <?php echo __d('business', 'Business Categories');?>
    </h3>
    <div class="box_content cate_list">
    <div class="row">
           
            <ul class="cat_by_alphabet" >
            <li><span><?php echo __d('business', 'Category by alphabet');?></span></li>
                <?php
                $range = array(
                    __d('business', 'A'),
                    __d('business', 'B'),  
                    __d('business', 'C'),  
                    __d('business', 'D'),  
                    __d('business', 'E'),  
                    __d('business', 'F'),  
                    __d('business', 'G'),  
                    __d('business', 'H'),  
                    __d('business', 'I'),  
                    __d('business', 'J'),  
                    __d('business', 'K'),  
                    __d('business', 'L'),  
                    __d('business', 'M'),  
                    __d('business', 'N'),  
                    __d('business', 'O'),  
                    __d('business', 'P'),  
                    __d('business', 'Q'),  
                    __d('business', 'R'),  
                    __d('business', 'S'),  
                    __d('business', 'T'),  
                    __d('business', 'U'), 
                    __d('business', 'V'),  
                    __d('business', 'W'),  
                    __d('business', 'X'),  
                    __d('business', 'Y'),  
                    __d('business', 'Z'),
                );
                foreach ($range as $char):?>
                <li <?php if($param != null && !is_numeric($param) && $param == $char):?>class="on"<?php endif;?>>
                    <a href="<?php echo $this->request->base;?>/categories/<?php echo $char;?><?php echo $is_app ? "?app_no_tab=1" : "";?>">
                        <?php echo $char;?>
                    </a>
                </li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="row cat_list_page cate_listing_column">
            <?php if($categories != null):?>
                <?php 
                    $item_per_column = round(count($categories) / 3);
                    foreach($categories as $k => $category):
                        $k = $k + 1;
                        $category_children = !empty($category['children']) ? $category['children'] : null;
                        $category = $category['BusinessCategory'];
                ?>
                    <?php if($k == 1 || $k % ($item_per_column + 1) == 0):?>
                    <!--<div class="col-md-6">-->
                    <?php endif;?>
                    <div class="category-clone col-md-6">
                        <a class="b sitemap-menu-item" href="<?php echo $category['moo_href'];?>">
                            <?php echo $category['name'];?>
                        </a>
                        
                        <br>
                        <?php if($category_children != null):?>
                            <?php foreach($category_children as $category_child):
                                $category_child = $category_child['BusinessCategory'];
                            ?>
                               
                                <a class="cate-child sitemap-menu-item" href="<?php echo $category_child['moo_href'];?>">
                                    <?php echo $category_child['name'];?>
                                </a>
                                
                                <br>
                            <?php endforeach;?>
                        <?php endif;?>
                                </div>
                    <?php if(($k == count($categories) || $k % $item_per_column == 0)):?>
                    <!--</div>-->
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
        </div>
        </div>
</div>