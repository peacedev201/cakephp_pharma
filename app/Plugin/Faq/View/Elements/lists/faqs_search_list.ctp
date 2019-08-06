<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooFaq"], function($, mooFaq) {
            mooFaq.initCreateFaq();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooFaq'), 'object' => array('$', 'mooFaq'))); ?>
    mooFaq.initCreateFaq();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
    
<?php if (!empty($faqs) && count($faqs) > 0) : ?>
    <ul id="list-content" class="search-list-filter ">
    <?php foreach ($faqs as $faq): ?>
        <li class="full_content fag-listing" style="list-style: none outside none;">
            <div class="faq_info">
                <h5 class="js_drop_down_detail_faq">
                    <a href="javascript:void(0)">
                        <?php echo $faq['Faq']['title']; ?>
                    </a>
                </h5>
                <div class="list_body" style="display: none;">
                    <ul class="list2 menu-list current" id="browse">
                        <li class="full_content faq_list">
                            <div>
                                <p>
                                    <?php echo $faq['Faq']['body']; ?>		
                                </p>
                            </div>
                        </li>	
                    </ul>
                    <?php if ($faq['Faq']['alow_comment'] && $faq['Faq']['comment_count'] != "0"): ?>		
                        <a href="<?php echo $faq['Faq']['moo_href']; ?>" class="">
                            <i class="fa fa-comments-o"></i>
                            <?php echo __d('faq', "View all %s comments", $faq['Faq']['comment_count']); ?>
                        </a>
                    <?php else: ?>	
                        <a href="<?php echo $faq['Faq']['moo_href']; ?>" >
                            <?php echo __d('faq', "View"); ?>
                        </a>
                    <?php endif; ?>	
                </div>
            </div>
        </li>
    <?php endforeach; ?>
       </ul>
    <?php if (isset($more_url) && $is_more_url): ?>
        <?php $this->Html->viewMore($more_url) ?>
    <?php endif; ?>
<?php endif; ?>



