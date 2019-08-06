<form class="form_search_product" action="<?php echo $this->request->base.'/stores';?>">
    <?php if($is_app):?>
        <?php echo $this->Form->hidden('app_no_tab', array(
            'value' => 1
        ));?>
    <?php endif;?>
    <ul class="list6">							
        <li>
            <label><?php echo __d('store', 'Product name');?></label>
            <?php echo $this->Form->text('keyword', array(
                'class' => 'orderby',
                'label' => false,
                'div' => false,
                'name' => 'keyword',
                'value' => !empty($this->request->query['keyword']) ? $this->request->query['keyword'] : ''
            ));?>
        </li>
        <?php if(empty($select_store_category_id)):?>
        <li>
            <label><?php echo __d('store', 'Category name');?></label>
            <?php echo $this->Form->hidden('store_category_id', array(
                'id' => 'search_category_id',
                'name' => 'store_category_id',
                'value' => !empty($this->request->query['store_category_id']) ? $this->request->query['store_category_id'] : ''
            ));?>
            <?php echo $this->Form->text('suggest_category', array(
                'class' => 'suggest_category',
                'label' => false,
                'div' => false,
                'name' => 'suggest_category',
                'value' => !empty($this->request->query['suggest_category']) ? $this->request->query['suggest_category'] : ''
            ));?>
        </li>
        <?php else:?>
            <?php echo $this->Form->hidden('store_category_id', array(
                'id' => 'search_category_id',
                'name' => 'store_category_id',
                'value' => !empty($store_category_id) ? $store_category_id : ''
            ));?>
        <?php endif;?>
        <li>
            <label><?php echo __d('store', 'Product Type');?></label>
            <?php 
            $product_types = $this->Store->loadProductType();
            array_unshift($product_types, array(
                '' => __d('store', 'All types')
            ));
            echo $this->Form->select('product_type', $product_types, array(
                'empty' => false,
                'name' => 'product_type',
                'value' => !empty($this->request->query['product_type']) ? $this->request->query['product_type'] : ''
            ));?>
        </li>
        <li>
            <label><?php echo __d('store', 'Sort By');?></label>
            <?php echo $this->Form->select('sortby', $this->Store->loadProductSorting(), array(
                'empty' => false,
                'class' => 'orderby',
                'name' => 'sortby',
                'value' => !empty($this->request->query['sortby']) ? $this->request->query['sortby'] : (isset($default_sorting) ? $default_sorting : '')
            ));?>
        </li>
        <li>
            <button class="btn btn-action padding-button" id="btn_search_product">
                <?php echo __d('store', 'Search');?>
            </button>
        </li>
    </ul>
</form>