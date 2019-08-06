<?php 
$proCats = $this->requestAction(STORE_PATH.'product_categories/load_product_category_list/');
?>
<?php if($proCats != null):?>
<ul class="category-wrapper thumbs noscript hidden-sm hidden-xs">
    <?php foreach($proCats as $proCat):
        $proCat_children = $proCat['children'];
        $proCat = $proCat['ProductCategory'];
    ?>
    <li class="category-main">
        <div class="category-name <?php //if($product_category_id == $proCat['id']):?>current-cat<?php //endif;?>">
            <a href="<?php echo STORE_URL;?>products/category/<?php echo $proCat['id'];?>">
                <?php echo $proCat['name'];?>
            </a>
        </div>
        <?php if($proCat_children != null):?>
        <div class="category-child">
            <ul>
                <?php foreach($proCat_children as $child):
                    $child = $child['ProductCategory'];
                ?>
                <li>
                    <a href="<?php echo STORE_URL;?>products/category/<?php echo $child['id'];?>">
                        <?php echo $child['name'];?>
                    </a>
                </li>
                <?php endforeach;?>
            </ul>
        </div>
        <?php endif;?>
    </li>
    <?php endforeach;?>
</ul>
<div class="clear"></div>

<ul class="m-category-wrapper visible-xs visible-sm ">
     <?php foreach($proCats as $proCat):
        $proCat_children = $proCat['children'];
        $proCat = $proCat['ProductCategory'];
    ?>
    <li><a aria-controls="m-cate-collapse<?php echo $proCat['id'];?>" aria-expanded="false" href="#m-my-cate-collapse<?php echo $proCat['id'];?>" data-toggle="collapse" class="no-ajax colappsed_top">
            <?php echo $proCat['name'];?> <span class="pull-right material-icons">expand_more</span>
        </a>
        <div id="m-my-cate-collapse<?php echo $proCat['id'];?>" class=" collapse ">
            <div class="slLeftexpand">
                <ul class="list2 menu-list">
                    <?php foreach($proCat_children as $child):
                    $child = $child['ProductCategory'];
                        ?>
                    <li class="no-ajax">
                        <a href="<?php echo STORE_URL;?>products/category/<?php echo $child['id'];?>">
                        <?php echo $child['name'];?><span class="pull-right material-icons">chevron_right</span>
                        </a>
                    </li>
                     <?php endforeach;?>
                   
                </ul>
            </div>
        </div>
    </li>
    <?php endforeach;?>
</ul>
<?php endif;?>