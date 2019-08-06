<?php $faqHelper = MooCore::getInstance()->getHelper('Faq_Faq'); ?>
<div class="box2 filter_block">
    <h3 class="cat-header-faq"><?php echo __d('faq', 'Categories') ?></h3>
    <div class="box_content">
        <ul class="list2 menu-list" id="faq-category">
            <li id="category-faq-name-home-selected"class="<?php if($category_id == 0 || (!empty($selected_category) && $selected_category['FaqHelpCategorie']['id']==$category_id)) echo 'current' ?>">
                <?php if (empty($selected_category)): ?>
                    <a class="json-view a-faq-full" data-url="<?php echo $this->request->base ?>/faq/faqs" href="<?php echo $this->request->base ?>/faq/faqs"><?php echo __d('faq', 'ALL') ?></a>
                <?php else: ?>
                	<?php $thumb = $faqHelper->getImage($selected_category);?>
                    <?php if($thumb): ?>
                    <img height="24" width="24" class="icon-cate-faq " src="<?php echo $thumb;?>">
                    <?php endif; ?>
                    <a id="category-faq" class="json-view a-faq-full" data-url="<?php echo $this->request->base ?>/faq/faqs/index/category:<?php echo $selected_category['FaqHelpCategorie']['id'] ?>" href="<?php echo $this->request->base ?>/faq/faqs/index/category:<?php echo $selected_category['FaqHelpCategorie']['id'] ?>"><?php echo $selected_category['FaqHelpCategorie']['name'] ?></a>
                <?php endif; ?>
            </li>
            <div class="category-faq-child">
                <?php foreach ($categories_menu as $category): ?>
                <li style="display: flex;" id="<?php if($faqHelper->checkCateHaveChild($category['FaqHelpCategorie']['id'],true)) echo 'category-faq-name-child' ?><?php if($category['FaqHelpCategorie']['id'] == $category_id) echo '-selected' ?>" class="<?php if($category['FaqHelpCategorie']['id'] == $category_id) echo 'current' ?>">
                	<?php $thumb = $faqHelper->getImage($category);?>
                    <?php if($thumb): ?>
                    <img height="24" width="24" class="icon-cate-faq" src="<?php echo $thumb?>">
                    <?php endif; ?>
                        <a id="category-faq"class="json-view" href="<?php echo $this->request->base ?>/faq/faqs/index/category:<?php echo $category['FaqHelpCategorie']['id'] ?>" data-url="<?php echo $this->request->base ?>/faq/faqs/index/category:<?php echo $category['FaqHelpCategorie']['id'] ?>"><?php echo $category['FaqHelpCategorie']['name'] ?></a>
                    </li>
                <?php endforeach; ?>
            </div>
            <li class="separate"></li>
        </ul>
    </div>
</div>	
