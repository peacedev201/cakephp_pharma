<?php
if (count($category)):
    echo $this->element('lists/faqs_detail');
else:
    ?>
    <li>
        <?php echo __d('faq', 'No more results found') ?>
    </li>
<?php endif; ?>