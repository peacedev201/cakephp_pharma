<?php

echo $this->Html->css(array(
        'Business.star-rating',
        'Business.business'), array('block' => 'css', 'minify'=>false));
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_markerclusterer', 'business_star_rating'), 
    'object' => array('$', 'mooBusiness')
));?>
mooBusiness.initSearchPage();
mooBusiness.initPaginator();
<?php $this->Html->scriptEnd(); ?> 

<?php echo $this->Element('mobile_menu');?>
<?php if($is_app):?>
    <div id="map_canvas" style="width: 100%;height: 300px;display: none"></div>
<?php endif;?>
<div class="box2 filter_block">
    <h1 class="header_search_page <?php if($businesses != null):?> pull-left <?php endif; ?>">
        <?php echo sprintf(__d('business', 'Businesses %s %s'), !empty($keyword) ? __d('business', 'for ').$keyword : '', !empty($keyword_location) && !$all ? __d('business', 'near ').$keyword_location : '');?>
    </h1>
    <?php if($businesses != null):?>
    <div class="search-bu">
        <a class="button pull-right show_map" href="javascript:void(0)" id="hide_search_map">
            <span><?php echo __d('business', 'Show map');?></span>  
        </a>
        <div class="pull-right sort_bus">

            <?php echo $this->Form->select('sort_by', $filter, array(
                'empty' => false,
                'id' => 'search_filter',
                'data-current_link' => $current_link,
                'data-sort_by' => $sort_by,
                'value' => !empty($sort_by) ? $sort_by : '',
            ));?>
            <script type="text/template" id="locationDataTemplate">
                <?php echo $locations;?>
            </script>
        </div>
    </div>
    <div class="clear"></div>
    <ul class="bus_list bussiness-list">
            <?php 
                //$range = range('A', 'Z');
                foreach($businesses as $index => $business):
            ?>
                <?php echo $this->Element('Business.misc/business_item', array(
                    'index' => $index,
                    'business' => $business
                ));?>
            <?php endforeach;?>
    </ul>
    <?php if($this->Paginator->hasPage(2)):?>
    <ul class="pagination">
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('business', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('business', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('business', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('business', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
    </ul>
    <?php endif; ?>
    <?php else:?>
    <ul class="business_list">
        <li><?php echo __d('business', 'No businesses found');?></li>
    </ul>
    <?php endif;?>
    <div class="clear"></div>
</div>