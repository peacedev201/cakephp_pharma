<?php if (count($categories)): ?>
    <?php foreach ($categories as $category): ?>
        <li class="full_content faq_list">
            <?php if ($category['Faqs']): ?>
                <?php $link_category = $this->request->base . '/faqs/index/category:' . $category['FaqHelpCategorie']['id']; ?>
                <?php if ($floor_category != 1): ?>
                    <?php $link_category = $link_category . '/onlycate:' . $category['FaqHelpCategorie']['id'] ?>
                <?php endif; ?>
                <h4> <a href="<?php echo $link_category ?>"><?php echo $category['FaqHelpCategorie']['name']; ?></a></h4>
                <ul id="faq-list-content" class="faq-content-list">
                    <?php if (count($category['Faqs'])): ?>
                        <?php echo $this->element('lists/faqs_detail', array('url_more' => $url_more, 'category' => $category), array('plugin' => 'Faq')); ?>
                    <?php else: ?>
                        <li class="clear text-center"><?php echo __d('faq', 'No more results found'); ?></li>
                    <?php endif; ?>
                </ul>
            <?php else: ?>
            <li class="clear text-center"><?php echo __d('faq', 'No more results found'); ?></li>
        <?php endif; ?>
        </li>	
    <?php endforeach; ?>
    <?php if (isset($is_view_more) && $is_view_more): ?>
        <?php $this->Html->viewMore($url_more) ?>
    <?php endif; ?>
<?php else: ?>
    <li class="clear text-center"><?php echo __d('faq', 'No more results found'); ?></li>
<?php endif; ?>