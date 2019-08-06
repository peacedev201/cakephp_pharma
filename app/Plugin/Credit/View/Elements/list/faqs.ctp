<ul class="faq_list" id="list-content">
<?php if(!empty($faqs)): ?>
    <?php foreach ($faqs as $faq): ?>
        <li class="full_content p_m_10">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $faq['CreditFaq']['id'] ?>" aria-expanded="true" aria-controls="collapse_<?php echo $faq['CreditFaq']['id'] ?>">
                <?php echo h($faq['CreditFaq']['question']); ?>
            </a>
            <div id="collapse_<?php echo $faq['CreditFaq']['id'] ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_<?php echo $faq['CreditFaq']['id'] ?>">
                <div style="padding-left: 15px;" class="panel-body">
                    <?php echo $faq['CreditFaq']['answer']; ?>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
<?php else: ?>
    <?php echo '<li class="clear text-center" style="width:100%;overflow:hidden">' . __d('credit', 'No more results found') . '</li>';?>
<?php endif; ?>
    <?php if (!empty($more_result)): ?>
        <?php $this->Html->viewMore($more_url) ?>
    <?php endif; ?>
</ul>



