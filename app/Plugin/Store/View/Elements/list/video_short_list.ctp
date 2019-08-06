<?php $videoHelper = MooCore::getInstance()->getHelper('Video_Video');?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo  __d('store', 'Select Video'); ?></h4>
</div>
<div class="modal-body">
    <div class="shop-products row grid-view ajax-modal">
        <form id="videoShortListForm">
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
                <div class="col-md-2">
                    <a class="btn btn-primary btn-lg load_product_short_list" id="search_video_short_list" href="javascript:void(0)">
                        <?php echo __d('store', "Search");?>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
        </form>
        <?php if(!empty($videos)): ?>
        <table class="table table-striped table-bordered table-hover dataTable no-footer short-product-table">
            <thead>
                <tr>
                    <th style="width: 50px"><?php echo  __d('store', "Image"); ?></th>
                    <th class="text-left"><?php echo  __d('store', "Video Name"); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($videos as $video):
                    $video = $video['Video'];
                ?>
                <tr style="cursor: pointer" class="select_video" data-id="<?php echo  $video['id'] ?>" data-image="<?php echo $videoHelper->getImage(array('Video' => $video), array('prefix' => '250')) ?>" data-title="<?php echo  $video['title'] ?>">
                    <td>
                        <div class="tiny_img">
                            <img src="<?php echo $videoHelper->getImage(array('Video' => $video), array('prefix' => '75_square'));?>" title="<?php echo $video['title'];?>" alt="<?php echo $video['title'];?>" />
                        </div>
                    </td>
                    <td class="text-left">
                        <a data-dismiss="modal" class="product_list" href="javascript:void(0);">
                            <?php echo  $video['title'] ?> 
                        </a>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <div class="row">
            <div id="video-short-list-paging" class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array('class' => 'paginate_button previous video_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array('class' => 'paginate_button previous video_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->numbers(array('class' => 'paginate_button video_short_list_link', 'tag' => 'li', 'separator' => '')); ?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array('class' => 'paginate_button next video_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array('class' => 'paginate_button next video_short_list_link', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                </ul>
            </div>
        </div>
        <?php else: ?>
            <?php echo  __d('store', "No videos"); ?>
        <?php endif; ?>
    </div>
</div>