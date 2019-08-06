<?php $faqHelper = MooCore::getInstance()->getHelper('Faq_Faq'); ?>
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
<div class="box2">
    <div class="box_content header-box-faq" style="background-image: url('<?php echo $this->request->base . Configure::read('Faq.faq_back_ground') ?>');background-repeat: no-repeat;background-position: center;background-size: 100%;">
        <form action="<?php echo $this->request->base ?>/faqs/index">
            <div class="form-group search_form_head_faq">
                <input placeholder="<?php echo __d('faq', 'Hi, How can we help?') ?>" class="form-control input-medium input-inline " value="" type="text" name="content_search"/>
                <input type="hidden" name="app_no_tab" value="1">
                <button class="btn btn-gray" id="submit_search_faq" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                <label style="opacity: 0;">_</label>
            </div>

        </form>
    </div>
    <div class="clear"></div>
</div>
<div class="bar-content">
    <div class="content_center">
        <ul id="list-content" class="faq-content-list">
            <select id="menu-category-faq" name="menu-category-faq">
                <?php if (empty($selected_category)): ?>
                    <?php //$lable = __d('faq', 'ALL'); ?>
                    <option><?php echo __d('faq', 'ALL') ?></option>
                <?php else : ?>
                    <option value="<?php echo $selected_category['FaqHelpCategorie']['id'] ?>"><?php echo $selected_category['FaqHelpCategorie']['name'] ?></option>
                    <?php //$lable = $selected_category['FaqHelpCategorie']['name']; ?>
                <?php endif; ?>
                <!--<optgroup label="<?php echo $lable ?>">-->
                    <?php foreach ($categories_menu as $category_menu): ?>
                        <option value="<?php echo $category_menu['FaqHelpCategorie']['id'] ?>"><?php echo $category_menu['FaqHelpCategorie']['name'] ?></option>
                    <?php endforeach; ?>
                <!--</optgroup>-->
            </select>
            <?php if ($is_search): ?>
                <h5><?php echo __d('faq', "We found %s results", count($category['Faqs'])); ?></h5>
                <?php echo $this->element('lists/faqs_detail', array('url_more' => $url_more, 'category' => $category), array('plugin' => 'Faq')); ?>
            <?php else: ?>
                <?php if (count($categories)): ?>
                    <?php echo $this->element('lists/faqs', array('is_view_more' => $is_view_more, 'url_more' => $url_more, 'categories' => $categories, 'floor_category' => $floor_category), array('plugin' => 'Faq')); ?>
                <?php else: ?>		
                    <li class="clear text-center"><?php echo __d('faq', 'No more results found'); ?></li>
                    <?php endif; ?>
                <?php endif; ?>
        </ul>
    </div>
</div>