<?php
if (count($categories)):
    echo $this->element('lists/faqs');
else:
    ?>
    <li>
        <?php echo __d('faq', 'No more results found') ?>
    </li>
<?php endif; ?>