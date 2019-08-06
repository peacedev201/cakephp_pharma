<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initForMobile();
<?php $this->Html->scriptEnd(); ?>

<div class="profile_plg_menu menu-bus-main">
    <div class="menu">
        <ul class="list2 menu_top_list">
            <li>
                <a href="javascript:void(0)" id="btn_search" data-id="mobile_search">
                    <i class="material-icons">search</i>  
                    <span><?php echo __d('business', 'Search');?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->request->base;?>/categories" <?php if($this->request->params['controller'] == 'business_categories' && $this->request->params['action'] == 'categories'):?>class="active"<?php endif;?> >
                    <i class="material-icons">featured_play_list</i>   
                    <span><?php echo __d('business', 'Category');?></span>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->request->base;?>/locations" <?php if($this->request->params['controller'] == 'business_locations' && $this->request->params['action'] == 'locations'):?>class="active"<?php endif;?>>
                    <i class="material-icons">room</i>   
                    <span><?php echo __d('business', 'Locations');?></span>
                </a>
            </li>
            <?php if($this->request->params['controller'] == 'business' && !empty($businesses) && in_array($this->request->params['action'], array('index', 'search')) && empty($this->request->params['task'])):?>
            <li>
                <a href="javascript:void(0)" id="hide_search_map" data-id="map_canvas">
                    <i class="material-icons">map</i>    
                    <span><?php echo __d('business', 'Map');?></span>
                </a>
            </li>
            <?php else:?>
            <li>
                <a href="<?php echo $this->request->base;?>/businesses"  <?php if($this->request->params['controller'] == 'business'):?>class="active"<?php endif;?>>
                    <i class="material-icons">business</i>    
                    <span><?php echo __d('business', 'Businesses');?></span>
                </a>
            </li>
            <?php endif;?>
        </ul>
    </div>
</div>

<div id="mobile_search" style="display: none">
    <?php echo $this->Element('search_form', array(
        'mobile' => true
    ));?>
</div>