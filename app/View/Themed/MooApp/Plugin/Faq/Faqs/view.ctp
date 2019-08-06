<?php
$faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooFaq', 'hideshare'), 'object' => array('$', 'mooFaq'))); ?>
mooFaq.initCreateFaq();
<?php $this->Html->scriptEnd(); ?>
<div class="box2">
    <div class="box_content header-box-faq" style="background-image: url('<?php echo $this->request->base . Configure::read('Faq.faq_back_ground') ?>');background-repeat: no-repeat;background-position: center;background-size: 100%;">
        <form action="<?php echo $this->request->base ?>/faqs/index">
            <div class="form-group search_form_head_faq">
                <input placeholder="<?php echo __d('faq', 'Hi, How can we help?') ?>" class="form-control input-medium input-inline " value="" type="text" name="content_search"/>
                <button class="btn btn-gray" id="submit_search_faq" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                <label style="opacity: 0;">_</label>
            </div>

        </form>
    </div>
    <div class="clear"></div>
</div>
<div id="list-content">
    <div class="bar-content full_content">
        <div class="content_center">
            <div id="list-content">
<!--                <div class="create_form">
                    <select id="menu-category-faq" name="menu-category-faq">
                        <?php if (empty($selected_category)): ?>
                            <?php //$lable = __d('faq', 'ALL'); ?>
                            <option><?php echo __d('faq', 'ALL') ?></option>
                        <?php else : ?>
                            <option value="<?php echo $selected_category['FaqHelpCategorie']['id'] ?>"><?php echo $selected_category['FaqHelpCategorie']['name'] ?></option>
                            <option value="<?php echo '0' ?>"><?php echo __d('faq', 'Back') ?></option>
                            <?php //$lable = $selected_category['FaqHelpCategorie']['name']; ?>
                        <?php endif; ?>
            <optgroup label="<?php echo $lable ?>">
                        <?php foreach ($categories_menu as $category_menu): ?>
                            <option value="<?php echo $category_menu['FaqHelpCategorie']['id'] ?>"><?php echo $category_menu['FaqHelpCategorie']['name'] ?></option>
                        <?php endforeach; ?>
                        </optgroup>
                    </select> 
                </div>-->

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
            </div>

            <div class="post_body">	
                <ul class="faq-view-detail">
                    <h1 class="faq-detail-title"><?php echo h($faq['Faq']['title']) ?></h1>
                    <div class="faq-detail-action moo_app_faq_action">
                            <div class="list_option">
                                <div class="dropdown">
                                    <button id="video_edit_<?php echo $faq["Faq"]["id"] ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon">
                                        <i class="material-icons">more_vert</i>
                                    </button>
                                    <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="video_edit_<?php echo $faq["Faq"]["id"] ?>">
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
                        </div>
                    <li class="full_content faq_list" style="list-style: none outside none;">
                        <div>
                            <p>
                                <?php echo $faq['Faq']['body']; ?>		
                            </p>
                        </div>
                    <div class="faq-userfull-content" id="userfull-<?php echo $faq['Faq']['id']; ?>">
                        <?php echo $this->element('lists/faqs_answer', array('faq' => $faq,'settings'=>false), array('plugin' => 'Faq')); ?>
                    </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php if ($faq['Faq']['alow_comment']) : ?>
        <div class="bar-content full_content faq-comment">
            <?php echo $this->renderComment(); ?>
        </div>
    <?php endif; ?>
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
</div>
