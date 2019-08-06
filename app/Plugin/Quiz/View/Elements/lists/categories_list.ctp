<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $categories = $this->requestAction("quizzes/categories_list"); ?>

<?php if(!empty($categories)): ?>
<ul class="list2 quiz-menu-list browseQuizzes">
    <li class="cat-header <?php if (Configure::read('core.enable_category_toggle')): echo 'cat_toggle'; endif; ?>"><?php echo __d('quiz', 'Categories'); ?></li>
    <?php foreach ($categories as $cat): ?>
        <?php if ($cat['Category']['header']): ?>
            <li class="category_header"><?php echo $cat['Category']['name']; ?></li>
            <?php foreach ($cat['children'] as $subcat): ?>
            <li id="cat_<?php echo $subcat['Category']['id']; ?>" class="sub-cat<?php echo (!empty($cat_id) && $cat_id == $subcat['Category']['id']) ? ' current' : ''; ?>">
                <a data-url="<?php echo $this->request->base . '/quizzes/browse/category/' . $subcat['Category']['id']; ?>" <?php if (!empty($subcat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($subcat['Category']['description']); ?>"<?php endif; ?> href="<?php echo $this->request->base . '/quizzes/index/' . $subcat['Category']['id'] . '/' . seoUrl($subcat['Category']['name']); ?>"><?php echo $subcat['Category']['name']; ?> 
                    <span class="badge_counter"><?php echo $subcat['Category']['item_count']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li id="cat_<?php echo $cat['Category']['id']; ?>"<?php echo (!empty($cat_id) && $cat_id == $cat['Category']['id']) ? ' class="current"' : ''; ?>>
                <a data-url="<?php echo $this->request->base . '/quizzes/browse/category/' . $cat['Category']['id']; ?>" <?php if (!empty($cat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($cat['Category']['description']); ?>"<?php endif; ?> href="<?php echo $this->request->base . '/quizzes/index/' . $cat['Category']['id'] . '/' . seoUrl($cat['Category']['name']); ?>"><?php echo $cat['Category']['name']; ?> 
                    <span class="badge_counter"><?php echo $cat['Category']['item_count']; ?></span>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
<?php endif; ?>