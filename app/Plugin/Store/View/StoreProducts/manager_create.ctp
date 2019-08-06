<?php
echo $this->Html->css(array(
    'fineuploader', 
    'Store.jquery-ui'), array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager', 'store_jquery_ui'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initCreateProduct();
    store_manager.initSelectAttributeDlg();
    <?php if(!empty($product['id'])):?>
    store_manager.loadProductAttributes('<?php echo $product['id'];?>', 1);
    <?php endif;?>
<?php $this->Html->scriptEnd(); ?>
    
<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php echo $this->Element('manager_menu'); ?>
<?php $this->end(); ?>
    
<div class="bar-content">
    <?php echo $this->Element('Store.mobile/mobile_manager_menu'); ?>
    <div class="content_center">
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo STORE_MANAGER_URL;?>">
                    <i class="material-icons">home</i>
                </a>
                <span class="divider"></span>
            </li>
            <li>
                <a href="<?php echo $url;?>">
                    <?php echo __d('store', "Manage Products");?>
                </a>
                <span class="divider"></span>
            </li>
            <li class="first">
                <a class="active" href="<?php echo $url;?>create">
                    <?php if($product['id'] > 0):?>
                        <?php echo __d('store', "Edit Product");?>
                    <?php else:?>
                        <?php echo __d('store', "Create Product");?>
                    <?php endif;?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php if($product['id'] > 0):?>
                    <?php echo __d('store', "Edit Product");?>
                <?php else:?>
                    <?php echo __d('store', "Create Product");?>
                <?php endif;?>
                <div class="pull-right">
                    <a id="btnSave" type="button" class="btn btn-primary" href="javascript:void(0)">
                        <?php echo __d('store', 'Save');?>
                    </a>
                    <a id="btnApply" type="button" class="btn btn-primary" href="javascript:void(0)">
                        <?php echo __d('store', 'Apply');?>
                    </a>
                    <a id="btnCancel" type="button" class="btn btn-primary" onclick="<?php echo $is_app ? "window.mobileAction.backOnly();" : "window.location = '".$url."'"?>">
                        <?php echo __d('store', 'Cancel');?>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="panel-body">
                <div id="errorMessage" class="error-message" style="display: none"></div>
                <div class="Metronic-alerts alert alert-success fade in" style="display: none"></div>
                <form class="form-horizontal" id="formProduct" method="post">
                    <?php echo $this->Form->hidden('id', array(
                        'value' => $product['id']
                    ));?>
                    <?php echo $this->Form->hidden('save_type', array(
                        'value' => 0
                    ));?>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#general" aria-controls="general" role="tab" data-toggle="tab"><?php echo __d('store', "General");?></a>
                        </li>
                        <li role="presentation">
                            <a href="#images" aria-controls="images" role="tab" data-toggle="tab"><?php echo __d('store', "Images");?></a>
                        </li>
                        <li role="presentation">
                            <a href="#videos" aria-controls="videos" role="tab" data-toggle="tab"><?php echo __d('store', "Video");?></a>
                        </li>
                        <li role="presentation">
                            <a href="#others" aria-controls="others" role="tab" data-toggle="tab"><?php echo __d('store', "Others");?></a>
                        </li>
                        <li role="presentation">
                            <a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab"><?php echo __d('store', "Attributes");?></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="general">
                            <div class="form-group">
                                <div class="col-md-2 col-xs-2 col-sm-2">
                                    <label for="enable"><?php echo __d('store', "Enable");?></label>
                                </div>
                                <div class="col-sm-3 col-xs-3">
                                    <?php echo $this->Form->checkbox('enable', array(
                                        'hidden' => false,
                                        'checked' => $product['id'] > 0 ? $product['enable'] : true
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="product_code"><?php echo __d('store', "Product Code");?> <span class="required">*</span></label>
                                </div>
                                <div class="col-sm-3">
                                    <?php echo $this->Form->input("product_code", array(
                                        'div' => false,
                                        'label' => false,
                                        'class' => 'form-control',
                                        'value' => $product['product_code']
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <labe><?php echo __d('store', "Product Name");?> <span class="required">*</span></label>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $this->Form->input("name", array(
                                        'div' => false,
                                        'label' => false,
                                        'class' => 'form-control',
                                        'value' => $product['name']
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <labe><?php echo __d('store', "Product Type");?> <span class="required">*</span></label>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $this->Form->select("product_type", $this->Store->loadProductType(), array(
                                        'div' => false,
                                        'label' => false,
                                        'empty' => false,
                                        'class' => 'form-control',
                                        'value' => $product['product_type']
                                    ));?>
                                    <div class="for_digital_product" style="margin-top:5px; <?php if($product['product_type'] != STORE_PRODUCT_TYPE_DIGITAL):?>display: none<?php endif;?>">
                                        <?php echo $this->Form->hidden("digital_file", array(
                                            'value' => $product['digital_file'],
                                        ));?>
                                        <?php echo __d('store', "Allow extensions").': '.Configure::read('Store.store_allow_digital_file_extensions');?>
                                        <div id="product_digital_file"></div>
                                        <div id="product_digital_file_preview">
                                            <?php if (!empty($product['digital_file'])): ?>
                                                <a href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>" target="_blank">
                                                    <?php echo $product['digital_file']?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="for_link_product" style="margin-top:5px; <?php if($product['product_type'] != STORE_PRODUCT_TYPE_LINK):?>display: none<?php endif;?>">
                                        <?php echo $this->Form->input("product_link", array(
                                            'div' => false,
                                            'label' => false,
                                            'placeholder' => __d('store', "Product link"),
                                            'class' => 'form-control',
                                            'value' => $product['product_link']
                                        ));?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label><?php echo __d('store', "Category");?> <span class="required">*</span></label>
                                </div>
                                <div class="col-sm-4">
                                    <select name="data[store_category_id]" class="form-control">
                                        <option value="0"><?php echo __d('store', "Select category");?></option>
                                        <?php echo $this->Category->outputOptionType($storeCats, 'StoreCategory', array('0'), $product['store_category_id']);?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label><?php echo __d('store', "Producer");?></label>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $this->Form->input("producer_id", array(
                                        'div' => false,
                                        'label' => false,
                                        'class' => 'form-control',
                                        'options' => $producers,
                                        'value' => $product['producer_id'],
                                        'empty' => __d('store',  "Select Producer")
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label><?php echo __d('store', "Price");?></label>
                                </div>
                                <div class="col-sm-3">
                                    <?php echo $this->Form->input("price", array(
                                        'div' => false,
                                        'label' => false,
                                        'class' => 'form-control',
                                        'value' => !empty($product['price']) ? $product['price'] : 0
                                    ));?>
                                </div>
                                <div class="clear"></div>
                                <div class="col-md-2">
                                </div>
                                <div class="col-sm-10">
                                    <?php echo __d('store', "This is based price of product, you can specify the price increment / decrement from this price based for each product attribute");?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="enable"><?php echo __d('store', "Quantity");?></label>
                                </div>
                                <div class="col-sm-3">
                                    <label>
                                        <?php echo $this->Form->checkbox('show_quanity', array(
                                            'hidden' => false,
                                            'checked' => $product['id'] > 0 ? $product['show_quanity'] : true
                                        ));?>
                                        <?php echo __d('store', "Show the quantity field?");?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label><?php echo __d('store', "Brief Information");?></label>
                                </div>
                                <div class="col-sm-10">
                                    <?php echo $this->Form->textarea('brief', array(
                                        'value' => $product['brief'], 
                                        'class' => 'form-control',
                                        'rows' => 10 
                                    )); ?>
                                    <?php echo __d('store', "A short description about product (max 255 characters)");?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label><?php echo __d('store', "Description");?></label>
                                </div>
                                <div class="col-sm-10">
                                    <?php echo $this->Form->tinyMCE('article', array(
                                        'value' => $product['article'], 
                                        'id' => 'editor2',
                                        'plugins' => 'emoticons link image code',
                                        'toolbar1' => (!$is_app && !$is_mobile) ? 'bold italic underline strikethrough | bullist numlist | link unlink image emoticons blockquote code' : 'bold italic underline strikethrough | bullist numlist | unlink emoticons blockquote'
                                    )); ?>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="images">
                            <?php echo sprintf(__d('store', 'The minimum image width must be %spx.'), PRODUCT_UPLOAD_MIN_WIDTH);?><br/>
                            <?php echo sprintf(__d('store', 'For the best image view, we suggest a square image dimension with width and height are higher than %spx.'), PRODUCT_PHOTO_LARGE_WIDTH);?><br/><br/>
                            <div id="product_image"></div>
                            <div class="clear"></div>
                            <div id="product_image_preview" style="margin-top:10px;">

                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tbImage">
                                <thead>
                                    <tr>
                                        <th><?php echo __d('store', 'Image');?></th>
                                        <th style="width: 10%" class="text-center"><?php echo __d('store', 'Main');?></th>
                                        <th style="width: 10%" class="text-center"><?php echo __d('store', 'Enable');?></th>
                                        <th style="width: 10%" class="text-center"><?php echo __d('store', 'Ordering');?></th>
                                        <th style="width: 5%" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($productImages)):?> 
                                        <?php foreach($productImages as $productImage):?> 
                                            <tr>
                                                <td>
                                                    <img class="product_image_item" src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH));?>">
                                                    <?php echo $this->Form->hidden('', array(
                                                        'id' => false,
                                                        'name' => 'images[is_select][]',
                                                        'class' => 'image_select',
                                                    ));?>
                                                    <?php echo $this->Form->hidden('', array(
                                                        'id' => false,
                                                        'name' => 'images[image_id][]',
                                                        'class' => 'image_id',
                                                        'div' => false,
                                                        'value' => $productImage['id']
                                                    ));?>
                                                    <?php echo $this->Form->hidden('', array(
                                                        'id' => false,
                                                        'name' => 'images[path][]',
                                                        'class' => 'image_path',
                                                        'div' => false,
                                                        'value' => $productImage['path']
                                                    ));?>
                                                    <?php echo $this->Form->hidden('', array(
                                                        'id' => false,
                                                        'name' => 'images[filename][]',
                                                        'class' => 'image_filename',
                                                        'div' => false,
                                                        'value' => $productImage['filename']
                                                    ));?>
                                                </td>
                                                <td class="text-center" style="vertical-align: middle">
                                                    <input class="is_main" name="images[is_main]" type="radio" <?php if($productImage['is_main'] == 1):?>checked="true"<?php endif;?>>
                                                </td>
                                                <td class="text-center" style="vertical-align: middle">
                                                    <?php echo $this->Form->checkbox('enable', array(
                                                        'name' => 'images[enable][]',
                                                        'hiddenField' => false,
                                                        'checked' => $productImage['enable'],
                                                        'class' => 'enable_image'
                                                    ));?>
                                                </td>
                                                <td class="text-center" style="vertical-align: middle">
                                                    <input class="btn btn-up" type="button" title="<?php echo __d('store', 'Up');?>" style="display: none;">
                                                    <input class="btn btn-down" type="button" title="<?php echo __d('store', 'Down');?>" style="display: none;">
                                                </td>
                                                <td class="text-center" style="vertical-align: middle">
                                                    <a href="javascript:void(0)" class="btn btn-primary delete_image">
                                                        <?php echo __d('store', 'Delete');?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                    <?php endif;?> 
                                </tbody>
                          </table>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="videos">
                            <div class="pull-right">
                                <a href="<?php echo $this->request->base;?>/stores/manager/products/create_video" data-target="#storeModal" data-toggle="modal" class="button button-action topButton button-mobi-top" title="<?php echo __d('store', 'Upload Video');?>" data-dismiss="" data-backdrop="static">
                                    <?php echo __d('store', 'Upload Video');?>
                                </a>
                            </div>
                            <br/><br/>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tbVideo">
                                    <thead>
                                        <tr>
                                            <th style="width: 75%"><?php echo __d('store', 'Video');?></th>
                                            <th style="width: 10%" class="text-center"><?php echo __d('store', 'Enable');?></th>
                                            <th style="width: 10%" class="text-center"><?php echo __d('store', 'Ordering');?></th>
                                            <th style="width: 5%" class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="others">
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 col-xs-4">
                                    <label><?php echo __d('store', "Out of stock");?></label>
                                </div>
                                <div class="col-sm-3 col-xs-3">
                                    <?php echo $this->Form->checkbox('out_of_stock', array(
                                        'hidden' => false,
                                        'checked' => ($product['out_of_stock'] == 1) ? true : false
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 col-xs-4">
                                    <label><?php echo __d('store', "Allow share");?></label>
                                </div>
                                <div class="col-sm-3 col-xs-3">
                                    <?php echo $this->Form->checkbox('allow_share', array(
                                        'hidden' => false,
                                        'checked' =>(empty($product['id']) || $product['allow_share'] == 1) ? true : false
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 col-xs-4">
                                    <label><?php echo __d('store', "Allow discussion");?></label>
                                </div>
                                <div class="col-sm-3 col-xs-3">
                                    <?php echo $this->Form->checkbox('allow_comment', array(
                                        'hidden' => false,
                                        'checked' => (empty($product['id']) || $product['allow_comment'] == 1) ? true : false
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 col-xs-4">
                                    <label><?php echo __d('store', "Allow review");?></label>
                                </div>
                                <div class="col-sm-3 col-xs-3">
                                    <?php echo $this->Form->checkbox('allow_review', array(
                                        'hidden' => false,
                                        'checked' => (empty($product['id']) || $product['allow_review'] == 1) ? true : false
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <labe><?php echo __d('store', "Warranty");?></label>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $this->Form->input("warranty", array(
                                        'div' => false,
                                        'label' => false,
                                        'class' => 'form-control',
                                        'value' => $product['warranty']
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <labe>
                                        <?php echo __d('store', "Weight");?>
                                        (<?php echo __d('store', "kg");?>)
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $this->Form->input("weight", array(
                                        'div' => false,
                                        'label' => false,
                                        'class' => 'form-control',
                                        'value' => $product['weight']
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2 col-sm-2 col-xs-4">
                                    <label><?php echo __d('store', "Allow promotion");?></label>
                                </div>
                                <div class="col-sm-3 col-xs-3">
                                    <?php echo $this->Form->checkbox('allow_promotion', array(
                                        'hidden' => false,
                                        'checked' => $product['allow_promotion'],
                                    ));?>
                                </div>
                            </div>
                            <div class="form-group promotion-group">
                                <div class="col-md-2">
                                    <labe><?php echo __d('store', "Promotion period");?></label>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-xs-2">
                                        <?php echo __d('store', "Start");?>
                                    </div>
                                    <div class="col-xs-4">
                                        <?php echo $this->Form->input("promotion_start", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => !empty($product['promotion_start']) ? date('m/d/Y', strtotime($product['promotion_start'])) : ''
                                        ));?>
                                    </div>
                                    <div class="col-xs-2">
                                        <?php echo __d('store', "End");?>
                                    </div>
                                    <div class="col-xs-4">
                                        <?php echo $this->Form->input("promotion_end", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => !empty($product['promotion_end']) ? date('m/d/Y', strtotime($product['promotion_end'])) : ''
                                        ));?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group promotion-group">
                                <div class="col-md-2">
                                    <labe><?php echo __d('store', "Promotion discount");?></label>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $this->Form->input("promotion_price", array(
                                        'div' => false,
                                        'label' => false,
                                        'class' => 'form-control',
                                        'value' => !empty($product['promotion_price']) ? $product['promotion_price'] : 0
                                    ));?>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="attributes">
                            <p><?php echo __d('store', 'You can create multiple attributes for your product like size, color, etc. These attributes will be visible to buyers on the product details page. For each attribute you can specify the price increment / decrement from base price of product.');?></p>
                            <a class="btn btn-primary" href="javascript:void(0)" id="btnShowAttrDlg">
                                <?php echo __d('store', 'Add attribute');?>
                            </a>
                            <div id="attributeToBuy"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    
<script type="text/template" id="imgItemTemplate">
    <tr>
        <td>
            <div style="width:100px;height:100px;overflow:hidden">
                <img class="image_image" style="max-width:100%" src="">
                <?php echo $this->Form->hidden('', array(
                    'id' => false,
                    'name' => 'images[is_select][]',
                    'class' => 'image_select',
                ));?>
                <?php echo $this->Form->hidden('', array(
                    'id' => false,
                    'name' => 'images[image_id][]',
                    'class' => 'image_id',
                ));?>
                <?php echo $this->Form->hidden('', array(
                    'id' => false,
                    'name' => 'images[path][]',
                    'class' => 'image_path',
                ));?>
                <?php echo $this->Form->hidden('', array(
                    'id' => false,
                    'name' => 'images[filename][]',
                    'class' => 'image_filename',
                ));?>
                <?php echo $this->Form->hidden('', array(
                    'id' => false,
                    'name' => 'images[select_image_id][]',
                    'class' => 'select_image_id',
                    'div' => false
                ));?>
          </div>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <input class="is_main" name="images[is_main]" type="radio">
        </td>
        <td class="text-center" style="vertical-align: middle">
            <?php echo $this->Form->checkbox('enable', array(
                'name' => 'images[enable][]',
                'hiddenField' => false,
                'id' => false,
                'checked' => true,
                'class' => 'enable_image'
            ));?>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <input class="btn btn-up" type="button" title="<?php echo __d('store', 'Up');?>" style="display: none;">
            <input class="btn btn-down" type="button" title="<?php echo __d('store', 'Down');?>" style="display: none;">
        </td>
        <td class="text-center" style="vertical-align: middle">
            <a href="javascript:void(0)" class="btn btn-primary delete_image">
                <?php echo __d('store', 'Delete');?>
            </a>
        </td>
    </tr>
</script>

<script type="text/template" id="videoItemTemplate">
    <tr>
        <td>
            <?php echo $this->Form->hidden('', array(
                'id' => false,
                'name' => 'videos[video_id][]',
                'class' => 'video_id',
                'div' => false
            ));?>
            <?php echo $this->Form->hidden('', array(
                'id' => false,
                'name' => 'videos[product_video_id][]',
                'class' => 'product_video_id',
                'div' => false
            ));?>
            <img class="video_image" src="">
            <div class="video_name"></div>
            <div class="clear"></div>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <?php echo $this->Form->checkbox('enable', array(
                'name' => 'videos[enable][]',
                'hiddenField' => false,
                'id' => false,
                'checked' => true,
                'class' => 'enable_video'
            ));?>
        </td>
        <td class="text-center" style="vertical-align: middle">
            <input class="btn btn-up" type="button" title="<?php echo __d('store', 'Up');?>" style="display: none;">
            <input class="btn btn-down" type="button" title="<?php echo __d('store', 'Down');?>" style="display: none;">
        </td>
        <td class="text-center" style="vertical-align: middle">
            <a href="javascript:void(0)" class="btn btn-primary delete_video">
                <?php echo __d('store', 'Delete');?>
            </a>
        </td>
    </tr>
</script>

<script type="text/template" id="videoData">
    <?php echo !empty($productVideos) ? json_encode($productVideos) : json_encode(array());?>
</script>

<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>