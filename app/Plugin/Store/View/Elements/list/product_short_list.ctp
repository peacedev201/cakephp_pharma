<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo  __d('store', 'Choose Product'); ?></h4>
</div>
<div class="modal-body">
    <div class="shop-products row grid-view ajax-modal">
        <form id="productShortListForm">
            <div class="form-group  form-search-app">
                <div class="col-md-4">
                    <?php echo $this->Form->input("keyword", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'placeholder' => __d('store', "Keyword"),
                            'value' => !empty($search['keyword']) ? $search['keyword'] : ''
                        ));?>
                </div>
                <div class="col-md-3">
                    <?php echo $this->Form->input('search_type', array(
                            'options' => array(
                                '1' => __d('store', "Name"),
                                '2' => __d('store', "Product code")
                            ),
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                            'selected' => !empty($search['search_type']) ? $search['search_type'] : '',
                        ));?>
                </div>
                <div class="col-md-2">
                    <a class="btn btn-primary btn-lg load_product_short_list" href="javascript:void(0)">
                        <?php echo __d('store', "Search");?>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
        </form>
        <?php if(!empty($products)): ?>
        <table class="table table-striped table-bordered table-hover dataTable no-footer short-product-table">
            <thead>
                <tr>
                    <th style="width: 50px"><?php echo  __d('store', "Image"); ?></th>
                    <th style="width: 10%"><?php echo  __d('store', "Code"); ?></th>
                    <th style="text-align: left"><?php echo  __d('store', "Product Name"); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product):
                    $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
                    $product = $product['StoreProduct'];
                ?>
                <tr style="cursor: pointer" class="select_product">
                    <td>
                        <div class="tiny_img">
                            <img src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_TINY_WIDTH));?>" title="<?php echo $product['name'];?>" alt="<?php echo $product['name'];?>" />
                        </div>
                    </td>
                    <td>
                        <?php echo  $product['product_code'] ?>
                    </td>
                    <td style="text-align: left">
                        <a data-dismiss="modal" class="product_list" href="javascript:void(0);" data-id="<?php echo  $product['id'] ?>" data-product-code="<?php echo  $product['product_code'] ?>" data-name="<?php echo  $product['name']; ?>" data-price="<?php echo  $product['price'] ?>" >
                            <?php echo  $product['name'] ?> 
                        </a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <div class="row">
            <div id="product-short-list-paging" class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array('class' => 'paginate_button previous product_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array('class' => 'paginate_button previous product_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->numbers(array('class' => 'paginate_button product_short_list_link', 'tag' => 'li', 'separator' => '')); ?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array('class' => 'paginate_button next product_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array('class' => 'paginate_button next product_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                </ul>
            </div>
        </div>
        <?php else: ?>
            <?php echo  __d('store', "No products"); ?>
        <?php endif; ?>
    </div>
</div>