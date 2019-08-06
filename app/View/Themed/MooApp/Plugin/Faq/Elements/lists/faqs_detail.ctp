
<?php foreach ($category['Faqs'] as $faq): ?>
    <li class="full_content fag-listing" style="list-style: none outside none;">
        <div class="faq_info">
            <h5 class="js_drop_down_detail_faq">
                <a href="javascript:void(0)">
                    <?php echo $faq['Faq']['title']; ?>
                </a>
            </h5>
            <div class="list_body" style="display: none;">
                <ul class="list2 menu-list current" id="browse">
                    <li class="full_content faq-detail-body">
                        <div>
                            <p>
                                <?php echo $faq['Faq']['body']; ?>
                            </p>
                        </div>
                    </li>	
                </ul>
                <a class="view-details-faq" href="<?php echo $faq['Faq']['moo_href']; ?>" >
                    <?php echo __d('faq', "View details"); ?>
                </a>
                
                    <div class="onlistFAQ list_option">
                        <div class="dropdown">
                            <a style="text-transform: capitalize;font-size: 15px;width: 50px;border-radius: 0 !important;" id="video_edit_<?php echo $faq["Faq"]["id"] ?>" ><?php echo __d('faq', 'Share'); ?></a>
                            <ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect" for="video_edit_<?php echo $faq["Faq"]["id"] ?>">
                                <?php echo
                                $this->element(
                                    'share/menu',
                                    array('param' =>
                                        'Faq_Faq',
                                        'action' => 'faq_item_detail' ,
                                        'id'=>$faq['Faq']['id']
                                    )
                                ); ?>
                            </ul>
                        </div>
                    </div>
                <div class="faq-userfull-content" id="userfull-<?php echo $faq['Faq']['id']; ?>">
                    <?php echo $this->element('lists/faqs_answer', array('faq' => $faq,'settings'=>false), array('plugin' => 'Faq')); ?>
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
<script>
    function doRefesh()
    {
        location.reload();
    }
</script>
