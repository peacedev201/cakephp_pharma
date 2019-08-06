
<?php foreach ($category['Faqs'] as $faq): ?>
    <li class="full_content fag-listing" style="list-style: none outside none;">
        <div class="faq_info">
            <h5 class="js_drop_down_detail_faq">
                <a href="javascript:void(0)">
                    <?php echo $faq['Faq']['title']; ?>
                </a>
            </h5>
            <div class="list_body" style="display: none;">
                <div class="full_content p_m_10" id="faq-content">
                     <?php echo $faq['Faq']['body']; ?>	
                </div>
                <a href="javascript:void(0);" share-url="<?php
                echo $this->Html->url(array(
                    'plugin' => FALSE,
                    'controller' => 'share',
                    'action' => 'ajax_share',
                    'Faq_Faq',
                    'id' => $faq['Faq']['id'],
                    'type' => 'faq_item_detail'
                        ), true);
                ?>" class="shareFeedBtn"><i class="icon-share"></i> <?php echo __d('faq', 'Share'); ?></a>
                <a class="view-details-faq" href="<?php echo $faq['Faq']['moo_href']; ?>" >
                    <?php echo __d('faq', "View details"); ?>
                </a>
                <div class="faq-userfull-content" id="userfull-<?php echo $faq['Faq']['id']; ?>">
                    <?php echo $this->element('lists/faqs_answer', array('faq' => $faq, 'settings' => false), array('plugin' => 'Faq')); ?>
                </div>
            </div>
        </div>
    </li>
<?php endforeach; ?>
<?php if ($category['is_view_all']): ?>
    <div class="view-all-faq">
        <a href="<?php echo $this->request->base . '/faqs/index/category:' . $category['FaqHelpCategorie']['id'] ?>/type:all"><?php echo __d('faq', 'View All') ?></a>
    </div>
    <div class="clear"></div>
<?php endif; ?>
<?php if (isset($category['is_view_more']) && $category['is_view_more']): ?>
    <?php $this->Html->viewMore($url_more, 'faq-list-content') ?>
<?php endif; ?>