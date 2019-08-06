<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBusiness"], function($, mooBusiness) {
        mooBusiness.initAdvancedSearchDialog();
    });
</script>
<?php endif?>
<div class="title-modal">
    <?php echo __d('business', 'Advanced Search');?>
    <button data-dismiss="modal" class="close" type="button">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <form class="global-search-bus popup_search_bus" id="formGlobalSearch">
        <div class="bus_main_form">
        <?php echo $this->Form->hidden('advanced', array(
            'id' => 'is_advanced',
            'value' => 1
        ));?>
        <div class="bus-input">
        <input type="text" class="global_search_category" name="keyword" placeholder="<?php echo __d('business', 'Find')?>">
        </div>
        <div class="bus-input">
        <input type="text" class="global_search_location" name="keyword_location" placeholder="<?php echo __d('business', 'Search address')?>" value="<?php echo $default_location_name;?>">

        </div>
        <input type="button" class="button" value="<?php echo __d('business', 'Go')?>" id="btn_global_search"/>
        </div>
        <div class="bus-input_option">
            <div class="search_cat">
            <?php echo $this->Form->checkbox('cat_name', array(
                'hiddenField' => false,
                'id' => 'category_name'
            ));?>
            <label for="category_name">
                <?php echo __d('business', 'Category name');?>
            </label>
            </div>
            <div class="search_cat">
            <?php echo $this->Form->checkbox('listing', array(
                'hiddenField' => false,
                'id' => 'listing_content'
            ));?>
            <label for="listing_content">
                <?php echo __d('business', 'Listing content');?>
            </label>
            </div>
            <div class="bus_search_des">

            <?php echo __d('business', '(category name, listing name, listing description, business type and company number)');?>
            </div>
        </div>
        <div class="clear"></div>
        <div class="bus-input_option">
            <ul class="list_distance">
            <li class="distance_item first_item">
                <input type="radio" name="data[distance]" id="miles1" value="0" checked="true" />
                <span class="adv_label"></span>
                <label for="miles1">
                    <span class="visible-xs visible-sm">
                        <i class="material-icons">location_on</i>
                    </span>
                    <span class="hidden-xs hidden-sm">

                        <?php echo __d('business', 'Location');?>
                    </span>
                </label>
            </li>
            <?php if($miles != null):?>
                <?php $max_distance = 0; ?>
                <?php foreach($miles as $mile):?>
                    <?php
                        if($mile > $max_distance) $max_distance = $mile;
                    ?>
                <?php endforeach;?>
                <?php 
                    $mini_distance = (1/$max_distance)*100;
                    //$current_mile = 0;
                    $z_index = count($miles) + 2;
                ?>

                <?php foreach($miles as $mile):?>

                    <li class="distance_item" style="width: <?php echo ($mile * $mini_distance) ?>%;z-index:<?php //echo $z_index--; ?>">
                        <input type="radio" name="data[distance]" id="miles<?php echo $mile;?>" value="<?php echo $mile;?>" />
                        <span class="adv_label"></span>
                        <label for="miles<?php echo $mile;?>">
                            <?php echo sprintf(__d('business', '%s miles'), $mile);?>
                        </label>
                     </li>
                     <?php $z_index--; ?>
                <?php endforeach;?>
            <?php endif;?>
            </ul>
        </div>
        <div class="clear"></div>
        
        <div style="display:none;" class="error-message" id="advancedSearchMessage"></div>
        <div class="clear"></div>
    </form>
</div>
