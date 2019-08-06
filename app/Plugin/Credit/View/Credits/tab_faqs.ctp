<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooCredit'), 'object' => array('$', 'mooCredit'))); ?>

<?php $this->Html->scriptEnd(); ?>
<div <?php if(!$this->request->is('ajax')): ?>class="content_center_home"<?php endif;?>>
	<div class="mo_breadcrumb">
        <h1><?php echo __d('credit', 'FAQs')?></h1>
    </div>

    <ul class="faq_list" id="list-content">
    	<?php echo $this->element( 'ajax/faqs_list' ); ?>
    </ul>
</div>


