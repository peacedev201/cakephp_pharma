<?php if(!$is_app): ?>
<div class="bar-content">
    <div class="content_center">
        <ul class="faq-breadcrumb">
            <li style="list-style: none outside none;">
                <?php $count_breadcrumb=  1 ?>
                <?php foreach ($breadcrumb as $brc): ?>
                    <a href="<?php echo($this->request->base . $brc['link']); ?>"><?php echo $brc['name']; ?></a>
                    <?php if($count_breadcrumb != count($breadcrumb)): ?>
                    <i class="glyphicon glyphicon-chevron-right" style="color: rgba(0, 0, 0, 0.54)" ></i>
                    <?php endif; ?>
                    <?php $count_breadcrumb++; ?>
                <?php endforeach; ?>
            </li>
        </ul>
        <ul id="list-content" class="faq-content-list">
            <?php if($is_search): ?>
            <h4><?php echo __d('faq', "We found %s results", count($category['Faqs'])); ?></h4>
                <?php echo $this->element('lists/faqs_detail', array('url_more' => $url_more, 'category' => $category), array('plugin' => 'Faq')); ?>
            <?php else: ?>
                <?php if (count($categories)): ?>
                    <?php echo $this->element('lists/faqs', array('is_view_more' => $is_view_more, 'url_more' => $url_more, 'categories' => $categories, 'floor_category'=>$floor_category), array('plugin' => 'Faq')); ?>
                <?php else: ?>		
                    <li class="clear text-center"><?php echo __d('faq', 'No more results found'); ?></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php endif; ?>