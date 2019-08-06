<?php $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo  __d('store', 'Select Image'); ?></h4>
</div>
<div class="modal-body">
    <div class="shop-products row grid-view ajax-modal">
        <form id="imageShortListForm">
            <div class="form-group  form-search-app">
                <div class="col-md-4">
                    <?php echo $this->Form->input("keyword", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'placeholder' => __d('store', "Album name"),
                            'value' => !empty($search['keyword']) ? $search['keyword'] : ''
                        ));?>
                </div>
                <div class="col-md-2">
                    <a class="btn btn-primary btn-lg load_product_short_list" id="search_image_short_list" href="javascript:void(0)">
                        <?php echo __d('store', "Search");?>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
        </form>
        <?php if(!empty($images)): ?>
        <table class="table table-striped table-bordered table-hover dataTable no-footer short-product-table">
            <thead>
                <tr>
                    <th style="width: 50px"><?php echo  __d('store', "Image"); ?></th>
                    <th class="text-left"><?php echo  __d('store', "Album"); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($images as $image):
                    $album = $image['Album'];
                    $image = $image['Photo'];
                ?>
                <tr style="cursor: pointer" class="select_image" data-id="<?php echo $image['id'] ?>" data-filename="<?php echo $image['thumbnail'] ?>" data-image="<?php echo $photoHelper->getImage(array('Photo' => $image), array('prefix' => '150_square'));?>" >
                    <td>
                        <div class="tiny_img">
                            <img src="<?php echo $photoHelper->getImage(array('Photo' => $image), array('prefix' => '150_square'));?>" title="<?php echo $image['caption'];?>" alt="<?php echo $image['thumbnail'];?>" />
                        </div>
                    </td>
                    <td class="text-left">
                        <?php echo  $album['title'] ?>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <div class="row">
            <div id="image-short-list-paging" class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array('class' => 'paginate_button previous image_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array('class' => 'paginate_button previous image_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->numbers(array('class' => 'paginate_button image_short_list_link', 'tag' => 'li', 'separator' => '')); ?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array('class' => 'paginate_button next image_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array('class' => 'paginate_button next image_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                </ul>
            </div>
        </div>
        <?php else: ?>
            <?php echo  __d('store', "No images"); ?>
        <?php endif; ?>
    </div>
</div>