<div class="box2">
	<?php $faqHelper = MooCore::getInstance()->getHelper('Faq_Faq'); ?>
    <div class="box_content header-box-faq" style="background-image: url('<?php echo $faqHelper->getBackground(Configure::read('Faq.faq_back_ground')); ?>');background-repeat: no-repeat;background-position: center;background-size: 100%;">
        <form action="<?php echo $this->request->base ?>/faqs/index">
            <div class="form-group search_form_head_faq">
                <input placeholder="<?php echo __d('faq','Hi, How can we help?') ?>" class="form-control input-medium input-inline " value="" type="text" name="content_search"/>
                <button class="btn btn-gray" id="submit_search_faq" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                <label style="opacity: 0;">_</label>
            </div>
            
        </form>
    </div>
    <div class="clear"></div>
</div>