<?php if (count($category_similar['Faqs'])): ?>
<div class="bar-content">
    <div class="content_center">
        <ul id="list-content" class="faq-content-list">
            <h4><?php echo __d('faq', "Similar FAQ"); ?></h4>
                    <?php echo $this->element('lists/faqs_detail', array('category' => $category_similar), array('plugin' => 'Faq')); ?>
        </ul>
    </div>
</div>
<?php endif; ?>