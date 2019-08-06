<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness'), 
    'object' => array('$', 'mooBusiness')
));?>
mooBusiness.initPaginator();
<?php $this->Html->scriptEnd(); ?> 

<div class="mo_breadcrumb">
    <h1 class="text-all-bu"><?php echo __d('business', 'All Businesses');?></h1>
    <?php if($can_create_business):?>
    <a class="button button-action topButton button-mobi-top" href="<?php echo $this->request->base."/businesses/create";?>">
        <?php echo __d('business', 'Add Business');?>
    </a>
    <?php endif;?>
    <?php if($businesses != null):?>
    <a class="button button-action topButton button-mobi-top pull-right show_map" href="javascript:void(0)" id="hide_search_map">
        <span><?php echo __d('business', 'Show map');?> </span> 
    </a>
    <?php endif;?>
</div>
<ul id="list-content" class="bussiness-list">
    <?php echo $this->Element('Business.lists/business_list', array(
        'businesses' => $businesses,
        'paging' => true,
        'index' => true
    ));?>
</ul>
<?php if($this->Paginator->hasPage(2)):?>
<ul class="pagination">
    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('business', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('business', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
    <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('business', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('business', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
</ul>
<?php endif;?>
<script type="text/template" id="locationDataTemplate">
    <?php echo $locations;?>
</script>