<?php
$categories = $this->requestAction(
        "groups/categories_list/"
);
?>
    <?php foreach ($categories as $key=>$cat): ?>
    <?php if($key == 1): ?>
    <li>
    <span id="cat_more" data-toggle="dropdown" class="visible-xs visible-sm"><i class="material-icons">add</i></span>
    <ul class="cat_more dropdown-menu" aria-labelledby="cat_more">
    <?php endif; ?>
        <?php if ($cat['Category']['header']): ?>
            <li class="category_header">
                <em id="cat_menu_<?php echo $cat_id; ?>" data-toggle="dropdown" ><?php echo $cat['Category']['name'] ?></em>
                <ul class="sub_cat dropdown-menu" aria-labelledby="cat_menu_<?php echo $cat_id; ?>">
                <?php foreach ($cat['children'] as $subcat): ?>

                    <li id="cat_<?php echo $subcat['Category']['id'] ?>" class="sub-cat <?php if (!empty($cat_id) && $cat_id == $subcat['Category']['id']) echo 'current'; ?>">
                        <a data-url="<?php echo $this->request->base ?>/groups/browse/category/<?php echo $subcat['Category']['id'] ?>" <?php if (!empty($subcat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($subcat['Category']['description']) ?>"<?php endif ?> href="<?php echo $this->request->base ?>/<?php echo $this->request->controller ?>/index/<?php echo $subcat['Category']['id'] ?>/<?php echo seoUrl($subcat['Category']['name']) ?>"><?php echo $subcat['Category']['name'] ?> 
                            </a>
                    </li>

                <?php endforeach; ?>
                </ul>
            </li>
            
        <?php else: ?>

            <li id="cat_<?php echo $cat['Category']['id'] ?>" <?php if (!empty($cat_id) && $cat_id == $cat['Category']['id']) echo 'class="current"'; ?>>
                <a class="json-view" data-url="<?php echo $this->request->base ?>/groups/browse/category/<?php echo $cat['Category']['id'] ?>" <?php if (!empty($cat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($cat['Category']['description']) ?>"<?php endif ?> href="<?php echo $this->request->base ?>/<?php echo $this->request->controller ?>/index/<?php echo $cat['Category']['id'] ?>/<?php echo seoUrl($cat['Category']['name']) ?>"><?php echo $cat['Category']['name'] ?> 
                    </a>
            </li>
        <?php endif; ?>
   <?php if($key == count($categories)): ?>
        
    </ul>
    </li>
    <?php endif; ?>
 <?php endforeach; ?>
